<?php
class IndexController extends Controller {
    /**
     * 实例化该类的时候先实例化父类，父类控制器判断__auto方法若存在，则调用
     * 避免 parent::__construct 使用过多
     * 直接继承Controller使用 __init()，间接继承使用 __auto
     */
    public function __init() {

    }

    public function __empty() {
        echo 'action empty';
    }

    public function index() {
        $code = new Code();
        $code->run();
        include 'sss.html';
    }
}