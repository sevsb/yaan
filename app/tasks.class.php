<?php
include_once(dirname(__FILE__) . "/config.php");

class tasks {
    private $summary = array();
    
    public function __construct($data) {
        $this->summary = $data;
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
    public function muffinid() {
        return $this->summary("muffinid");
    }
    public function wechat_userid() {
        return $this->summary("wechat_userid");
    }
    public function title() {
        return $this->summary("title");
    }
    public function location() {
        return $this->summary("location");
    }
    public function address() {
        return $this->summary("address");
    }
    public function content() {
        return $this->summary("content");
    }
    public function answers() {
        return $this->summary("answers");
    }
    public function status() {
        return $this->summary("status");
    }
    
    
    public static function add($muffinid, $title, $content, $address, $location){

        $pid = $muffinid;
        $mtitle = null;
        $face = null;
        
        $new_muffin_id = db_muffins::inst()->add($pid, $mtitle, $face);
        logging::d("muffin_id"," muffin_id : $muffin_id");
        if (!$new_muffin_id) {
            return false;
        }
        $task_ret = db_sheets::inst()->add($new_muffin_id, $title, $content, $address, $location);
        if (!$task_ret) {
            return false;
        }
        //commit();
        return $task_ret;
    }

    public function del($id) {
        $ret1 = db_muffins::inst()->del($id);
        $ret2 = db_sheets::inst()->del($id);
        return $ret1 && $ret2;
    }
    
    public static function load_all() {
        $all_muffins = db_muffins::inst()->get_all_muffins();
        $all_mufininfos = db_muffininfos::inst()->get_all_muffininfos();
        $result_array = [];
        foreach ($all_muffins as $id => $muffin) {
            $pid = $muffin['pid'];
            if ($pid == 0 || $pid = '') {
                foreach ($all_mufininfos as $infoid => $muffininfo) {
                    $muffinid = $muffininfo['muffinid'];
                    if ($muffinid == $id) {
                        $result_array[$id] = new projects($muffininfo);
                    }
                }
            }
        }
        return $result_array;
    }
    
    public static function load_tasks($projectid) {
        $all_muffins = db_muffins::inst()->get_all_muffins();
        $all_sheets = db_sheets::inst()->get_all_sheets();
        $result_array = [];
        
        foreach ($all_sheets as $id => $sheet) {
            $sheetid = $sheet['muffinid'];
            foreach ($all_muffins as $mufid => $muffin) {
                $pid = $muffin['pid'];
                if ($mufid == $sheetid && $pid == $projectid) {
                    $result_array[$id] = new tasks($sheet);
                }
            }       
        }
        return $result_array;
    }

}

?>