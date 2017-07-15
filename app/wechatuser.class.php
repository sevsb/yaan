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

    public function location_objects() {
        $locs = $this->locations();
        if (empty($locs)) {
            return array();
        }
        $locs = json_decode($locs, true);
        $arr = array();
        foreach ($locs as $s) {
            $arr[] = new location($s);
        }
        return $arr;
    }

    public function update_location($location) {
        $locations = $this->location_objects();
        if (count($locations) >= settings::intance()->load("RECORD_LOCATIONS", 3)) {
            array_shift($locations);
        }
        $locations[] = $location;

        $locs = array();
        foreach ($locations as $l) {
            $locs []= $l->pack_info();
        }

        $this->summary["locations"] = json_encode($locs);
        $this->save();
    }

    public function save() {
        $id = $this->id();
        if ($id == 0) {
            return false;
        }
        return db_wechatusers::inst()->update_user($id, $this->nick(), $this->face(), $this->tasks(), $this->pass(), $this->reject(), $this->locations());
    }

    public function pack_info() {
        $locations = $this->location_objects();
        $locs = array();
        foreach ($locations as $l) {
            $locs []= $l->pack_info();
        }
        return array(
            "id" => $this->id(),
            "nick" => $this->nick(),
            "face" => $this->face(),
            "score" => $this->score(),
            "pass" => $this->pass(),
            "reject" => $this->reject(),
            "tasks" => $this->tasks(),
            "locations" => $locs,
        );
    }

    public static function create($id) {
        $user = db_wechatusers::inst()->get_user_by_id($id);
        if (empty($user)) {
            return null;
        }
        return new wechatuser($user);
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


