<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');

/**
 * CSRF过滤白名单
 */

return [

    'home' => [
        '404/index/ueditor',
    ],

    'admin' => [
        '404/index/ueditor',
    ],

];