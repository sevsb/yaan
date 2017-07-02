<?php
include_once(dirname(__FILE__) . "/../config.php");
include_once(dirname(__FILE__) . "/database/db_wechatusers.class.php");
include_once(FRAMEWORK_PATH . "cache.php");

class wechatuser {
    private $summary = array();
    
    private function __construct($summary) {
        $this->summary = $summary;
    }

    public function id() {
        return $this->summary["id"];
    }

    private static $cache = cache::instance();
    public static function load_all() {
        $cached = self::$cache->load("all_wechatuser");
        if ($cached != null) {
            return $cached;
        }
        $wusers = db_wechatusers::inst()->get_all_users();
        $us = array();
        foreach ($wusers as $k => $summary) {
            $us [$k]= new wechatuser($summary);
        }
        self::$cache->save("all_wechatuser", $us);
        return $us;
    }

    public static function get_user_by_id($userid) {
        $user = db_wechatusers::inst()->get_user_by_id($userid);
        return new wechatuser($user);
    }

    public static function get_user_by_openid($openid) {
        $user = db_wechatusers::inst()->get_user_by_openid($openid);
        return new wechatuser($user);
    }

};


