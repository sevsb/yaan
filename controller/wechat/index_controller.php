<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");

class index_controller {
    public function home_action() {
        $user = get_session_assert("user");

        $faceurl = $user["face"];
        $faceurl = str_replace("http://", "https://", $faceurl);
        $user["face"] = $faceurl;
        logging::d("Debug", $faceurl);

        $baiduak = settings::instance()->load("BAIDU_MAP_AK");

        $tpl = new tpl("wechat/header", "wechat/footer");
        $tpl->set("STATUS_ASSIGNED", tasks::STATUS_ASSIGNED);
        $tpl->set("STATUS_NOTREVIEW", tasks::STATUS_NOTREVIEW);
        $tpl->set("STATUS_PASS", tasks::STATUS_PASS);
        $tpl->set("STATUS_REJECT", tasks::STATUS_REJECT);
        $tpl->set("user", $user);
        $tpl->set("baiduak", $baiduak);
        $signPackage = WXApi::inst()->get_SignPackage();
        $tpl->set("signPackage", $signPackage);
        $tpl->display("wechat/index/home");
    }

    public function taskaround_action() {
        $user = get_session_assert("user");
        $faceurl = $user["face"];
        $faceurl = str_replace("http://", "https://", $faceurl);
        $user["face"] = $faceurl;

        $baiduak = settings::instance()->load("BAIDU_MAP_AK");

        $tpl = new tpl("wechat/header", "wechat/footer");
        $tpl->set("user", $user);
        $tpl->set("baiduak", $baiduak);
        $signPackage = WXApi::inst()->get_SignPackage();
        $tpl->set("signPackage", $signPackage);
        $tpl->display("wechat/index/taskaround");
    }

    public function sheet_action() {
        // $user = get_session_assert("user");
        $tpl = new tpl("wechat/header", "wechat/footer");
        //$taskId = get_request_assert("task");
        $taskId = get_request("task");
        $task = tasks::create_by_id($taskId);
        $paperId = $task->paperid();
        $answerid = $task->answerid();
        $userId = $task->wechat_userid();

        if (!empty($task->project()->paperid())) {
            $questionnaire = questionnaires::load_by_id($paperid);
            $answer_list = null;
            $answerid = db_answer::inst()->add_answer($paperid, $questionnaire->title(),  $questionnaire->notes(), $answer_list);
            $ret = tasks::modify_task_answerid($taskid, $answerid);
        }
        
        if (!empty($answerid)) {
            $tpl = new tpl("admin/noheader", "admin/footer");
            $tpl->set("taskid", $taskId);
            $tpl->set("id", $paperId);
            $tpl->set("userId", $userId);
            $tpl->display("wechat/index/answer"); 
            return;
        }
        
        $tpl->set("taskId", $taskId);
        $tpl->set("paperId", $paperId);
        $tpl->set("userId", $userId);
        $imgRoot = rtrim(UPLOAD_URL, "/") . "/";
        $tpl->set("imgRoot", $imgRoot);
        $signPackage = WXApi::inst()->get_SignPackage();
        $tpl->set("signPackage", $signPackage);
        $tpl->display("wechat/index/sheet");
    }

    private function create_answer($userId, $paperId, $sheet = null) {
        $answer = new answer(
            array(
                "id" => 0,
                "type" => answer::TYPE_WORD,
                "title" => "ANSWER_TITLE",
                "choice" => "",
                "reply" => "{\"type\":0,\"data\":{\"type\":0,\"data\":{\"imgList\":[]}}}"
            )
        );
        $ret = $answer->save();
        if($ret === false)
            return false;

        if(empty($sheet)) {
            $sheet = new sheet(
                array(
                    "id" => 0,
                    "userid" => $userId,
                    "paperid" => $paperId,
                    "title" => "SHEET_TITLE",
                    "info" => "SHEET_INFO",
                    "answers" => 0,
                    "status" => sheet::STATUS_NOTREVIEW,
                )
            );
        }
        $sheet->set_answers($answer->id());
        $ret = $sheet->save();
        if($ret === false)
            return false;
        return $answer;
    }

    public function initData_ajax() {
        $paperId = get_request("paperId");
        $userId = get_request("userId");

        if(empty($paperId) || empty($userId)) {
            $ret = array("ret" => "fail", "info" => "error Id: PaperId or UserId is empty.");
            return $ret;
        }

        $needCreateSheet = true;
        $sheetsList = sheet::load_all();
        $ret = "";
        foreach($sheetsList as $sheet) {
            if($sheet->paperid() == $paperId) {
                $needCreateSheet = false;
                $answersList = $sheet->answers();
                if(!empty($answersList)) {

                    if(count($answersList) > 1) {
                        $ret = array("ret" => "fail", "info" => "error Answers: More than one answer.");
                        return $ret;
                    }

                    foreach($answersList as $answer) {
                        if(!empty($answer->reply())) {
                            $ret = $answer->reply()->replies();
                        } else {
                            $answer->setReply("{\"type\":0,\"data\":{\"type\":0,\"data\":{\"imgList\":[]}}}");
                            if($ret === false)
                                return "fail|数据库操作失败，请稍后重试。";
                            else
                                $ret = array();
                        }
                        $ret = array("answerId" => $answer->id(), "photosList" => $ret);
                    }
                } else {
                    $ret = $this->create_answer($userId, $paperId, $sheet);
                    if($ret === false)
                        return "fail|数据库操作失败，请稍后重试。";
                    $ret = array("answerId" => $ret->id(), "photosList" => array());
                }
            }
        }

        if($needCreateSheet) {
            $ret = $this->create_answer($userId, $paperId);
            if($ret === false)
                return "fail|数据库操作失败，请稍后重试。";
            $ret = array("answerId" => $ret->id(), "photosList" => array());
        }

        $ret = array("ret" => "success", "info" => $ret);
        return $ret;
    }

    public function updateImg_ajax() {
        $imgData = get_request("imgData");

        $imgName = null;

        if(substr($imgData, 0, 5) == "data:") {
            $ret = uploadImageViaFileReader($imgData, function($imgName) {
                return $imgName;
            });
            logging::e("uploadImage-ret", $ret);
            if (strncmp($ret, "fail|", 5) == 0) {
                return $ret;
            }
            $ret = array("ret" => "success", "imgUrl" => $ret);
        } else {
            $ret = array("ret" => "fail");
        }

        return $ret;
    }

    public function deleteImg_ajax() {
        $imgName = get_request("imgName");

        if(!empty($imgName)) {
            $ret = deleteUploadImageByFilename($imgName);
        } else {
            $ret = array("ret" => "fail");
        }

        return $ret;
    }

    public function updatePhotosList_ajax() {
        $answerId = get_request("answerId");
        $photosList = get_request("photosList");

        $reply = array("type" => 0, "data"=>array("imgList" => $photosList));
        $reply = new answer_reply_word($reply);
        $answer = answer::load((int)$answerId);
        $answer->setReply($reply);
        $ret = $answer->save();
        return ($ret !== false) ? "success" : "fail|数据库操作失败，请稍后重试。";
    }

    public function sumbitSheet_ajax() {
        $taskId = get_request("taskId");
        $ret = tasks::modify_task_status($taskId, tasks::STATUS_NOTREVIEW);
        return ($ret !== false) ? "success" : "fail|数据库操作失败，请稍后重试。";
    }
}

?>
