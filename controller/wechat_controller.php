<?php
include_once(dirname(__FILE__) . "/../config.php");

class wechat_controller {

    public function index_action() {
        $ret = WXApi::inst()->doOAuth(true);
        if ($ret == null) {
            die("认证失败。");
        }
        // $_SESSION["wechat"] = $ret;
        $openid = $ret["openid"];
        $user = db_wechatusers::inst()->get_user_by_openid($openid);
        if (empty($user)) {
            $nick = $ret["nickname"];
            $faceurl = $ret["headimgurl"];
            $id = db_wechatusers::inst()->add($openid, $nick, $faceurl);
            $user = array("id" => $id, "openid" => $openid, "nickname" => $nick, "face" => $faceurl);
        }
        $_SESSION["user"] = $user;
        $_SESSION["user.name"] = $user["nickname"];
        go("wechat/index/home");
    }

    public function test_action() {
        unset($_SESSION["user"]);
        unset($_SESSION["user.name"]);

        $userid = get_request_assert("user");
        $user = db_wechatusers::inst()->get_user_by_id($userid);
        logging::assert(!empty($user), "No such user.");
        $_SESSION["user"] = $user;
        $_SESSION["user.name"] = $user["nickname"];

        logging::d("Debug", $user);
        go("wechat/index/home");
    }
}













