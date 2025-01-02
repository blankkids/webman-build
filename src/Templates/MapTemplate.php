<?php

namespace Blankkids\WebmanBuild\Templates;

class MapTemplate
{
    /**
     * @param $name
     * @param $module_name
     * @return array
     */
    public static function getConfig($name, $module_name)
    {
        $class = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.map', '');
        if ($suffix && !strpos($class, $suffix)) {
            $class .= $suffix;
        }
        $class = str_replace('\\', '/', $class);
        $namespace = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'map';
        $file = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'map' . DIRECTORY_SEPARATOR . $class . '.php';

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
            printf("%s 已经存在!跳过创建映射‌枚举\n", $file);
            return;
        }

        $enum_config = EnumTemplate::getConfig($name, $module_name);
        $enum_class = $enum_config['class'];
        $enum_namespace = $enum_config['namespace'];

        $use_enum = $enum_namespace . DIRECTORY_SEPARATOR . $enum_class;
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $controller_content = <<<EOF
<?php

namespace $namespace;

use $use_enum;

/**
 * notes: 映射‌枚举
 * desc: 转化枚举值(一个表对应一个枚举类对应一个映射‌枚举)
 */
class $class
{
    /** @return mixed 禁用状态: 1-开启，2-关闭 */
    public static function getStatusMap()
    {
        return [
            $enum_class::DISABLE_STATUS => '禁用',
            $enum_class::ENABLE_STATUS => '启用',
        ];
    }
}

EOF;
        file_put_contents($file, $controller_content);
    }


}