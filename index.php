<?php
/**
 *  index.php CMS 入口
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-6-1
 */
//declare(strict_types=1);
header('X-Frame-Options: SAMEORIGIN'); //防止被站外加入iframe中浏览

// 是否是开发者模式（1开启、0关闭）
define('IS_DEV', 0);

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

// 移动入口标识
!defined('IS_MOBILE') && define('IS_MOBILE', FALSE);

// 项目标识
!defined('IS_SELF') && define('IS_SELF', 'index');

// 入口文件名称
!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

//CMS根目录
define('CMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('PHPCMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include CMS_PATH.'cms/base.php';

// 开始，自动进入安装界面监测代码 
if (!is_file(CACHE_PATH.'install.lock')) {
	require CMS_PATH.'install.php';
	exit;
}
// 判断环境
if (version_compare(PHP_VERSION, '7.0.0') < 0) {
    echo "<font color=red>PHP版本必须在7.0及以上</font>";exit;
}
if (file_exists('install') && is_file(CACHE_PATH.'install.lock')) {
	pc_base::load_sys_func('dir');
	dir_delete('install');
}
// 结束，安装之后可以删除此段代码

pc_base::creat_app();
?>