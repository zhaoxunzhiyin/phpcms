<?php

/**
 * 子站入口
 */

!defined('SITE_ID') && define('SITE_ID', '{SITE_ID}');
!defined('FIX_WEB_DIR') && define('FIX_WEB_DIR', '{FIX_WEB_DIR}');

// 执行主站程序
require '{CMS_PATH}index.php';