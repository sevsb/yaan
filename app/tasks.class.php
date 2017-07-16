<?php
include_once(dirname(__FILE__) . "/../config.php");

class tasks {
    const STATUS_PENDING = 0;
    const STATUS_PASS = 1;
    const STATUS_REJECT = 2;

    private $summary = array();
    private $mProject = null;
    
    public function __construct($data) {
        $this->summary = $data;
    }
    
    private function summary($key, $def = "") {
        if (isset($this->summary[$key])) {
            return $this->summary[$key];
        }
        return $def;
    }
    
    public static function create($muffinid){
        if (!empty($muffinid)) {
            $muffininfos = db_muffininfos::inst()->get_one_muffininfos($muffinid);
            //logging::d("createPJT", "muffininfos: $muffininfos");
            return new tasks($muffininfos);
        }
        return new tasks(null);

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

    public function location_obj() {
        $loc = new location($this->location());
        return $loc;
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
    
    public function project() {
        if ($this->mProject === null) {
            $muffininfos = db_muffininfos::inst()->get_all_cached();
            $muffins = db_muffins::inst()->get_all_cached();
            $mid = $this->muffinid();
            $projectid = $muffins[$mid]["pid"];
            foreach ($muffininfos as $info) {
                if ($info["muffinid"] == $projectid) {
                    $this->mProject = new projects($info);
                    break;
                }
            }
        }
        return $this->mProject;
    }

    public function from_projectid() {
        $muffinid = $this->summary('muffinid');
        $ret = db_muffins::inst()->get_project_id($muffinid);
        return $ret;
    }

    public function is_valid() {
        return !empty($this->summary);
    }
    
    public function pack_info() {
        return array(
            "id" => $this->id(),
            "title" => $this->title(),
            "address" => $this->address(),
            "content" => $this->content(),
            "status" => $this->status(),
            "location" => $this->location_obj()->pack_info(),
            "project" => $this->project()->pack_info(),
        );
    }

    public static function add($muffinid, $title, $content, $address, $location){

        $pid = $muffinid;
        $mtitle = null;
        $face = null;
        
        $new_muffin_id = db_muffins::inst()->add($pid, $mtitle, $face);
        logging::d("new_muffin_id"," new_muffin_id : $new_muffin_id");
        if (!$new_muffin_id) {
            return false;
        }
        $paperid = db_papers::inst()->add_word_paper($title);
        if ($paperid === false) {
            $paperid = 0;
        }
        $task_ret = db_muffininfos::inst()->add_task($new_muffin_id, $title, $content, $address, $location, $paperid);
        if (!$task_ret) {
            return false;
        }
        //commit();
        return $task_ret;
    }
    
    public static function modify($taskid, $muffinid, $title, $content, $address, $location){

        $pid = $muffinid;
        $mtitle = null;
        $face = null;
        
        $ret1 = db_muffins::inst()->modify($taskid, $pid, $mtitle, $face);
        logging::d("new_muffin_id"," new_muffin_id : $new_muffin_id");
        if (!$ret1) {
            return false;
        }
        $ret2 = db_muffininfos::inst()->modify_task($taskid, $title, $content, $address, $location);
        if (!$ret2) {
            return false;
        }
        //commit();
        return $ret2;
    }

    public static function del($id) {
        $ret1 = db_muffins::inst()->del($id);
        $ret2 = db_muffininfos::inst()->del($id);
        return $ret1 && $ret2;
    }
    
    public static function load_all() {
        $all_muffins = db_muffins::inst()->get_all_cached();
        $all_mufininfos = db_muffininfos::inst()->get_all_cached();
        $result_array = [];

        foreach ($all_mufininfos as $id => $info) {
            $muffinid = $info["muffinid"];
            $pid = $all_muffins[$muffinid]["pid"];
            if ($pid == 0 || empty($pid)) {
                continue;
            }
            $result_array[$id] = new tasks($info);
        }
        return $result_array;
    }
    
    public static function load_tasks($projectid) {
        $all_muffins = db_muffins::inst()->get_all_muffins();
        $all_muffininfos = db_muffininfos::inst()->get_all_muffininfos();
        $result_array = [];
        
        foreach ($all_muffininfos as $id => $info) {
            $infoid = $info['muffinid'];
            foreach ($all_muffins as $mufid => $muffin) {
                $pid = $muffin['pid'];
                if ($mufid == $infoid && $pid == $projectid) {
                    $result_array[$id] = new tasks($info);
                }
            }       
        }
        return $result_array;
    }

    public static function load_user_tasks($uid) {
        $all_muffininfos = db_muffininfos::inst()->get_all_muffininfos();
        $arr = array();
        foreach ($all_muffininfos as $id => $info) {
            if ($info["wechat_userid"] == $uid) {
                $arr [$id]= new tasks($info);
            }
        }
        return $arr;
    }

    public static function load_around($loc) {
        $location = new location($loc);
        $tasks = self::load_all();
        $arr = array();
        foreach ($tasks as $id => $task) {
            if (!empty($task->wechat_userid())) {
                continue;
            }
            if ($task->status() != self::STATUS_PENDING) {
                continue;
            }
            // logging::d("Debug", "compareing " . json_encode($task->location_obj()->pack_info()) . " with " . json_encode($location->pack_info()));
            if ($task->location_obj()->is_same_city_with($location)) {
                // logging::d("Debug", "SAME!");
                $arr[$id] = $task;
            }
        }
        return $arr;
    }
}

?>
