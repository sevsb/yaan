<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class index_controller {
    public function home_action() {
        $tpl = new tpl("wechat/header", "wechat/footer");
        $tpl->display("wechat/index/home");
    }

    public function sheet_action() {
        // 姑且先放在index中
        $tpl = new tpl("wechat/header", "wechat/footer");
        $tpl->display("wechat/index/sheet");
    }
}

?>