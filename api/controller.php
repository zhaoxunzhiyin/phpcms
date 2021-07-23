<?php
defined('IN_CMS') or exit('No permission resources.');

header('Access-Control-Allow-Origin:*'); //临时处理，后面在强化它
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');

chdir(__DIR__);

$session_storage = 'session_'.pc_base::load_config('system','session_storage');
pc_base::load_sys_class($session_storage);
$siteinfo = getcache('sitelist', 'commons');
$siteid = intval($input->get('siteid'));
if(!$siteid) $siteid = get_siteid() ? get_siteid() : 1 ;
$userid = $_SESSION['userid'] ? $_SESSION['userid'] : (param::get_cookie('_userid') ? param::get_cookie('_userid') : param::get_cookie('userid'));
$isadmin = $_SESSION['roleid'] ? 1 : 0;
$groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
$site_setting = string2array($siteinfo[$siteid]['setting']);
$filename = (SYS_ATTACHMENT_FILE ? $siteid.'/' : '').$site_setting['filename'];
$imageAllowFiles = ".".$site_setting['imageAllowFiles'];
$imageAllowFiles = str_replace("|","|.",$imageAllowFiles);
$imageAllowFiles = explode('|',$imageAllowFiles);
$catcherAllowFiles = ".".$site_setting['catcherAllowFiles'];
$catcherAllowFiles = str_replace("|","|.",$catcherAllowFiles);
$catcherAllowFiles = explode('|',$catcherAllowFiles);
$videoAllowFiles = ".".$site_setting['videoAllowFiles'];
$videoAllowFiles = str_replace("|","|.",$videoAllowFiles);
$videoAllowFiles = explode('|',$videoAllowFiles);
$fileAllowFiles = ".".$site_setting['fileAllowFiles'];
$fileAllowFiles = str_replace("|","|.",$fileAllowFiles);
$fileAllowFiles = explode('|',$fileAllowFiles);
$imageManagerAllowFiles = ".".$site_setting['imageManagerAllowFiles'];
$imageManagerAllowFiles = str_replace("|","|.",$imageManagerAllowFiles);
$imageManagerAllowFiles = explode('|',$imageManagerAllowFiles);
$fileManagerAllowFiles = ".".$site_setting['fileManagerAllowFiles'];
$fileManagerAllowFiles = str_replace("|","|.",$fileManagerAllowFiles);
$fileManagerAllowFiles = explode('|',$fileManagerAllowFiles);
$videoManagerAllowFiles = ".".$site_setting['videoManagerAllowFiles'];
$videoManagerAllowFiles = str_replace("|","|.",$videoManagerAllowFiles);
$videoManagerAllowFiles = explode('|',$videoManagerAllowFiles);

