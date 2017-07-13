<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class sheet_controller {
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $tpl->display("admin/sheet/index");
    }



    public function sheetlist_action() {
        $sheets = sheet::load_all();
        $data = array();
        foreach ($sheets as $sheet) {
            $data []= $sheet->pack_info();
        }
        $res = array("op" => "sheetlist", "data" => $data);
        echo json_encode($res);
    }
}













