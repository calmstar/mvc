<?php

/**
 * 框架和Smarty的桥梁类
 * Class SmartyView
 */
class SmartyView {
    private static $smarty = null;

    public function __construct() {
        if(!is_null(self::$smarty)) return;
        $smarty = new Smarty();
        // 模板目录
        $smarty->template_dir = APP_TPL_PATH . '/' . CONTROLLER . '/';
        // 编译
        $smarty->compile_dir = APP_COMPILE_PATH;
        // 缓存
        $smarty->cache_dir = APP_CACHE_PATH;
        $smarty->caching = C('CACHE_ON');
        $smarty->cache_lifetime = C('CACHE_TIME');
        // 定界符
        $smarty->left_delimiter = C('LEFT_DELIMITER');
        $smarty->right_delimiter = C('RIGHT_DELIMITER');

        self::$smarty = $smarty;
    }

    protected function display ($tpl) {
        self::$smarty->display($tpl, $_SERVER['REQUEST_URI']);
    }

    protected function assign ($var, $value) {
        self::$smarty->assign($var, $value);
    }

    // smarty 模板缓存
    protected function isCached ($tpl=null) {
        if (!C('SMARTY_ON')) halt('请先开启smarty');
        $tpl = $this->getTpl($tpl);
        return self::$smarty->isCached($tpl, $_SERVER['REQUEST_URI']);
    }

}
?>