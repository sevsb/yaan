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
        $wechatusers = wechatuser::load_all();
        $tpl->set('wechatusers', $wechatusers);
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
    
    public function assign_ajax() {
        $userid = get_request('userid');
        $taskid = get_request('taskid');
        logging::d("Debug", $userid);
        logging::d("Debug", $taskid);
        $userid == null ? $status = 0 : $status = 1;
        $ret = db_muffininfos::inst()->update_wechat_userid($taskid, $userid, $status);
        if ($userid != null) {
            $task = tasks::create($taskid);
            $task_title = $task->title();
            $project_title = $task->project()->title();
            $project_limit_time = $task->project()->limit_time();
            $wechatuser = wechatuser::create($userid);
            $openid = $wechatuser->openid();
            $data_array = array(
                "touser" => $openid,
                "template_id" => "ANRlAUP0QhXMBatijUM_Ez5ouM77JpAgGM4AubwFdBw",
                "url" => "http://yaan.rendajinrong.com/?wechat/index/home",
                "miniprogram" => array(
                    "appid" => "",
                    "pagepath" => ""),
                "data" => array(
                    "first" => array(
                        "value" =>"你好，你被分配到一个新的任务！",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" =>"$project_title",
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" =>"$task_title",
                        "color" => "#173177"
                    ),
                    "keyword3" => array(
                        "value" =>"xytest",
                        "color" => "#173177"
                    ),
                    "keyword4" => array(
                        "value" =>"$project_limit_time",
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" =>"请尽快落实任务！",
                        "color" => "#173177"
            )));
            logging::d("SENDDATA",json_encode($data_array));
            $result = wxApi::inst()->send_template_message($data_array);
            logging::d("SENDTEMMSG",$result);
        }
        return $ret ? array('ret' => 'success') : array('status' => 'fail');
    }
    
    

}













