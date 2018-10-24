<?php
/**
 * Created by PhpStorm.
 * User: star
 * Date: 2018/10/13
 * Time: 10:54
 *
 * 框架 核心类
 */
final class WXING {
    public static function run() {
        self::_set_const();
        defined('DEBUG') || define('DEBUG',false);
        if(DEBUG) {
            // 开发模式
            self::_create_dir();
            self::_import_file();
        }else {
            // 上线模式默认不显示任何级别的错误
            error_reporting(0);
            // 上线模式默认 _create_dir ，_import_file不必每次都载入，采用临时文件方法保存所有需要载入的类
            require TEMP_PATH.'/~boot.php';
        }

        Application::run();
    }

    /**
     * 定义一些目录路径
     */
    private static function _set_const() {
        // var_dump(__FILE__)当前位置 /mnt/hgfs/vmshare_cent68/mvc.local.com/MVC/WXING/WXING.php
        // WXING_PATH /mnt/hgfs/vmshare_cent68/mvc.local.com/MVC/WXING
        // windows 分隔符换成linux
        $path = str_replace('\\','/',__FILE__);
        define('WXING_PATH', dirname($path)); // 框架目录，最后不带斜杠
        define('CONFIG_PATH', WXING_PATH.'/Config');
        define('DATA_PATH', WXING_PATH.'/Data');
        define('LIB_PATH', WXING_PATH.'/Lib');
        define('CORE_PATH', LIB_PATH.'/Core');
        define('FUNCTION_PATH', LIB_PATH.'/Function');

        define('EXTENDS_PATH', WXING_PATH . '/Extends');
        define('ORG_PATH', EXTENDS_PATH . '/Org');
        define('TOOL_PATH', EXTENDS_PATH . '/Tool');

        define('ROOT_PATH', dirname(WXING_PATH)); //项目根目录

        define('TEMP_PATH', ROOT_PATH.'/Temp');
        define('LOG_PATH', TEMP_PATH.'/Log');
        define('LOG_SYSTEM_PATH', LOG_PATH.'/SysLog');
        define('LOG_SELF_PATH', LOG_PATH.'/SelfLog');

        define('APP_PATH', ROOT_PATH.'/'.APP_NAME); //具体的应用目录
        define('APP_CONFIG_PATH', APP_PATH.'/Config'); //具体应用目录下Config子目录
        define('APP_CONTROLLER_PATH', APP_PATH.'/Controller');
        define('APP_TPL_PATH', APP_PATH.'/Tpl');
        define('APP_PUBLIC_PATH', APP_TPL_PATH.'/Public');

        // smarty临时目录
        define('APP_COMPILE_PATH', TEMP_PATH.'/Compile');
        define('APP_CACHE_PATH', TEMP_PATH.'/Cache');

        // 应用公共目录
        define('COMMON_PATH', ROOT_PATH. '/Common');
        define('COMMON_CONFIG_PATH', COMMON_PATH. '/Config');
        define('COMMON_MODEL_PATH', COMMON_PATH. '/Model');
        define('COMMON_LIB_PATH', COMMON_PATH. '/Lib');

        define('WXING_VERSION','1.0.0');
        define('IS_POST', ('POST' == $_SERVER['REQUEST_METHOD']) ? true : false );
        if(isset($_SERVER['HTTP_X_REQUEST_METHOD']) && $_SERVER['HTTP_X_REQUEST_METHOD'] == 'XMLHttpRequest' ) {
            define('IS_AJAX', true);
        }else {
            define('IS_AJAX', false);
        }
    }

    /**
     * 根据定义路径，创建 应用 文件夹
     */
    private static function _create_dir() {
        $arr = array(
            APP_CONFIG_PATH,
            APP_CONTROLLER_PATH,
            APP_TPL_PATH,
            APP_PUBLIC_PATH,
            LOG_SYSTEM_PATH,
            LOG_SELF_PATH,
            COMMON_CONFIG_PATH,
            COMMON_MODEL_PATH,
            COMMON_LIB_PATH,
            APP_COMPILE_PATH,
            APP_CACHE_PATH,
        );
        foreach ($arr as $v){
            is_dir($v) || mkdir($v,0777,true); //不存在这个文件才去创建，避免覆盖
        }

        is_file(APP_TPL_PATH.'/success.html') || copy(DATA_PATH.'/Tpl/success.html',APP_TPL_PATH.'/success.html');
        is_file(APP_TPL_PATH.'/error.html') || copy(DATA_PATH.'/Tpl/error.html',APP_TPL_PATH.'/error.html');
        is_file(APP_TPL_PATH.'/halt.html') || copy(DATA_PATH.'/Tpl/halt.html',APP_TPL_PATH.'/halt.html');
    }

    /**
     * 载入框架必须载入的文件,载入的文件，整个项目可以调用
     * 与后面自动加载的系统文件不同
     */
    private static function _import_file() {
        $file_arr = array(
            FUNCTION_PATH.'/function.php',
            ORG_PATH . '/Smarty/Smarty.class.php',
            CORE_PATH . '/SmartyView.class.php', // 因为Controller要继承SmartyView，所以必须放在Controller前面
            CORE_PATH.'/Controller.class.php',
            CORE_PATH.'/Log.class.php',
            CORE_PATH.'/Application.class.php',
        );
        $str = '';
        foreach ($file_arr as $v) {
            $str .= trim(substr(file_get_contents($v),5,-2)); // 将所有要载入的类或文件放在一个文件夹
            require_once $v;
        }
        $str = "<?php\r\n" . $str;
        file_put_contents(TEMP_PATH . '/~boot.php',$str) || die('No Access');
    }
}

WXING::run();