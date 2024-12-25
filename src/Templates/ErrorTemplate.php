<?php

namespace Blankkids\WebmanBuild\Templates;

use app\domain\base\error\BaseErr;

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

use $usebase;

/**
 * notes: 根模块-总错误码
 * desc: 错误码区间,根据模块下的 doc.md 定义来设置. 注意 按数据单元做好注释, 每个单元错误码预留20位数间隔.
 */
class $class extends $baseClass
{
    protected static \$data = [
        //默认
        "ID_NOT_FOUND"                     => ['code' => 200000, 'msg' => 'ID 不存在'],
        "ID_NOT_UNIQUE"                    => ['code' => 200001, 'msg' => 'ID 已存在'],
        "BATCH_IDS_NOT_FOUND"              => ['code' => 200002, 'msg' => '批量数据中 有ID不存在'],
        "BATCH_IDS_NOT_UNIQUE"             => ['code' => 200003, 'msg' => '批量数据中 有ID已存在'],
        //自定义
        //# XXX 数据单元 使用
        
    ];

}

EOF;
        file_put_contents($file, $controller_content);
    }

    public static function createBase()
    {
        list($baseClass, $basenamespace, $basefile) = self::createErrBase();

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

class $class extends $baseClass
{

    protected static \$data = [
        "REQUEST_SUCCESS"               => ['code' => 100000, 'msg' => '请求成功'],
        "REQUEST_FAIL"                  => ['code' => 100001, 'msg' => '请求失败'],
        "TOO_FAST_REQUESTS"             => ['code' => 100002, 'msg' => '请求过于频繁'],
        "TOO_MANY_REQUESTS"             => ['code' => 100003, 'msg' => '请求次数过多,请稍后再试'],

        //全局错误码 限制在 150000-199999 之间, 重新分配code
        "WHERE_SEARCH_OPERATOR_FAIL"    => ['code' => 150000, 'msg' => '_WHERE 查询表达式错误. 格式: k1/v1,k2/v2 '],
        "WHERE_IN_SEARCH_OPERATOR_FAIL" => ['code' => 150001, 'msg' => '_WHERE_IN 查询表达式错误. 格式: k1/v1,v2|k2/v1,v2 '],
        "TOKEN_RECORD_FAIL"             => ['code' => 150002, 'msg' => '全局授权记录不存在'],
        "TOKEN_MUST"                    => ['code' => 150003, 'msg' => '需要 TOKEN 授权'], 
        "TOKEN_FAIL"                    => ['code' => 150004, 'msg' => '授权 TOKEN 有误'], 
        "NO_AUTH_SCOPE"                 => ['code' => 150005, 'msg' => '请求不在设定权限范围内'], 
        "AUTH_SCOPE_FAIL"               => ['code' => 150006, 'msg' => '请求不在设定权限范围内'],
        "WRONG_BATCH_DATA"              => ['code' => 150007, 'msg' => '批处理数据输入错误'],
        "TOKEN_VERIFY_FAIL"             => ['code' => 150008, 'msg' => '授权 TOKEN 校验失败'],
        "SCENE_VALIDATE_PARAM_FAIL"     => ['code' => 150009, 'msg' => '场景验证参数错误'],
        //
        "THIRD_API_FAIL"                => ['code' => 160000, 'msg' => '抱歉,当前使用人数过多,请稍后再试.'], //第三方接口异常
    ];

}

EOF;
        file_put_contents($file, $controller_content);
        return [$class, $namespace, $file];
    }

    public static function createErrBase()
    {
        $config = self::getConfig('BaseErr', 'common');
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

    protected static \$data=[
    ];

    static function code(\$type){
        return self::\$data[\$type]['code'];
    }

    static function msg(\$type){
        return self::\$data[\$type]['msg'];
    }

}

EOF;
        file_put_contents($file, $controller_content);
        return [$class, $namespace, $file];
    }
}