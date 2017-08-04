<?php
include_once(dirname(__FILE__) . "/../../config.php");

class project_controller {
    
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $flag = get_request('flag', 0);
        $all_projects = projects::load_all();
        $tpl->set('flag', $flag);
        $tpl->set('all_projects', $all_projects);
        $tpl->display("admin/project/index");
    }
    
    public function new_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $projectmuffinid = get_request('projectmuffinid');
        $readonly = get_request('readonly');
        $project = projects::create($projectmuffinid);
        $tpl->set('muffinid', $projectmuffinid);
        $tpl->set('readonly', $readonly);
        $tpl->set('project', $project);
        $project_types = db_settings::inst()->load('project_types');
        $project_types = $project_types['value'];
        $project_types = explode(',', $project_types);
        $tpl->set('project_types', $project_types);
        $tpl->display("admin/project/new");
    }
    
    
    public function view_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $projectmuffinid = get_request('projectmuffinid');
        $project = projects::create($projectmuffinid);
        $tpl->set('muffinid', $projectmuffinid);
        $tpl->set('readonly', $readonly);
        $tpl->set('project', $project);
        $project_types = db_settings::inst()->load('project_types');
        $project_types = $project_types['value'];
        $project_types = explode(',', $project_types);
        $tpl->set('project_types', $project_types);
        $tpl->display("admin/project/view");
    }
    
    public function add_ajax() {
        $project_id = get_request('project_id');
        $title = get_request('title');
        $description = get_request('description');
        $maintext = get_request('maintext');
        $cover = get_request('cover');
        $limit_time = get_request('limit_time');
        $paperfile = get_request('paperfile');
        $type = get_request('type');

        $covername = null;
        $paperfilename = null;
        
        if (substr($cover, 0, 5) == "data:") {
            $ret = uploadImageViaFileReader($cover, function($covername) {
                return $covername;
            });
            logging::e("uploadImage-ret", $ret);
            if (strncmp($ret, "fail|", 5) == 0) {
                return $ret;
            }
            $covername = $ret;
        }else {
            $covername = explode('/', $cover);
            $covername = end($covername);
        }
        if (!empty($paperfile)) {
            $ret2 = uploadFileViaFileReader($paperfile);
            logging::e("uploadFile-ret", $ret2);
            $ret2 = explode("|", $ret2);
            if ($ret2[0] == 'fail') {
                return false;
            }
            $paperfilename = $ret2[1];
        }
        $result = projects::add($project_id, $title, $type, $description, $maintext, $covername, $limit_time, $paperfilename);
        return $result ? array('ret' => "success",'info' => $result ) : array("ret"=>"fail", "info" => 'failed!') ;
    }
    
    public function modify_ajax() {
        $muffinid = get_request('muffinid');
        $project_id = get_request('project_id');
        $title = get_request('title');
        $description = get_request('description');
        $maintext = get_request('maintext');
        $cover = get_request('cover');
        $limit_time = get_request('limit_time');
        $paperfile = get_request('paperfile');
        $type = get_request('type');

        $covername = null;
        $paperfilename = null;
        
        if (substr($cover, 0, 5) == "data:") {
            $ret = uploadImageViaFileReader($cover, function($covername) {
                return $covername;
            });
            logging::e("uploadImage-ret", $ret);
            if (strncmp($ret, "fail|", 5) == 0) {
                return $ret;
            }
            $covername = $ret;
        }else {
            $covername = explode('/', $cover);
            $covername = end($covername);
        }
        if (!empty($paperfile)) {
            if (substr($paperfile, 0, 5) == "data:") {
                $ret2 = uploadFileViaFileReader($paperfile);
                logging::e("uploadFile-ret", $ret2);
                $ret2 = explode("|", $ret2);
                if ($ret2[0] == 'fail') {
                    return false;
                }
                $paperfilename = $ret2[1];
            }else {
                $paperfilename = explode('/', $paperfile);
                $paperfilename = end($paperfilename);
            }
        }
      
        $result = projects::modify($muffinid, $project_id, $title, $type, $description, $maintext, $covername, $limit_time, $paperfilename);
        return $result ? 'success' : 'fail';
    }
    
    public function del_ajax(){
        $del_id = get_request('del_id');
        
        $wanna_del_tasks = db_muffins::inst()->load_tasks_by_project($del_id);
        $wanna_del_tasks = array_keys($wanna_del_tasks);
        logging::d('wanna_del_tasks', json_encode($wanna_del_tasks));
        //return;
        $ret = projects::del($del_id);
        $ret2 = true;
        if (!empty($wanna_del_tasks) && is_array($wanna_del_tasks)) {
            foreach ($wanna_del_tasks as $tid) {
                $ret2 &= tasks::del($tid);
            }
        }
        return $ret && $ret2 ? 'success' : 'fail';
    }
    
    public function update_status_ajax(){
        $muffinid = get_request('muffinid');
        $status = get_request('sid');
        
        $ret = projects::update_status($muffinid, $status);
        return $ret ? 'success' : 'fail';
    }

}













