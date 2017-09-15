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
        $final_array = [];
        $data = array();
        foreach ($sheets as $sheet) {
            $data []= $sheet->pack_info();
        }
        $final_array['sheets'] = $data;
        
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













