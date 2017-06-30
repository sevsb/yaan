<?php
include_once(dirname(__FILE__) . "/../app/config.php");

class task_controller {
    
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $tpl = new tpl("index/mainheader", "admin/footer");
        $muffinid = get_request("muffinid");
        $tasks = tasks::load_tasks($muffinid);
        $tpl->set('muffinid', $muffinid);
        $tpl->set('tasks', $tasks);
        $tpl->display("task/index");
    }
    
    public function new_action() {
        $tpl = new tpl("index/mainheader", "admin/footer");
        //$project_types = db_settings::inst()->load('project_types');
        //$project_types = $project_types['value'];
        //$project_types = explode(',', $project_types);
        //$tpl->set('project_types', $project_types);
        $tpl->display("task/new");
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
    
    

}













