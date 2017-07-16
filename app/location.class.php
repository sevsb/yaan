<?php

include_once(dirname(__FILE__) . "/lcs.class.php");

class location_node {
    private $mCode = 0;
    private $mTitle = null;
    // private $lcs = null;

    public function location_node($summary) {
        if (is_array($summary)) {
            $this->mCode = $summary["code"];
            $this->mTitle = $summary["title"];
        } else {
            $this->mCode = 0;
            $this->mTitle = $summary;
        }
        // $this->lcs = new LCS();
    }

    public function &code() {
        return $this->mCode;
    }

    public function &title() {
        return $this->mTitle;
    }

    public function equals($o) {
        return ($this->title() == $o->title());
        // return ($this->code() == $o->code());
        /*
        if ($this->code() == $o->code()) {
            return true;
        }
        $percent = $this->lcs->getSimilar($this->title(), $o->title());
        return ($percent > 0.9);
         */
    }

    public function pack_info() {
        return array("code" => "{$this->code()}", "title" => "{$this->title()}");
    }
}

class location {
    private $mProvince = null;
    private $mCity = null;
    private $mDistrict = null;
    private $mTile = null;

    public function location($summary) {
        if (is_string($summary)) {
            $arr = json_decode($summary, true);
        } else if (is_array($summary)) {
            $arr = $summary;
        }
        if (isset($arr["province"])) {
            $this->mProvince = new location_node($arr["province"]);
        } else {
            $this->mProvince = new location_node($arr["provice"]);
        }
        $this->mCity = new location_node($arr["city"]);
        $this->mDistrict = new location_node($arr["district"]);
        $this->mTime = isset($summary["time"]) ? $summary["time"] : 0;
    }

    public function &province() {
        return $this->mProvince;
    }

    public function &city() {
        return $this->mCity;
    }

    public function &district() {
        return $this->mDistrict;
    }

    public function epoch_time() {
        return $this->mTime;
    }

    public function set_time($time) {
        $this->mTime = $time;
    }

    public function equals($o) {
        return ($this->province()->equals($o->province()) && $this->city()->equals($o->city()) && $this->district()->equals($o->district()));
    }

    public function is_same_city_with($o) {
        return ($this->province()->equals($o->province()) && (empty($o->city()->title()) || empty($this->city()->title()) || $this->city()->equals($o->city())));
    }

    public function pack_info() {
        return array("province" => $this->province()->pack_info(), 
                     "city" => $this->city()->pack_info(),
                     "district" => $this->district()->pack_info(),
                     "time" => $this->mTime,
                     "timestr" => date("Y-m-d H:i:s", $this->mTime),
                 );
    }
};










