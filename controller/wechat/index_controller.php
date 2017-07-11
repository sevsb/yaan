<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class index_controller {
    public function home_action() {
        $user = get_session_assert("user");

        $tpl = new tpl("wechat/header", "wechat/footer");
        $tpl->set("user", $user);
        $tpl->display("wechat/index/home");
    }

    public function sheet_action() {
        // 姑且先放在index中
        $tpl = new tpl("wechat/header", "wechat/footer");
        $signPackage = WXApi::inst()->get_SignPackage();
        $tpl->set('signPackage', $signPackage);
        $tpl->display("wechat/index/sheet");
    }
}

?>