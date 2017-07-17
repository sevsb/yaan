<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_answers extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_answers();
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
        return $this->get_all_table(TABLE_ANSWERS);
    }

    public function add_answer($type, $title, $choice, $reply) {
        return $this->insert(TABLE_ANSWERS, array("type" => $type, "title" => $title, "choice" => $choice, "reply" => $reply));
    }

    public function update_answer($id, $type, $title, $choice, $reply) {
        $id = (int)$id;
        return $this->update(TABLE_ANSWERS, array("type" => $type, "title" => $title, "choice" => $choice, "reply" => $reply), "id = $id");
    }

    public function get_one_answer($id) {
        $id = (int)$id;
        return $this->get_one_table(TABLE_ANSWERS, "id = $id");
    }

    public function get_some_answers($idarr) {
        foreach ($idarr as $k => $id) {
            $idarr[$k] = (int)$id;
        }
        $where = "id = " . implode(" OR id = ", $idarr);
        return $this->get_all_table(TABLE_ANSWERS, $where);
    }
};


