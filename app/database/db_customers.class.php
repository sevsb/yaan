<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_customers extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_customers();
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

    public function add($name, $tel) {
        return $this->insert(TABLE_CUSTOMERS, array("name" => $name, "tel" => $tel));
    }
    
    public function modify($id, $name, $tel) {
        return $this->update(TABLE_CUSTOMERS, array("name" => $name, "tel" => $tel), "id = '$id'");
    }
    
    public function del($id) {
        return $this->delete(TABLE_CUSTOMERS, "id = '$id'");
    }
    
    public function get_all_customers() {
        return $this->get_cached('all_customers', TABLE_CUSTOMERS);
    }
    
    public function get_customer_detail($id) {
        return $this->get_one_table(TABLE_CUSTOMERS, "id = '$id'");
    }

};


