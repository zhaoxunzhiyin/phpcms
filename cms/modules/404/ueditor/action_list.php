<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取已上传的文件列表
 */
include "Uploader.class.php";

/* 判断类型 */
switch (pc_base::load_sys_class('input')->get('action')) {
    /* 列出文件 */
    case 'listfile':
        $config = array(
            'siteid'=>$this->config['siteid'],
            'module'=>$this->config['module'],
            'catid'=>$this->config['catid'],
            'userid'=>$this->config['userid'],
            'isadmin'=>$this->config['isadmin'],
            'roleid'=>$this->config['roleid'],
            'groupid'=>$this->config['groupid'],
            'is_wm'=>$this->config['is_wm'],
            'is_esi'=>$this->config['is_esi'],
            'attachment'=>$this->config['attachment'],
            'image_reduce'=>$this->config['image_reduce']
        );
        $siteid = $this->config['siteid'];
        $catid = $this->config['catid'];
        $userid = $this->config['userid'];
        $isadmin = $this->config['isadmin'];
        $roleid = $this->config['roleid'];
        $groupid = $this->config['groupid'];
        $allowFiles = $this->config['fileManagerAllowFiles'];
        $listSize = $this->config['fileManagerListSize'];
        $path = $this->config['fileManagerListPath'];
        break;
    /* 列出视频 */
    case 'listvideo':
        $config = array(
            'siteid'=>$this->config['siteid'],
            'module'=>$this->config['module'],
            'catid'=>$this->config['catid'],
            'userid'=>$this->config['userid'],
            'isadmin'=>$this->config['isadmin'],
            'roleid'=>$this->config['roleid'],
            'groupid'=>$this->config['groupid'],
            'is_wm'=>$this->config['is_wm'],
            'is_esi'=>$this->config['is_esi'],
            'attachment'=>$this->config['attachment'],
            'image_reduce'=>$this->config['image_reduce']
        );
        $siteid = $this->config['siteid'];
        $catid = $this->config['catid'];
        $userid = $this->config['userid'];
        $isadmin = $this->config['isadmin'];
        $roleid = $this->config['roleid'];
        $groupid = $this->config['groupid'];
        $allowFiles = $this->config['videoManagerAllowFiles'];
        $listSize = $this->config['videoManagerListSize'];
        $path = $this->config['videoManagerListPath'];
        break;
    /* 列出音频 */
    case 'listmusic':
        $config = array(
            'siteid'=>$this->config['siteid'],
            'module'=>$this->config['module'],
            'catid'=>$this->config['catid'],
            'userid'=>$this->config['userid'],
            'isadmin'=>$this->config['isadmin'],
            'roleid'=>$this->config['roleid'],
            'groupid'=>$this->config['groupid'],
            'is_wm'=>$this->config['is_wm'],
            'is_esi'=>$this->config['is_esi'],
            'attachment'=>$this->config['attachment'],
            'image_reduce'=>$this->config['image_reduce']
        );
        $siteid = $this->config['siteid'];
        $catid = $this->config['catid'];
        $userid = $this->config['userid'];
        $isadmin = $this->config['isadmin'];
        $roleid = $this->config['roleid'];
        $groupid = $this->config['groupid'];
        $allowFiles = $this->config['musicManagerAllowFiles'];
        $listSize = $this->config['musicManagerListSize'];
        $path = $this->config['musicManagerListPath'];
        break;
    /* 列出图片 */
    case 'listimage':
    default:
        $config = array(
            'siteid'=>$this->config['siteid'],
            'module'=>$this->config['module'],
            'catid'=>$this->config['catid'],
            'userid'=>$this->config['userid'],
            'isadmin'=>$this->config['isadmin'],
            'roleid'=>$this->config['roleid'],
            'groupid'=>$this->config['groupid'],
            'is_wm'=>$this->config['is_wm'],
            'is_esi'=>$this->config['is_esi'],
            'attachment'=>$this->config['attachment'],
            'image_reduce'=>$this->config['image_reduce']
        );
        $siteid = $this->config['siteid'];
        $catid = $this->config['catid'];
        $userid = $this->config['userid'];
        $isadmin = $this->config['isadmin'];
        $roleid = $this->config['roleid'];
        $groupid = $this->config['groupid'];
        $allowFiles = $this->config['imageManagerAllowFiles'];
        $listSize = $this->config['imageManagerListSize'];
        $path = $this->config['imageManagerListPath'];
}
$allowFiles = explode('.', join("", $allowFiles));
if (!$allowFiles[0]) {
    unset($allowFiles[0]);
}

/* 获取参数 */
$size = pc_base::load_sys_class('input')->get('size') ? html2code(pc_base::load_sys_class('input')->get('size')) : $listSize;
$start = pc_base::load_sys_class('input')->get('start') ? html2code(pc_base::load_sys_class('input')->get('start')) : 0;
$end = $start + $size;

/* 获取文件列表 */
$thisdb = pc_base::load_model('attachment_model');
$where = array('fileext'=>$allowFiles, 'module<>'=>'member', 'siteid'=>$siteid);
if ($isadmin && $roleid && cleck_admin($roleid)) {$where2 = array();} else {$where2 = array('isadmin'=>(int)$isadmin, 'userid'=>(int)$userid);}
$where = dr_array22array($where, $where2);
$data = $thisdb->select($where,'*','','uploadtime desc');
$total = $thisdb->count($where);
$files = array();
if ($data) {
    $index = 0;
    foreach ($data as $t) {
        if ($index >= $start && $index < $end) {
            $files[] = array(
                'id'=> $t['aid'],
                'url'=> dr_get_file_url($t),
                'name'=> $t['filename'],
                'original'=> $t['filename'],
                'mtime'=> $t['uploadtime']
            );
        }
        $index++;
    }
}

if (!$total) {
    return json_encode(array(
        "state" => "no match file",
        "list" => array(),
        "start" => $start,
        "total" => 0
    ), JSON_UNESCAPED_UNICODE);
}

/* 返回数据 */
$result = json_encode(array(
    "state" => "SUCCESS",
    "list" => $files,
    "start" => $start,
    "total" => $total
), JSON_UNESCAPED_UNICODE);

return $result;