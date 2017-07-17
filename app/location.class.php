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
    private $mCode = 0;

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
        if (isset($arr["adcode"])) {
            $this->mCode = $arr["adcode"];
        }
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

    public function code() {
        return $this->mCode;
    }

    public function set_time($time) {
        $this->mTime = $time;
    }

    private function title_equals($o) {
        return ($this->province()->equals($o->province()) && $this->city()->equals($o->city()) && $this->district()->equals($o->district()));
    }

    public function equals($o) {
        logging::d("Debug", "Comparing location: " . json_encode($this->pack_info()) . " vs " . json_encode($o->pack_info()));

        if ($this->code() != 0 && $o->code() != 0) {
            logging::d("Debug", "\tcompare code.");
            return ($this->code() == $o->code());
        }
        if ($this->code() == 0 && $o->code() == 0) {
            logging::d("Debug", "\tcompare title because neither has code.");
            return $this->title_equals($o);
        }

        if ($this->code() != 0) {
            if ($o->province()->code() != 0) {
                logging::d("Debug", "\tcompare code with item.code.");
                $ret = ($this->code() == $o->province()->code());
                $ret |= ($this->code() == $o->city()->code());
                $ret |= ($this->code() == $o->district()->code());
                return $ret;
            }
            logging::d("Debug", "\tcompare title because the other one has no item code.");
            return $this->title_equals($o);
        }
        logging::d("Debug", "\treverse compare.");
        return $o->equals($this);
    }

    public function is_same_city_with($o) {
        return $this->equals($o);
    }

    public function pack_info() {
        return array("province" => $this->province()->pack_info(), 
                     "city" => $this->city()->pack_info(),
                     "district" => $this->district()->pack_info(),
                     "time" => $this->mTime,
                     "timestr" => date("Y-m-d H:i:s", $this->mTime),
                     "adcode" => $this->code(),
                 );
    }
};










