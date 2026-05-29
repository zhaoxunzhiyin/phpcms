<?php
if (!defined('IN_CMS')) exit('No direct script access allowed');

/**
 * CSRF过滤白名单
 */

return [

    'admin' => [
        'admin/index/public_ajax_add_panel',
        'admin/index/public_ajax_delete_panel',
        'admin/site/public_upload_index',
    ],

];