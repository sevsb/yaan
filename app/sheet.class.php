<?php

include_once(dirname(__FILE__) . "/../config.php");

class sheet {
    const STATUS_NOTREVIEW = 0;
    const STATUS_PASS = 1;
    const STATUS_REJECT = 2;

    private $summary = null;
    private $mPaper = null;
    private $mTask = null;
    private $mProject = null;
    private $mQuestion = null;
    private $mAnswers = null;

    public function sheet($summary) {
        if (!empty($summary)) {
            $this->summary = $summary;
        } else {
            $this->summary = array(
                "id" => 0,
                "userid" => 0,
                "paperid" => 0,
                "title" => 0,
                "info" => 0,
                "answers" => "",
                "status" => self::STATUS_NOTREVIEW,
            );
        }
    }

    public function task() {
        if ($this->mTask == null) {
            $tasks = db_muffininfos::inst()->get_all_cached();
            foreach ($tasks as $id => $task) {
                if ($task["paperid"] == $this->summary["paperid"]) {
                    $this->mTask = new tasks($task);
                    // logging::d("Debug", $this->mTask);
                    break;
                }
            }
        }
        return $this->mTask;
    }

    // public function project() {
    //     if ($this->mProject == null) {
    //         $muffininfos = db_muffininfos::inst()->get_all_cached();
    //         $muffins = db_muffins::inst()->get_all_cached();
    //         $task = $this->task();
    //         $mid = $task->muffinid();
    //         $projectid = $muffins[$mid]["pid"];
    //         foreach ($muffininfos as $info) {
    //             if ($info["muffinid"] == $projectid) {
    //                 $this->mProject = new projects($info);
    //                 break;
    //             }
    //         }
    //     }
    //     return $this->mProject;
    // }

    public function id() {
        return $this->summary["id"];
    }

    public function userid() {
        return $this->summary["userid"];
    }

    public function paperid() {
        return $this->summary["paperid"];
    }

    public function title() {
        return $this->summary["title"];
    }

    public function info() {
        return $this->summary["info"];
    }

    public function status() {
        return $this->summary["status"];
    }

    public function status_text() {
        switch ($this->status()) {
        case self::STATUS_PASS:
            return "通过";
        case self::STATUS_REJECT:
            return "不通过";
        default:
            return "未审核";
        }
    }

    public function answers() {
        if ($this->mAnswers == null) {
            $aids = explode(",", $this->summary["answers"]);
            $this->mAnswers = answer::load($aids);
        }
        return $this->mAnswers;
    }

    public function answers_text() {
        return $this->summary["answers"];
    }


    public function set_userid($userid) {
        $this->summary["userid"] = (int)$userid;
    }

    public function set_paperid($paperid) {
        $this->summary["paperid"] = (int)$paperid;
    }

    public function set_title($title) {
        $this->summary["title"] = $title;
    }

    public function set_info($info) {
        $this->summary["info"] = $info;
    }

    public function set_answers($ans) {
        if (is_string($ans)) {
            $this->summary["answers"] = $ans;
        } else if (empty($ans)) {
            $this->summary["answers"] = "";
        }
    }

    public function reset_review() {
        $this->summary["status"] = self::STATUS_NOTREVIEW;
    }

    public function pass() {
        $this->summary["status"] = self::STATUS_PASS;
    }

    public function reject() {
        $this->summary["status"] = self::STATUS_REJECT;
    }


    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $ret = db_sheets::inst()->add_sheet($this->userid(), $this->paperid(), $this->title(), $this->info(), $this->answers_text(), $this->status());
            if ($ret !== false) {
                $this->summary["id"] = $ret;
            }
        } else {
            $ret = db_sheets::inst()->update_sheet($id, $this->userid(), $this->paperid(), $this->title(), $this->info(), $this->answers_text(), $this->status());
        }
        return $ret;
    }

    public static function create($id) {
        $s = db_sheets::inst()->get_sheet_by_id($id);
        if ($s === false) {
            return null;
        }
        return new sheet($s);
    }
    
    public static function create_by_paperid($paperid) {
        $s = db_sheets::inst()->get_sheet_by_paperid($paperid);
        if ($s === false) {
            return null;
        }
        return new sheet($s);
    }

    public static function load_all() {
        $sheets = db_sheets::inst()->load_all();
        $arr = array();
        foreach ($sheets as $id => $sheet) {
            $arr[$id] = new sheet($sheet);
        }
        return $arr;
    }

    public function pack_info() {
        $users = wechatuser::load_all();
        $uid = $this->userid();
        $user = $users[$uid];

        $ans = array();
        foreach ($this->answers() as $id => $answer) {
            $ans []= $answer->pack_info();
        }
        logging::d('TASKIS:',$this->task());
        logging::d('paperid:',$this->paperid());
        return array(
            "info" => array(
                "id" => $this->id(),
                "title" => $this->title(),
                "info" => $this->info(),
                "status" => $this->status_text(),
                "nstatus" => $this->status(),
                "user" => $user->pack_info(),
            ),
            "task" => $this->task()->pack_info(),
            "answers" => $ans,
        );
    }
};

