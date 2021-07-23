<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取已上传的文件列表
 */
include "Uploader.class.php";

/* 判断类型 */
switch ($input->get('action')) {
    /* 列出文件 */
    case 'listfile':
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
            'image_reduce'=>$CONFIG['image_reduce']
        );
        $siteid = $CONFIG['siteid'];
        $catid = $CONFIG['catid'];
        $userid = $CONFIG['userid'];
        $isadmin = $CONFIG['isadmin'];
        $groupid = $CONFIG['groupid'];
        $allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        $path = $CONFIG['fileManagerListPath'];
        break;
    /* 列出文件 */
    case 'listvideo':
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
            'image_reduce'=>$CONFIG['image_reduce']
        );
        $siteid = $CONFIG['siteid'];
        $catid = $CONFIG['catid'];
        $userid = $CONFIG['userid'];
        $isadmin = $CONFIG['isadmin'];
        $groupid = $CONFIG['groupid'];
        $allowFiles = $CONFIG['videoManagerAllowFiles'];
        $listSize = $CONFIG['videoManagerListSize'];
        $path = $CONFIG['videoManagerListPath'];
        break;
    /* 列出图片 */
    case 'listimage':
    default:
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
            'image_reduce'=>$CONFIG['image_reduce']
        );
        $siteid = $CONFIG['siteid'];
        $catid = $CONFIG['catid'];
        $userid = $CONFIG['userid'];
        $isadmin = $CONFIG['isadmin'];
        $groupid = $CONFIG['groupid'];
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];
}
$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

$array_test = explode('|',$allowFiles);
$length = sizeof($array_test);
for($i=0;$i<$length;$i++){
    $s_str .= "'".$array_test[$i]."',";
}
$s_str = substr($s_str, 0, strlen($s_str) - 1);

/* 获取参数 */
$size = $input->get('size') ? htmlspecialchars($input->get('size')) : $listSize;
$start = $input->get('start') ? htmlspecialchars($input->get('start')) : 0;
$end = $start + $size;

/* 获取文件列表 */
$thisdb = pc_base::load_model('attachment_model');
//$where = " and catid=".$catid." and siteid=".$siteid;
$where = "fileext in (".$s_str.") ".$where." and userid=".$userid."";
$total = $thisdb->count($where);
$data = $thisdb->listinfo($where,'aid desc','',$total);
$files = array();
if ($data) {
    $index = 0;
    foreach ($data as $t) {
        if ($index >= $start && $index < $end) {
            $files[] = array(
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
        "total" => $total
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