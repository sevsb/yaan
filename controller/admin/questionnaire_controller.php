<?php
include_once(dirname(__FILE__) . "/../../config.php");

class questionnaire_controller {
    
    public function preaction($action) {
        login::assert();
    }
    
    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        
        $all_questionnaires = questionnaires::load_all();
        $tpl->set('questionnaires', $all_questionnaires);
        
        $tpl->display("admin/questionnaire/index");
    }
    
    public function new_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        
        //         $task = tasks::create($taskid);
        $questionnaire = questionnaires::create(0);
        $questions = questions::load_by_nid($questionnaire->id());
        $i = 1;
        for($i=1;$i<=count($questions); $i++){
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
        $tpl->set('questionnaire', $questionnaire);
        $tpl->set('questions', $questions);
        
        $tpl->display("admin/questionnaire/new");
    }
    
    public function edit_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        $id = get_request("id", 0);
        
//         $task = tasks::create($taskid);
        $questionnaire = questionnaires::load_by_id($id);
        $questions = questions::load_by_nid($questionnaire->id());
        $i = 1;
        for($i=1;$i<=count($questions); $i++){
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
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
    
    public function answer_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        $nid = get_request("id", 0);
        
        //         $task = tasks::create($taskid);
        $questionnaire = db_questionnaires::inst()->get_questionnaires_by_id($nid);
        file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."{$questionnaire['notes']}id:{$questionnaire['title']}\r\n", FILE_APPEND);
        $answer = answers::create($nid,$questionnaire['title'],$questionnaire['notes']);
        $questions = questions::load_by_nid($questionnaire['id']);
        $i = 1;
        for($i=1;$i<=count($questions); $i++){
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
        $tpl->set('questionnaire', $questionnaire);
        $tpl->set('questions', $questions);
        $tpl->set('answer', $answer);
        
        $tpl->display("admin/questionnaire/answer");
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
    
    public function addAnswer_action() {
        
        $id = get_request("id", 0);
        $answer = answers::load_by_id($id);
        $questionnaire = questionnaires::load_by_id($answer['nid']);
        $questions = questions::load_by_nid($questionnaire['id']);
        $i = 1;
        for($i=1;$i<=count($questions); $i++){
            if(isset($_POST[$questions[$i]['id']])){
                $answers[$questions[$i]['id']] = urldecode($_POST[$questions[$i]['id']]);
            }
        }
        $str = serialize($answers);
        
        db_answer::inst()->modify_answer($id, $answer['title'], $answer['notes'], $str);
    }
    
    public function answerView_action() {
        $tpl = new tpl();
        $tpl = new tpl("admin/header", "admin/footer");
        $id = get_request("id", 0);
        
        //         $task = tasks::create($taskid);
        $answer = answers::load_by_id($id);
        $questionnaire = questionnaires::load_by_id($answer['nid']);
        $questions = questions::load_by_nid($questionnaire['id']);
        $i = 1;
        for($i=1;$i<=count($questions); $i++){
            $questions[$i]['options'] = questionoptions::load_by_qid($questions[$i]['id']);
        }
        $answers = unserialize($answer['content']);
        $tpl->set('questionnaire', $questionnaire);
        $tpl->set('questions', $questions);
        $tpl->set('answer', $answer);
        $tpl->set('answers', $answers);
        
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
            
            if($type=='star'){
                $value= get_request("selectstar", "");
                $id = db_question::inst()->add_questions($nid, $title, $type, $notes, $value);
                file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."$type,selectstar:$value\r\n", FILE_APPEND);
            }else if($type=='range'){
                $value= get_request("selectstar", "");
                $setnumber= get_request("setnumber", "");
                $id = db_question::inst()->add_questions($nid, $title, $type, $notes, json_encode(array('selectstar'=>$value,'setnumber'=>$setnumber)));
                file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."$type,selectstar:$value,setnumber:$setnumber\r\n", FILE_APPEND);
            }else if($type=='text'){
                $id = db_question::inst()->add_questions($nid, $title, $type, $notes);
                file_put_contents("./log_" . date("Y-m-d") . ".txt",  "\n".date("H:i:s", time()).':'.__METHOD__.':'."$type,notes:$notes\r\n", FILE_APPEND);
            }else{
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
        
        echo $id;
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