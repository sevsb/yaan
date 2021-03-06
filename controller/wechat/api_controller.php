<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");
include_once(dirname(__FILE__) . "/../../app/location.class.php");

class api_controller {

    private function pack_tasks_info($user, $loc) {
        $loc = new location($loc);
        $infos = db_muffininfos::inst()->get_all_muffininfos();
        $res = array();
        foreach ($infos as $info) {
            if ($info["wechat_userid"] == $user["id"]) {
                $res []= $info;
            }

            if (empty($info["location"])) {
                continue;
            }
            $location = new location($info["location"]);
            if ($location->equals($loc)) {
                $res []= $info;
            }
        }
        $muffins = db_muffins::inst()->get_all_muffins();
        foreach ($res as $k => $info) {
            $mid = $info["muffinid"];
            $muffin = $muffins[$mid];
            $pid = $muffin["pid"];
            $muffin = $muffins[$pid];
            foreach ($infos as $inf) {
                if ($inf["muffinid"] == $muffin["id"]) {
                    $res[$k]["project"] = $inf;
                    break;
                }
            }
        }

        $tasks = array();
        foreach ($res as $k => $info) {
            $project = $info["project"];
            if ($project["limit_time"] < time()) {
                // continue;
            }

            $acceptable = empty($info["wechat_userid"]) ? true : false;
            $accepted = ($user["id"] == $info["wechat_userid"]) ? true : false;
            $deadline = new DateTime("@" . $project["limit_time"]);
            $tasks [] = array(
                "id" => $info["id"],
                "projectid" => $project["project_id"],
                "type" => $project["type"],
                "project_title" => $project["title"],
                "task_title" => $info["title"],
                "cover" => UPLOAD_URL . "/" . $project["cover"],
                "text" => $project["text"],
                "deadline" => $deadline->format("Y-m-d"),
                "word" => FILEUPLOAD_URL . "/" . $project["paperfile"],
                "status" => $project["status"],
                "address" => $info["address"],
                "content" => $info["content"],
                "acceptable" => $acceptable,
                "accepted" => $accepted,
            );
        }
        // logging::d("Debug", $tasks);
        echo json_encode($tasks);
    }


    private function update_user_location() {
        $updated = get_session("locupdated", null);
        if ($updated == null) {
            $user = get_session_assert("user");
            $loc = get_request_assert("loc");
            $location = new location($loc);
            $wu = wechatuser::create($user["id"]);
            if ($wu == null) {
                return;
            }
            $wu->update_location($location);
            $_SESSION["locupdated"] = true;
        }
    }

    public function tasks_action() {
        $user = get_session_assert("user");
        $loc = get_request_assert("loc");
        $_SESSION["temp.wechatapi.location"] = $loc;
        $this->update_user_location();
        return $this->pack_tasks_info($user, $loc);
    }

    public function mytasks_action() {
        $user = get_session_assert("user");
        $weuser = wechatuser::create($user["id"]);
        echo json_encode(array("op" => "userinfo", "data" => $weuser->pack_info()));
    }

    public function accept_action() {
        $task = get_request_assert("task");
        $user = get_session_assert("user");
        logging::d("Debug", $task);
        logging::d("Debug", $user);
        $ret = db_muffininfos::inst()->update_wechat_userid($task, $user["id"], tasks::STATUS_ASSIGNED);
        if ($ret) {
            $loc = get_session_assert("temp.wechatapi.location");
            $tasks = tasks::load_around($loc);
            $data = array();
            foreach ($tasks as $task) {
                $data []= $task->pack_info();
            }
            echo json_encode(array("op" => "taskaround", "data" => $data));
        }
    }

    public function taskaround_action() {
        $user = get_session_assert("user");
        $loc = get_request_assert("loc");
        $_SESSION["temp.wechatapi.location"] = $loc;
        $tasks = tasks::load_around($loc);
        $data = array();
        foreach ($tasks as $task) {
            $data []= $task->pack_info();
        }
        echo json_encode(array("op" => "taskaround", "data" => $data));
    }
    
