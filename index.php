<?php

/**
 * 入口程序
 * 开发者可在这里定义系统目录变量
 */

declare(strict_types=1);
header('X-Frame-Options: SAMEORIGIN'); //防止被站外加入iframe中浏览

// 是否是开发者模式（TRUE开启、FALSE关闭），上线之后建议关闭此开关
define('IS_DEV', FALSE);

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

// 移动入口标识
!defined('IS_MOBILE') && define('IS_MOBILE', FALSE);

// 入口文件名称
!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

//CMS根目录
define('CMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

// 入口文件目录
!defined('SELF_DIR') && define('SELF_DIR', CMS_PATH);

include CMS_PATH.'cms/base.php';

// 开始，自动进入安装界面监测代码 
if (!is_file(CACHE_PATH.'install.lock')) {
	require CMS_PATH.'install.php';
	exit;
}
// 判断安装
if (file_exists('install') && is_file(CACHE_PATH.'install.lock')) {
	dr_dir_delete('install', TRUE);
}
// 结束，安装之后可以删除此段代码

// 执行主程序
pc_base::creat_app();