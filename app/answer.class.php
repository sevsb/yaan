<?php

include_once(dirname(__FILE__) . "/../config.php");


class answer_reply_word extends answer_reply {
    private $mImages = null;
    public function answer_reply_word($dataarr) {
        parent::answer_reply($dataarr);
        $this->mImages = $dataarr["imgList"];
    }

    public function replies() {
        return $this->mImages;
    }


    public function pack_info() {
        $data = array();
        foreach ($this->mImages as $image) {
            $exif = new exif(UPLOAD_DIR. "/" . $image["imgUrl"]);
            $data []= array(
                "image" => UPLOAD_URL . "/" . $image["imgUrl"],
                "thumbnail" => mkUploadThumbnail($image["imgUrl"], 100, 0),
                "comment" => $image["imgContent"],
                "uploadloc" => $image["imgLocation"],
                "exifloc" => $exif->location(),
            );
        }
        return array("type" => question::TYPE_WORD, "data" => $data);
    }
 
};

class answer_reply_radio extends answer_reply {
    public function answer_reply_radio($dataarr) {
        parent::answer_reply($dataarr);
    }
    public function replies() {
        return array();
    }
};

class answer_reply_multi extends answer_reply {
    public function answer_reply_multi($dataarr) {
        parent::answer_reply($dataarr);
    }
    public function replies() {
        return array();
    }
};

class answer_reply_completion extends answer_reply {
    public function answer_reply_completion($dataarr) {
        parent::answer_reply($dataarr);
    }
    public function replies() {
        return array();
    }
};

class answer_reply {
    protected $dataarr = null;
    public function answer_reply($dataarr) {
        $this->dataarr = $dataarr;
    }

    public function replies() {
        return array();
    }

    public function toJson() {
        return json_encode($this->dataarr);
    }

    public function pack_info() {
        return $this->dataarr;
    }

    public static function create($choice) {
        if (empty($choice)) {
            return null;
        }
        // logging::d("Debug", $choice);
        $arr = json_decode($choice, true);
        switch ($arr["type"]) {
        case question::TYPE_WORD:
            return new answer_reply_word($arr["data"]);
        case question::TYPE_RADIO:
            return new answer_reply_radio($arr["data"]);
        case question::TYPE_MULTI:
            return new answer_reply_multi($arr["data"]);
        case question::TYPE_COMPLETION:
            return new answer_reply_completion($arr["data"]);
        }
        return null;
    }
};

class answer {
    const TYPE_WORD = 0;
    const TYPE_RADIO = 1;
    const TYPE_MULTI = 2;
    const TYPE_COMPLETION = 3;

    private $mChoice = null;
    private $mReply = null;
    private $summary = null;
    public function answer($summary) {
        if (!empty($summary)) {
            $this->summary = $summary;
            $this->mChoice = question_choice::create($summary["choice"]);
            $this->mReply = answer_reply::create($summary["reply"]);
        } else {
            $this->summary =  array("id" => 0, "userid" => 0, "type" => 0, "title" => "", "choice" => "", "reply" => "");
        }
    }

    public function setUserId($uid) {
        $this->summary["userid"] = $uid;
    }
    public function setType($type) {
        $this->summary["type"] = $type;
    }
    public function setTitle($title) {
        $this->summary["title"] = $title;
    }
    public function setChoice($choice) {
        $this->mChoice = $choice;
    }
    public function setReply($reply) {
        $this->mReply = $reply;
    }

    public function id() {
        return $this->summary["id"];
    }

    public function type() {
        return $this->summary["type"];
    }

    public function title() {
        return $this->summary["title"];
    }

    public function &choice() {
        return $this->mChoice;
    }

    public function &reply() {
        return $this->mReply;
    }

    public function saveStorage() {
        $choice = $this->mChoice != null ? $this->mChoice->toJson() : "";
        $reply = $this->mReply != null ? $this->mReply->toJson() : "";
        $id = $this->id();
        if ($id == 0) {
            $ret = db_answers::inst()->add_answer($this->userid(), $this->type(), $this->title(), $choice, $reply);
            if ($ret !== false) {
                $this->summary["id"] = $ret;
            }
        } else {
            $ret = db_answers::inst()->update_answer($id, $this->userid(), $this->type(), $this->title(), $choice, $reply);
        }
        return $ret;
    }

    public static function load($answerid) {
        if (is_array($answerid)) {
            $ans = db_answers::inst()->get_some_answers($answerid);
            $arr = array();
            foreach ($ans as $id => $an) {
                $arr[$id] = new answer($an);
            }
            return $arr;
        } else if (is_int($answerid)) {
            $ans = db_answers::inst()->get_one_answer($answerid);
            return new answer($ans);
        } else {
            return null;
        }
    }

    public function pack_info() {
        $data = array(
            "id" => $this->id(),
            "type" => $this->type(),
            "title" => $this->title(),
            "reply" => $this->reply()->pack_info(),
        );
        return $data;
    }
};







