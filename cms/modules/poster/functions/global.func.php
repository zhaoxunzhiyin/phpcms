<?php
/**
 * 广告模板配置函数
 */
function get_types() {
	$poster_template = poster_template();
	$TYPES = array();
	if (is_array($poster_template) && !empty($poster_template)){
		foreach ($poster_template as $k => $template) {
			$TYPES[$k] = $template['name'];
		}
	}
	return $TYPES;
}

/**
 * 广告模板函数
 */
function poster_template() {
	return array (
		'banner' => 
		array (
			'name' => '矩形横幅',
			'select' => '0',
			'padding' => '0',
			'size' => '1',
			'option' => '0',
			'num' => '1',
			'iscore' => '1',
			'type' => 
			array (
				'images' => '图片',
				'flash' => '动画',
			),
		),
		'fixure' => 
		array (
			'name' => '固定位置',
			'align' => 'align',
			'select' => '0',
			'padding' => '1',
			'size' => '1',
			'option' => '0',
			'num' => '1',
			'iscore' => '1',
			'type' => 
			array (
				'images' => '图片',
				'flash' => '动画',
			),
		),
		'float' => 
		array (
			'name' => '漂浮移动',
			'select' => '0',
			'padding' => '1',
			'size' => '1',
			'option' => '0',
			'num' => '1',
			'iscore' => '1',
			'type' => 
			array (
				'images' => '图片',
				'flash' => '动画',
			),
		),
		'couplet' => 
		array (
			'name' => '对联广告',
			'align' => 'scroll',
			'select' => '0',
			'padding' => '1',
			'size' => '1',
			'option' => '0',
			'num' => '2',
			'iscore' => '1',
			'type' => 
			array (
				'images' => '图片',
				'flash' => '动画',
			),
		),
		'imagechange' => 
		array (
			'name' => '图片轮换广告',
			'select' => '0',
			'padding' => '0',
			'size' => '1',
			'option' => '1',
			'num' => '1',
			'iscore' => '1',
			'type' => 
			array (
				'images' => '图片',
			),
		),
		'imagelist' => 
		array (
			'name' => '图片列表广告',
			'select' => '0',
			'padding' => '0',
			'size' => '1',
			'option' => '1',
			'num' => '1',
			'iscore' => '1',
			'type' => 
			array (
				'images' => '图片',
			),
		),
		'text' => 
		array (
			'name' => '文字广告',
			'select' => '0',
			'padding' => '0',
			'size' => '0',
			'option' => '1',
			'num' => '1',
			'iscore' => '1',
			'type' => 
			array (
				'text' => '文字',
			),
		),
		'code' => 
		array (
			'name' => '代码广告',
			'type' => 
			array (
				'text' => '代码',
			),
			'num' => 1,
			'iscore' => 1,
			'option' => 0,
		),
	);
}
?>