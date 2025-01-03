<?php

namespace Blankkids\WebmanBuild\Templates;

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
        $namespace = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . $file_path;
        $file = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . $file_path . DIRECTORY_SEPARATOR . $class . '.php';

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

class $class
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
            $controller_content = "public function {$value}() {\n\t\treturn true;\n\t}\n";
            self::changeForCodeBlockTag($value, $update_file, $controller_content);
        }

    }


    public static function changeForCodeBlockTag($code_block, &$content, &$changeContent)
    {
        preg_match('/\/\/{@block_c}(.*)\/\/{@block_c\//', $content, $m);
        if (isset($m[1])) {
            //清除掉 "模板" 注释
            $m[1] = preg_replace("/模板/i", '', $m[1]);
            //#
            $changeContent = preg_replace("/(\\/\\/{@block_" . $code_block . "}).*(\\/\\/{@block_" . $code_block . "\\/})/", "$1" . $m[1] . "$2", $changeContent);
        }
    }
}