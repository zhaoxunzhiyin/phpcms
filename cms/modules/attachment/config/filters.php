<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');

/**
 * CSRF过滤白名单
 */

return [

    'home' => [
        'attachment/attachments/upload',
        'attachment/attachments/h5upload',
        'attachment/attachments/download',
    ],

    'admin' => [
        'attachment/attachments/upload',
        'attachment/attachments/h5upload',
        'attachment/attachments/download',
    ],

];