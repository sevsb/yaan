<?php

include_once(dirname(__FILE__) . "/../config.php");

class mc {

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null) {
            self::$instance = new mc();
        }
        return self::$instance->m;
    }

    private $m = null;
    private function __construct() {
        $this->m = new Memcache();
        $ret = $this->m->connect(MEMCACHE_SERVER, MEMCACHE_PORT);
        if ($ret === false) {
            $this->m = null;
        }
    }
};


