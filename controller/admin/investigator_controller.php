<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class investigator_controller {
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $tpl->display("admin/investigator/index");
    }

    public function listall_action() {
        $users = wechatuser::load_all();
        $data = array();
        foreach ($users as $user) {
            $data []= $user->pack_info();
        }
        $res = array("op" => "wechatlist", "data" => $data);
        echo json_encode($res);
    }
}













