<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');

/**
 * CSRF过滤白名单
 */

return [

    'admin' => [
        'collection/node/public_upload_index',
    ],

];