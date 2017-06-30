<?php

include_once "wxApi.php";
include_once "crypto/wxBizMsgCrypt.php";

// FIXME: 下面的参数从数据库的setting表中读取
define("APP_ID", "");
define("APP_SECRET", "");
define("TOKEN", "");
define("ENCODING_AES_KEY", "");

$wxObj = new WXCallback();
if(!empty($_GET['echostr'])) {
    $wxObj->valid($_GET['signature'], $_GET['timestamp'], $_GET['nonce'], $_GET['echostr']);
}


class WXCallback {
    /**
     * 微信验证，一般在配置服务器url时使用
     * @param $signature 微信加密签名，signature结合了开发者填写的token参数和请求中
     *                   的timestamp参数、nonce参数。
     * @param $timestamp 时间戳
     * @param $nonce 随机数
     * @param $echostr 随机字符串
     */
    public function valid($signature, $timestamp, $nonce, $echostr) {
        if($this->checkSignature($signature, $timestamp, $nonce)){
            echo $echoStr;
        } else {
            echo ErrorCode::$ValidateSignatureError;
        }
        exit();
    }

    /**
     * 检验signature对请求进行校验。若确认此次GET请求来自微信服务器，请原样返回
     * echostr参数内容，则接入生效，成为开发者成功，否则接入失败。
     */
    private function checkSignature($signature, $timestamp, $nonce) {
        $token = TOKEN;

        // 将token、timestamp、nonce三个参数进行字典序排序
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);

        // 将三个参数字符串拼接成一个字符串进行sha1加密
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        // 开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }
}

?>