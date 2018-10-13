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
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
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