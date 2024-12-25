<?php

namespace Blankkids\WebmanBuild\Templates;

class ServiceTemplate
{
    /**
     * @param $name
     * @param $module_name
     * @return array
     */
    public static function getConfig($name, $module_name)
    {
        $class = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.service', '');
        $file_path = config('plugin.blankkids.webman-build.app.child_path.service', '');
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
 * notes: 领域层-业务类
 * desc: 当不同 应用端/模块 的 应用层-业务类,对同一个表数据(或第三方API)进行操作, 该表的操作代码分散在多个应用端中且冗余, 就需要抽象到这一层.
 * 领域层-业务类 允许 被 跨应用端/模块 调用, 而 各应用层-业务 则保持隔离, 避免应用层业务耦合.
 * 调用原则: 向下调用[仓储类,第三方服务-SDK]
 */
class $class extends $baseClass
{

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

class $class
{

}

EOF;
        file_put_contents($file, $controller_content);
        return [$class, $namespace, $file];
    }

}