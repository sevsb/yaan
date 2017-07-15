<?php
include_once(dirname(__FILE__) . "/../../config.php");

class system_controller {
    
    public function preaction($action) {
        login::assert();
    }

    public function settings_action() {
        $settings = settings::instance()->get_all();
        $tpl = new tpl("admin/header", "admin/footer");
        $tpl->set('settings', $settings);
        $tpl->display("admin/system/settings");
    }

    public function project_types_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $project_types = db_settings::inst()->load('project_types');
        $tpl->set('project_types', $project_types['value']);
        $tpl->display("admin/system/project_types");
    }
    
    public function new_project_type_ajax() {
        $title = get_request('title');
        logging::d("new_project_type_ajax", $title);
        $ret = settings::instance()->add_project_type($title);
        logging::d("add_project_type", $ret);
        return $ret ? 'success' : 'fail';
    }
    
    public function del_project_type_ajax() {
        $id = get_request('id');
        logging::d("del_project_type", $id);
        $ret = settings::instance()->del_project_type($id);
        logging::d("del_project_type", $ret);
        return $ret ? 'success' : 'fail';
    }
    
    public function update_project_types_ajax() {
        $types = get_request('types');
        $new_types = [];
        foreach ($types as $type) {
            $id = $type["id"];
            $title = $type["title"];
            $new_types[$id] = $title;
        }
        $new_types = implode(',', $new_types);
        return db_settings::inst()->save("project_types", $new_types) ? "success" : 'fail'; 
    }
    

    public function update_setting_ajax() {
        $key = get_request_assert("extra");
        $val = get_request_assert("value");
        $ret = settings::instance()->save($key, $val);
        return ($ret !== false) ? "success" : "fail|数据库操作失败，请稍后重试。";
    }

}













