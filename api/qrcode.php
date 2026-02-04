<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取二维码接口
 */
$value = urldecode(pc_base::load_sys_class('input')->get('text'));
$thumb = urldecode(pc_base::load_sys_class('input')->get('thumb'));
$matrixPointSize = (int)pc_base::load_sys_class('input')->get('size');
$errorCorrectionLevel = dr_safe_replace(pc_base::load_sys_class('input')->get('level'));
$background = dr_safe_username(urldecode(pc_base::load_sys_class('input')->get('background')));
$pcolor = dr_safe_username(urldecode(pc_base::load_sys_class('input')->get('pcolor')));
$mcolor = dr_safe_username(urldecode(pc_base::load_sys_class('input')->get('mcolor')));

$color = [
	hex2rgb($background),//背景色
	hex2rgb($pcolor),//定位角的颜色
	hex2rgb($mcolor),//中间内容的颜色
];

if ($value) {
	//生成二维码图片
	pc_base::load_sys_class('qrcode');
	dr_mkdirs(CACHE_PATH.'caches_qrcode/caches_data/');
	$file = CACHE_PATH.'caches_qrcode/caches_data/qrcode-'.md5($value.$thumb.$matrixPointSize.$errorCorrectionLevel).'-qrcode.png';
	if (!IS_DEV && is_file($file)) {
		$QR = imagecreatefrompng($file);
	} else {
		\QRcode::png($value, $file, $errorCorrectionLevel, $matrixPointSize, 3, false, $color);
		if (!is_file($file)) {
			exit('二维码生成失败');
		}
		$QR = imagecreatefromstring(file_get_contents($file));
		if ($thumb) {
			if (strpos($thumb, 'https://') !== false
				&& strpos($thumb, '/') !== false
				&& strpos($thumb, 'http://') !== false) {
				exit('图片地址不规范');
			}
			$img = getimagesize($thumb);
			if (!$img) {
				exit('此图片不是一张可用的图片');
			}
			$code = dr_catcher_data($thumb);
			if (!$code) {
				exit('图片参数不规范');
			}
			$logo = imagecreatefromstring($code);
			$QR_width = imagesx($QR);//二维码图片宽度
			$QR_height = imagesy($QR);//二维码图片高度
			$logo_width = imagesx($logo);//logo图片宽度
			$logo_height = imagesy($logo);//logo图片高度
			$logo_qr_width = $QR_width / 4;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			//重新组合图片并调整大小
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
			imagepng($QR, $file);
		}
	}

	// 输出图片
	ob_start();
	ob_clean();
	header("Content-type: image/png");
	$QR && imagepng($QR);
	exit;
}
?>