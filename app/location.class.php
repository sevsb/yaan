<?php

include_once(dirname(__FILE__) . "/lcs.class.php");

class location_node {
    private $mCode = 0;
    private $mTitle = 0;
    // private $lcs = null;

    public function location_node($summary) {
        $this->mCode = $summary["code"];
        $this->mTitle = $summary["title"];
        // $this->lcs = new LCS();
    }

    public function &code() {
        return $this->mCode;
    }

    public function &title() {
        return $this->mTitle;
    }

    public function equals($o) {
        return ($this->code() == $o->code());
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

    public function location($summary) {
        $arr = json_decode($summary, true);
        $this->mProvince = new location_node($arr["province"]);
        $this->mCity = new location_node($arr["city"]);
        $this->mDistrict = new location_node($arr["district"]);
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

    public function equals($o) {
        return ($this->province()->equals($o->province()) && $this->city()->equals($o->city()) && $this->district()->equals($o->district()));
    }

    public function pack_info() {
        return array("province" => $this->province()->pack_info(), 
                     "city" => $this->city()->pack_info(),
                     "district" => $this->district()->pack_info());
    }
};










