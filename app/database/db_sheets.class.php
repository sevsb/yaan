<?php

include_once(dirname(__FILE__) . "/../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_sheets extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_sheets();
        return self::$instance;
    }

    private function __construct() {
        try {
            $this->init(MYSQL_DATABASE);
        } catch (PDOException $e) {
            logging::e("PDO.Exception", $e, false);
            die($e);
            // $this->init();
        }
    }
    
    public function add($new_muffin_id, $title, $content, $address, $location) {
        return $this->insert(TABLE_SHEETS, array("muffinid" => $new_muffin_id, "title" => $title, "location" => $location, "address" => $address, "content" => $content, "status" => "0"));
    }
    
    public function get_all_sheets() {
        return $this->get_all_table(TABLE_SHEETS);
    }
};


