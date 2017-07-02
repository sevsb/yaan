<?php

include_once(dirname(__FILE__) . "/../config.php");

class reply_base {
    protected $summary = array();
    public function __construct($data) {
        $summary = $data;
    }
    public function init() {
        logging::fatal("Choice", "cannot run here.");
    }
};

// {"reply": "A"}
class radio_reply extends reply_base {
};

// {"reply": "ABC"}
class multi_reply extends reply_base {
};

// {"reply": "xxxxxx"}
class completion_reply extends reply_base {
};

// {"files": ["pic1.jpg", "pic2.png"]}
class word_reply extends reply_base {
    private $jsonarr = array();
    public function init() {
        $jsonarr = json_decode($this->data, true);
    }
    public function get_replies() {
        return $jsonarr["files"];
    }
};

class reply {
    public static function create($type, $data) {
        return null;
    }
};

