<?php
$fields = array('text'=>'单行文本',
	'textarea'=>'多行文本',
	'editor'=>'编辑器',
	'catid'=>'栏目',
	'title'=>'标题',
	'box'=>'选项',
	'image'=>'图片',
	'images'=>'多图片',
	'number'=>'数字',
	'datetime'=>'日期和时间',
	'posid'=>'推荐位',
	'keyword'=>'关键词',
	'author'=>'作者',
	'copyfrom'=>'来源',
	'groupid'=>'会员组',
	'islink'=>'转向链接',
	'template'=>'模板',
	'pages'=>'分页选择',
	'typeid'=>'类别',
	'readpoint'=>'积分、点数',
	'linkage'=>'联动菜单（单选）',
	'linkages'=>'联动菜单（多选）',
	'downfile'=>'镜像下载',
	'file'=>'单文件上传',
	'downfiles'=>'多文件上传',
	'map'=>'地图字段',
	'omnipotent'=>'万能字段',
	'linkfield'=>'关联字段',
	'tabletexts'=>'信息表格',
	'color'=>'颜色选取',
	'touchspin'=>'增减量',
	'textbtn'=>' 文本事件',
	'redirect'=>'转向链接',
	'wxurl'=>'导入微信文章',
	'word'=>'Word导入编辑器',
);
//不允许删除的字段，这些字段讲不会在字段添加处显示
$not_allow_fields = array('catid','typeid','title','keyword','posid','template','username');
//允许添加但必须唯一的字段
$unique_fields = array('pages','readpoint','author','copyfrom','islink');
//禁止被禁用的字段列表
$forbid_fields = array('catid','title','updatetime','inputtime','url','listorder','status','template','username');
//禁止被删除的字段列表
$forbid_delete = array('catid','typeid','title','thumb','keywords','updatetime','inputtime','posids','url','listorder','status','template','username');
//可以追加 JS和CSS 的字段
$att_css_js = array('text','textarea','box','number','keyword','typeid');
?>