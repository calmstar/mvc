<?php
/**
 * Created by PhpStorm.
 * User: star
 * Date: 2018/10/13
 * Time: 16:23
 */

/**
 * 格式化打印函数
 * @param $arr
 */
function p($arr) {
    if(is_bool($arr) || is_null($arr)) {
        var_dump($arr);
    }else {
        echo '<pre style="padding: 10px;border-radius: 5px;background-color: #f5f5f5;border: 1px solid #ccc;font-size: 14px;">'.print_r($arr, true).'</pre>';
    }
}

/**
 * 跳转地址功能
 * @param $url
 * @param int $time
 * @param string $msg
 */
function go($url, $time=0, $msg='') {
    $msg = !empty($msg) ? $msg : '一会就跳走了';
    if(!headers_sent()) {
        $time == 0 ? header('Location:'.$url) : header("refresh:{$time};url={$url}");
        die($msg);
    }else {
        echo "<meta http-equiv='Refresh' content='{$time};URL={$url}' >";
        if($time) die($msg);
    }
}

/**
 * 终止程序并打印信息
 * @param $error
 * @param string $level
 * @param int $type
 * @param null $dest
 */
function halt($error, $level='ERROR', $type=3, $dest=NULL) {
    // 记录日志
    if(is_array($error)) {
        Log::write($error['msg'], $level, $type, $dest);
    }else {
        Log::write($error, $level, $type, $dest);
    }
    // 输出错误信息
    $e = array();
    if(DEBUG) {
        // 开发模式
        if(!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['line'] = $trace[0]['line'];
            $e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
            $e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
            ob_start();
            debug_print_backtrace();
            $e['trace'] = htmlspecialchars(ob_get_clean());
        }else {
            $e = $error;
        }
    }else {
        // 线上模式
        if($url = C('ERROR_URL')) {
            go($url);
        }else{
            $e['message'] = C('ERROR_MSG');
        }
    }
    include  APP_TPL_PATH.'/halt.html';
    die;
}


/**
 * 1 加载配置项 C('$sysConfig') C($userConfig)
 * 2 读取配置  C('code_len)
 * 3 临时动态改变配置 C('code_len',20)
 * 4 返回所有配置项 C()
 * 所以，可能传 两个或一个或零 变量，一个变量时可能传进一个数组
 * @param null $var
 * @param null $value
 * @return array|mixed|null|void
 */
function C($var = null, $value = null) {
    static $config = array();
    // 加载配置项
    if(is_array($var)) {
        $config = array_merge($config, array_change_key_case($var, CASE_UPPER)); //将var放在后面，保证优先级
        return;  // 表明中止，仅加载调用
    }

    if(is_string($var)) {
        $var = strtoupper($var);
        // 两个参数传递
        if(!is_null($value)) {
            $config[$var] = $value;
            return;  // 仅动态修改配置
        }
        // 读取配置
        return isset($config[$var]) ? $config[$var] : NULL ;
    }
    // 返回所有配置
    return $config;
}

/**
 * 输出自己定义的常量
 */
function print_const() {
    $const = get_defined_constants(true); // 对常量分类
    p($const['user']);
}

?>