<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 抓取远程图片
 */
set_time_limit(0);
include("Uploader.class.php");

/* 上传配置 */
$config = array(
    'siteid'=>$this->config['siteid'],
    'module'=>$this->config['module'],
    'catid'=>$this->config['catid'],
    'userid'=>$this->config['userid'],
    'isadmin'=>$this->config['isadmin'],
    'groupid'=>$this->config['groupid'],
    'is_wm'=>$this->config['is_wm'],
    'is_esi'=>$this->config['is_esi'],
    'attachment'=>$this->config['attachment'],
    'image_reduce'=>$this->config['image_reduce'],
    "pathFormat" => $this->config['catcherPathFormat'],
    "maxSize" => $this->config['catcherMaxSize'],
    "allowFiles" => $this->config['catcherAllowFiles'],
    "oriName" => "remote.png"
);
$fieldName = $this->config['catcherFieldName'];

/* 抓取远程图片 */
if (intval($this->config['is_esi'])) {
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