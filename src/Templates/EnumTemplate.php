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
        $file_path = config('plugin.blankkids.webman-build.app.child_path.enum', '');
        if ($suffix && !strpos($class, $suffix)) {
            $class .= $suffix;
        }
        $class = str_replace('\\', '/', $class);
        $domain_path = config('plugin.blankkids.webman-build.app.domain_path', 'app');
        $module_path = str_replace("{module_name}", $module_name, $domain_path);
        $namespace = $module_path . DIRECTORY_SEPARATOR . $file_path;
        $file = $module_path . DIRECTORY_SEPARATOR . $file_path . DIRECTORY_SEPARATOR . $class . '.php';

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
        list($baseClass, $basenamespace, $basefile) = self::createBase();
        $usebase = $basenamespace . DIRECTORY_SEPARATOR . $baseClass;

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

use $usebase;

/**
 * notes: 数据单元常量
 * desc: 状态层 - 业务中用到的常量,统一放这里, 一个数据单元 对应 一个常量类(一个表对应一个枚举类对应一个映射‌枚举)
 */
class $class extends $baseClass
{
    /** @var int 禁用状态: 1-开启，2-关闭 */
    //统一命名方式(字段名_状态类型)
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    //状态映射(统一命名方式：字段名_MAP)
    const STATUS_MAP = [
        self::STATUS_ENABLE => '开启',
        self::STATUS_DISABLE => '关闭',
    ];

    /** @var int 性别枚举: 1-男，2-女 */
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    const SEX_MAP = [
        self::SEX_MALE => '男',
        self::SEX_FEMALE => '女',
    ];

    /** @var mixed 其他枚举 */
    //其他

}

EOF;
        file_put_contents($file, $controller_content);
    }


    public static function createBase()
    {
        $config = self::getConfig('Base', 'common');
        $class = $config['class'];
        $namespace = $config['namespace'];
        $file = $config['file'];

        if (is_file($file)) {
            return [$class, $namespace, $file];
        }

        $path = pathinfo($file, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $controller_content = <<<EOF
<?php

namespace $namespace;

class BaseEnum
{

}

EOF;
        file_put_contents($file, $controller_content);
        return [$class, $namespace, $file];
    }
}