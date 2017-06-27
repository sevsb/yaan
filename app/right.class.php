<?php
include_once(dirname(__FILE__) . "/config.php");

class right {
    // view / modify for each right.
    // manage = view + modify

    // admin
    const manage_admin = "manage_admin";
    // const manage_user = "manage_user";
    // const manage_group = "manage_group";
    const manage_system = "manage_system";


    public static function name($right = null) {
        $right_name_table = array(
            self::manage_admin => "用户管理",
        );
        if ($right == null)
            return $right_name_table;

        if (isset($right_name_table[$right]))
            return $right_name_table[$right];

        return $right;
    }

    public static function has($right) {
        $uid = get_session("userid");
        if ($uid == 1) {
            return ALLOW_ROOT;
        }

        $perms = get_session("perms");
        if ($perms == null)
            return false;
        if (in_array($right, $perms))
            return true;
        return false;
    }

    public static function assert($right) {
        if (!self::has($right)) {
            // die("you have no right to access this page.");
            die("<script> alert('you have no right to access this page.');history.go(-1);</script>");  
        }
    }

};


