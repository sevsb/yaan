<?php

include_once(dirname(__FILE__) . "/../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_settings extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_settings();
        return self::$instance;
    }

    private function __construct() {
        try {
            $this->init(MYSQL_DATABASE);
        } catch (PDOException $e) {
            logging::e("PDO.Exception", $e, false);
            die($e);
        }
    }

    public function load_all() {
        return $this->get_all_table(TABLE_SETTINGS);
    }

    public function load($key) {
        $key = $this->escape($key);
        return $this->get_one_table(TABLE_SETTINGS, "name = $key");
    }

    public function save($key, $value) {
        $row = $this->load($key);
        if (empty($row)) {
            return $this->insert(TABLE_SETTINGS, array("name" => $key, "value" => $value));
        } else {
            $id = $row["id"];
            $ret = $this->update(TABLE_SETTINGS, array("value" => $value), "id = $id");
            return ($ret !== false) ? $id : $ret;
        }
    }
    
//-------------------------EVENT-SETTINGS-----------------------------------
        
    public function load_event_settings() {
        return $this->get_all_table(TABLE_EVENT_SETTINGS, "", "order by type");
    }
    
    public function load_event_setting($id) {
        return $this->get_one_table(TABLE_EVENT_SETTINGS, "id = $id");
    }
    
    public function add_event_setting($title, $color, $type) {
        return $this->insert(TABLE_EVENT_SETTINGS, array("title" => $title, "color" => $color, "type" => $type));
    } 
    
    public function update_event_setting($id, $title, $color, $type) {
        return $this->update(TABLE_EVENT_SETTINGS, array("title" => $title, "color" => $color, "type" => $type), "id = $id");
    }

    
};


