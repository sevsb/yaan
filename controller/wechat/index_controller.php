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
        // 姑且先放在index中
        $tpl = new tpl("wechat/header", "wechat/footer");
        // $signPackage = WXApi::inst()->get_SignPackage();
        $imgRoot = rtrim(UPLOAD_URL, "/") . "/";
        $tpl->set("imgRoot", $imgRoot);
        // $tpl->set("signPackage", $signPackage);
        $tpl->display("wechat/index/sheet");
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
        $ret = '';
        foreach($sheetsList as $sheet) {
            if($sheet->paperid() == $paperId) {

                if($sheet->userid() != $userId) {
                    $ret = array("ret" => "fail", "info" => "error Sheet: Not match UserId for Sheet.");
                    return $ret;
                }

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
                        }
                    }
                } else {
                    $answer = new answer(
                        array(
                            "id" => 0,
                            "type" => answer::TYPE_WORD,
                            "title" => "ANSWER_TITLE",
                            "choice" => "",
                            "reply" => ""
                        )
                    );
                    $answer->save();
                    $sheet->set_answers($answer->id());
                    $sheet->save();
                }
            }
        }

        if($needCreateSheet) {
            $sheet = new sheet(
                array(
                    "id" => 0,
                    "userid" => $userId,
                    "paperid" => $paperId,
                    "title" => "SHEET_TITLE",
                    "info" => "SHEET_INFO",
                    "answers" => $answer->id(),
                    "status" => sheet::STATUS_NOTREVIEW,
                )
            );
            $sheet->save();
            $answer = new answer(
                array(
                    "id" => 0,
                    "type" => answer::TYPE_WORD,
                    "title" => "ANSWER_TITLE",
                    "choice" => "",
                    "reply" => ""
                )
            );
            $answer->save();
            $sheet->set_answers($answer->id());
            $sheet->save();
        }

        $ret = array("answerId" => $answer->id(), "photosList" => $ret);
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

    public function updatePhotosList_ajax() {
        $answerId = get_request("answerId");
        $photosList = get_request("photosList");


        $reply = array("type" => 0, "data"=>array("imgList" => $photosList));
        $reply = new answer_reply_word($reply);
        $answer = answer::load((int)$answerId);
        $answer->setReply($reply);
        $answer->save();
        $ret = array("ret" => "fail");
        return $ret;

    }
}

?>
