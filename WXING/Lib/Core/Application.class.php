<?php

/**
 * Class Application 框架应用类
 */
final class Application {
    public static function run() {
        self::_init();
        self::_set_url();
        spl_autoload_register(array(__CLASS__,'_autoload'));// __CLASS__系统魔术常量代表当前类Application
        self::_create_demo();
        self::_app_run();
    }

    /**
     * 实例化应用控制器
     */
    private static function _app_run() {
        $c = isset($_GET[C('VAR_CONTROLLER')]) ? $_GET[C('VAR_CONTROLLER')] : 'Index';
        $a = isset($_GET[C('VAR_ACTION')]) ? $_GET[C('VAR_ACTION')] : 'index';
        define('CONTROLLER', $c);
        define('ACTION', $a);
        $c .= 'Controller';
        $obj = new $c;
        $obj->$a();
    }

    /**
     * 创建demo
     */
    private static function _create_demo() {
        $path = APP_CONTROLLER_PATH.'/IndexController.class.php';
        $str = <<<str
<?php
class IndexController extends Controller {
    /**
     * 实例化该类的时候先实例化父类，父类控制器判断__auto方法若存在，则调用
     * 避免 parent::__construct 使用过多
     * 直接继承Controller使用 __init()，间接继承使用 __auto
     */
    public function __init() {

    }

    public function index() {
        header('Content-type:text/html;charset=utf-8');
        echo '<h1>欢迎使用WXING框架</h1>';
    }
}
str;
        is_file($path) || file_put_contents($path,$str);

    }

    /**
     * 自动载入功能，载入应用目录中的控制器文件
     */
    private static function _autoload($className) {
        require APP_CONTROLLER_PATH.'/'.$className.'.class.php';
    }
    /*
     * 定义http的url变量
     */
    private static function _set_url() {
        $path = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
        $path = str_replace('\\','/',$path);
        // 为了跟物理路径区分，采用前后两个下划线定义url常量（不要和 系统魔术常量 冲突）
        define('__APP__',$path);
        define('__ROOT__',dirname(__APP__));
        define('__TPL__',__ROOT__.'/'.APP_NAME.'/Tpl');
        define('__PUBLIC__',__TPL__.'/Public');

    }

    /**
     * 初始化框架
     */
    private static function _init() {
        // 优先级：按如下顺序 系统配置<公共配置<用户配置
        // 加载系统配置项
        C(include CONFIG_PATH.'/config.php');

        // 公共配置
        $commonPath = COMMON_CONFIG_PATH . '/config.php';
        $commonConfig = <<<str
<?php 
return array(
    //配置项 => 配置值
);
str;
        is_file($commonPath) || file_put_contents($commonPath, $commonConfig); // 应对没有配置文件的情况
        // 加载公共配置
        C(include $commonPath);


        // 用户配置
        $userPath = APP_CONFIG_PATH.'/config.php';
        $userConfig = <<<str
<?php
return array(
    //配置项 => 配置值
    
);
str;
        is_file($userPath) || file_put_contents($userPath,$userConfig); //文件被创建时，不使用file_put_contents覆盖
        // 加载用户配置项,优先级高
        C(include $userPath);

        // 设置时区
        date_default_timezone_set(C('DEFAULT_TIME_ZONE'));

        // 是否开启session
        C('SESSION_AUTO_START') && session_start();


        // debug模式时设置下面变量
        if(DEBUG) {
            // 是否显示错误报告
            ini_set('display_errors', C('DISPLAY_ERRORS'));
            // 若显示错误，显示哪些级别的错误
            ini_set("error_reporting", C('ERROR_REPORTING'));
            // 是否开启log_errors(上面定义抛出来的错误)
            ini_set('log_errors', C('LOG_ERRORS'));
            // 错误日志记录到哪里
            ini_set('error_log',C('ERROR_LOG_PATH').'/'.date('Y-m-d').'.log');
        }

    }
}
?>