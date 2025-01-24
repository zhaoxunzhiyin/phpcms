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
            'siteid'=>$this->config['siteid'],
            'module'=>$this->config['module'],
            'catid'=>$this->config['catid'],
            'userid'=>$this->config['userid'],
            'isadmin'=>$this->config['isadmin'],
            'groupid'=>$this->config['groupid'],
            'is_wm'=>$this->config['is_wm'],
            'is_esi'=>$this->config['is_esi'],
            'attachment'=>intval($this->config['attachment']),
            'image_reduce'=>$this->config['image_reduce'],
            "pathFormat" => $this->config['imagePathFormat'],
            "maxSize" => $this->config['imageMaxSize'],
            "allowFiles" => $this->config['imageAllowFiles']
        );
        $fieldName = $this->config['imageFieldName'];
        break;
    case 'uploadscrawl':
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
            "pathFormat" => $this->config['scrawlPathFormat'],
            "maxSize" => $this->config['scrawlMaxSize'],
            "allowFiles" => $this->config['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $this->config['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
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
            "pathFormat" => $this->config['videoPathFormat'],
            "maxSize" => $this->config['videoMaxSize'],
            "allowFiles" => $this->config['videoAllowFiles']
        );
        $fieldName = $this->config['videoFieldName'];
        break;
    case 'uploadmusic':
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
            "pathFormat" => $this->config['musicPathFormat'],
            "maxSize" => $this->config['musicMaxSize'],
            "allowFiles" => $this->config['musicAllowFiles']
        );
        $fieldName = $this->config['musicFieldName'];
        break;
    case 'uploadword':
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
            "pathFormat" => $this->config['wordPathFormat'],
            "maxSize" => $this->config['wordMaxSize'],
            "allowFiles" => $this->config['wordAllowFiles']
        );
        $fieldName = $this->config['wordFieldName'];
        break;
    case 'uploadfile':
    default:
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
            "pathFormat" => $this->config['filePathFormat'],
            "maxSize" => $this->config['fileMaxSize'],
            "allowFiles" => $this->config['fileAllowFiles']
        );
        $fieldName = $this->config['fileFieldName'];
        break;
    case 'uploadscreen':
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
            "pathFormat" => $this->config['snapscreenPathFormat'],
            "maxSize" => $this->config['snapscreenMaxSize'],
            "allowFiles" => $this->config['snapscreenAllowFiles']
        );
        $fieldName = $this->config['snapscreenFieldName'];
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