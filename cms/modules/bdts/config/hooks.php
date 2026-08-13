<?php

/**
 * 自定义钩子
 *
 */

/**
 * 内容发布或者修改之后
 */
pc_base::load_sys_class('hooks')::app_on('bdts', 'module_content_after', function($data, $old) {
    // 内容发布或者修改之后
    if ($data['status'] == 99) {
        // 99表示审核通过的
        pc_base::load_app_class('admin_bdts', 'bdts')->module_bdts(
            $data['tablename'],
            $data['url'],
            $old ? 'edit' : 'add' //
        );
    }
});