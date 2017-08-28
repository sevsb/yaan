<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_question extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_question();
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
        return $this->get_cached("db:question", TABLE_QUESTION);
    }

    public function get_all_questionnaires() {
        return $this->get_all_table(TABLE_QUESTION);
    }

    public function get_by_naireid($nid) {
        return $this->get_all_table(TABLE_QUESTION, "nid = $nid and is_remove = 0","order by id");
    }
    
    public function get_assoc_by_naireid($nid,$qid) {
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."nid:$nid\r\n", FILE_APPEND);
        return $this->get_all_table(TABLE_QUESTION, "nid = $nid and is_remove = 0 and (type='check' or type='radio') and id<$qid","order by id");
    }

    public function get_questions_by_id($id) {
        $id = (int)$id;
        return $this->get_one_table(TABLE_QUESTION, "id = $id");
    }

    public function add_questions($nid, $title, $type, $notes, $value='') {
        return $this->insert(TABLE_QUESTION, array("nid" => $nid, "title" => $title, "type" => $type, "notes" => $notes, "value" =>$value));
    }

    public function modify_questions($id, $title, $type, $notes, $value='') {
        return $this->update(TABLE_QUESTION, array("title" => $title, "type" => $type, "notes" => $notes, "value" => $value), "id = $id");
    }
    
    public function set_parent($id, $optionid) {
        return $this->update(TABLE_QUESTION, array("is_parent" => $optionid), "id = $id");
    }

    public function remove($id){
        return $this->update(TABLE_QUESTION, array("is_remove" => 1), "id = $id");
    }
    
    public function del($id){
        return $this->delete(TABLE_QUESTION, "id = '$id'");
    }
};


