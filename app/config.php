<?php

if (file_exists(dirname(__FILE__) . "/../../PATH.php")) {
    include_once(dirname(__FILE__) . "/../../PATH.php");
}

include_once(dirname(__FILE__) . "/../../framework/config.php");
include_once(dirname(__FILE__) . "/database/db_user.class.php");
include_once(dirname(__FILE__) . "/database/db_settings.class.php");
include_once(dirname(__FILE__) . "/database/db_customers.class.php");
include_once(dirname(__FILE__) . "/database/db_muffininfos.class.php");
include_once(dirname(__FILE__) . "/database/db_muffins.class.php");
include_once(dirname(__FILE__) . "/database/db_wechatusers.class.php");
include_once(dirname(__FILE__) . "/user.class.php");
include_once(dirname(__FILE__) . "/upload.php");
include_once(dirname(__FILE__) . "/thumbnail.php");
include_once(dirname(__FILE__) . "/mailer.class.php");
include_once(dirname(__FILE__) . "/settings.class.php");
include_once(dirname(__FILE__) . "/projects.class.php");
include_once(dirname(__FILE__) . "/customers.class.php");
include_once(dirname(__FILE__) . "/login.class.php");
include_once(FRAMEWORK_PATH . "/helper.php");
include_once(FRAMEWORK_PATH . "/logging.php");
include_once(FRAMEWORK_PATH . "/tpl.php");


defined('UPLOAD_DIR') or define('UPLOAD_DIR', ROOT_PATH . '/upload/images');
defined('UPLOAD_URL') or define('UPLOAD_URL', rtrim(INSTANCE_URL, "/") . '/upload/images');

defined('FILEUPLOAD_DIR') or define('FILEUPLOAD_DIR', ROOT_PATH . '/upload/files');
defined('FILEUPLOAD_URL') or define('FILEUPLOAD_URL', rtrim(INSTANCE_URL, "/") . '/upload/files');

defined('THUMBNAIL_DIR') or define('THUMBNAIL_DIR', ROOT_PATH . '/upload/thumbnails');
defined('THUMBNAIL_URL') or define('THUMBNAIL_URL', rtrim(INSTANCE_URL, "/") . '/upload/thumbnails');
defined('UPLOAD_LIMIT') or define('UPLOAD_LIMIT', 10 * 1024 * 1024);
defined('PICSERVICE_IP') or define('PICSERVICE_IP', "http://pic.zizhuzhuangxiu.cn");
defined('PICSERVICE_URL') or define('PICSERVICE_URL', "http://pic.zizhuzhangxiu.cn/");
//defined('PICSERVICE_IP') or define('PICSERVICE_IP', "http://127.0.0.1");
//defined('PICSERVICE_URL') or define('PICSERVICE_URL', "http://127.0.0.1/picservice/");


// security
defined('ALLOW_ROOT') or define('ALLOW_ROOT', true);

// database
defined('MYSQL_SERVER') or define('MYSQL_SERVER', '180.76.188.68');
defined('MYSQL_USERNAME') or define('MYSQL_USERNAME', 'yaan');
defined('MYSQL_PASSWORD') or define('MYSQL_PASSWORD', 'yaan');
defined('MYSQL_DATABASE') or define('MYSQL_DATABASE', 'yaan');
defined('MYSQL_PREFIX') or define('MYSQL_PREFIX', 'yaan_');

defined('MYSQL_COMMON') or define('MYSQL_COMMON', 'common_');

// db_muffininfos 
defined('TABLE_MUFFININFOS') or define('TABLE_MUFFININFOS', MYSQL_PREFIX . "muffininfos");
// db_muffins
defined('TABLE_MUFFINS') or define('TABLE_MUFFINS', MYSQL_COMMON . "muffins");
// db_wechatusers
defined('TABLE_WECHATUSERS') or define('TABLE_WECHATUSERS', MYSQL_COMMON . "wechatusers");


// db_settings
defined('TABLE_SETTINGS') or define('TABLE_SETTINGS', MYSQL_PREFIX . "settings");

// db_user
defined('TABLE_USERS') or define('TABLE_USERS', MYSQL_PREFIX . "users");
defined('TABLE_USERSETTINGS') or define('TABLE_USERSETTINGS', MYSQL_PREFIX . "user_settings");
defined('TABLE_USER_GROUPS') or define('TABLE_USER_GROUPS', MYSQL_PREFIX . "user_groups");

// mailer
defined('MAIL_SUBJECT_PREFIX') or define('MAIL_SUBJECT_PREFIX', '');

// projects
defined('TABLE_PROJECTS') or define('TABLE_PROJECTS', MYSQL_PREFIX . "projects");

// staffs
defined('TABLE_STAFFS') or define('TABLE_STAFFS', MYSQL_PREFIX . "staffs");
defined('TABLE_STAFF_SERVICES') or define('TABLE_STAFF_SERVICES', MYSQL_PREFIX . "staff_services");

// duty
defined('TABLE_DUTY') or define('TABLE_DUTY', MYSQL_PREFIX . "duty");
defined('TABLE_ORDERS') or define('TABLE_ORDERS', MYSQL_PREFIX . "orders");
defined('TABLE_EVENT_SETTINGS') or define('TABLE_EVENT_SETTINGS', MYSQL_PREFIX . "event_settings");

// customers
defined('TABLE_CUSTOMERS') or define('TABLE_CUSTOMERS', MYSQL_PREFIX . "customers");
