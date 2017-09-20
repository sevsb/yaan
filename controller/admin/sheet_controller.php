<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class sheet_controller {
    public function preaction($action) {
        login::assert();
    }

    public function index_action() {
        $baiduak = settings::instance()->load("BAIDU_MAP_AK");
        $tpl = new tpl("admin/header", "admin/footer");
        $tpl->set("baiduak", $baiduak);
        $tpl->display("admin/sheet/index");
    }
    
    public function answer_list_action() {
        $task_list = tasks::load_all();
        $data = [];
        foreach ($task_list as $tid => $task) {
            if($task->status() == tasks::STATUS_NOTREVIEW) {
                $data [$tid]= $task->pack_info();
            }
            

            //$data [$tid]->project= $task->project()->pack_info();
            //$data [$tid]->user = wechatuser::create($task->wechat_userid());
        }
        var_dump($data);
        echo json_encode($data);
    }


    public function sheetlist_action() {
        $sheets = sheet::load_all();
        $questions = db_question::inst()->get_all_questionnaires();
        $questionoptions = db_questionoptions::inst()->get_all_options();
        $answer_list = db_answers::inst()->load_all();
        $final_array = [];
        $data = array();
        foreach ($sheets as $sheet) {
            if ($sheet->task()->status() == tasks::STATUS_PENDING || $sheet->task()->status() == tasks::STATUS_ASSIGNED ) {
                continue;
            }
            $data []= $sheet->pack_info();
        }
        foreach ($data as $id =>$sht) {
            $paperid = $sht['task']['project']['paperid'];
            $answerid = $sht['answers'][0]['id'];
            if (!empty($paperid)) {
                $paper_answers = $sht['answers'][0]['content'];
                $paper_answer = json_decode($paper_answers);
                
                if (!empty($answer_list[$answerid]) && !empty($answer_list[$answerid]['reply'])) {
                    $img_list = json_decode($answer_list[$answerid]['reply']);
                    //dump_var($img_list);
                }
                
                
                foreach ($questions as $qid => $question) {
                    if ($question['nid'] == $paperid && $question['is_remove'] != 1) {
                        $question['options'] = [];
                        $question['answer_value'] = [];
                        $question['img_list'] = [];
                        foreach ($paper_answer as $p_answer) {
                            if ($p_answer->id == $qid) {
                                array_push($question['answer_value'], $p_answer->value);
                            }
                        }
                        if (!empty($img_list->$qid)) {
                            $question['img_list'] = $img_list->$qid;
                            //dump_var($question['img_list']);
                            foreach ($question["img_list"] as $qiid => $image) {
                                //dump_var($image);
                                $question["img_list"][$qiid]->img = UPLOAD_URL . "/" . $image->imgUrl;
                                $question["img_list"][$qiid]->thumbnail = mkUploadThumbnail($image->imgUrl, 100, 0);
                            }
                                //"image" => UPLOAD_URL . "/" . $image["imgUrl"],
                                //"thumbnail" => mkUploadThumbnail($image["imgUrl"], 100, 0),
                            
                        }
                        $question['answer_value'] = $question['answer_value'][0];
                        foreach ($questionoptions as $oid => $option) {
                            if ($option['qid'] == $qid) {
                                $option['status'] = 'nocheck';
                                if ($question['type'] == 'radio') {
                                    if ($option['value'] == $question['answer_value']){
                                        $option['status'] = 'checked';
                                    }
                                } else if ($question['type'] == 'check')  {
                                    if (in_array($option['value'], (array)$question['answer_value'])){
                                        $option['status'] = 'checked';
                                    }
                                }
                                array_push($question['options'], $option);
                            }
                        }
                        $question['value'] = json_decode($question['value']);
                        $question_list[$qid] = $question;
                    }
                }
                
                $data[$id]['questions'] = $question_list; 
            } else {
                //$data[$id]['answer_content'] = false;
                $data[$id]['questions'] = false; 
            }
        }
        $final_array['sheets'] = $data;
        $final_array['all_options'] = $questionoptions;
        $res = array("op" => "sheetlist", "data" => $final_array);
        echo json_encode($res);
    }

    public function review_action() {
        $sid = get_request_assert("sheet");
        $review = get_request_assert("review");
        if ($review != "PASS" && $review != "REJECT") {
            logging::fatal("Debug", "unknown operation: $review");
        }

        $sheet = sheet::create($sid);
        logging::d('SHEET', json_encode($sheet));
        $wechat_userid = $sheet->userid();
        $wechat_user = wechatuser::create($wechat_userid);
        $openid = $wechat_user->openid();
        $task_title = $sheet->task()->title();
        logging::d('wechat_userid', json_encode($wechat_userid));
        logging::d('task_title', json_encode($task_title));
        
        if ($review == "PASS") {
            $sheet->pass();
            $data_array = array(
                "touser" => $openid,
                "template_id" => "M6sVbyyZU4d94tI3gBRU1uoIX5TXXOWbblnjuqWI2Yc",
                "url" => "http://yaan.rendajinrong.com/?action=wechat.index&debugroute=1",
                "miniprogram" => array(
                    "appid" => "",
                    "pagepath" => ""),
                "data" => array(
                    "first" => array(
                        "value" =>"你好",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" =>"$task_title",
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" =>"您的任务审批已经通过",
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" =>"感谢您的支持",
                        "color" => "#173177"
            )));
            $result = wxApi::inst()->send_template_message($data_array);
            logging::d("SENDTEMMSG",$result);
        } else if ($review == "REJECT") {
            $sheet->reject();
            $data_array = array(
                "touser" => $openid,
                "template_id" => "VuF_vWJfP6a1SuMlRUyKLOHzTgmT2M4VdQrfDzRffaw",
                "url" => "http://yaan.rendajinrong.com/?action=wechat.index&debugroute=1",
                "miniprogram" => array(
                    "appid" => "",
                    "pagepath" => ""),
                "data" => array(
                    "first" => array(
                        "value" =>"你好",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" =>"$task_title",
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" =>"您的任务审核未通过",
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" =>"感谢您的支持",
                        "color" => "#173177"
            )));
            $result = wxApi::inst()->send_template_message($data_array);
            logging::d("SENDTEMMSG",$result);
        }
        $sheet->save();

        return $this->sheetlist_action();
    }
}













