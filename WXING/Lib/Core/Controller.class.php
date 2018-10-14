<?php
/**
 * Created by PhpStorm.
 * User: star
 * Date: 2018/10/13
 * Time: 23:37
 */

class Controller {
    private $var = array();

    public function __construct() {
        // 实例化过程 Index--Common--Controller，然后本类Controller依次在子类（Index,Common）中找init和auto方法，找到就调用
        // 必须注意 调用子类方法的顺序
        // 若有二级继承，则二级子类（Index）使用auto，一级子类（Common）使用init
        // 目前最多二级继承
        if(method_exists($this, '__init')) {
            $this->__init();
        }
        if(method_exists($this, '__auto')) {
            $this->__auto();
        }
    }

    /**  成功跳转方法
     * @param $msg
     * @param null $url
     * @param int $time
     */
    protected function success($msg, $url=NULL, $time=3) {
        $url = $url ? "window.location.href='".$url."'" : 'window.history.back(-1)';
        include APP_TPL_PATH.'/success.html';
        die;
    }

    /**  失败跳转方法
     * @param $msg
     * @param null $url
     * @param int $time
     */
    protected function error($msg, $url=NULL, $time=3) {
        $url = $url ? "window.location.href='".$url."'" : 'window.history.back(-1)';
        include APP_TPL_PATH.'/error.html';
        die;
    }

    /**
     * 载入模板
     * 如果有指定载入哪个模板，就加载指定，否则默认跟tp一样
     * @param $tpl
     */
    protected function display($tpl=null) {
        if(is_null($tpl)) {
            $path = APP_TPL_PATH . '/' . CONTROLLER . '/' . ACTION .'.html';
        }else {
            // 指定载入模板
            $suffix = strchr($tpl, '.'); //可能有传后缀，可能没传
            $tpl = empty($suffix) ? $tpl . '.html' : $tpl;
            $path = APP_TPL_PATH . '/' . CONTROLLER . '/' . $tpl . '.html';
        }
        if(!is_file($path)) halt($path . '模板文件不存在');

        extract($this->var);  // 此处方法全局变量，并进行转换--
        include $path;
    }

    /**
     * 模板分配变量方法
     * @param $var
     * @param $value
     */
    protected function assign($var, $value) {
        $this->var[$var] = $value;
    }

}
?>