<?php

/**
 * 安装程序
 */

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);
ini_set('display_errors', 1);
!defined('WEBPATH') && define('WEBPATH', dirname(__FILE__).'/');
!defined('WRITEPATH') && define('WRITEPATH', WEBPATH.'caches/');

$rt = array();
// 判断环境
if (version_compare(PHP_VERSION, '7.0.0') < 0) {
    $rt[] = echo_msg('PHP版本要求：7.0及以上，当前'.PHP_VERSION);
}

// GD库判断
if (!function_exists('imagettftext')) {
    $rt[] = echo_msg('PHP扩展库：GD库未安装或GD库版本太低');
}
if (!extension_loaded('curl')) {
    $rt[] = echo_msg('PHP扩展库：CURL未安装');
}
if (!extension_loaded('json')) {
    $rt[] = echo_msg('PHP扩展库：JSON未安装');
}
if (!extension_loaded('mbstring')) {
    $rt[] = echo_msg('PHP扩展库：mbstring未安装');
}
if (!extension_loaded('xml')) {
    $rt[] = echo_msg('PHP扩展库：xml未安装');
}

$mysqli = function_exists('mysqli_init') ? mysqli_init() : 0;
if (!$mysqli) {
    $rt[] = echo_msg('PHP环境必须启用Mysqli扩展');
}

$post = intval(@ini_get("post_max_size"));
$file = intval(@ini_get("upload_max_filesize"));

if ($file > $post) {
    $rt[] = echo_msg('系统配置不合理，post_max_size值('.$post.')必须大于upload_max_filesize值('.$file.')');
}
if ($file < 10) {
    $rt[] = echo_msg('系统环境只允许上传'.$file.'MB文件，可以设置upload_max_filesize值提升上传大小');
}
if ($post < 10) {
    $rt[] = echo_msg('系统环境要求每次发布内容不能超过'.$post.'MB（含文件），可以设置post_max_size值提升发布大小');
}

if (!function_exists('mb_substr')) {
    $rt[] = echo_msg('PHP不支持mbstring扩展，必须开启');
}
if (!function_exists('curl_init')) {
    $rt[] = echo_msg('PHP不支持CURL扩展，必须开启');
}
if (!function_exists('mb_convert_encoding')) {
    $rt[] = echo_msg('PHP的mb函数不支持，无法使用百度关键词接口');
}
if (!function_exists('imagecreatetruecolor')) {
    $rt[] = echo_msg('PHP的GD库版本太低，无法支持验证码图片');
}
if (!function_exists('ini_get')) {
    $rt[] = echo_msg('系统函数ini_get未启用，将无法获取到系统环境参数');
}
if (!function_exists('gzopen')) {
    $rt[] = echo_msg('zlib扩展未启用，必须开启');
}
if (!function_exists('gzinflate')) {
    $rt[] = echo_msg('函数gzinflate未启用，必须开启');
}
if (!function_exists('fsockopen')) {
    $rt[] = echo_msg('PHP不支持fsockopen，可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等');
}
if (!function_exists('openssl_open')) {
    $rt[] = echo_msg('PHP不支持openssl，可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等');
}
if (!ini_get('allow_url_fopen')) {
    $rt[] = echo_msg('allow_url_fopen未启用，远程图片无法保存、网络图片无法上传、可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等');
}
if (!class_exists('ZipArchive')) {
    $rt[] = echo_msg('php_zip扩展未开启，无法使用解压缩功能');
}

// 判断目录权限
foreach (array(
             WRITEPATH,
             WRITEPATH.'configs/',
             WRITEPATH.'caches_admin/',
             WRITEPATH.'caches_attach/',
             WRITEPATH.'caches_commons/',
             WRITEPATH.'caches_content/',
             WRITEPATH.'caches_linkage/',
             WRITEPATH.'caches_member/',
             WRITEPATH.'caches_model/',
             WRITEPATH.'caches_scan/',
             WRITEPATH.'caches_template/',
             WRITEPATH.'caches_tpl_data/',
             WRITEPATH.'poster_js/',
             WRITEPATH.'vote_js/',
             WRITEPATH.'sessions/',
             WEBPATH.'html/',
             WEBPATH.'uploadfile/',
         ) as $t) {
    if (!dr_check_put_path($t)) {
        $rt[] = echo_msg('目录（'.$t.'）不可写');
    }
}
if ($rt) {
    foreach ($rt as $t) {
        echo $t;
    }
} else {
    header('Location: install');
}

// 检查目录权限
function dr_check_put_path($dir) {
    if (!$dir) {
        return 0;
    } elseif (!is_dir($dir)) {
        return 0;
    }
    $size = @file_put_contents($dir.'test.html', 'test');
    if ($size === false) {
        return 0;
    } else {
        @unlink($dir.'test.html');
        return 1;
    }
}

/**
 * 将路径进行安全转换变量模式
 */
function safe_replace_path($path) {
    foreach (array(
                 WRITEPATH,
                 WRITEPATH.'configs/',
                 WRITEPATH.'caches_admin/',
                 WRITEPATH.'caches_attach/',
                 WRITEPATH.'caches_commons/',
                 WRITEPATH.'caches_content/',
                 WRITEPATH.'caches_linkage/',
                 WRITEPATH.'caches_member/',
                 WRITEPATH.'caches_model/',
                 WRITEPATH.'caches_scan/',
                 WRITEPATH.'caches_template/',
                 WRITEPATH.'caches_tpl_data/',
                 WRITEPATH.'poster_js/',
                 WRITEPATH.'vote_js/',
                 WRITEPATH.'sessions/',
                 WEBPATH.'html/',
                 WEBPATH.'uploadfile/',
             ) as $t) {
        $path = str_replace('（'.$t.'）', '', $path);
    }
    return $path;
}

// 输出
function echo_msg($msg) {
    $str .= '<div style="border-bottom: 1px dashed #9699a2; padding: 10px;">';
    $str .= '<a href="https://www.baidu.com/s?ie=UTF-8&wd='.urlencode(safe_replace_path($msg)).'" target="_blank" style="color:red;text-decoration:none;">'.$msg.'</a>';
    $str .= '</div>';
    return $str;
}