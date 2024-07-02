<?php
defined('IN_CMS') or exit('No permission resources.');

header('Access-Control-Allow-Origin:*'); //临时处理，后面在强化它
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');

chdir(__DIR__);

$siteid = intval(pc_base::load_sys_class('input')->get('siteid'));
if(!$siteid) $siteid = get_siteid();
$userid = param::get_session('userid') ? param::get_session('userid') : (param::get_cookie('_userid') ? param::get_cookie('_userid') : param::get_cookie('userid'));
$isadmin = param::get_session('roleid') ? 1 : 0;
$groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
$filename = (SYS_ATTACHMENT_FILE ? $siteid.'/' : '').dr_site_value('filename', $siteid);
$imageAllowFiles = dr_site_value('imageAllowFiles', $siteid) ? '.'.dr_site_value('imageAllowFiles', $siteid) : '';
$imageAllowFiles = str_replace('|','|.',$imageAllowFiles);
$imageAllowFiles = explode('|',$imageAllowFiles);
$catcherAllowFiles = dr_site_value('catcherAllowFiles', $siteid) ? '.'.dr_site_value('catcherAllowFiles', $siteid) : '';
$catcherAllowFiles = str_replace('|','|.',$catcherAllowFiles);
$catcherAllowFiles = explode('|',$catcherAllowFiles);
$videoAllowFiles = dr_site_value('videoAllowFiles', $siteid) ? '.'.dr_site_value('videoAllowFiles', $siteid) : '';
$videoAllowFiles = str_replace('|','|.',$videoAllowFiles);
$videoAllowFiles = explode('|',$videoAllowFiles);
$musicAllowFiles = dr_site_value('musicAllowFiles', $siteid) ? '.'.dr_site_value('musicAllowFiles', $siteid) : '';
$musicAllowFiles = str_replace('|','|.',$musicAllowFiles);
$musicAllowFiles = explode('|',$musicAllowFiles);
$fileAllowFiles = dr_site_value('fileAllowFiles', $siteid) ? '.'.dr_site_value('fileAllowFiles', $siteid) : '';
$fileAllowFiles = str_replace('|','|.',$fileAllowFiles);
$fileAllowFiles = explode('|',$fileAllowFiles);
$imageManagerAllowFiles = dr_site_value('imageManagerAllowFiles', $siteid) ? '.'.dr_site_value('imageManagerAllowFiles', $siteid) : '';
$imageManagerAllowFiles = str_replace('|','|.',$imageManagerAllowFiles);
$imageManagerAllowFiles = explode('|',$imageManagerAllowFiles);
$fileManagerAllowFiles = dr_site_value('fileManagerAllowFiles', $siteid) ? '.'.dr_site_value('fileManagerAllowFiles', $siteid) : '';
$fileManagerAllowFiles = str_replace('|','|.',$fileManagerAllowFiles);
$fileManagerAllowFiles = explode('|',$fileManagerAllowFiles);
$videoManagerAllowFiles = dr_site_value('videoManagerAllowFiles', $siteid) ? '.'.dr_site_value('videoManagerAllowFiles', $siteid) : '';
$videoManagerAllowFiles = str_replace('|','|.',$videoManagerAllowFiles);
$videoManagerAllowFiles = explode('|',$videoManagerAllowFiles);
$musicManagerAllowFiles = dr_site_value('musicManagerAllowFiles', $siteid) ? '.'.dr_site_value('musicManagerAllowFiles', $siteid) : '';
$musicManagerAllowFiles = str_replace('|','|.',$musicManagerAllowFiles);
$musicManagerAllowFiles = explode('|',$musicManagerAllowFiles);

