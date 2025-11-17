<?php

if (!defined('IN_CMS')) exit('No direct script access allowed');

/**
 * Memcached缓存配置文件
 */

return array(
    'host'   => '127.0.0.1',
    'port'   => 11211,
    'weight' => 1,
    'raw'    => false,
);