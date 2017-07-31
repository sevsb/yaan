<?php

include_once(dirname(__FILE__) . "/../config.php");
include_once(dirname(__FILE__) . "/question.class.php");

class choice_base {
    protected $summary = array();
    public function __construct($data) {
        $summary = $data;
    }
    public function init() {
        logging::fatal("Choice", "cannot run here.");
    }
};

// {"choices": ["A": "choice1", "B": "choice2"]}
class radio_choice extends choice_base {
};

// {"choices": ["A": "choice1", "B": "choice2"]}
class multi_choice extends choice_base {
};

// {"accept": "number|string"}
class completion_choice extends choice_base {
};

// {"files": ["word1.doc", "word2.doc"]}
class word_choice extends choice_base {
    private $jsonarr = array();
    public function init() {
        $jsonarr = json_decode($this->data, true);
    }
    public function get_paper() {
        return $jsonarr["files"];
    }
};

class choice {
    public static function create($type, $data) {
        $c = null;
        switch ($type) {
        case question::TYPE_WORD:
            $c = new word_choice($data);
        case question::TYPE_RADIO:
        case question::TYPE_MULTI:
        case question::TYPE_COMPLETION:
        default:
            $c = new choice_base($data);
        }
        $c->init();
        return $c;
    }
};

