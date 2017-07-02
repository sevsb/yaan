<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class index_controller {
    public function preaction($action) {
        login::assert();
    }
    public function index_action() {
         $tpl = new tpl("admin/header", "admin/footer");
         $tpl->display("admin/index/index");
    }

}













