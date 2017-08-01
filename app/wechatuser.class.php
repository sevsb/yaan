<?php
include_once(dirname(__FILE__) . "/../config.php");
include_once(dirname(__FILE__) . "/database/db_wechatusers.class.php");
include_once(FRAMEWORK_PATH . "cache.php");

class wechatuser {
    private $summary = array();
    private $mRunningTasks = null;
    
    private function __construct($summary) {
        $this->summary = $summary;
    }

    public function id() {
        return $this->summary["id"];
    }
    public function openid() {
        return $this->summary["openid"];
    }

    public function nick() {
        return $this->summary["nickname"];
    }

    public function face() {
        return $this->summary["face"];
    }

    public function taskcount() {
        return $this->summary["taskcount"];
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

    public function running_tasks() {
        if ($this->mRunningTasks === null) {
            $this->mRunningTasks = tasks::load_user_tasks($this->id());
        }
        return $this->mRunningTasks;
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
        $location->set_time(time());

        $locations = $this->location_objects();
        if (count($locations) >= settings::instance()->load("RECORD_LOCATIONS", 3)) {
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
        logging::d("Debug", "save wechatuser infos: $id");
        if ($id == 0) {
            return false;
        }
        return db_wechatusers::inst()->update_user($id, $this->nick(), $this->face(), $this->taskcount(), $this->pass(), $this->reject(), $this->locations());
    }

    public function pack_info() {
        $locations = $this->location_objects();
        $locs = array();
        foreach ($locations as $l) {
            $locs []= $l->pack_info();
        }

        $tasks = $this->running_tasks();
        $tarr = array();
        foreach ($tasks as $tid => $task) {
            $tarr []= $task->pack_info();
        }

        return array(
            "id" => $this->id(),
            "nick" => $this->nick(),
            "face" => $this->face(),
            "score" => $this->score(),
            "pass" => $this->pass(),
            "reject" => $this->reject(),
            "taskcount" => $this->taskcount(),
            "locations" => $locs,
            "tasks" => $tarr,
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


