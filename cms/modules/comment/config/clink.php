<?php

return [

    [
        'name' => '_get_name', // 站点权限是模块的链接名称
        'icon' => 'fa fa-comment', // 图标
        'color' => 'yellow', // 颜色class red green blue
        'url' => 'javascript:view_comment(\'{id_encode}\');', // 后台链接：对于点击的地址{m}是模块目录，{modelid}是模型modelid，{catid}是栏目catid，{id}是内容id，{siteid}是站点siteid
        'field' => '', // 统计数量的字段，填写模块内容的主表字段，只能填写int数字类型的字段
        'displayorder' => 0,
    ],

];