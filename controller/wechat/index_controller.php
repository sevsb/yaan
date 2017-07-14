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
        $tpl->set("signPackage", $signPackage);
        $tpl->display("wechat/index/home");
    }

    public function sheet_action() {
        // 姑且先放在index中
        $tpl = new tpl("wechat/header", "wechat/footer");
        $signPackage = WXApi::inst()->get_SignPackage();
        $imgRoot = rtrim(UPLOAD_URL, "/") . "/";
        $tpl->set("imgRoot", $imgRoot);
        $tpl->set("signPackage", $signPackage);
        $tpl->display("wechat/index/sheet");
    }

    public function updateImg_ajax() {
        $imgData = get_request("imgData");

        $imgName = null;

        if(substr($imgData, 0, 5) == "data:") {
            $ret = uploadImageViaFileReader($imgData, function($imgName) {
                return $imgName;
            });
            logging::e("uploadImage-ret", $ret);
            if (strncmp($ret, "fail|", 5) == 0) {
                return $ret;
            }
            $ret = array("ret" => "success", "imgUrl" => $ret);
        } else {
            $ret = array("ret" => "fail");
        }

        return $ret;
    }
}

?>
