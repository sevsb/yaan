<?php
include_once(dirname(__FILE__) . "/../../config.php");

class task_controller {
    
    public function preaction($action) {
        $_string = $_SERVER['QUERY_STRING'];
        if ($_string != 'admin/task/task_reminder') {
            login::assert();
        }
    }
    public function load_all_broadcast_areas_action() {
        $all_broadcast_areas = array();
        $tasks = tasks::load_all();
        foreach ($tasks as $task) {
            $id = $task->id();
            $broadcast_areas = $task->broadcast_area();
            $all_broadcast_areas[$id] = $broadcast_areas;
        }
        echo json_encode($all_broadcast_areas);
    }

    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $projectmuffinid = get_request("projectmuffinid");
        $flag = get_request('flag', 0);
        $project = projects::create($projectmuffinid);
        $project_title = $project->title();
        $tasks = tasks::load_tasks($projectmuffinid);
        $wechatusers = wechatuser::load_all();
        $tpl->set('flag', $flag);
        $tpl->set('wechatusers', $wechatusers);
        $tpl->set('project_title', $project_title);
        $tpl->set('muffinid', $projectmuffinid);
        $tpl->set('tasks', $tasks);
        $tpl->display("admin/task/index");
    }
    
    public function import_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $muffinid = get_request("muffinid");
        $tpl->set('muffinid', $muffinid);
        $baiduak = settings::instance()->load("BAIDU_MAP_AK");
        $tpl->set('baiduak', $baiduak);
        $tpl->display("admin/task/import");
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

    public function process_import_file_action() {
        //$muffinid = get_request('muffinid');
        $import_file = get_request('import_file');

        $import_file_name = null;

        if (!empty($import_file)) {
            if (substr($import_file, 0, 5) == "data:") {
                $ret2 = uploadFileViaFileReader($import_file);
                logging::e("uploadFile-ret", $ret2);
                $ret2 = explode("|", $ret2);
                if ($ret2[0] == 'fail') {
                    return false;
                }
                $import_file_name = $ret2[1];
            }
        }
        $result_data = tasks::process_data_from_importfile($import_file_name);
        
        //return $result ? 'success' : 'fail';
        echo json_encode($result_data);
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
        
        return $result ? array('ret' => 'success', 'info' => $result) : array('status' => 'fail');
    }

    public function do_import_ajax() {
        $muffinid = get_request('muffinid');
        $task_list = get_request('task_list');
        logging::d("TASKIMPORT", "muffinid: $muffinid");
        //$result = true;
        foreach ($task_list as $task) {
            if (empty($task)) {
                continue;
            }
            $title = $task['title'];
            $address = $task['address'];
            $location = $task['location'];
            $content = $task['content'];
            logging::d("TASKIMPORT", "title: $title");
            logging::d("TASKIMPORT", "address: $address");
            logging::d("TASKIMPORT", "location: $location");
            logging::d("TASKIMPORT", "content: $content");
            $result = tasks::add($muffinid, $title, $content, $address, $location);
            logging::d("TASKIMPORT", "result: $result");
        }
        
        return $result ? array('ret' => 'success', 'info' => $result) : array('status' => 'fail');
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
        $userid == null ? $status = tasks::STATUS_PENDING : $status = tasks::STATUS_ASSIGNED;
        $ret = db_muffininfos::inst()->update_wechat_userid($taskid, $userid, $status);
        if ($userid != null) {
            $task = tasks::create_by_id($taskid);
            $task_title = $task->title();
            $project_title = $task->project()->title();
            $project_limit_time = $task->project()->limit_time();
            $wechatuser = wechatuser::create($userid);
            $openid = $wechatuser->openid();
            $data_array = array(
                "touser" => $openid,
                "template_id" => "ANRlAUP0QhXMBatijUM_Ez5ouM77JpAgGM4AubwFdBw",
                "url" => "http://yaan.rendajinrong.com/?action=wechat.index&debugroute=1",
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
                        "value" =>"您为此任务负责人",
                        "color" => "#173177"
                    ),
                    "keyword4" => array(
                        "value" =>"$project_limit_time",
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" =>"请尽快落实任务！点击跳转到我的任务",
                        "color" => "#173177"
            )));
            $result = wxApi::inst()->send_template_message($data_array);
            logging::d("SENDTEMMSG",$result);
        }
        return $ret ? array('ret' => 'success') : array('status' => 'fail');
    }
    
    public function update_broadcast_area_ajax() {
        $taskid = get_request('taskid');
        $broadcast_loctions = get_request('broadcast_loctions');
        logging::d("taskid", "taskid: $taskid");
        logging::d("broadcast_loctions", "broadcast_loctions: $broadcast_loctions");
        //return true;
        $result = tasks::update_broadcast_area($taskid, $broadcast_loctions);
        return $result ? 'success' : 'fail';
    }
    
    public function server_check_action() {
        $taskid = get_request('taskid');
        $broadcast_loctions = get_request('broadcast_loctions');
        logging::d("taskid", "taskid: $taskid");
        logging::d("broadcast_loctions", "broadcast_loctions: $broadcast_loctions");
        //return true;
        $result = tasks::update_broadcast_area($taskid, $broadcast_loctions);
        return $result ? 'success' : 'fail';
    }
    
    public function task_reminder_action() {
        //var_dump($_SESSION);
        //var_dump($_SERVER);
        $wechatuser_list = wechatuser::load_all();
        foreach ($wechatuser_list as $wechatuser) {
            $user_tasks = $wechatuser->running_tasks();
            $openid = $wechatuser->openid();
            foreach ($user_tasks as $user_task) {
                $task_limit_time_stamp = $user_task->project()->limit_time_stamp();
                $time = time();
                $time_diff = $task_limit_time_stamp - $time;
                if ($time_diff <= 3600 * 24 * 3) {
                    var_dump($task_limit_time_stamp);
                    var_dump($time);
                    var_dump($time_diff);
                    $title = $user_task->title();
                    $limit_time = $user_task->project()->limit_time();
                    $left_days = round($time_diff / (3600 * 24));
                    $data_array = array(
                        "touser" => $openid,
                        "template_id" => "2ff21t6_3QPDqGh9VGpTfKk2p62YCXs2S7km_F_LAzw",
                        "url" => "http://yaan.rendajinrong.com/?action=wechat.index&debugroute=1",
                        "miniprogram" => array(
                            "appid" => "",
                            "pagepath" => ""),
                        "data" => array(
                            "first" => array(
                                "value" =>"您好，您的任务即将到期！",
                                "color" => "#173177"
                            ),
                            "keyword1" => array(
                                "value" =>"$title",
                                "color" => "#173177"
                            ),
                            "keyword2" => array(
                                "value" =>"略",
                                "color" => "#173177"
                            ),
                            "keyword3" => array(
                                "value" =>"$limit_time",
                                "color" => "#173177"
                            ),
                            "keyword4" => array(
                                "value" =>"$left_days 天",
                                "color" => "#173177"
                            ),
                            "remark" => array(
                                "value" =>"请按时完成任务！",
                                "color" => "#173177"
                    )));
                    $result = wxApi::inst()->send_template_message($data_array);
                    var_dump($data_array);
                    logging::d("SENDTEMMSG data_array", $data_array);
                    logging::d("SENDTEMMSG result", $result);
                }
            }
        }
        return;
    }   

}













