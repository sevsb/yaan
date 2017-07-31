<?php
include_once(dirname(__FILE__) . "/../../config.php");

class questionnaire_controller {
    
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
        $pid = get_request("muffinid", 0);
        $taskid = get_request("taskid");
        
        $task = tasks::create($taskid);
        $all_projects = projects::load_all();
        $tpl->set('all_projects', $all_projects);
        $tpl->set('muffinid', $muffinid);
        $tpl->set('task', $task);
        $tpl->display("admin/questionnaire/edit");
    }
    
    public function edit_action() {
        $tpl = new tpl();
//         $tpl = new tpl("admin/header", "admin/footer");
        $pid = get_request("projectmuffinid", 0);
        
//         $task = tasks::create($taskid);
        $questionnaire = questionnaires::create($pid);
        $tpl->set('questionnaire', $questionnaire);
        
        $tpl->display("admin/questionnaire/edit");
    }
    
    public function editTitle_action() {
        //         $tpl = new tpl("admin/header", "admin/footer"); file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."upload token:".$token.", key_id:$key_id, date:$data, uas_serial_id:$uas_serial_id, planeModel_id:$planeModel_id, is_key:$is_key, key_data:$key_data \r\n", FILE_APPEND);
        
        $id = get_request("id", 0);
        
        if(!empty($id)){
            $title = get_request("title", "");
            $notes = get_request("notes", "");
            //         $task = tasks::create($taskid);
            $questionnaire = questionnaires::modify($id,$title,$notes);
        }
        
    }
    
    public function addQuestions_action() {
        
        $nid = get_request("nid", 0);
        
        if(!empty($nid)){
            $title = get_request("title", "");
            $notes = get_request("notes", "");
            $type = get_request("type", "");
            $value= get_request("value", "");
            
            $id = db_question::inst()->add_questions($nid, $title, $type, $notes, $value);
//             $questions = db_question::inst()->get_questions_by_id($id);
            $titles = get_request("titles");
            $values = get_request("values");
            file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."titles:".count($titles).",values,".count($values)."\r\n", FILE_APPEND);
            $i = 0;
            foreach($titles as $option ){
                $questionoptions[] = questionoptions::create($id, $option, $values[$i], $i);
                $i++;
            }
        }
        
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
        return $ret ? array('ret' => 'success') : array('status' => 'fail');
    }
    
    
    
}