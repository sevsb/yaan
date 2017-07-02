<?php
include_once(dirname(__FILE__) . "/../config.php");

class settings {
    private static $instance = null;
    public static function instance() {
        if (self::$instance == null)
            self::$instance = new settings();
        return self::$instance;
    }

    private $configs = array();
    private $event_settings = array();
    
    private function __construct() {
        $configs = db_settings::inst()->load_all();
        foreach ($configs as $config) {
            $key = $config["name"];
            $this->configs[$key] = $config;
        }
    }

    public function load($key, $def = null) {
        return isset($this->configs[$key]) ? $this->configs[$key]["value"] : $def;
    }

    public function save($key, $val) {
        $id = db_settings::inst()->save($key, $val);
        if ($id !== false) {
            $this->configs[$key] = array("id" => $id, "name" => $key, "value" => $val);
        }
        return $id;
    }

    public function get_all() {
        return $this->configs;
    }
};


