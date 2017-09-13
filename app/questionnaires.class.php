<?php
include_once(dirname(__FILE__) . "/../config.php");

class questionnaires {
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
    public function pid() {
        return $this->summary("pid");
    }
    public function title() {
        return $this->summary("title");
    }
    public function notes() {
        return $this->summary("notes");
    }
    public function count() {
        return $this->summary("count", 0);
    }
    public function is_valid() {
        return !empty($this->summary);
    }

    public static function create($pid){
        $userid = get_session('user.id');
        $id = db_questionnaires::inst()->add_questionnaires(0,'问卷标题','', $userid);
        $questionnaires = db_questionnaires::inst()->get_questionnaires_by_id($id);
        //logging::d("createPJT", "muffininfos: $muffininfos");
        return new questionnaires($questionnaires);
    }

    public static function createPid($pid){
        $questionnaires = null;
        if (!empty($pid)) {
            $questionnaires = db_questionnaires::inst()->get_one_by_projectid($pid);
        }
        //         var_dump($questionnaires);
        if(empty($questionnaires)){
            $id = db_questionnaires::inst()->add_questionnaires(0,'','');
            $questionnaires = db_questionnaires::inst()->get_one_by_projectid($pid);
        }
        //logging::d("createPJT", "muffininfos: $muffininfos");
        return new questionnaires($questionnaires);
        
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
    
    public static function save_naire($id){
        
        $questionnaires = db_questionnaires::inst()->save_naire($id);
        if (!$questionnaires) {
            return false;
        }
        return $questionnaires;
    }
    
    public static function remove_naire($id){
        
        $questionnaires = db_questionnaires::inst()->remove_naire($id);
        if (!$questionnaires) {
            return false;
        }
        return $questionnaires;
    }

    public static function load_all() {
        $all_questionnaires = db_questionnaires::inst()->get_all_questionnaires();
        return $all_questionnaires;
    }
    
    public static function load_all_naires() {
        $result_array = [];
        $all_questionnaires = db_questionnaires::inst()->get_all_questionnaires();
        foreach ($all_questionnaires as $id => $questionnaire) {
            $result_array[$id] = new questionnaires($questionnaire);
        }
        return $result_array;
    }
    
    public static function load_by_id($id) {
        $questionnaires = db_questionnaires::inst()->get_questionnaires_by_id($id);
        return new questionnaires($questionnaires);
    }
    
    public static function get_cache_naire($userid) {
        $cache_naire = db_questionnaires::inst()->get_cache_naire($userid);
        logging::d('cache_naire',"cache_naire: $cache_naire");
        return empty($cache_naire) ? null : new questionnaires($cache_naire);
    }

    public function pack_info() {
        return array(
            "id" => $this->id(),
            "title" => $this->title(),
            "notes" => $this->notes()
        );
    }
}

?>
