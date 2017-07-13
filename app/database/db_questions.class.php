<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_questions extends database {
    const TYPE_WORD = 0;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_questions();
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
        return $this->get_all_table(TABLE_QUESTIONS);
    }

    public function add_word_question($title, $choice) {
        $type = self::TYPE_WORD;
        return $this->insert(array("type" => $type, "title" => $title, "choice" => $choice));
    }
};


