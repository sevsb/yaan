<?php
include_once(dirname(__FILE__) . "/../../config.php");

class questionnaire_controller {
    
    public function preaction($action) {
        login::assert();
    }
    
    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $userid = get_session('user.id');
        $all_questionnaires = questionnaires::load_all();
        $tpl->set('questionnaires', $all_questionnaires);
        $tpl->set('userid', $userid);
        $tpl->display("admin/questionnaire/index");
    }
    
    public function new_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        $userid = get_session('user.id');
        $cache_questionnaire = questionnaires::get_cache_naire($userid);
        $questionoptions = db_questionoptions::inst()->get_all_options();
        if (!empty($cache_questionnaire)) {
            $questionnaire = $cache_questionnaire;
        }else {
            $questionnaire = questionnaires::create(0);
        }
        $questions = questions::load_by_nid($questionnaire->id());
        foreach ($questions as $i => $question) {
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
        $tpl->set('questionoptions', $questionoptions);
        $tpl->set('questionnaire', $questionnaire);
        $tpl->set('questions', $questions);
        
        $tpl->display("admin/questionnaire/new");
    }
    
    public function edit_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        $id = get_request("id", 0);
        
        $questionnaire = questionnaires::load_by_id($id);
        $questions = questions::load_by_nid($questionnaire->id());
        $questionoptions = db_questionoptions::inst()->get_all_options();
        foreach ($questions as $i => $question) {
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
        $tpl->set('questionoptions', $questionoptions);
        $tpl->set('questionnaire', $questionnaire);
        $tpl->set('questions', $questions);
        
        $tpl->display("admin/questionnaire/edit");
    }
    
    public function editPid_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        $pid = get_request("projectmuffinid", 0);
        
        //         $task = tasks::create($taskid);
        $questionnaire = questionnaires::createPid($pid);
        $questions = questions::load_by_nid($questionnaire->id());
        $i = 1;
        for($i=1;$i<=count($questions); $i++){
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
        $tpl->set('questionnaire', $questionnaire);
        $tpl->set('questions', $questions);
        
        $tpl->display("admin/questionnaire/edit");
    }
    
    public function preview_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/noheader", "admin/footer");
        $nid = get_request("id", 0);
        
        $tpl->set('nid', $nid);
        $tpl->display("admin/questionnaire/preview");
    }
    
    public function get_preview_by_naireid_action() {

        $naireid = get_request("naireid");
        $questionnaire = questionnaires::load_by_id($naireid);
        logging::d('get_preview_by_naireid_action',$naireid);
        $all_questionoptions = db_questionoptions::inst()->get_all_options();
        $question_list = questions::load_by_nid($naireid);

        $assoc_question_list = [];
        $option_checked_array = [];
        
        foreach ($question_list as $qid => $question) {
            $_questionid = $question['id'];
            $_question_pid = $question['is_parent'];
            $_question_value = json_decode($question['value']);
            $question_list[$qid]['value'] = $_question_value;
            $question_list[$qid]['answer_value'] = null;
            $question_list[$qid]['status'] = empty($_question_pid) ? 'show' : "hide";
            if ($question['type'] == 'radio' || $question['type'] == 'check' ){
                $question_list[$qid]['options'] = [];
                foreach ($all_questionoptions as $questionoption) { //提取问题的options
                    if ($questionoption['qid'] ==  $_questionid ) {
                        $questionoption['status'] = 'nochecked';
                        array_push($question_list[$qid]['options'], $questionoption);
                    }
                }
            }
            if (!empty($_question_pid)) {
                if (empty($assoc_question_list[$_question_pid])) {
                    $assoc_question_list[$_question_pid] = [];
                }
                array_push($assoc_question_list[$_question_pid], $_questionid);   //提取关联问题集
            }
            if (!empty($_question_pid)) {
                $option_question_id = $all_questionoptions[$_question_pid]['qid'];
                $question_list[$qid]['parent_question_option'] = ["parent_question_id" => $option_question_id, "parent_option_id" => $_question_pid];
            }
        }
        
        $all_data['questionnaire'] = $questionnaire->pack_info();
        $all_data['question_list'] = $question_list;
        $all_data['assoc_question_list'] = $assoc_question_list;
        echo json_encode(array("op" => "get_answer_by_taskid", "data" => $all_data));
        
    }
    
    public function assoc_action() {
        $tpl = new tpl();
        $qid = get_request("qid", 0);
        $question = questions::load_by_id($qid);
        $questions = questions::load_assoc_by_nid($question['nid'],$qid);
        $i = 1;
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."qid:".json_encode($questions)."\r\n", FILE_APPEND);
        foreach($questions as $question){
            $question['options'] = questionoptions::load_by_qid($question['id']);
            $tqlquestions[$i] = $question;
            $i++;
            file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."$i:".$question['id']."\r\n", FILE_APPEND);
        }
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."qid:".json_encode($tqlquestions)."\r\n", FILE_APPEND);
        $tpl->set('questions', $tqlquestions);
        $tpl->set('qid', $qid);
        
        $tpl->display("admin/questionnaire/assoc");
    }

    public function editAssoc_action() {
        $tpl = new tpl();
        $qid = get_request("qid", 0);
        $id = get_request("optionsRadios", 0);
        $optionid = get_request($id, 0);
        
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."qid:$qid, id:$id, optionid:$optionid\r\n", FILE_APPEND);
        
        db_question::inst()->set_parent($qid,$optionid);
        
        echo "关联成功";
    }
    
    public function addAnswer_ajax() {
        $id = get_request("id", 0);
        $answer_list = get_request("answer_list");
        
        $questionnaire = questionnaires::load_by_id($id);
        $ret = db_answer::inst()->add_answer($id, $questionnaire->title(),  $questionnaire->notes(), $answer_list);
        return $ret ? 'success' : 'fail';
    }
    
    public function answerView_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        $id = get_request("id", 0);
        
        $answer = answers::load_by_id($id);
        $questionnaire = questionnaires::load_by_id($answer['nid']);
        $questions = questions::load_by_nid($questionnaire->id());
        logging::d('answer', $answer);
        logging::d('questionnaire', $questionnaire);
        logging::d('questions', $questions);
        //return;
        foreach ($questions as $i => $question) {
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
        $answers = json_decode($answer['content']);
        $answer_list = [];
        foreach ($answers as $answer_elf) {
            $answer_list[$answer_elf->id] = $answer_elf;
        }
        $tpl->set('questionnaire', $questionnaire);
        $tpl->set('questions', $questions);
        $tpl->set('answer', $answer);
        $tpl->set('answers', $answer_list);
        
        $tpl->display("admin/questionnaire/answerView");
    }
    
    public function answerList_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        
        $answers = answers::load_all();
        
        $tpl->set('answers', $answers);
        
        $tpl->display("admin/questionnaire/answerList");
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
    
    public function editQuestions_action() {

        $qid = get_request("qid", 0);
        
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."qid:$qid\r\n", FILE_APPEND);
        
        $questions = questions::load_by_id($qid);
        $questions['options'] = questionoptions::load_by_qid($qid);
        
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."qid:".json_encode($questions)."\r\n", FILE_APPEND);
        echo json_encode($questions);
    }
    
    public function delQuestions_action() {

        $qid = get_request("qid", 0);
        
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."qid:$qid\r\n", FILE_APPEND);
        
        $questions = questions::remove($qid);
        echo $qid;
    }
    
    public function editQuestionsDo_action() {
        
        $id = get_request("id", 0);
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."id:$id\r\n", FILE_APPEND);
        
        if(!empty($id)){
            $title = get_request("title", "");
            $notes = get_request("notes", "");
            $type = get_request("type", "");
            $value= get_request("value", "");
            
            $qid = db_question::inst()->modify_questions($id, $title, $type, $notes, $value);
            //             $questions = db_question::inst()->get_questions_by_id($id);
            $titles = get_request("titles");
            $values = get_request("values");
            file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."id:$id, titles:".count($titles).",values,".count($values)."\r\n", FILE_APPEND);
            $i = 0;
            db_questionoptions::inst()->removeAll($id);
            foreach($titles as $option ){
                $questionoptions[] = questionoptions::create($id, $option, $values[$i], $i);
                $i++;
            }
        }
    }
    
    public function addQuestions_action() {
        
        $nid = get_request("nid", 0);
        
        if(!empty($nid)){
            $title = get_request("title", "");
            $notes = get_request("notes", "");
            $type = get_request("type", "");
            $is_upload = get_request("is_upload", 0);
            
            if($type=='star'){
                $value= get_request("selectstar", "");
                $id = db_question::inst()->add_questions($nid, $is_upload, $title, $type, $notes, $value);
                file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."$type,selectstar:$value\r\n", FILE_APPEND);
            }else if($type=='range'){
                $value= get_request("selectstar", "");
                $setnumber= get_request("setnumber", "");
                $id = db_question::inst()->add_questions($nid, $is_upload, $title, $type, $notes, json_encode(array('selectstar'=>$value,'setnumber'=>$setnumber)));
                file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."$type,selectstar:$value,setnumber:$setnumber\r\n", FILE_APPEND);
            }else if($type=='text'){
                $id = db_question::inst()->add_questions($nid, $is_upload, $title, $type, $notes);
                file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."$type,notes:$notes\r\n", FILE_APPEND);
            }else{
                $value= get_request("value", "");
                
                $id = db_question::inst()->add_questions($nid, $is_upload, $title, $type, $notes, $value);
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
        
        echo $id;
    }
    
    public function save_naire_ajax() {
        $id = get_request('id');
        $result = questionnaires::save_naire($id);
        return $result ? 'success' : 'fail';
    }    
    
    public function remove_answer_ajax() {
        $id = get_request('id');
        $result = answers::remove_answer($id);
        return $result ? 'success' : 'fail';
    }
    
    public function remove_naire_ajax() {
        $id = get_request('id');
        $result = questionnaires::remove_naire($id);
        return $result ? 'success' : 'fail';
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