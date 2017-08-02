<?php

include_once(dirname(__FILE__) . "/../config.php");

/**
 * 生成缩略图函数（支持图片格式：gif、jpeg、png和bmp）
 * @author ruxing.li
 * @param  string $src      源图片路径
 * @param  int    $width    缩略图宽度（只指定高度时进行等比缩放）
 * @param  int    $width    缩略图高度（只指定宽度时进行等比缩放）
 * @param  string $filename 保存路径（不指定时直接输出到浏览器）
 * @return bool
 * http://blog.csdn.net/liruxing1715/article/details/28597843
 */


function mkThumbnail($src, $width = 0, $height = 0, $filename = null) {
    if ($width === null && $height === null)
        return false;
    if ($width < 0)
        return false;
    if ($height < 0)
        return false;
    if ($width == 0 && $height == 0)
        return false;

    $size = getimagesize($src);
    if (!$size)
        return false;

    list($src_w, $src_h, $src_type) = $size;
    $src_mime = $size['mime'];
    switch($src_type) {
    case 1 :
        $img_type = 'gif';
        break;
    case 2 :
        $img_type = 'jpeg';
        break;
    case 3 :
        $img_type = 'png';
        break;
    case 15 :
        $img_type = 'wbmp';
        break;
    default :
        return false;
    }

    if ($width == 0)
        $width = $src_w * ($height / $src_h);
    if ($height == 0)
        $height = $src_h * ($width / $src_w);

    $imagecreatefunc = 'imagecreatefrom' . $img_type;
    $src_img = $imagecreatefunc($src);
    $dest_img = imagecreatetruecolor($width, $height);

    // 解决透明色会成黑色的问题
    $color = imagecolorallocate($dest_img, 255, 255, 255);
    // imagecolortransparent($dest_img, $color);
    imagefill($dest_img, 0, 0, $color);

    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $src_w, $src_h);

    $imagefunc = 'image' . $img_type;
    if ($filename !== null) {
        $imagefunc($dest_img, $filename);
    } else {
        header('Content-Type: ' . $src_mime);
        $imagefunc($dest_img);
    }
    imagedestroy($src_img);
    imagedestroy($dest_img);
    return true;
}

// $result = mkThumbnail('./IMG_3324.JPG', 147, 147);

function mkUploadThumbnail($filename, $width = 0, $height = 0) {
    if (empty($filename)) {
        return null;
    }
    $filepath = rtrim(UPLOAD_DIR, "/") . "/$filename";
    $thumbnail = rtrim(THUMBNAIL_DIR, "/") . "/thumbnail-$filename";
    if (!is_dir(THUMBNAIL_DIR)) {
        mkdir(THUMBNAIL_DIR, 0777, true);
    }
    if (is_file($thumbnail)) {
        return rtrim(THUMBNAIL_URL, "/") . "/thumbnail-$filename";
    }

    if (!is_file($filepath)) {
        return null;
    }
    mkThumbnail($filepath, $width, $height, $thumbnail);
    if (is_file($thumbnail)) {
        return rtrim(THUMBNAIL_URL, "/") . "/thumbnail-$filename";
    }
    return null;
}


