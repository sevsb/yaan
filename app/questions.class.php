<?php
include_once(dirname(__FILE__) . "/../config.php");

class questions {
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
    public function nid() {
        return $this->summary("nid");
    }
    public function title() {
        return $this->summary("title");
    }
    public function type() {
        return $this->summary("type");
    }
    public function notes() {
        return $this->summary("notes");
    }
    public function value() {
        return $this->summary("value", 0);
    }
    public function is_valid() {
        return !empty($this->summary);
    }

    public static function create($nid, $title, $type, $notes, $value){
        $question = null;
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."nid:$nid, title:$title notes:$notes \r\n", FILE_APPEND);
        
        if (!empty($nid)) {
            $id = db_question::inst()->add_questions($nid, $title, $type, $notes, $value);
            $question = db_question::inst()->get_questions_by_id($id);
        }
        //logging::d("createPJT", "muffininfos: $muffininfos");
        return new questions($question);
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
    
    public static function modify($id, $title, $notes){
        
        $questionnaires = db_questionnaires::inst()->modify_questionnaires($id, $title, $notes);
        if (!$questionnaires) {
            return false;
        }
        return $questionnaires;
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
