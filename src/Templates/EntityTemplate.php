<?php

namespace Blankkids\WebmanBuild\Templates;

use Doctrine\Inflector\InflectorFactory;
use support\Db;
use Webman\Console\Util;

class EntityTemplate
{
    /**
     * @param $name
     * @param $namespace
     * @param $file
     * @return void
     */
    public static function create($class, $tableName, $namespace, $file, $connection = null)
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }

        $table = Util::classToName($tableName);
        $pk = 'id';
        $fieldItem = '';
        $properties = '';
        $connection = $connection ?: 'mysql';
        try {
            $prefix = config("database.connections.$connection.prefix") ?? '';
            $database = config("database.connections.$connection.database");
            $inflector = InflectorFactory::create()->build();
            $table_plura = $inflector->tableize($tableName);
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