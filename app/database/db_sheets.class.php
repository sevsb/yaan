<?php

include_once(dirname(__FILE__) . "/../../config.php");
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

    public function __construct() {
        try {
            $this->init(MYSQL_DATABASE);
        } catch (PDOException $e) {
            logging::e("PDO.Exception", $e, false);
            die($e);
        }
    }

    public function load_all() {
        return $this->get_all_table(TABLE_SHEETS);
    }

    public function get_sheet_by_id($id) {
        $id = (int)$id;
        return $this->get_one_table(TABLE_SHEETS, "id = $id");
    }
    
    public function get_sheet_by_paperid($paperid) {
        $paperid = (int)$paperid;
        return $this->get_one_table(TABLE_SHEETS, "paperid = $paperid");
    }

    public function add_sheet($userid, $paperid, $title, $info, $answers, $status) {
        return $this->insert(TABLE_SHEETS, array("userid" => $userid, "paperid" => $paperid, "title" => $title, "info" => $info, "answers" => $answers, "status" => $status));
    }

    public function update_sheet($id, $userid, $paperid, $title, $info, $answers, $status) {
        $id = (int)$id;
        return $this->update(TABLE_SHEETS, array("userid" => $userid, "paperid" => $paperid, "title" => $title, "info" => $info, "answers" => $answers, "status" => $status), "id = $id");
    }

    public function del_by_paperid($paperid) {
        return $this->delete(TABLE_SHEETS, "paperid = '$paperid'");
    }
};


