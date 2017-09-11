<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_answer extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_answer();
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
        return $this->get_cached("db:answer", TABLE_ANSWER);
    }

    public function get_all_answer() {
        return $this->get_all_table(TABLE_ANSWER, "is_remove != 1");
    }

    public function get_one_bynid($nid) {
        return $this->get_one_table(TABLE_ANSWER, "nid = $nid");
    }

    public function get_answer_by_id($id) {
        $id = (int)$id;
        return $this->get_one_table(TABLE_ANSWER, "id = $id");
    }

    public function add_answer($nid, $title, $notes, $content) {
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."id:$nid, title:$title notes:$notes \r\n", FILE_APPEND);
        return $this->insert(TABLE_ANSWER, array("nid" => $nid, "title" => $title, "notes" => $notes, "content" => $content));
    }

    public function modify_answer($id, $title, $notes, $content) {
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."id:$id, title:$title notes:$notes \r\n", FILE_APPEND);
        return $this->update(TABLE_ANSWER, array("title" => $title, "notes" => $notes, "content" => $content), "id = $id");
    }
    
    public function remove($id){
        return $this->update(TABLE_ANSWER, array("is_remove" => 1), "id = $id");
    }

    public function del($id){
        return $this->delete(TABLE_ANSWER, "id = '$id'");
    }
};