    public function update_answer_action() {
        $taskid = get_request("taskid");
        $question_id = get_request("question_id");
        $qtype = get_request("qtype");
        $value = get_request("value");
        
        $task = tasks::create_by_id($taskid);
        $answerid = $task->answerid();
        
        logging::d("taskid", $taskid);
        logging::d("question_id", $question_id);
        logging::d("qtype", $qtype);
        logging::d("value", $value);

        $answer = answer::load($answerid);
        $answer_list = $answer->content();
        $reply = json_decode($answer->get_reply());
        
        //logging::d('answer_list: ', $answer_list);
        $answer_list = json_decode($answer_list);

        if (empty($answer_list)) {
            $answer_list = new stdClass();
            if ( $qtype == 'check') {
                $value = explode(",", $value);
            }
            $obj = new stdClass();
            $obj->id = $question_id;
            $obj->value = $value;
            $answer_list->$question_id = $obj;
        }else{
            if ($qtype == 'check') {
                $value = explode(",", $value);
            }
            if (empty($answer_list->$question_id)) {
                $answer_list->$question_id = new stdClass;
            }
            $answer_list->$question_id->id = $question_id;
            $answer_list->$question_id->value = $value;
            logging::d('UPDATE_ANS', "value: " . $value);
            logging::d('UPDATE_ANS', "answer_list value: " . $answer_list->$question_id->value);
        }
        $ret = db_answers::inst()->update_answser_content($answerid, json_encode($answer_list));
        return $this->get_answer_by_taskid_action();
    }
   
    
    public function get_answer_by_taskid_action() {

        $taskid = get_request("taskid");
        $task = tasks::create_by_id($taskid);
        $answerid = $task->answerid();
        $paperid = $task->project()->paperid();
        $questionnaire = questionnaires::load_by_id($paperid);
        $all_questionoptions = db_questionoptions::inst()->get_all_options();
        //var_dump($all_questionoptions);
        $question_list = questions::load_by_nid($paperid);
        $answer = answer::load($answerid);
        $photo_list = json_decode($answer->get_reply());
        $answer_list = $answer->content();
        $answer_list = json_decode($answer_list);
        $assoc_question_list = [];
        $option_checked_array = [];
        
        foreach ($question_list as $qid => $question) {
            $_questionid = $question['id'];
            $_question_pid = $question['is_parent'];
            $_question_value = json_decode($question['value']);
            $question_list[$qid]['answer_value'] = null;
            $question_list[$qid]['value'] = $_question_value;
            $question_list[$qid]['status'] = 'hide';
            $question_list[$qid]['parent_question_option'] = null;
            if ($question['type'] == 'radio' || $question['type'] == 'check' ){
                $question_list[$qid]['options'] = [];
                foreach ($all_questionoptions as $questionoption) { //提取问题的options
                    if ($questionoption['qid'] ==  $_questionid ) {
                        array_push($question_list[$qid]['options'], $questionoption);
                    }
                }
            }
            if (!empty($_question_pid)) {
                $option_question_id = $all_questionoptions[$_question_pid]['qid'];
                $question_list[$qid]['parent_question_option'] = ["parent_question_id" => $option_question_id, "parent_option_id" => $_question_pid];
            }
            foreach ((array)$answer_list as $answer) {
                $_answerid = $answer->id;
                if ($_questionid == $_answerid) {
                    $question_list[$qid]['answer_value'] = $answer->value; //提取问题答案
                    if ($question['type'] == 'radio') {                 //选择题的option给出status,前端用于判断是否展示
                        foreach ($question_list[$qid]['options'] as $opt_id => $option) {
                            if ($option['value'] == $answer->value){
                                $question_list[$qid]['options'][$opt_id]['status'] = "checked";
                                array_push($option_checked_array, $opt_id);
                            }else {
                                $question_list[$qid]['options'][$opt_id]['status'] = "nochecked";
                            }
                        }
                    }else if ($question['type'] == 'check') {
                       
                        foreach ($question_list[$qid]['options'] as $opt_id => $option) {
                            if (in_array($option['value'], (array)$answer->value)){
                                $question_list[$qid]['options'][$opt_id]['status'] = "checked";
                                array_push($option_checked_array, $opt_id);
                            }else {
                                $question_list[$qid]['options'][$opt_id]['status'] = "nochecked";
                                
                            }
                        }
                    }
                }
            }
        }
        
        foreach ($question_list as $qid => $question) {
            if (empty($question['is_parent'])) {
                $question_list[$qid]['status'] = 'show';
            } else {
                $question_list[$qid]['status'] = 'show';
                $__question = $question;
                while($__question['is_parent']) {
                    logging::d("WHILE_start", "WHILE_startWHILE_startWHILE_startWHILE_startWHILE_start");
                    logging::d("WHILE_start", json_encode($__question));
                    $parent_question_id = $__question['parent_question_option']['parent_question_id'];
                    $parent_question_opt_id = $__question['parent_question_option']['parent_option_id'];
                    
                    foreach ($question_list[$parent_question_id]["options"] as $opt){
                        if ($opt['id'] == $parent_question_opt_id && $opt['status'] == 'nochecked'){
                            logging::d("WHILE", "opt['id']:" . $opt['id']);
                            logging::d("WHILE", "opt['status']:" . $opt['status']);
                            $question_list[$qid]['status'] = 'hide';
                        }
                    }
                    $__question = $question_list[$parent_question_id];
                    logging::d("WHILE", "now_questionis:".json_encode($__question));
                }
            }
        }
        //dump_var($question_list);
        $all_data['photo_list'] = $photo_list;
        $all_data['questionnaire'] = $questionnaire->pack_info();
        $all_data['question_list'] = $question_list;
        $all_data['answer_list'] = $answer_list;
        $all_data['assoc_question_list'] = $assoc_question_list;
        $all_data['answerid'] = $answerid;
        echo json_encode(array("op" => "get_answer_by_taskid", "data" => $all_data));
    }
}
