<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');

/**
 * CSRF过滤白名单
 */

return [

    'member' => [
        'member/index/uploadavatar',
    ],

];