<?php
include_once(dirname(__FILE__) . "/../../config.php");

class project_controller {
    
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $all_projects = projects::load_all();
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
        
        $ret = projects::del($del_id);
        //return $ret;
        return $ret ? 'success' : 'fail';
    }

}













