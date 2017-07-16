<?php
include_once(dirname(__FILE__) . "/../config.php");

class projects {
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
    public function title() {
        return $this->summary("title");
    }
    public function muffinid() {
        return $this->summary("muffinid");
    }
    public function project_id() {
        return $this->summary("project_id");
    }
    public function type() {
        return $this->summary("type");
    }
    public function description() {
        return $this->summary("description");
    }
    public function cover_url() {
        $url = rtrim(UPLOAD_URL, "/") . "/" . $this->summary["cover"];
        return $url;
    }
    public function cover_thumbnail_url() {
        $url = mkUploadThumbnail($this->summary["cover"], 0, 500);
        return $url;
    }
    public function text() {
        return $this->summary("text");
    }
    public function limit_time() {
        $lmt_time = $this->summary('limit_time');
        $ret = date('Y-m-d', $lmt_time);
        return $ret;
    }
    public function deadline() {
        return $this->limit_time();
        // $deadline = new DateTime("@" . $this->summary('limit_time'));
        // $deadline = $deadline->format("Y-m-d");
        // return $deadline;
    }
    public function paperfile() {
        return $this->summary("paperfile");
    }
    public function paperfile_url() {
        return rtrim(FILEUPLOAD_URL, "/") . "/" . $this->summary["paperfile"];
    }
    public function status() {
        return $this->summary("status");
    }
    public function reward() {
        return $this->summary("reward");
    }
    public function stars() {
        return $this->summary("stars");
    }
    public function is_valid() {
        return !empty($this->summary);
    }

    public static function create($muffinid){
        if (!empty($muffinid)) {
            $muffininfos = db_muffininfos::inst()->get_one_muffininfos($muffinid);
            //logging::d("createPJT", "muffininfos: $muffininfos");
            return new projects($muffininfos);
        }
        return new projects(null);

    }

    public static function del($id){
        
        $wanna_del_tasks = db_muffins::inst()->load_tasks_by_project($id);
        $wanna_del_tasks = array_keys($wanna_del_tasks);
        array_push($wanna_del_tasks, $id);
        if(!empty($wanna_del_tasks)){
            foreach ($wanna_del_tasks as $k) {
                $ret4 = db_muffins::inst()->del($k);
                $ret3 = db_muffininfos::inst()->del($k);
            }
        }else{
            $ret4 = true;
            $ret3 = true;
        }
        return $ret3 && $ret4;
    }
    
    public static function add($project_id, $title, $type, $description, $maintext, $cover, $limit_time, $paperfile){
        //begin_transaction();
        
        $pid = null;
        $mtitle = null;
        $face = null;
        
        $muffin_id = db_muffins::inst()->add($pid, $mtitle, $face);
        logging::d("muffin_id"," muffin_id : $muffin_id");
        if (!$muffin_id) {
            return false;
        }
        $muffininfo_ret = db_muffininfos::inst()->add_project($project_id, $muffin_id, $title, $type, $description, $maintext, $cover, $limit_time, $paperfile);
        if (!$muffininfo_ret) {
            return false;
        }
        //commit();
        return $muffin_id;
    }
    
    public static function modify($muffinid, $project_id, $title, $type, $description, $maintext, $cover, $limit_time, $paperfile){
        
        $muffininfo_ret = db_muffininfos::inst()->modify_project($muffinid, $project_id, $muffin_id, $title, $type, $description, $maintext, $cover, $limit_time, $paperfile);
        if (!$muffininfo_ret) {
            return false;
        }
        return $muffininfo_ret;
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

    public function pack_info() {
        return array(
            "id" => $this->id(),
            "projectid" => $this->project_id(),
            "type" => $this->type(),
            "title" => $this->title(),
            "text" => $this->text(),
            "description" => $this->description(),
            "word" => $this->paperfile_url(),
            "status" => $this->status(),
            "deadline" => $this->deadline(),
            "cover" => $this->cover_url(),
        );
    }
}

?>
