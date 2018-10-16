<?php

/**
 * Class Application 框架应用类
 */
final class Application {
    public static function run() {
        self::_init();
        // 错误处理，此前面调用的都是框架代码，后面开始是用户写的代码，可能出错，所以在这里引入
        set_error_handler(array(__CLASS__,'error'));
        self::_user_import();
        self::_set_url();
        spl_autoload_register(array(__CLASS__,'_autoload'));// __CLASS__系统魔术常量代表当前类Application
        self::_create_demo();
        self::_app_run();
    }

    public static function error($errno, $error, $file, $line) {
        switch ($errno) {
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                if(DEBUG) {
                    include DATA_PATH . '/Tpl/notice.html';
                }
                break;
        }
    }

    /**
     * 实例化应用控制器，控制器接受请求，分发找控制器文件中心
     */
    private static function _app_run() {
        $c = isset($_GET[C('VAR_CONTROLLER')]) ? $_GET[C('VAR_CONTROLLER')] : 'Index';
        $a = isset($_GET[C('VAR_ACTION')]) ? $_GET[C('VAR_ACTION')] : 'index';
        define('CONTROLLER', $c);
        define('ACTION', $a);
        $c .= 'Controller';
        // 判断类 $c 是否存在，class_exists判断时会通过自动加载后再判断
        if( class_exists($c) ) {
            // 访问的控制器存在
            $obj = new $c;
            if(!method_exists($obj, $a)) {
                // 访问的动作存在
                if(method_exists($obj, '__empty')) {
                    // 空方法存在就调用
                    $obj->__empty();
                }else {
                    // 空方法不存在，报出错误
                    halt($c . '控制器中的' . $a . ' 动作未找到');
                }
            }else {
                // 访问的动作不存在
                $obj->$a();
            }
        }else {
            // 访问的控制器不存在时，实例化空控制器，_autoload中已实现自动加载
            $obj = new EmptyController();
            $obj->index();
        }
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
        switch (true) {
            case  strlen($className) > 10 && substr($className, -10) == 'Controller' :  // 从后面取十个字符
                $path = APP_CONTROLLER_PATH.'/'.$className.'.class.php';
                // 空控制器的操作
                if( !is_file($path) ) {
                    // 不存在访问的控制器文件
                    $emptyPath = APP_CONTROLLER_PATH . '/EmptyController.class.php';
                    if(is_file($emptyPath)) {
                        // 用户定义了空控制器
                        include $emptyPath;
                        return;
                    }else {
                        // 用户没有定义了空控制器
                        halt($path . '控制器文件未找到');
                    }
                }
                // 存在访问的控制器文件
                require $path;
                break;
            default :
                // 框架中的Tool的类文件
                $path = TOOL_PATH.'/'.$className.'.class.php';
                if(!is_file($path)) halt($path . '类文件未找到');
                require $path;
                break;
        }
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
     * 加载公共文件夹 Common/Lib 下指定的文件（在任意的config.php的AUTO_LOAD_FILE指定）
     */
    private static function _user_import() {
        $file_arr = C('AUTO_LOAD_FILE');
        if(is_array($file_arr) && !empty($file_arr)) {
            foreach ($file_arr as $v) {
                require_once COMMON_LIB_PATH . '/' . $v;
            }
        }
    }

    /**
     * 初始化框架，加载配置文件
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