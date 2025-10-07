<?php

/**
 * 环境监测程序（正式上线后可删除本文件）
 */

define('IN_CMS', TRUE);
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors', 1);

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('CMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
!defined('CACHE_PATH') && define('CACHE_PATH', CMS_PATH.'caches'.DIRECTORY_SEPARATOR);
!defined('CONFIGPATH') && define('CONFIGPATH', CACHE_PATH.'configs'.DIRECTORY_SEPARATOR);

if (is_file(CONFIGPATH.'version.php')) {
    $vcfg = require CONFIGPATH.'version.php';
    echo_msg(1, '当前CMS版本：'.$vcfg['cms_version'].'（'.$vcfg['cms_downtime'].'）- '.$vcfg['cms_updatetime']);
}

echo_msg(1, '当前脚本地址：'.$_SERVER['SCRIPT_NAME']);
echo_msg(1, '当前脚本路径：'.__FILE__);

if (preg_match('/[\x{4e00}-\x{9fff}]+/u', CMS_PATH)) {
    echo_msg(0, 'WEB目录['.CMS_PATH.']不允许出现中文或全角符号');
}

foreach (array(' ', '[', ']') as $t) {
    if (strpos(CMS_PATH, $t) !== false) {
        echo_msg(0, 'WEB目录['.CMS_PATH.']不允许出现'.($t ? $t : '空格').'符号');
    }
}

foreach (array(CMS_PATH.'index.php', CONFIGPATH.'database.php', CONFIGPATH.'rewrite.php', CONFIGPATH.'hooks.php', CONFIGPATH.'system.php' ) as $t) {
    if (is_file($t) && check_bom($t)) {
        echo_msg(0, '<font color=red>文件['.str_replace(CMS_PATH, '', $t).']编码存在严重问题</font>');
    }
}

foreach (array(CACHE_PATH.'caches_file/caches_data/', CACHE_PATH.'caches_template/', CACHE_PATH.'sessions/', CMS_PATH.'uploadfiles/' ) as $t) {
    if (is_dir($t) && !check_put_path($t)) {
        echo_msg(0, '<font color=red>目录['.str_replace(CMS_PATH, '', $t).']无法写入文件，请给可读可写权限：0777</font>');
    }
}

if (isset($_GET['log']) && $_GET['log']) {
    if (!is_file(CACHE_PATH.'error_log.php')) {
        exit('没有错误日志记录');
    }
    echo nl2br(file_get_contents(CACHE_PATH.'error_log.php'));
    exit;
} elseif (isset($_GET['daylog']) && $_GET['daylog']) {
    if (!is_file(CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d').'.php')) {
        exit('今天没有错误日志记录');
    }
    echo nl2br(file_get_contents(CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d').'.php'));
    exit;
} elseif (isset($_GET['phpinfo']) && $_GET['phpinfo']) {
    phpinfo();
    exit;
}

echo_msg(1, '客户端信息：'.$_SERVER['HTTP_USER_AGENT']);

// 判断环境
$min = '7.1.0';
if (version_compare(PHP_VERSION, $min) < 0) {
    echo_msg(0, "<font color=red>PHP版本建议在".$min."及以上，当前".PHP_VERSION."</font>");
} else {
    echo_msg(1, 'PHP版本要求：'.$min.'及以上，当前'.PHP_VERSION.'，<a style="color:blue;text-decoration:none;" href="'.SELF.'?phpinfo=true">查看环境</a>');
}

// GD库判断
if (!function_exists('imagettftext')) {
    echo_msg(0, 'PHP扩展库：GD库未安装或GD库版本太低');
}
if (! extension_loaded('curl')) {
    echo_msg(0, 'PHP扩展库：CURL未安装');
}
if (! extension_loaded('json')) {
    echo_msg(0, 'PHP扩展库：JSON未安装');
}
if (! extension_loaded('mbstring')) {
    echo_msg(0, 'PHP扩展库：mbstring未安装');
}
if (! extension_loaded('xml')) {
    echo_msg(0, 'PHP扩展库：xml未安装');
}
if (!function_exists('chmod')) {
    echo_msg(0, 'PHP函数chmod被禁用，需要开启');
}

if (is_file(CMS_PATH.'caches/configs/database.php')) {
    $db = require CMS_PATH.'caches/configs/database.php';
}

$mysqli = function_exists('mysqli_init') ? mysqli_init() : 0;
if (!$mysqli) {
    echo_msg(0, 'PHP环境必须启用Mysqli扩展');
}

if (isset($db['default']['hostname']) && $db['default']['hostname'] && strpos($db['default']['hostname'], '，') === false) {
    if (!@mysqli_real_connect($mysqli, $db['default']['hostname'], $db['default']['username'], $db['default']['password'])) {
        echo_msg(0, '['.mysqli_connect_errno().'] - ['.mysqli_connect_error().'] 无法连接到数据库服务器（'.$db['default']['hostname'].'），请检查用户名（'.$db['default']['username'].'）和密码（'.$db['default']['password'].'）是否正确');
    } elseif (!@mysqli_select_db($mysqli, $db['default']['database'])) {
        echo_msg(0, '指定的数据库（'.$db['default']['database'].'）不存在，请手动创建');
    } else {
        if ($result = mysqli_query($mysqli, "SELECT userid FROM ".$db['default']['tablepre']."member LIMIT 1")) {
            echo_msg(1, 'MySQL数据连接正常');
        } else {
            echo_msg(0, '数据库（'.$db['default']['database'].'）查询异常：'.mysqli_error($mysqli));
        }
    }
    if (strpos($db['default']['database'], '.') !== false) {
        echo_msg(0,  '数据库名称（'.$db['default']['database'].'）不规范，不能存在.号');
    }
    $version = mysqli_get_server_version($mysqli);
    if ($version) {
        if ($version > 50500) {
            echo_msg(1, 'MySQL版本要求：5.5及以上，当前'.substr($version, 0, 1).'.'.substr($version, 2));
        } else {
            echo_msg(0, 'MySQL版本要求：5.5及以上，当前'.substr($version, 0, 1).'.'.substr($version, 2));
        }
    }
    $rs = mysqli_query($mysqli, 'show engines');
    if ($rs) {
        $status = false;
        foreach($rs as $row){
            if($row['Engine'] == 'MyISAM' && ($row['Support'] == 'YES' || $row['Support'] == 'DEFAULT') ){
                $status = true;
            }
        }
        if (!$status) {
            echo_msg(0, 'MySQL不支持MyISAM存储引擎，无法安装');
        } else {
            echo_msg(1, 'MySQL支持MyISAM存储引擎');
        }
    }
    if (!mysqli_set_charset($mysqli, "utf8mb4")) {
        echo_msg(0, "MySQL不支持utf8mb4编码（".mysqli_error($mysqli)."）");
    }
    $mysqli && mysqli_close($mysqli);
}

if (!$version) {
    echo_msg(1, 'MySQL版本要求：5.5及以上');
}

$post = intval(@ini_get("post_max_size"));
$file = intval(@ini_get("upload_max_filesize"));

if ($file > $post) {
    echo_msg(1,'系统配置不合理，post_max_size值('.$post.')必须大于upload_max_filesize值('.$file.')');
}
if ($file < 10) {
    echo_msg(1,'系统环境只允许上传'.$file.'MB文件，可以设置upload_max_filesize值提升上传大小');
}
if ($post < 10) {
    echo_msg(1,'系统环境要求每次发布内容不能超过'.$post.'MB（含文件），可以设置post_max_size值提升发布大小');
}


if (!function_exists('mb_substr')) {
    echo_msg(0, 'PHP不支持mbstring扩展，必须开启');
}
if (!function_exists('curl_init')) {
    echo_msg(0, 'PHP不支持CURL扩展，必须开启');
}
if (!function_exists('mb_convert_encoding')) {
    echo_msg(0, 'PHP的mb函数不支持，无法使用百度关键词接口');
}
if (!function_exists('imagecreatetruecolor')) {
    echo_msg(0,'PHP的GD库版本太低，无法支持验证码图片');
}
if (!function_exists('ini_get')) {
    echo_msg(0, '系统函数ini_get未启用，将无法获取到系统环境参数');
}
if (!function_exists('gzopen')) {
    echo_msg(0,'zlib扩展未启用，必须开启');
}
if (!function_exists('gzinflate')) {
    echo_msg(0,'函数gzinflate未启用，必须开启');
}
if (!function_exists('fsockopen')) {
    echo_msg(0,'PHP不支持fsockopen，可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等');
}
if (!function_exists('openssl_open')) {
    echo_msg(0,'PHP不支持openssl，可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等');
}
if (!ini_get('allow_url_fopen')) {
    echo_msg(0,'allow_url_fopen未启用，远程图片无法保存、网络图片无法上传、可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录等');
}
if (!class_exists('ZipArchive')) {
    echo_msg(0,'php_zip扩展未开启，无法使用解压缩功能');
}

// 存在错误日志
if (is_file(CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d').'.php')) {
    $log = file_get_contents(CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d').'.php');
    echo_msg(1, '系统故障的错误日志记录：<a style="color:blue;text-decoration:none;" href="'.SELF.'?daylog=true">查看日志</a>');
}

// 存在错误日志
if (is_file(CACHE_PATH.'error_log.php')) {
    $log = file_get_contents(CACHE_PATH.'error_log.php');
    echo_msg(1, '系统故障的错误日志记录：<a style="color:blue;text-decoration:none;" href="'.SELF.'?log=true">查看日志</a>');
}

// 输出
function echo_msg($code, $msg) {
    echo '<div style="border-bottom: 1px dashed #9699a2; padding: 10px;">';
    if (!$code) {
        echo '<a href="https://www.baidu.com/s?ie=UTF-8&wd='.urlencode($msg).'" target="_blank" style="color:red;text-decoration:none;">'.$msg.'</a>';
    } else {
        echo '<font color=green>'.$msg.'</font>';
    }
    echo '</div>';
}
// 检查bom
function check_bom($filename) {
    $contents = file_get_contents($filename);
    $charset[1] = substr($contents, 0, 1);
    $charset[2] = substr($contents, 1, 1);
    $charset[3] = substr($contents, 2, 1);
    if ($charset[1] != '<') {
        return false;
    } elseif (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
        return true;
    } else {
        return false;
    };
}
// 检查目录权限
function check_put_path($dir) {

    $size = @file_put_contents($dir.'test.html', 'test');
    if ($size === false) {
        return 0;
    } else {
        @unlink($dir.'test.html');
        return 1;
    }
}

echo '<div style=" padding: 10px; color:blue">';
echo '如果以上提示文字是红色选项，就必须修改正确的环境配置 (*^▽^*) ，<font color="red">当网站正式上线后，请删除本文件吧~</font>';
echo '</div>';