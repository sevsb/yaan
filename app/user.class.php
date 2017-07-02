<?php
include_once(dirname(__FILE__) . "/../config.php");

class user {
    private $summary = array();

    public function __construct($data) {
        $this->summary = $data;
    }

    public static function load_all_users($include_librarian = true) {
        $users = array();

        $ret = db_user::inst()->get_all_users();
        foreach ($ret as $uid => $user) {
            $users[$uid] = new user($user);
        }
        return $users;
    }

    public static function create($userid) {
        $user = db_user::inst()->get_one_user($userid);
        return new user($user);
    }

    private function summary($key, $def = "") {
        if (isset($this->summary[$key])) {
            return $this->summary[$key];
        }

        return $def;
    }

    public function id() {
        return $this->summary("id", 0);
    }

    public function nick() {
        return $this->summary("nick");
    }

    public function email() {
        return $this->summary("email");
    }

    public function faceurl($full = false) {
        if (!isset($this->summary["faceurl"])) {
            $this->summary["faceurl"] = rtrim(UPLOAD_URL, "/") . "/" . $this->summary("face");
        }
        if ($full) {
            return mk_domain_url($this->summary["faceurl"]);
        }
        return $this->summary["faceurl"];
    }

    public function face_thumbnail($full = false) {
        if (!isset($this->summary["facethumbnail"])) {
            $this->summary["facethumbnail"] = mkUploadThumbnail($this->summary("face"), 100, 100);
        }
        if ($full) {
            return mk_domain_url($this->summary["facethumbnail"]);
        }
        return $this->summary["facethumbnail"];
    }

    public function last_login_time() {
        $time = get_session("user.last_login_time");
        return date("Y-m-d H:i:s", $time);
    }
};


