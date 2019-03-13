<?php
/**
 * 框架配置目录
 */
return array(
    // 自动加载 Common/Lib目录下的文件
    'AUTO_LOAD_FILE' => array(),

    // 验证码
    'CODE_LEN' => 4,

    // 默认时区
    'DEFAULT_TIME_ZONE' => 'PRC',

    // 是否开启session
    'SESSION_AUTO_START' => TRUE,

    // 针对开发模式，是否开启错误提示，线上模式已在WXING.php中默认不开启
    'DISPLAY_ERRORS' => 1,
    // 若开启错误提示，提示哪些级别的
    // E_NOTICE只显示提示；E_NOTICE | E_WARNING 显示notice和warning；E_ERROR
    // 产品阶段 E_ALL & ~E_DEPRECATED 显示所有错误除了弃用的
    // 开发阶段 E_ALL | E_STRICT 显示所有错误
    'ERROR_REPORTING' => E_ALL | E_STRICT,
    // 是否开启错误日志记录，上线时为1，开发为0
    'LOG_ERRORS' => 0,
    // 错误日志记录路径
    'ERROR_LOG_PATH' => LOG_SYSTEM_PATH,

    // 获取url控制器的键名称
    'VAR_CONTROLLER' => 'c',
    // 获取url动作的键名称
    'VAR_ACTION' => 'a',

    // 是否开启 日志类 记录日志
    'SAVE_LOG' => TRUE,

    // 错误跳转地址
    'ERROR_URL' => '',
    'ERROR_MSG' => '出错，请稍后再试',

    // 数据库配置
    'DB_CHARSET' => 'utf8',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_USER' => 'jt_vag_com',
    'DB_PASSWORD' => 'RGDLrbzrFsCT5Hcy',
    'DB_DATABASE' => 'jt_vag_com',
    'DB_PREFIX' => 'jt_',

    // smarty
    'SMARTY_ON' => true,
    'LEFT_DELIMITER' => '{@',
    'RIGHT_DELIMITER' => '}',
    'CACHE_ON' => true,
    'CACHE_TIME' => 2,

);