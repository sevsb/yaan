<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_questionnaires extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_questionnaires();
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
        return $this->get_cached("db:questionnaires", TABLE_QUESTIONNAIRES);
    }

    public function get_all_questionnaires() {
        return $this->get_all_table(TABLE_QUESTIONNAIRES, "is_remove = 0 and is_cache != 1");
    }

    public function get_one_by_projectid($pid) {
        return $this->get_one_table(TABLE_QUESTIONNAIRES, "pid = $pid");
    }

    public function get_questionnaires_by_id($id) {
        $id = (int)$id;
        return $this->get_one_table(TABLE_QUESTIONNAIRES, "id = $id");
    }
    
    public function get_cache_naire($userid) {
        $userid = (int)$userid;
        return $this->get_one_table(TABLE_QUESTIONNAIRES, "creater_id = $userid and is_cache = 1");
    }

    public function add_questionnaires($pid, $title, $notes, $userid) {
        return $this->insert(TABLE_QUESTIONNAIRES, array("pid" => $pid, "title" => $title, "notes" => $notes, "count" => 0, "creater_id" => $userid, "is_cache" => 1));
    }

    public function modify_questionnaires($id, $title, $notes) {
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."id:$id, title:$title notes:$notes \r\n", FILE_APPEND);
        return $this->update(TABLE_QUESTIONNAIRES, array("title" => $title, "notes" => $notes), "id = $id");
    }
    
    public function save_naire($id) {
        //file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."id:$id, title:$title notes:$notes \r\n", FILE_APPEND);
        return $this->update(TABLE_QUESTIONNAIRES, array("is_cache" => 0), "id = $id");
    }
    
    public function remove_naire($id) {
        return $this->update(TABLE_QUESTIONNAIRES, array("is_remove" => 1), "id = $id");
    }
    
    public function remove($id){
        return $this->update(TABLE_QUESTIONNAIRES, array("is_remove" => 1), "id = $id");
    }

    public function del($id){
        return $this->delete(TABLE_QUESTIONNAIRES, "id = '$id'");
    }
};


