<?php

/**
 * 移动端域名api
 */

header('Content-Type: text/html; charset=utf-8');

if (!is_file('../index.php')) {
    echo '当前服务器无法访问跨目录文件';
    exit();
}

echo 'cms ok';exit;