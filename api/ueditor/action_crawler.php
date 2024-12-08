<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 抓取远程图片
 */
set_time_limit(0);
include("Uploader.class.php");

/* 上传配置 */
$config = array(
    'siteid'=>$CONFIG['siteid'],
    'module'=>$CONFIG['module'],
    'catid'=>$CONFIG['catid'],
    'userid'=>$CONFIG['userid'],
    'isadmin'=>$CONFIG['isadmin'],
    'groupid'=>$CONFIG['groupid'],
    'is_wm'=>$CONFIG['is_wm'],
    'is_esi'=>$CONFIG['is_esi'],
    'attachment'=>$CONFIG['attachment'],
    'image_reduce'=>$CONFIG['image_reduce'],
    "pathFormat" => $CONFIG['catcherPathFormat'],
    "maxSize" => $CONFIG['catcherMaxSize'],
    "allowFiles" => $CONFIG['catcherAllowFiles'],
    "oriName" => "remote.png"
);
$fieldName = $CONFIG['catcherFieldName'];

/* 抓取远程图片 */
if (intval($CONFIG['is_esi'])) {
    $list = array();
    if (pc_base::load_sys_class('input')->post($fieldName)) {
        $source = pc_base::load_sys_class('input')->post($fieldName);
    } else {
        $source = pc_base::load_sys_class('input')->get($fieldName);
    }
    foreach ($source as $imgUrl) {
        $item = new Uploader($imgUrl, $config, "remote");
        $info = $item->getFileInfo();
        array_push($list, array(
            "state" => $info["state"],
            "url" => $info["url"],
            "size" => $info["size"],
            "title" => html2code($info["title"]),
            "original" => html2code($info["original"]),
            "source" => html2code($imgUrl)
        ));
    }

    /* 返回抓取数据 */
    return json_encode(array(
        'state'=> count($list) ? 'SUCCESS':'ERROR',
        'list'=> $list
    ), JSON_UNESCAPED_UNICODE);
}