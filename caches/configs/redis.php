<?php

if (!defined('IN_CMS')) exit('No direct script access allowed');

/**
 * Redis缓存配置文件
 */

return array(
    'host'     => '127.0.0.1',
    'password' => null,
    'port'     => 6379,
    'timeout'  => 0,
    'database' => 0,
);