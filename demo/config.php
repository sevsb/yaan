<?php

if (file_exists("PATH.php")) {
    include_once("PATH.php");
}

defined('ROOT_PATH') or define('ROOT_PATH', '/var/www/yaan/');
defined('APP_PATH') or define('APP_PATH', dirname(__FILE__));

defined('MYSQL_SERVER') or define('MYSQL_SERVER', 'localhost');
defined('MYSQL_USERNAME') or define('MYSQL_USERNAME', 'yaan');
defined('MYSQL_PASSWORD') or define('MYSQL_PASSWORD', 'yaan');
defined('MYSQL_DATABASE') or define('MYSQL_DATABASE', 'yaan');
defined('MYSQL_PREFIX') or define('MYSQL_PREFIX', 'yaan_');


defined('LOG_DIR') or define('LOG_DIR', dirname(__FILE__) . '/logs/');

defined('WEAUTH_APPID') or define('WEAUTH_APPID', '');
defined('WEAUTH_SECRET') or define('WEAUTH_SECRET', '');
defined('WECHAT_NOTICE_TEMPLATE') or define('WECHAT_NOTICE_TEMPLATE', '');


