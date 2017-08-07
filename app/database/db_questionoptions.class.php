<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_questionoptions extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_questionoptions();
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
        return $this->get_cached("db:questionoptions", TABLE_QUESTIONOPTIONS);
    }

    public function get_all_options() {
        return $this->get_all_table(TABLE_QUESTIONOPTIONS);
    }

    public function get_by_questionid($qid) {
        return $this->get_all_table(TABLE_QUESTIONOPTIONS, "qid = $qid and is_remove = 0","order by option_id");
    }

    public function get_options_by_id($id) {
        $id = (int)$id;
        return $this->get_one_table(TABLE_QUESTIONOPTIONS, "id = $id");
    }

    public function add_options($qid, $title, $value, $option_id) {
        return $this->insert(TABLE_QUESTIONOPTIONS, array("qid" => $qid, "title" => $title, "value" => $value, "option_id" => $option_id));
    }

    public function modify_options($id, $title, $value) {
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."id:$id, title:$title value:$value \r\n", FILE_APPEND);
        return $this->update(TABLE_QUESTIONOPTIONS, array("title" => $title, "value" => $value), "id = $id");
    }
    
    public function remove($id){
        return $this->update(TABLE_QUESTIONOPTIONS, array("is_remove" => 1), "id = $id");
    }

    public function del($id){
        return $this->delete(TABLE_QUESTIONOPTIONS, "id = $id");
    }
};