$CONFIG = array (
    'siteid'=>$siteid,
    'module'=>trim($input->get('module')),
    'catid'=>intval($input->get('catid')),
    'userid'=>intval($userid),
    'isadmin'=>intval($isadmin),
    'groupid'=>intval($groupid),
    'is_wm'=>intval($input->get('is_wm')),
    'is_esi'=>intval($input->get('is_esi')),
    'attachment'=>intval($input->get('attachment')),
    'image_reduce'=>intval($input->get('image_reduce')),
    /* 上传图片配置项 */
    'imageActionName'=>'uploadimage', /* 执行上传图片的action名称 */
    'imageFieldName'=>'upfile', /* 提交的图片表单名称 */
    'imageMaxSize'=>$site_setting['imageMaxSize'] * 1000, /* 上传大小限制，单位B */
    'imageAllowFiles'=>$imageAllowFiles, /* 上传图片格式显示 */
    'imageCompressEnable'=>true, /* 是否压缩图片,默认是true */
    'imageCompressBorder'=>1600, /* 图片压缩最长边限制 */
    'imageInsertAlign'=>'none', /* 插入的图片浮动方式 */
    'imageUrlPrefix'=>'', /* 图片访问路径前缀 */
    'imagePathFormat'=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
                                /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
                                /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
                                /* {time} 会替换成时间戳 */
                                /* {yyyy} 会替换成四位年份 */
                                /* {yy} 会替换成两位年份 */
                                /* {mm} 会替换成两位月份 */
                                /* {dd} 会替换成两位日期 */
                                /* {hh} 会替换成两位小时 */
                                /* {ii} 会替换成两位分钟 */
                                /* {ss} 会替换成两位秒 */
                                /* 非法字符 \ =>* ? ' < > | */
                                /* 具请体看线上文档=>fex.baidu.com/ueditor/#use-format_upload_filename */

    /* 涂鸦图片上传配置项 */
    'scrawlActionName'=>'uploadscrawl', /* 执行上传涂鸦的action名称 */
    'scrawlFieldName'=>'upfile', /* 提交的图片表单名称 */
    'scrawlPathFormat'=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    'scrawlMaxSize'=>$site_setting['upload_maxsize'] * 1000, /* 上传大小限制，单位B */
    'scrawlUrlPrefix'=>'', /* 图片访问路径前缀 */
    'scrawlInsertAlign'=>'none',

    /* 截图工具上传 */
    'snapscreenActionName'=>'uploadscreen', /* 执行上传截图的action名称 */
    'snapscreenFieldName'=>'upfile', /* 提交的截图表单名称 */
    'snapscreenMaxSize'=>$site_setting['upload_maxsize'] * 1000, /* 上传大小限制，单位B */
    'snapscreenAllowFiles'=>$alowexts, /* 上传图片格式显示 */
    'snapscreenCompressEnable'=>true, /* 是否压缩图片,默认是true */
    'snapscreenCompressBorder'=>1600, /* 图片压缩最长边限制 */
    'snapscreenInsertAlign'=>'none', /* 插入的图片浮动方式 */
    'snapscreenUrlPrefix'=>'', /* 图片访问路径前缀 */
    'snapscreenPathFormat'=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */

    /* 抓取远程图片配置 */
    'catcherLocalDomain'=>array('127.0.0.1', 'localhost', 'img.baidu.com'),
    'catcherActionName'=>'catchimage', /* 执行抓取远程图片的action名称 */
    'catcherFieldName'=>'source', /* 提交的图片列表表单名称 */
    'catcherPathFormat'=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    'catcherUrlPrefix'=>'', /* 图片访问路径前缀 */
    'catcherMaxSize'=>$site_setting['catcherMaxSize'] * 1000, /* 上传大小限制，单位B */
    'catcherAllowFiles'=>$catcherAllowFiles, /* 抓取图片格式显示 */

    /* 上传视频配置 */
    'videoActionName'=>'uploadvideo', /* 执行上传视频的action名称 */
    'videoFieldName'=>'upfile', /* 提交的视频表单名称 */
    'videoPathFormat'=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    'videoUrlPrefix'=>'', /* 视频访问路径前缀 */
    'videoMaxSize'=>$site_setting['videoMaxSize'] * 1000, /* 上传大小限制，单位B，默认100MB */
    'videoAllowFiles'=>$videoAllowFiles, /* 上传视频格式显示 */

    /* 上传文件配置 */
    "fileActionName"=>"uploadfile", /* controller里,执行上传视频的action名称 */
    "fileFieldName"=>"upfile", /* 提交的文件表单名称 */
    "filePathFormat"=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    "fileUrlPrefix"=>'', /* 文件访问路径前缀 */
    "fileMaxSize"=>$site_setting['fileMaxSize'] * 1000, /* 上传大小限制，单位B，默认50MB */
    "fileAllowFiles"=>$fileAllowFiles, /* 上传文件格式显示 */

    /* 列出指定目录下的图片 */
    'imageManagerActionName'=>'listimage', /* 执行图片管理的action名称 */
    'imageManagerListPath'=>'', /* 指定要列出图片的目录 */
    'imageManagerListSize'=>$site_setting['imageManagerListSize'], /* 每次列出文件数量 */
    'imageManagerUrlPrefix'=>'', /* 图片访问路径前缀 */
    'imageManagerInsertAlign'=>'none', /* 插入的图片浮动方式 */
    'imageManagerAllowFiles'=>$imageAllowFiles, /* 列出的文件类型 */

    /* 列出指定目录下的文件 */
    'fileManagerActionName'=>'listfile', /* 执行文件管理的action名称 */
    'fileManagerListPath'=>'', /* 指定要列出文件的目录 */
    'fileManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
    'fileManagerListSize'=>$site_setting['fileManagerListSize'], /* 每次列出文件数量 */
    'fileManagerAllowFiles'=>$fileAllowFiles, /* 列出的文件类型 */

    /* 列出指定目录下的视频 */
    'videoManagerActionName'=>'listvideo', /* 执行文件管理的action名称 */
    'videoManagerListPath'=>'', /* 指定要列出文件的目录 */
    'videoManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
    'videoManagerListSize'=>$site_setting['videoManagerListSize'], /* 每次列出文件数量 */
    'videoManagerAllowFiles'=>$videoAllowFiles /* 列出的文件类型 */
);
$action = $input->get('action');

if (intval($userid)) {
    switch ($action) {
        case 'config':
            $result =  json_encode($CONFIG, JSON_UNESCAPED_UNICODE);
            break;

        /* 上传图片 */
        case 'uploadimage':
        /* 上传涂鸦 */
        case 'uploadscrawl':
        /* 上传视频 */
        case 'uploadvideo':
        /* 上传文件 */
        case 'uploadfile':
        /* 上传截图 */
        case 'uploadscreen':
            $result = include("ueditor/action_upload.php");
            break;

        /* 列出图片 */
        case 'listimage':
        /* 列出文件 */
        case 'listfile':
        /* 列出视频 */
        case 'listvideo':
            $result = include("ueditor/action_list.php");
            break;

        /* 抓取远程文件 */
        case 'catchimage':
            $result = include("ueditor/action_crawler.php");
            break;

        /* 删除文件 */
        case 'deleteimage':
            $result = include("ueditor/action_delete.php");
            break;

        default:
            $result = json_encode(array(
                'state'=> '请求地址出错'
            ), JSON_UNESCAPED_UNICODE);
            break;
    }
} elseif ($action == 'config') {
    $result = json_encode($CONFIG, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'uploadscreen') {
    $result = json_encode(array(
        'state'=> '本图标功能已禁用，请使用截图软件截图后，再粘贴进编辑器中'
    ), JSON_UNESCAPED_UNICODE);
} else {
    $result = json_encode(array(
        'state'=> '请登录在操作'
    ), JSON_UNESCAPED_UNICODE);
}

/* 输出结果 */
if ($input->get("callback")) {
    if (preg_match("/^[\w_]+$/", $input->get("callback"))) {
        echo htmlspecialchars($input->get("callback")) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ), JSON_UNESCAPED_UNICODE);
    }
} else {
    echo $result;
}
exit;