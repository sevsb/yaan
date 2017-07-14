<?php

include_once(dirname(__FILE__) . "/../config.php");

class question_choice_word extends question_choice {
    public function question_choice_word($dataarr) {
        parent::question_choice($dataarr);
    }
};

class question_choice_radio extends question_choice {
    public function question_choice_radio($dataarr) {
        parent::question_choice($dataarr);
    }
};

class question_choice_multi extends question_choice {
    public function question_choice_multi($dataarr) {
        parent::question_choice($dataarr);
    }
};
class question_choice_completion extends question_choice {
    public function question_choice_completion($dataarr) {
        parent::question_choice($dataarr);
    }
};

class question_choice {
    protected $dataarr = null;
    public function question_choice($dataarr) {
        $this->$dataarr = $dataarr;
    }

    public function toJson() {
        return json_encode($this->dataarr);
    }

    public static function create($choice) {
        if (empty($choice)) {
            return null;
        }
        $arr = json_decode($choice, true);
        switch ($arr["type"]) {
        case question::TYPE_WORD:
            return new question_choice_word($arr["data"]);
        case question::TYPE_RADIO:
            return new question_choice_radio($arr["data"]);
        case question::TYPE_MULTI:
            return new question_choice_multi($arr["data"]);
        case question::TYPE_COMPLETION:
            return new question_choice_completion($arr["data"]);
        }
        return null;
    }
};

class question {
    const TYPE_WORD = 0;
    const TYPE_RADIO = 1;
    const TYPE_MULTI = 2;
    const TYPE_COMPLETION = 3;

    private $mChoice = null;
    public function question($summary) {
        $this->mChoice = question_choice::create($summary["choice"]);
    }
};

