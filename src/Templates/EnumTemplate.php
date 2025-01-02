<?php

namespace Blankkids\WebmanBuild\Templates;

class EnumTemplate
{
    /**
     * @param $name
     * @param $module_name
     * @return array
     */
    public static function getConfig($name, $module_name)
    {
        $class = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.enum', '');
        if ($suffix && !strpos($class, $suffix)) {
            $class .= $suffix;
        }
        $class = str_replace('\\', '/', $class);
        $namespace = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name  . DIRECTORY_SEPARATOR . 'enum';
        $file = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name  . DIRECTORY_SEPARATOR . 'enum' . DIRECTORY_SEPARATOR . $class . '.php';

        return [
            'class' => $class,
            'namespace' => $namespace,
            'file' => $file,
        ];
    }

    /**
     * @param $name
     * @param $module_name
     * @return void
     */
    public static function create($name, $module_name)
    {
        $config = self::getConfig($name, $module_name);
        $class = $config['class'];
        $namespace = $config['namespace'];
        $file = $config['file'];

        $path = pathinfo($file, PATHINFO_DIRNAME);

        if (is_file($file)) {
            printf("%s 已经存在!跳过创建枚举\n", $file);
            return;
        }

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $controller_content = <<<EOF
<?php

namespace $namespace;

/**
 * notes: 数据单元常量
 * desc: 状态层 - 业务中用到的常量,统一放这里, 一个数据单元 对应 一个常量类(一个表对应一个枚举类对应一个映射‌枚举)
 */
class $class
{
    /** @var int 禁用状态: 1-开启，2-关闭 */
    const DISABLE_STATUS = 1;
    const ENABLE_STATUS  = 2;

}

EOF;
        file_put_contents($file, $controller_content);
    }


}