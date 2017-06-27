<?php
include_once(dirname(__FILE__) . "/config.php");
include_once(dirname(__FILE__) . "/smtp.php");

class mailer {
    const LEND_THRESHOLD = 2592000; // 30 * 24 * 60 * 60;

    private static $instance = null;
    public static function instance() {
        if (self::$instance == null)
            self::$instance = new mailer();
        return self::$instance;
    }

    private $smtp = null;
    private $sender = null;

    private function __construct() {
        $smtp_server = settings::instance()->load("smtp_server");
        $smtp_port = settings::instance()->load("smtp_port", "25");
        $smtp_user = settings::instance()->load("notify_account");
        $smtp_password = settings::instance()->load("notify_password");
        $this->sender = settings::instance()->load("notify_email");
        if (empty($this->sender) || empty($smtp_user) || empty($smtp_password) || empty($smtp_server)) {
            logging::d("Mailer", "smtp is not configured.");
            return;
        }
        $this->smtp = new smtp($smtp_server, $smtp_port, true, $smtp_user, $smtp_password);
    }

    private function send($receivers, $subject, $content, $cc = '') {
        if (is_array($receivers)) {
            $receivers = implode(",", $receivers);
        }
        $subject = MAIL_SUBJECT_PREFIX . $subject;
        logging::d("Debug", "send mail to $receivers from {$this->sender}, subject: $subject");
        if ($this->smtp === null) {
            logging::d("Mailer", "smtp is not configured.");
            return false;
        }
        $ret = $this->smtp->sendmail($receivers, $this->sender, $subject, $content, 'HTML', $cc);
        logging::d("Mailer", "send mail result: $ret");
        return $ret;
    }
};

// $c = mailer::load_mail_content(book::create(1));
// echo $c;

