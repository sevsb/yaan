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


    public function tasks_action() {
        $user = get_session_assert("user");
        $loc = get_request_assert("loc");
        $_SESSION["temp.wechatapi.location"] = $loc;
        return $this->pack_tasks_info($user, $loc);
    }

    public function accept_action() {
        $task = get_request_assert("task");
        $user = get_session_assert("user");
        logging::d("Debug", $task);
        logging::d("Debug", $user);
        $ret = db_muffininfos::inst()->update_wechat_userid($task, $user["id"]);
        if ($ret) {
            $loc = get_session_assert("temp.wechatapi.location");
            return $this->pack_tasks_info($user, $loc);
        }
    }
}

