<?php

/**
 * API入口
 */

// API接口项目标识
define('IS_API', 'api');

// 入口文件名称
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// 入口文件目录
define('SELF_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);

require('index.php'); // 引入主文件