$CONFIG = array (
    'siteid'=>$siteid,
    'module'=>trim(pc_base::load_sys_class('input')->get('module')),
    'catid'=>intval(pc_base::load_sys_class('input')->get('catid')),
    'userid'=>intval($userid),
    'isadmin'=>intval($isadmin),
    'groupid'=>intval($groupid),
    'is_wm'=>intval(pc_base::load_sys_class('input')->get('is_wm')),
    'is_esi'=>intval(pc_base::load_sys_class('input')->get('is_esi')),
    'attachment'=>intval(pc_base::load_sys_class('input')->get('attachment')),
    'image_reduce'=>intval(pc_base::load_sys_class('input')->get('image_reduce')),
    /* 上传图片配置项 */
    'imageActionName'=>'uploadimage', /* 执行上传图片的action名称 */
    'imageFieldName'=>'upfile', /* 提交的图片表单名称 */
    'imageMaxSize'=>(intval(dr_site_value('imageMaxSize', $siteid)) ? intval(dr_site_value('imageMaxSize', $siteid)) : intval(dr_site_value('upload_maxsize', $siteid))) * 1024 *1024, /* 上传大小限制，单位B */
    'imageAllowFiles'=>$imageAllowFiles, /* 上传图片格式显示 */
    'imageCompressEnable'=>false, /* 是否压缩图片,默认是true */
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
    'scrawlMaxSize'=>intval(dr_site_value('upload_maxsize', $siteid)) * 1024 *1024, /* 上传大小限制，单位B */
    'scrawlUrlPrefix'=>'', /* 图片访问路径前缀 */
    'scrawlInsertAlign'=>'none',

    /* 截图工具上传 */
    'snapscreenActionName'=>'uploadscreen', /* 执行上传截图的action名称 */
    'snapscreenFieldName'=>'upfile', /* 提交的截图表单名称 */
    'snapscreenMaxSize'=>intval(dr_site_value('upload_maxsize', $siteid)) * 1024 *1024, /* 上传大小限制，单位B */
    'snapscreenAllowFiles'=>$alowexts, /* 上传图片格式显示 */
    'snapscreenCompressEnable'=>false, /* 是否压缩图片,默认是true */
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
    'catcherMaxSize'=>(intval(dr_site_value('catcherMaxSize', $siteid)) ? intval(dr_site_value('catcherMaxSize', $siteid)) : intval(dr_site_value('upload_maxsize', $siteid))) * 1024 *1024, /* 上传大小限制，单位B */
    'catcherAllowFiles'=>$catcherAllowFiles, /* 抓取图片格式显示 */

    /* 上传视频配置 */
    'videoActionName'=>'uploadvideo', /* 执行上传视频的action名称 */
    'videoFieldName'=>'upfile', /* 提交的视频表单名称 */
    'videoPathFormat'=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    'videoUrlPrefix'=>'', /* 视频访问路径前缀 */
    'videoMaxSize'=>(intval(dr_site_value('videoMaxSize', $siteid)) ? intval(dr_site_value('videoMaxSize', $siteid)) : intval(dr_site_value('upload_maxsize', $siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认100MB */
    'videoAllowFiles'=>$videoAllowFiles, /* 上传视频格式显示 */

    /* 上传音频配置 */
    'musicActionName'=>'uploadmusic', /* 执行上传音频的action名称 */
    'musicFieldName'=>'upfile', /* 提交的音频表单名称 */
    'musicPathFormat'=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    'musicUrlPrefix'=>'', /* 音频访问路径前缀 */
    'musicMaxSize'=>(intval(dr_site_value('musicMaxSize', $siteid)) ? intval(dr_site_value('musicMaxSize', $siteid)) : intval(dr_site_value('upload_maxsize', $siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认20MB */
    'musicAllowFiles'=>$musicAllowFiles, /* 上传音频格式显示 */

    /* 上传文件配置 */
    "fileActionName"=>"uploadfile", /* controller里,执行上传视频的action名称 */
    "fileFieldName"=>"upfile", /* 提交的文件表单名称 */
    "filePathFormat"=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    "fileUrlPrefix"=>'', /* 文件访问路径前缀 */
    "fileMaxSize"=>(intval(dr_site_value('fileMaxSize', $siteid)) ? intval(dr_site_value('fileMaxSize', $siteid)) : intval(dr_site_value('upload_maxsize', $siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认50MB */
    "fileAllowFiles"=>$fileAllowFiles, /* 上传文件格式显示 */

    /* 上传Word配置 */
    "wordActionName"=>"uploadword", /* controller里,执行上传视频的action名称 */
    "wordFieldName"=>"upfile", /* 提交的文件表单名称 */
    "wordPathFormat"=>$filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
    "wordUrlPrefix"=>'', /* 文件访问路径前缀 */
    "wordMaxSize"=>(intval(dr_site_value('fileMaxSize', $siteid)) ? intval(dr_site_value('fileMaxSize', $siteid)) : intval(dr_site_value('upload_maxsize', $siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认50MB */
    "wordAllowFiles"=>$fileAllowFiles, /* 上传文件格式显示 */

    /* 列出指定目录下的图片 */
    'imageManagerActionName'=>'listimage', /* 执行图片管理的action名称 */
    'imageManagerListPath'=>'', /* 指定要列出图片的目录 */
    'imageManagerListSize'=>intval(dr_site_value('imageManagerListSize', $siteid)), /* 每次列出文件数量 */
    'imageManagerUrlPrefix'=>'', /* 图片访问路径前缀 */
    'imageManagerInsertAlign'=>'none', /* 插入的图片浮动方式 */
    'imageManagerAllowFiles'=>$imageAllowFiles, /* 列出的文件类型 */

    /* 列出指定目录下的文件 */
    'fileManagerActionName'=>'listfile', /* 执行文件管理的action名称 */
    'fileManagerListPath'=>'', /* 指定要列出文件的目录 */
    'fileManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
    'fileManagerListSize'=>intval(dr_site_value('fileManagerListSize', $siteid)), /* 每次列出文件数量 */
    'fileManagerAllowFiles'=>$fileAllowFiles, /* 列出的文件类型 */

    /* 列出指定目录下的视频 */
    'videoManagerActionName'=>'listvideo', /* 执行文件管理的action名称 */
    'videoManagerListPath'=>'', /* 指定要列出文件的目录 */
    'videoManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
    'videoManagerListSize'=>intval(dr_site_value('videoManagerListSize', $siteid)), /* 每次列出文件数量 */
    'videoManagerAllowFiles'=>$videoAllowFiles, /* 列出的文件类型 */

    /* 列出指定目录下的音频 */
    'musicManagerActionName'=>'listmusic', /* 执行文件管理的action名称 */
    'musicManagerListPath'=>'', /* 指定要列出文件的目录 */
    'musicManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
    'musicManagerListSize'=>intval(dr_site_value('musicManagerListSize', $siteid)), /* 每次列出文件数量 */
    'musicManagerAllowFiles'=>$musicAllowFiles /* 列出的文件类型 */
);
$action = pc_base::load_sys_class('input')->get('action');

$error = check_upload_auth($isadmin, $userid, $groupid);
if (!$error) {
    switch ($action) {
        case 'config':
            $result = json_encode($CONFIG, JSON_UNESCAPED_UNICODE);
            break;

        /* 上传图片 */
        case 'uploadimage':
        /* 上传涂鸦 */
        case 'uploadscrawl':
        /* 上传视频 */
        case 'uploadvideo':
        /* 上传音频 */
        case 'uploadmusic':
        /* 上传文件 */
        case 'uploadfile':
        /* 上传文件 */
        case 'uploadword':
            $result = include("ueditor/action_upload.php");
            break;

        /* 上传截图 */
        case 'uploadscreen':
            $result = json_encode(array(
                'state'=> '本图标功能已禁用，请使用截图软件截图后，再粘贴进编辑器中'
            ), JSON_UNESCAPED_UNICODE);
            break;

        /* 列出图片 */
        case 'listimage':
        /* 列出文件 */
        case 'listfile':
        /* 列出视频 */
        case 'listvideo':
        /* 列出音频 */
        case 'listmusic':
            $result = include("ueditor/action_list.php");
            break;

        /* 抓取远程文件 */
        case 'catchimage':
            $result = include("ueditor/action_crawler.php");
            break;

        /* 删除文件 */
        case 'deletefile':
            cleck_admin(param::get_session('roleid')) ? $result = include("ueditor/action_delete.php") : $result = json_encode(array('state'=> '需要超级管理员账号操作'), JSON_UNESCAPED_UNICODE);
            break;

        default:
            $result = json_encode(array(
                'state'=> '请求地址出错'
            ), JSON_UNESCAPED_UNICODE);
            break;
    }
} elseif ($action == 'config') {
    $result = json_encode($CONFIG, JSON_UNESCAPED_UNICODE);
} else {
    $result = json_encode(array(
        'state'=> $error ? $error : '请登录在操作'
    ), JSON_UNESCAPED_UNICODE);
}

/* 输出结果 */
if (pc_base::load_sys_class('input')->get("callback")) {
    if (preg_match("/^[\w_]+$/", pc_base::load_sys_class('input')->get("callback"))) {
        echo html2code(pc_base::load_sys_class('input')->get("callback")) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ), JSON_UNESCAPED_UNICODE);
    }
} else {
    echo $result;
}
exit;

// 验证权限脚本
function check_upload_auth($isadmin = 0, $userid = 0, $groupid = 8) {

    $grouplist = getcache('grouplist', 'member');

    $error = '';
    if (defined('SYS_CSRF') && SYS_CSRF && csrf_hash() != (string)$_GET['token']) {
        $error = L('跨站验证禁止上传文件');
    } elseif ($isadmin) {
        return;
    } elseif (!$isadmin && !$grouplist[$groupid]['allowattachment']) {
        $error = L('您的用户组不允许上传文件');
    } elseif (!$isadmin && !$userid) {
        $error = L('游客不允许上传文件');
    } elseif (!$isadmin && check_upload($userid, $isadmin, $groupid)) {
        $error = L('用户存储空间已满');
    }

    if ($error) {
        return L($error);
    }

    return;
}

// 验证附件上传权限，直接返回1 表示空间不够
function check_upload($uid, $isadmin = 0, $groupid = 8) {
    $grouplist = getcache('grouplist', 'member');
    if ($isadmin) {
        return;
    }
    // 获取用户总空间
    $total = abs((int)$grouplist[$groupid]['filesize']) * 1024 * 1024;
    if ($total) {
        // 判断空间是否满了
        $filesize = get_member_filesize($uid);
        if ($filesize >= $total) {
            return 1;
        }
    }
    return;
}

// 用户已经使用附件空间
function get_member_filesize($uid) {
    $db = pc_base::load_model('attachment_model');
    $db->query('SELECT sum(filesize) as filesize FROM `'.$db->dbprefix('attachment').'` where userid='.intval($uid).' and isadmin='.intval($isadmin));
    $row = $db->fetch_array();
    return intval($row[0]['filesize']);
}