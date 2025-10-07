<?php
/**
 * 生成验证码
 * 类用法
 * $checkcode = new checkcode();
 * $checkcode->show_code();
 * //取得验证
 * $checkcode->get_code();
 */
class checkcode {

	//资源
	private $img;

	//画布宽度
	public $width = 100;

	//画布高度
	public $height = 35;

	//背景颜色
	public $background = '#ffffff';

	//验证码
	public $code;

	//验证码的随机种子
	public $code_string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	//验证码长度
	public $code_len = 4;

	//验证码字体
	public $font;

	//验证码字体大小
	public $font_size = 20;

	//验证码字体颜色
	public $font_color;


	/**
	 * 构造函数
	 */
	public function __construct() {
		$rand = rand(0,1);
		if($rand==0) {
			$this->font = PC_PATH.'libs'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'font'.DIRECTORY_SEPARATOR.'elephant.ttf';
		} else {
			$this->font = PC_PATH.'libs'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'font'.DIRECTORY_SEPARATOR.'Vineta.ttf';
		}
		if (!is_file($this->font)) log_message('error', '验证码字体文件（'.$this->font.'）不存在，可能无法生成验证码！');
		if (!$this->check_gd()) log_message('error', 'PHP扩展GD库未开启！');
	}


	/**
	 * 生成验证码
	 */
	private function create_code() {
		$setting = getcache('common','commons');
		$sysadmincodemodel = isset($setting['sysadmincodemodel']) ? (int)$setting['sysadmincodemodel'] : 0;
		if ($sysadmincodemodel==1) {
			$this->code_string = '1234567890';
		} else if ($sysadmincodemodel==2) {
			$this->code_string = 'abcdefghkmnprstuvwyzABCDEFGHKLMNPRSTUVWYZ';
		} else if ($sysadmincodemodel==3) {
			if (isset($setting['captcha_charset']) && $setting['captcha_charset']) {
				$this->code_string = $setting['captcha_charset'];
			}
		}
		$code = '';
		for ($i = 0; $i < $this->code_len; $i++) {
			$code .= $this->code_string [mt_rand(0, strlen($this->code_string) - 1)];
		}
		$this->code = trim($code);
	}


	/**
	 * 返回验证码
	 */
	public function get_code() {
		return strtolower($this->code);
	}


	/**
	 * 建画布
	 */
	public function create() {
		$w = $this->width;
		$h = $this->height;
		$background = $this->background;
		$img = imagecreatetruecolor($w, $h);
		$background = imagecolorallocate($img, hexdec(substr($background, 1, 2)), hexdec(substr($background, 3, 2)), hexdec(substr($background, 5, 2)));
		imagefill($img, 0, 0, $background);
		$this->img = $img;
		$this->create_line();
		$this->create_font();
		$this->create_pix();
		@ob_start();
		@ob_clean(); //关键代码，防止出现'图像因其本身有错无法显示'的问题。
	}


	/**
	* 画线
	*/
	private function create_line(){
		$w = $this->width;
		$h = $this->height;
		$line_color = "#dcdcdc";
		$color = imagecolorallocate($this->img, hexdec(substr($line_color, 1, 2)), hexdec(substr($line_color, 3, 2)), hexdec(substr($line_color, 5, 2)));
		$l = $h/5;
		for($i=1;$i<$l;$i++){
			$step =$i*5;
			imageline($this->img, 0, $step, $w,$step, $color);
		}
		$l= $w/10;
		for($i=1;$i<$l;$i++){
			$step =$i*10;
			imageline($this->img, $step, 0, $step,$h, $color);
		}
	}


	/**
	 * 写入验证码文字
	 */
	private function create_font() {
		$this->create_code();
		$color = $this->font_color;
		if (!empty($color)) {
			$font_color = imagecolorallocate($this->img, hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));
		}
		$x = intval(($this->width - 10) / $this->code_len);
		for ($i = 0; $i < $this->code_len; $i++) {
			if (empty($color)) {
				$font_color = imagecolorallocate($this->img, mt_rand(50, 155), mt_rand(50, 155), mt_rand(50, 155));
			}
			imagettftext($this->img, $this->font_size, mt_rand(- 30, 30), $x * $i + mt_rand(6, 10), mt_rand(intval($this->height / 1.3), $this->height - 5), $font_color, $this->font, $this->code [$i]);
		}
		$this->font_color = $font_color;
	}


	/**
	 * 画线
	 */
	private function create_pix() {
		$pix_color = $this->font_color;
		for ($i = 0; $i < 50; $i++) {
			imagesetpixel($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), $pix_color);
		}

		for ($i = 0; $i < 2; $i++) {
			imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $pix_color);
		}
		for ($i = 0; $i < 1; $i++) {
			// 设置画线宽度
			// imagesetthickness($this->img, mt_rand(1, 3));
			imagearc($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height)
					, mt_rand(0, 160), mt_rand(0, 200), $pix_color);
		}
		imagesetthickness($this->img, 1);
	}

	/**
	 * 显示验证码
	 */
	public function show_code() {
		$this->create();
		header('Content-type:image/png');
		imagepng($this->img);
		imagedestroy($this->img);
	}

	/**
	 * 验证GD库
	 */
	private function check_gd() {
		return extension_loaded('gd') && function_exists("imagepng");
	}
}