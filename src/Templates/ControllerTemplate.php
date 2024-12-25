<?php

namespace Blankkids\WebmanBuild\Templates;

class ControllerTemplate
{
    /**
     * @param $name
     * @param $namespace
     * @param $file
     * @return void
     */
    public static function create($name, $namespace, $file)
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $controller_content = <<<EOF
<?php

namespace $namespace;

use support\Request;

class $name
{
    public function index(Request \$request)
    {
        return response(__CLASS__);
    }

}

EOF;
        file_put_contents($file, $controller_content);
    }


}