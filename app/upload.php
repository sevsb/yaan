<?php

include_once(dirname(__FILE__) . "/../config.php");

function uploadImageViaFileReader($imgsrc = null, $callback = null, $args = null) {
    $whitelist = array("image/jpeg", "image/pjpeg", "image/png", "image/x-png", "image/gif");

    if ($imgsrc == null) {
        $imgsrc = get_request_assert("imgsrc");
    } else if (substr($imgsrc, 0, 5) != "data:") {
        $imgsrc = get_request_assert($imgsrc);
    }

    // data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgIC…gAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA//Z
    $arr = explode(";", $imgsrc);
    if (count($arr) != 2) {
        return "fail|数据错误.";
    }

    $arr1 = explode(":", $arr[0]);
    if (count($arr1) != 2) {
        return "fail|数据错误..";
    }
    $type = $arr1[1];
    if (!in_array($type, $whitelist)) {
        return "fail|不支持的文件格式: $type.";
    }

    $type = explode('/', $type);
    $extension = $type[1];

    $arr = explode('base64,', $imgsrc);
    $image_content = base64_decode($arr[1]);

    if (!file_exists(UPLOAD_DIR)) {
        $ret = @mkdir(UPLOAD_DIR, 0777, true);
        if ($ret === false) {
            return "fail|上传目录创建失败.";
        }
    }

    $filename = md5($image_content) . ".$extension";

    $filepath = UPLOAD_DIR . "/$filename";
    if (!file_put_contents($filepath, $image_content)) {
        return 'fail|创建文件失败.';
    }
    if ($callback != null) {
        return $callback($filename, $args);
    }
    return "success";
}

function deleteUploadImageByFilename($filename) {
    $filepath = UPLOAD_DIR . "/$filename";

    if (!file_exists($filepath)) {
        return "success";
        return "fail|目标上传图片不存在.";
    }

    if(!unlink($filepath)) {
        return "fail|删除上传图片失败.";
    } else {
        return "success";
    }
}

function uploadFileViaFileReader($filesrc = null) {
    $whitelist = array(
    "application/msword" => "doc", 
    "application/vnd.ms-excel" => "xls", 
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => "xlsx", 
    "application/vnd.ms-excel.sheet.macroEnabled.12" => "xlsm", 
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => "docx", 
    "image/jpeg" => "jpg", 
    "application/pdf" => "pdf", 
    "image/png" => "png"
    );

    if ($filesrc == null) {
        $filesrc = get_request_assert("paperfile");
    } else if (substr($filesrc, 0, 5) != "data:") {
        $filesrc = get_request_assert($paperfile);
    }

    // data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgIC…gAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA//Z
    $arr = explode(";", $filesrc);
    if (count($arr) != 2) {
        return "fail|数据错误.";
    }

    $arr1 = explode(":", $arr[0]);
    if (count($arr1) != 2) {
        return "fail|数据错误..";
    }
    $type = $arr1[1];
    if (!array_key_exists($type, $whitelist)) {
        return "fail|不支持的文件格式: $type.";
    }

    $extension = $whitelist[$type];

    $arr = explode('base64,', $filesrc);
    $file_content = base64_decode($arr[1]);

    if (!file_exists(FILEUPLOAD_DIR)) {
        $ret = @mkdir(FILEUPLOAD_DIR, 0777, true);
        if ($ret === false) {
            return "fail|上传目录创建失败.";
        }
    }

    $filename = md5($file_content) . ".$extension";

    $filepath = FILEUPLOAD_DIR . "/$filename";
    if (!file_put_contents($filepath, $file_content)) {
        return 'fail|创建文件失败.';
    }
    return "success|$filename";

}
