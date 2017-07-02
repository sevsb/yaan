<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_muffininfos extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_muffininfos();
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

    public function get_all_muffininfos() {
        return $this->get_all_table(TABLE_MUFFININFOS);
    }

    public function add($project_id, $muffin_id, $title, $type, $description, $maintext, $cover, $limit_time, $paperfile) {
        return $this->insert(TABLE_MUFFININFOS, array("project_id" => $project_id, "muffinid" => $muffin_id, "title" => $title, "type" => $type, "description" => $description, "text" => $maintext, "cover" => $cover, "limit_time" => $limit_time, "paperfile" => $paperfile, "status" => "未领取"));
    }
    
    public function del($id){
        return $this->delete(TABLE_MUFFININFOS, "muffinid = '$id'");
    }
    
};


