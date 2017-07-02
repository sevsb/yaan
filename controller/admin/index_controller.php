<?php
include_once(dirname(__FILE__) . "/../..//config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class index_controller {
    public function preaction($action) {
        login::assert();
    }
    public function index_action() {
         $tpl = new tpl("wechat/index/mainheader", "wechat/index/footer");
         $salt = login::mksalt();

         $tpl->set("salt", $salt);
         $tpl->display("wechat/index/index");
    }

}













