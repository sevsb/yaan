<?php

include_once(dirname(__FILE__) . "/config.php");

class login {

    public static function do_login($email, $cipher) {
        $salt = get_session("login_salt");

        $user = db_user::inst()->get_user($email);
        logging::d("Login", "user = " . dump_var($user, true));
        if ($user == null) {
            return "invalid email.";
        }

        $password = $user["password"];
        $c1 = md5($email. $salt . $password);
        logging::d("Login", "email = " . $email);
        logging::d("Login", "salt = " . $salt);
        logging::d("Login", "password = " . $password);
        logging::d("Login", "c1 = " . $c1);
        logging::d("Login", "cipher = " . $cipher);
        if ($c1 == $cipher) {
            $_SESSION["user.id"] = $user["id"];
            $_SESSION["user.name"] = $user["nick"];
            $_SESSION["user.email"] = $user["email"];
            $_SESSION["user.face"] = mkUploadThumbnail($user["face"], 100, 100);
            $_SESSION["user.large-face"] = $user["face"];
            $_SESSION["user.last_login_time"] = $user["last_login_time"];
            $_SESSION["user.admin"] = ($user["admin"] == "1");
            $_SESSION["login.next"] = HOME_URL . "?main/main";
            db_user::inst()->update_login_time($user["id"]);

            $token = "l" . md5($user["id"] . uniqid());
            db_user::inst()->update_login_token($user["id"], $token);

            // jump to homepage after login.
            $refer = get_session("login.next");
            if ($refer == null) {
                $refer = HOME_URL;
            }

            $delimiter = (strstr($refer, "?") === false) ? "?" : "&";
            $refer = $refer . $delimiter . "userid={$user["id"]}&token=$token";

            logging::i("Login", "login success, jump to $refer");
            return array("ret" => "success", "refer" => $refer);
        }
        return "invalid password.";
    }

    public static function bye() {
        unset($_SESSION["admin.login"]);
        unset($_SESSION["user.id"]);
        unset($_SESSION["user.name"]);
        unset($_SESSION["user.email"]);
        unset($_SESSION["user.face"]);
        unset($_SESSION["user.large-face"]);
    }

    public static function mksalt() {
        $salt = md5(uniqid());
        $_SESSION["login_salt"] = $salt;
        return $salt;
    }

    public static function assert() {
        $refer = $_SERVER["REQUEST_URI"];
        $_SESSION["login_refer"] = $refer;
        logging::d("Login", "refer from $refer");

        $uid = get_session("user.id");
        if ($uid == null) {
            go("login");
        }
    }
};

