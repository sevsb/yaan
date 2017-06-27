<?php
include_once(dirname(__FILE__) . "/config.php");

class customers {
    
    public function __construct($data) {
        $this->summary = $data;
    }
    
    public static function add($name, $tel) {
        $ret = db_customers::inst()->add($name, $tel);
        return $ret;
    }
    
    public static function modify($id, $name, $tel) {
        $ret = db_customers::inst()->modify($id, $name, $tel);
        return $ret;
    }
    
    public static function del($id) {
        $ret = db_customers::inst()->del($id);
        return $ret;
    }
    
    public static function get_all_customers() {
        return db_customers::inst()->get_all_customers();
    }
    
    public static function get_customer_detail($id) {
        return db_customers::inst()->get_customer_detail($id);
    }
    
}

?>