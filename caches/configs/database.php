<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');
return array(
    'default' => array (
        'hostname' => '127.0.0.1',
        'port' => 3306,
        'database' => 'cmsv10',
        'username' => 'root',
        'password' => 'root',
        'tablepre' => 'v10_',
        'charset' => 'utf8mb4',
        'type' => 'mysqli',
        'debug' => true,
        'pconnect' => 0,
        'autoconnect' => 0
    ),
);
?>