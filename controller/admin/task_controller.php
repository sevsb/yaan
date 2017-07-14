<?php
include_once(dirname(__FILE__) . "/../../config.php");

class task_controller {
    
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $projectmuffinid = get_request("projectmuffinid");
        $project = projects::create($projectmuffinid);
        $project_title = $project->title();
        $tasks = tasks::load_tasks($projectmuffinid);
        $tpl->set('project_title', $project_title);
        $tpl->set('muffinid', $projectmuffinid);
        $tpl->set('tasks', $tasks);
        $tpl->display("admin/task/index");
    }
    
    public function new_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $muffinid = get_request("muffinid", 0);
        $taskid = get_request("taskid");
        
        $task = tasks::create($taskid);
        $all_projects = projects::load_all();
        $tpl->set('all_projects', $all_projects);
        $tpl->set('muffinid', $muffinid);
        $tpl->set('task', $task);
        $tpl->display("admin/task/new");
    }
    
    public function add_ajax() {
        $muffinid = get_request('muffinid');
        $title = get_request('title');
        $content = get_request('content');
        $address = get_request('address');
        $location = get_request('loc');
        
        logging::d("TASKADD", "muffinid: $muffinid");
        logging::d("TASKADD", "title: $title");
        logging::d("TASKADD", "content: $content");
        logging::d("TASKADD", "address: $address");
        logging::d("TASKADD", "location: $location");


        $result = tasks::add($muffinid, $title, $content, $address, $location);
        return $result ? 'success' : 'fail';
    }
    
    public function modify_ajax() {
        $taskid = get_request('taskid');
        $muffinid = get_request('muffinid');
        $title = get_request('title');
        $content = get_request('content');
        $address = get_request('address');
        $location = get_request('loc');
        
        logging::d("TASKADD", "muffinid: $muffinid");
        logging::d("TASKADD", "title: $title");
        logging::d("TASKADD", "content: $content");
        logging::d("TASKADD", "address: $address");
        logging::d("TASKADD", "location: $location");


        $result = tasks::modify($taskid, $muffinid, $title, $content, $address, $location);
        return $result ? 'success' : 'fail';
    }
    
    public function del_ajax() {
        $del_id = get_request('del_id');
        logging::d("TASKDEL", "del_id: $del_id");
        $result = tasks::del($del_id);
        return $result ? 'success' : 'fail';
    }
    
    

}













