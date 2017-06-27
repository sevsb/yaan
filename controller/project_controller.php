<?php
include_once(dirname(__FILE__) . "/../app/config.php");

class project_controller {
    
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $tpl = new tpl("index/mainheader", "admin/footer");
        //$project_types = db_settings::inst()->load('project_types');
        //$tpl->set('project_types', $project_types);
        $tpl->display("project/index");
    }
    
    

}













