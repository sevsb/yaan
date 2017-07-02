<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class login_controller {
    public function index_action() {
        // dump_var($_SESSION);

        $salt = login::mksalt();

        $tpl = new tpl("admin/login/header", "admin/login/footer");
        $tpl->set("salt", $salt);
        $tpl->display("admin/login/login");
    }

    public function get_salt_ajax() {
        return get_session('login_salt');
    }
    
    public function logout_action() {
        login::bye();
        go("admin");
    }

    public function login_ajax() {
        $email = get_request_assert("email");
        $cipher = get_request_assert("cipher");
        return login::do_login($email, $cipher);
    }


    /*
    public function register_action() {
        $tpl = new tpl("wechat/index/header", "wechat/index/footer");
        $tpl->display("wechat/index/register");
    }

    public function forget_action() {
        $tpl = new tpl("wechat/index/header", "wechat/index/footer");
        $tpl->display("wechat/index/forget");
    }


    public function forget_ajax() {
        $email = get_request_assert("email");
        return mailer::instance()->send_forget_password($email);
    }

    public function forget2_action() {
        $userid = get_request_assert("userid");
        $token = get_request_assert("token");
        $user = db_user::inst()->get_one_user($userid);
        if (empty($token) || empty($user["token"]) || $user["token"] != $token) {
            die("invalid token.");
        }
        $_SESSION["forget.userid"] = $userid;
        $_SESSION["forget.token"] = $token;
        go("login/forget3");
    }

    public function forget3_action() {
        // dump_var($_SESSION);
        $tpl = new tpl("wechat/index/header", "wechat/index/footer");
        $tpl->display("wechat/index/forget2");
    }

    public function update_forget_ajax() {
        $password = get_request_assert("password");
        $userid = get_session("forget.userid");
        $token = get_session("forget.token");
        if ($userid === null || $token === null) {
            return "fail|token expired.";
        }

        $user = db_user::inst()->get_one_user($userid);
        if (empty($token) || empty($user["token"]) || $user["token"] != $token) {
            return "fail|invalid token.";
        }
        $ret = db_user::inst()->update_password($userid, $password);
        if ($ret !== false) {
            unset($_SESSION["forget.userid"]);
            unset($_SESSION["forget.token"]);
            db_user::inst()->disable_login_token($userid);
        }
        return ($ret !== false) ? "success" : "fail|数据库操作失败，请稍后重试。";
    }

    public function register_ajax() {
        $nick = get_request_assert("nick");
        $email = get_request_assert("email");
        $password = get_request_assert("password");
        $face = get_request_assert("face");

        $u = db_user::inst()->get_user($email);
        if (!empty($u)) {
            return "fail|邮箱已被注册.";
        }

        $args = array("nick" => $nick, "email" => $email, "password" => $password);
        
        return uploadImageViaFileReader($face, function($filename, $args) {
            mkUploadThumbnail($filename, 100, 100);
            $ret = db_user::inst()->add($args["nick"], $args["email"], $args["password"], $filename);
            return ($ret !== false) ? "success" : "fail|数据库操作失败，请稍后重试。";
        }, $args);
    }

    public function update_password_ajax() {
        $newp = get_request_assert("newp");
        $oldp = get_request_assert("oldp");

        $userid = get_session("user.id");
        if ($userid == null) {
            return "fail|未登录。";
        }

        $user = db_user::inst()->get_one_user($userid);
        if ($user["password"] != $oldp) {
            return "fail|旧密码错误.";
        }

        $ret = db_user::inst()->update_password($userid, $newp);
        return ($ret !== false) ? "success|修改成功。" : "fail|数据库操作失败，请稍后重试。";
    }

    public function update_password_token_ajax() {
        $newp = get_request_assert("newp");
        $oldp = get_request_assert("oldp");
        $userid = get_request_assert("userid");
        $token = get_request_assert("token");

        $user = db_user::inst()->get_one_user($userid);
        if (empty($token) || empty($user["token"]) || $user["token"] != $token) {
            return "fail|invalid token.";
        }
        if ($user["password"] != $oldp) {
            return "fail|旧密码错误.";
        }

        $ret = db_user::inst()->update_password($userid, $newp);
        return ($ret !== false) ? "success|修改成功。" : "fail|数据库操作失败，请稍后重试。";
    }

    public function authorize_ajax() {
        $userid = get_request_assert("userid");
        $token = get_request_assert("token");

        $user = db_user::inst()->get_one_user($userid);
        if (empty($token) || empty($user["token"]) || $user["token"] != $token) {
            return "fail|invalid token.";
        }

        unset($user["password"]);
        unset($user["token"]);
        unset($user["tokentime"]);
        $user["ret"] = "success";
        $user["face"] = DOMAIN_URL . UPLOAD_URL . "/" . $user["face"]; 
        return $user;
    }
    */
}













