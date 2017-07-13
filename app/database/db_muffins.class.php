<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_muffins extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_muffins();
        return self::$instance;
    }

    public function __construct() {
        try {
            $this->init(MYSQL_DATABASE);
        } catch (PDOException $e) {
            logging::e("PDO.Exception", $e, false);
            die($e);
            // $this->init();
        }
    }

    public function get_all_cached() {
        return $this->get_cached("db:muffins", TABLE_MUFFINS);
    }

    public function get_all_muffins() {
        return $this->get_all_table(TABLE_MUFFINS);
    }
    
    public function load_tasks_by_project($muffinid) {
        return $this->get_all_table(TABLE_MUFFINS, "pid = $muffinid");
    }
    
    public function get_project_id($muffinid) {
        return $this->get_one_table(TABLE_MUFFINS, "id = $muffinid");
    }

    public function add($pid, $title, $face) {
        return $this->insert(TABLE_MUFFINS, array("pid" => $pid, "title" => $title, "face" => $face));
    }

    public function modify($id, $pid, $mtitle, $face) {
        return $this->update(TABLE_MUFFINS, array("pid" => $pid, "title" => $title, "face" => $face), "id = $id");
    }
    
    public function del($id) {
         return $this->delete(TABLE_MUFFINS, "id = '$id'");
    }
    
    public function del_by_project($id) {
         return $this->delete(TABLE_MUFFINS, "id = '$id' or pid = $id");
    }
};


