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

        $tpl = new tpl("wechat/header", "wechat/footer");
        $tpl->set("user", $user);
        $signPackage = WXApi::inst()->get_SignPackage();
        $tpl->set('signPackage', $signPackage);
        $tpl->display("wechat/index/home");
    }

    public function sheet_action() {
        // 姑且先放在index中
        $tpl = new tpl("wechat/header", "wechat/footer");
        $signPackage = WXApi::inst()->get_SignPackage();
        $tpl->set('signPackage', $signPackage);
        $tpl->display("wechat/index/sheet");
    }

    public function add_photo_ajax() {
        $photo = get_request('photo');

        $photoName = null;

        if (substr($photo, 0, 5) == "data:") {
            $ret = uploadImageViaFileReader($photo, function($photoName) {
                return $photoName;
            });
            logging::e("uploadImage-ret", $ret);
            if (strncmp($ret, "fail|", 5) == 0) {
                return $ret;
            }
            $photoName = $ret;
        }else {
            $photoName = explode('/', $photo);
            $photoName = end($photoName);
        }

        return $photoName;
    }
}

?>
