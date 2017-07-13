<?php

include_once(dirname(__FILE__) . "/../config.php");

class sheet {
    private $summary = null;
    private $mPaper = null;
    private $mTask = null;
    private $mProject = null;
    private $mQuestion = null;

    public function sheet($summary) {
        $this->summary = $summary;
    }

    public function task() {
        if ($this->mTask == null) {
            $tasks = db_muffininfos::inst()->get_all_cached();
            foreach ($tasks as $id => $task) {
                if ($task["paperid"] == $this->summary["paperid"]) {
                    $this->mTask = new task($task);
                    break;
                }
            }
        }
        return $this->mTask;
    }

    public function project() {
        if ($this->mProject == null) {
            $muffininfos = db_muffininfos::inst()->get_all_cached();
            $muffins = db_muffins::inst()->get_all_cached();
            $task = $this->task();
            $mid = $task->muffinid();
            $projectid = $muffininfos[$mid]["pid"];
            foreach ($muffininfos as $info) {
                if ($info["muffinid"] == $projectid) {
                    $this->mProject = new projects($info);
                    break;
                }
            }
        }
        return $this->mProject;
    }

    public static function load_all() {
        $sheets = db_sheets::inst()->load_all();
        $arr = array();
        foreach ($sheets as $id => $sheet) {
            $arr[$id] = new sheet($sheet);
        }
        return $arr;
    }
};

