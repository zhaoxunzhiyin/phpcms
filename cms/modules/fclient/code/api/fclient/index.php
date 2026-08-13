<?php

/**
 * 客户端通信
 */

//declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);

define('IN_CMS', TRUE);

define('CMS_PATH', dirname(dirname(dirname(__FILE__))).'/');

require 'func.php';
$sync = require 'sync.php';

if (!$_GET['id'] || !$_GET['sync']) {
    _json(0, '通信密钥验证为空');
} elseif ($_GET['id'] != md5($sync['id'])) {
    _json(0, '通信ID验证失败');
} elseif ($_GET['sync'] != $sync['sn']) {
    _json(0, '通信密钥验证失败');
} elseif (!is_file(CMS_PATH.'caches/configs/database.php')) {
    _json(0, '客户端网站数据配置文件不存在');
}

$db = require CMS_PATH.'caches/configs/database.php';

// 连接数据库
$mysqli = function_exists('mysqli_init') ? mysqli_init() : 0;
if (!$mysqli) {
    exit(_json(0, '客户端网站的PHP环境必须启用Mysqli扩展'));
} elseif (!@mysqli_real_connect($mysqli, $db['default']['hostname'], $db['default']['username'], $db['default']['password'])) {
    exit(_json(0, '客户端网站：[mysqli_real_connect] 无法连接到数据库服务器（'.$db['default']['hostname'].'），请检查用户名（'.$db['default']['username'].'）和密码（'.$db['default']['password'].'）是否正确'));
} elseif (!@mysqli_select_db($mysqli, $db['default']['database'])) {
    exit(_json(0, '客户端网站：指定的数据库（'.$db['default']['database'].'）不存在'));
}
mysqli_set_charset($mysqli,'utf8');


$preifx = $db['default']['tablepre'];

$at = isset($_GET['at']) ? $_GET['at'] : '';

switch ($at) {

    case 'admin':
        // 进入后台
        $admin = '';
        $files = dr_file_map(CMS_PATH);
        foreach ($files as $file) {
            if (strpos($file, '.php') !== false) {
                $code = file_get_contents(CMS_PATH.$file);
                if (strpos($code, "define('IS_ADMIN', TRUE)") !== false) {
                    $admin = $file;
                }
            }
        }

        if (!$admin) {
            exit('没有在客户端网站找到后台入口文件');
        }

        // cookie

        header('Location: /'.$admin.'?m=admin&c=index&a=fclient&id='.$_GET['id'].'&sync='.$_GET['sync']);
        exit;
        break;

    default:
        // 数据分析
        if (!$_POST['data']) {
            _json(0, '网站数据验证为空');
        }
        $rt = file_put_contents('sync.php', $_POST['data']);
        if (!$rt) {
            exit(_json(0, '客户端网站/api/fclient/目录无法写入文件'));
        }

        $sync = require 'sync.php';

        // 更新域名入库
        //$sql = 'update `'.$preifx.'site` set `name`="'.$sync['name'].'",`domain`="'.dr_cms_domain_name($sync['domain']).'" where siteid=1';
        $sql = 'update `'.$preifx.'site` set `name`="'.$sync['name'].'",`domain`="'.$sync['domain'].'" where siteid=1';
        if (!@mysqli_query($mysqli, $sql)) {
            exit(_json(0, '客户端站执行数据操作失败'));
        }

        if (is_file('version.php')) {
            $version = require 'version.php';
        } else {
            $version = '1.0';
        }

        _json(1, '数据通信成功', $version);
        break;
}
