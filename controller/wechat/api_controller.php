<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../app/login.class.php");
include_once(dirname(__FILE__) . "/../../app/location.class.php");

class api_controller {

    public function tasks_action() {
        $loc = new location(get_request_assert("loc"));
        $infos = db_muffininfos::inst()->get_all_muffininfos();
        $res = array();
        foreach ($infos as $info) {
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
            $tasks [] = array(
                "id" => $info["id"],
                "projectid" => $project["project_id"],
                "type" => $project["type"],
                "project_title" => $project["title"],
                "task_title" => $info["title"],
                "cover" => $project["cover"],
                "text" => $project["text"],
                "deadline" => $project["limit_time"],
                "word" => $project["paper_file"],
                "status" => $project["status"],
                "address" => $info["address"],
                "content" => $info["content"],
            );
        }
        echo json_encode($tasks);
    }
}

