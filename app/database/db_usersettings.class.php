<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_usersettings extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_usersettings();
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

    public function load_all($user = 0) {
        $user = (int)$user;
        if ($user > 0) {
            return $this->get_all_table(TABLE_USERSETTINGS, "userid = $user");
        } else {
            return $this->get_all_table(TABLE_USERSETTINGS);
        }
    }

    public function load($user, $key) {
        $user = (int)$user;
        $key = $this->escape($key);
        return $this->get_one_table(TABLE_USERSETTINGS, "userid = $user AND name = $key");
    }

    public function save($user, $key, $value) {
        $row = $this->load($user, $key);
        if (empty($row)) {
            return $this->insert(TABLE_USERSETTINGS, array("userid" => $user, "name" => $key, "value" => $value));
        } else {
            $id = $row["id"];
            $ret = $this->update(TABLE_USERSETTINGS, array("value" => $value), "id = $id");
            return ($ret !== false) ? $id : $ret;
        }
    }
};


