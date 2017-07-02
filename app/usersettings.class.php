<?php
include_once(dirname(__FILE__) . "/../config.php");

class usersettings {
    private static $instance = array();
    public static function instance($userid) {
        if (!isset(self::$instance[$userid]))
            self::$instance[$userid] = new usersettings($userid);
        return self::$instance[$userid];
    }

    private $configs = array();
    private $userid = 0;

    private function __construct($userid) {
        $this->userid = $userid;
        $configs = db_usersettings::inst()->load_all($userid);
        foreach ($configs as $config) {
            $key = $config["name"];
            $this->configs[$key] = $config;
        }
    }

    public function load($key, $def = null) {
        return isset($this->configs[$key]) ? $this->configs[$key]["value"] : $def;
    }

    public function save($key, $val) {
        $id = db_usersettings::inst()->save($this->userid, $key, $val);
        if ($id !== false) {
            $this->configs[$key] = array("id" => $id, "userid" => $this->userid, "name" => $key, "value" => $val);
        }
        return $id;
    }

    public function get_all() {
        return $this->configs;
    }
};


