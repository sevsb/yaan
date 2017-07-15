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

    public function nick() {
        return $this->summary["nickname"];
    }

    public function face() {
        return $this->summary["face"];
    }

    public function tasks() {
        return $this->summary["tasks"];
    }

    public function pass() {
        return $this->summary["pass"];
    }

    public function reject() {
        return $this->summary["reject"];
    }

    public function score() {
        return $this->pass() - $this->reject();
    }

    public function locations() {
        return $this->summary["locations"];
    }

    public function pack_info() {
        return array(
            "id" => $this->id(),
            "nick" => $this->nick(),
            "face" => $this->face(),
            "score" => $this->score(),
            "pass" => $this->pass(),
            "reject" => $this->reject(),
            "tasks" => $this->tasks(),
            "locations" => $this->locations(),
        );
    }

    public static function load_all() {
        $cached = cache::instance()->load("all_wechatuser");
        if ($cached != null) {
            return $cached;
        }
        $wusers = db_wechatusers::inst()->get_all_users();
        $us = array();
        foreach ($wusers as $k => $summary) {
            $us [$k]= new wechatuser($summary);
        }
        cache::instance()->save("all_wechatuser", $us);
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


