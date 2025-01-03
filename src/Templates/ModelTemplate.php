<?php

namespace Blankkids\WebmanBuild\Templates;

use Doctrine\Inflector\InflectorFactory;
use support\Db;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Webman\Console\Util;

class ModelTemplate
{
    /**
     * @param $name
     * @param $module_name
     * @return array
     */
    public static function getConfig($name, $module_name)
    {
        $class = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.model', '');
        $file_path = config('plugin.blankkids.webman-build.app.child_path.model', '');
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
     * @param $class
     * @param $namespace
     * @param $file
     * @param string|null $connection
     * @return void
     */
    public static function create($name, $module_name, $connection = null)
    {
        $config = self::getConfig($name, $module_name);
        $class = $config['class'];
        $namespace = $config['namespace'];
        $file = $config['file'];

        if (is_file($file)) {
            printf("%s 已经存在!跳过创建模型\n", $file);
            return;
        }

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $table = Util::classToName($class);
        $table_val = 'null';
        $pk = 'id';
        $properties = '';
        $connection = $connection ?: 'mysql';
        try {
            $prefix = config("database.connections.$connection.prefix") ?? '';
            $database = config("database.connections.$connection.database");
            $inflector = InflectorFactory::create()->build();
            $table_plura = $inflector->tableize($name);
            $con = Db::connection($connection);
            if ($con->select("show tables like '{$prefix}{$table_plura}'")) {
                $table_val = "'{$table_plura}'";
                $table = "{$prefix}{$table_plura}";
            }

            foreach ($con->select("select COLUMN_NAME,DATA_TYPE,COLUMN_KEY,COLUMN_COMMENT from INFORMATION_SCHEMA.COLUMNS where table_name = '$table' and table_schema = '$database' ORDER BY ordinal_position") as $item) {
                if ($item->COLUMN_KEY === 'PRI') {
                    $pk = $item->COLUMN_NAME;
                    $item->COLUMN_COMMENT .= "(主键)";
                }

            }
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        $model_content = <<<EOF
<?php

namespace $namespace;

use support\Model;

class $class extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected \$connection = '$connection';
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected \$table = $table_val;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected \$primaryKey = '$pk';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public \$timestamps = false;
    
    
}

EOF;
        file_put_contents($file, $model_content);
    }

    /**
     * @param string $type
     * @return string
     */
    public static function getType(string $type)
    {
        if (strpos($type, 'int') !== false) {
            return 'integer';
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
                return 'integer';
            case 'float':
                return 'float';
            default:
                return 'mixed';
        }
    }
}