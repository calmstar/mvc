<?php
/**
 * Created by PhpStorm.
 * User: star
 * Date: 2018/10/14
 * Time: 11:24
 */

class Log {
    public static function write($msg, $level='ERROR', $type=3, $dest=NULL) {
        if(!C('SAVE_LOG')) return;
        if(is_null($dest)) $dest = LOG_SELF_PATH.'/'.date('Y-m-d').'.log';
        if(is_dir(LOG_SELF_PATH)) error_log("[TIME]:".date('Y-m-d H:i:s')." {$level} : {$msg} \r\n", $type, $dest);
    }
}
?>