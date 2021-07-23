<?php

/**
 * 后台管理中心
 */

// 后台管理标识
define('IS_ADMIN', TRUE);

// 项目标识
define('IS_SELF', 'admin');

// 入口文件名称
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

require('index.php'); // 引入主文件
?>