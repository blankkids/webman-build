<?php

namespace Blankkids\WebmanBuild\Templates;

use Doctrine\Inflector\InflectorFactory;
use support\Db;
use Webman\Console\Util;

class EntityTemplate
{
    /**
     * @param $name
     * @param $module_name
     * @return array
     */
    public static function getConfig($name, $module_name)
    {
        $class = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.entity', '');
        $file_path = config('plugin.blankkids.webman-build.app.child_path.entity', '');
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
     * @param $connection
     * @return void
     */
    public static function create($name, $module_name, $connection = null)
    {
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

        $table = Util::classToName($class);
        $pk = 'id';
        $fieldItem = '';
        $properties = '';
        $comments = '';
        $connection = $connection ?: 'mysql';
        try {
            $prefix = config("database.connections.$connection.prefix") ?? '';
            $database = config("database.connections.$connection.database");
            $inflector = InflectorFactory::create()->build();
            $table_plura = $inflector->tableize($name);
            $con = Db::connection($connection);
            if ($con->select("show tables like '{$prefix}{$table_plura}'")) {
                $table = "{$prefix}{$table_plura}";
            }
            $tableComment = $con->select('SELECT table_comment FROM information_schema.`TABLES` WHERE table_schema = ? AND table_name = ?', [$database, $table]);
            if (!empty($tableComment)) {
                $comments = $tableComment[0]->table_comment ?? $tableComment[0]->TABLE_COMMENT;
            }
            foreach ($con->select("select COLUMN_NAME,DATA_TYPE,COLUMN_KEY,COLUMN_COMMENT from INFORMATION_SCHEMA.COLUMNS where table_name = '$table' and table_schema = '$database' ORDER BY ordinal_position") as $item) {
                $type = self::getType($item->DATA_TYPE);
                $functionName = self::underscoreToCamelCase($item->COLUMN_NAME);

                $fieldItem .= "// {$item->COLUMN_COMMENT}
    private \${$item->COLUMN_NAME};
    ";

                $properties .= "
    /**
     * {$item->COLUMN_COMMENT}
     * @return {$type}
     */
    public function get{$functionName}(): {$type}
    {
        return \$this->{$item->COLUMN_NAME};
    }

    /**
     * {$item->COLUMN_COMMENT}
     * @param {$type} \${$item->COLUMN_NAME}
     */
    public function set{$functionName}({$type} \${$item->COLUMN_NAME}): void
    {
        \$this->{$item->COLUMN_NAME} = \${$item->COLUMN_NAME};
    }
";
            }
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        $controller_content = <<<EOF
<?php

namespace $namespace;

/**
 * notes：数据库字段映射类 （实体类）
 * desc: $comments （有修改可以用命令重新生成，切勿手动修改）
 */
class $class
{
    $fieldItem

    $properties
}

EOF;
        file_put_contents($file, $controller_content);
    }

    /**
     * @param string $type
     * @return string
     */
    public static function getType(string $type)
    {
        if (strpos($type, 'int') !== false) {
            return 'int';
        }
        switch ($type) {
            case 'varchar':
            case 'string':
            case 'text':
            case 'date':
            case 'time':
            case 'guid':
            case 'datetimetz':
            case 'datetime':
            case 'decimal':
            case 'enum':
                return 'string';
            case 'boolean':
                return 'int';
            case 'float':
                return 'float';
            default:
                return 'mixed';
        }
    }

    public static function underscoreToCamelCase($string)
    {
        $parts = explode('_', $string);
        foreach ($parts as $index => $part) {
            $parts[$index] = ucfirst($part);
        }
        return implode('', $parts);
    }
}