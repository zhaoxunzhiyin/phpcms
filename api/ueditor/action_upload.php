<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 上传附件和上传视频
 */
include "Uploader.class.php";

/* 上传配置 */
$base64 = "upload";
switch (html2code(pc_base::load_sys_class('input')->get('action'))) {
    case 'uploadimage':
        $config = array(
            'siteid'=>$CONFIG['siteid'],
            'module'=>$CONFIG['module'],
            'catid'=>$CONFIG['catid'],
            'userid'=>$CONFIG['userid'],
            'isadmin'=>$CONFIG['isadmin'],
            'groupid'=>$CONFIG['groupid'],
            'is_wm'=>$CONFIG['is_wm'],
            'is_esi'=>$CONFIG['is_esi'],
            'attachment'=>intval($CONFIG['attachment']),
            'image_reduce'=>$CONFIG['image_reduce'],
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
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
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
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
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadmusic':
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
            "pathFormat" => $CONFIG['musicPathFormat'],
            "maxSize" => $CONFIG['musicMaxSize'],
            "allowFiles" => $CONFIG['musicAllowFiles']
        );
        $fieldName = $CONFIG['musicFieldName'];
        break;
    case 'uploadword':
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
            "pathFormat" => $CONFIG['wordPathFormat'],
            "maxSize" => $CONFIG['wordMaxSize'],
            "allowFiles" => $CONFIG['wordAllowFiles']
        );
        $fieldName = $CONFIG['wordFieldName'];
        break;
    case 'uploadfile':
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
            'image_reduce'=>$CONFIG['image_reduce'],
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
    case 'uploadscreen':
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
            "pathFormat" => $CONFIG['snapscreenPathFormat'],
            "maxSize" => $CONFIG['snapscreenMaxSize'],
            "allowFiles" => $CONFIG['snapscreenAllowFiles']
        );
        $fieldName = $CONFIG['snapscreenFieldName'];
        break;
}

/* 生成上传实例对象并完成上传 */
$up = new Uploader($fieldName, $config, $base64);

/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */
if (html2code(pc_base::load_sys_class('input')->get('action'))=='uploadword') {
    $cache_file = $up->getFileInfo();
    if ($cache_file['url']) {
        $html = readWordToHtml(str_replace(SYS_UPLOAD_URL, SYS_UPLOAD_PATH, $cache_file['url']), $config['module'], $config['isadmin'], $config['userid'], $config['catid'], $config['siteid'], $config['is_wm'], $config['attachment'], $config['image_reduce'], md5(FC_NOW_URL.pc_base::load_sys_class('input')->get_user_agent().pc_base::load_sys_class('input')->ip_address().$config['userid']));
        return json_encode(array(
            'status'=> 1,
            'msg'=> '',
            'data'=> $html
        ));
    } else {
        return json_encode(array(
            'status'=> 0,
            'msg'=> $up->getFileInfo()['state']
        ));
    }
} else {
    return json_encode($up->getFileInfo(), JSON_UNESCAPED_UNICODE);
}