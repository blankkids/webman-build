<?php

namespace Blankkids\WebmanBuild\Templates;

class ErrorTemplate
{
    /**
     * @param $name
     * @param $module_name
     * @return array
     */
    public static function getConfig($name, $module_name)
    {
        $class = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.error', '');
        $file_path = config('plugin.blankkids.webman-build.app.child_path.error', '');
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
    public static function create($name, $module_name)
    {
        $config = self::getConfig($name, $module_name);
        $class = $config['class'];
        $namespace = $config['namespace'];
        $file = $config['file'];

        $path = pathinfo($file, PATHINFO_DIRNAME);

        if (is_file($file)) {
            printf("%s 已经存在!跳过创建错误码\n", $file);
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
 * notes: 根模块-总错误码
 * desc: 错误码区间,根据模块下的 doc.md 定义来设置. 注意 按数据单元做好注释, 每个单元错误码预留20位数间隔.
 */
class $class
{
    protected static \$data = [
        //默认
        "ID_NOT_FOUND"                     => ['code' => 100000, 'msg' => 'ID 不存在'],
        "ID_NOT_UNIQUE"                    => ['code' => 100001, 'msg' => 'ID 已存在'],
        "BATCH_IDS_NOT_FOUND"              => ['code' => 100002, 'msg' => '批量数据中 有ID不存在'],
        "BATCH_IDS_NOT_UNIQUE"             => ['code' => 100003, 'msg' => '批量数据中 有ID已存在'],
        //自定义
        //# XXX 数据单元 使用
        
    ];

    public static function code(\$type)
    {
        return self::\$data[\$type]['code'];
    }

    public static function msg(\$type)
    {
        return self::\$data[\$type]['msg'];
    }
}

EOF;
        file_put_contents($file, $controller_content);
    }


}