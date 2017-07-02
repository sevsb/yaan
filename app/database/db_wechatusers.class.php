<?php

include_once(dirname(__FILE__) . "/../../config.php");
include_once(FRAMEWORK_PATH . "logging.php");
include_once(FRAMEWORK_PATH . "cache.php");
include_once(FRAMEWORK_PATH . "database.php");

class db_wechatusers extends database {
    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_wechatusers();
        return self::$instance;
    }

    private function __construct() {
        try {
            $this->init(MYSQL_DATABASE);
        } catch (PDOException $e) {
            logging::e("PDO.Exception", $e, false);
            die($e);
            // $this->init();
        }
    }

    public function get_all_users() {
        return $this->get_all_table(TABLE_WECHATUSERS);
    }

    public function get_user_by_id($userid) {
        $userid = (int)$userid;
        return $this->get_one_table(TABLE_WECHATUSERS, "id = $userid");
    }

    public function get_user_by_openid($openid) {
        $openid = $this->escape($openid);
        return $this->get_one_table(TABLE_WECHATUSERS, "openid = $openid");
    }


    public function add($openid, $nickname, $faceurl) {
        $openid = $this->escape($openid);
        $nickname = $this->escape($nickname);
        $faceurl = $this->escape($faceurl);
        return $this->insert(TABLE_WECHATUSERS, array("openid" => $openid, "nickname" => $nickname, "face" => $faceurl));
    }

    public function update_location_by_id($userid, $location) {
        $userid = (int)$userid;
        $location = $this->escape($location);
        return $this->update(TABLE_WECHATUSERS, array("location" => $location), "id = $userid");
    }

    public function update_location_by_openid($openid, $location) {
        $location = $this->escape($location);
        $openid = $this->escape($openid);
        return $this->update(TABLE_WECHATUSERS, array("location" => $location), "openid = $openid");
    }

    public function update_profile($userid, $openid, $nickname, $faceurl) {
        $userid = (int)$userid;
        $openid = $this->escape($openid);
        $nickname = $this->escape($nickname);
        $faceurl = $this->escape($faceurl);
        return $this->update(TABLE_WECHATUSERS, array("openid" => $openid, "nickname" => $nickname, "face" => $faceurl, "id = $userid"));
    }
};


