<?php
include_once(dirname(__FILE__) . "/../app/config.php");

class main_controller {
    
    public function preaction($action) {
        login::assert();
    }
    public function main_action() {
        $tpl = new tpl("main/header", "main/footer");
        $tpl->display("main/main");
    }

}













