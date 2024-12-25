<?php

namespace Blankkids\WebmanBuild\Templates;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class LogicTemplate
{
    /**
     * @param $name
     * @param $module_name
     * @return array
     */
    public static function getConfig($name, $module_name)
    {
        $class = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.logic', '');
        $file_path = config('plugin.blankkids.webman-build.app.child_path.logic', '');
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
    public static function create($name, $module_name, $curd_name)
    {
        list($baseClass, $basenamespace, $basefile) = self::createBase();
        $usebase = $basenamespace . DIRECTORY_SEPARATOR . $baseClass;

        $config = self::getConfig($name, $module_name);
        $class = $config['class'];
        $namespace = $config['namespace'];
        $file = $config['file'];

        $path = pathinfo($file, PATHINFO_DIRNAME);

        if (!is_file($file)) {
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true) && !is_dir($path)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
                }
            }

            $controller_content = <<<EOF
<?php

namespace $namespace;

use $usebase;

//{@hidden}

//{@hidden}

class $class extends $baseClass
{
//{@block_c}
//{@block_c/}

//{@block_cj}
//{@block_cj/}

//{@block_bc}
//{@block_bc/}

//{@block_u}
//{@block_u/}

//{@block_bu}
//{@block_bu/}

//{@block_br}
//{@block_br/}

//{@block_r}
//{@block_r/}

//{@block_d}
//{@block_d/}
}

EOF;
            file_put_contents($file, $controller_content);

        }

        $update_file = file_get_contents($file);
        $make_fun = explode(',', $curd_name);
        foreach ($make_fun as $value) {
            $changeContent = self::getUpdateTemple($value);
            $update_file = self::changeContent($value, $changeContent, $update_file);
        }
        $update_file = file_put_contents($file, $update_file);

    }

    public static function getUpdateTemple($value)
    {
        $changeContent = '';
        switch ($value) {
            case 'c':
                $changeContent = "";
                break;
            default:
                break;
        }
        return $changeContent;
    }

    public static function changeHidden($changeContent, $update_file)
    {
        $pattern = "/(\/\/\{@hidden\})(.*?)(\/\/\{@block_\\/\})/s";
        preg_match($pattern, $update_file, $matches);

        if (isset($matches[1])) {
            $newContent = $matches[1] . "\n" . $changeContent . "\n" . $matches[3];
            $update_file = preg_replace($pattern, $newContent, $update_file);
        }
        return $update_file;
    }

    public static function changeContent($value, $changeContent, $update_file)
    {
        $pattern = "/(\/\/\{@block_" . preg_quote($value, '/') . "\})(.*?)(\/\/\{@block_" . preg_quote($value, '/') . "\\/\})/s";
        preg_match($pattern, $update_file, $matches);

        if (isset($matches[1]) && !empty($changeContent)) {
            $newContent = $matches[1] . "\n" . $changeContent . "\n" . $matches[3];
            $update_file = preg_replace($pattern, $newContent, $update_file);
        }
        return $update_file;
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
