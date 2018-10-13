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
        self::_create_dir();
        self::_import_file();
        Application::run();
    }

    /**
     * 定义一些目录路径
     */
    private static function _set_const() {
        // var_dump(__FILE__)当前位置 /mnt/hgfs/vmshare_cent68/mvc.local.com/MVC/WXING/WXING.php
        // windows 分隔符换成linux
        $path = str_replace('\\','/',__FILE__);
        define('WXING_PATH',dirname($path)); // 框架目录，最后不带斜杠
        define('CONFIG_PATH',WXING_PATH.'/Config');
        define('DATA_PATH',WXING_PATH.'/Data');
        define('LIB_PATH',WXING_PATH.'/Lib');
        define('CORE_PATH',LIB_PATH.'/Core');
        define('FUNCTION_PATH',LIB_PATH.'/Function');

        define('ROOT_PATH',dirname(WXING_PATH)); //项目根目录
        define('APP_PATH',ROOT_PATH.'/'.APP_NAME); //具体的应用目录

        define('APP_CONFIG_PATH',APP_PATH.'/Config'); //具体应用目录下Config子目录
        define('APP_CONTROLLER_PATH',APP_PATH.'/Controller');
        define('APP_LOG_PATH',APP_PATH.'/Log');
        define('APP_TPL_PATH',APP_PATH.'/Tpl');
        define('APP_PUBLIC_PATH',APP_TPL_PATH.'/Public');
    }

    /**
     * 根据定义路径，创建 应用 文件夹
     */
    private static function _create_dir() {
        $arr = array(
            APP_CONFIG_PATH,
            APP_CONTROLLER_PATH,
            APP_LOG_PATH,
            APP_TPL_PATH,
            APP_PUBLIC_PATH,
        );
        foreach ($arr as $v){
            is_dir($v) || mkdir($v,0777,true);
        }
    }

    /**
     * 载入框架必须载入的文件,载入的文件，整个项目可以调用
     */
    private static function _import_file() {
        $file_arr = array(
            FUNCTION_PATH.'/function.php',
            CORE_PATH.'/Application.class.php',
            CORE_PATH.'/Controller.class.php',
        );
        foreach ($file_arr as $v) {
            require_once $v;
        }
    }
}

WXING::run();