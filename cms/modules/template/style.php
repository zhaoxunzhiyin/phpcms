<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class style extends admin {
	//模板文件夹
	private $input,$filepath;
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->filepath = TPLPATH;
	}
	
	public function init(){
		pc_base::load_app_func('global', 'admin');
		$list = template_list('', 1);
		include $this->admin_tpl('style_list');
	}
	
	public function disable() {
		$style = $this->input->get('style') && trim($this->input->get('style')) ? trim($this->input->get('style')) : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if (!preg_match('/([a-z0-9_\-]+)/i',$style)) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		}
		$filepath = $this->filepath.$style.DIRECTORY_SEPARATOR.'config.php';
		if (file_exists($filepath)) {
			$arr = include $filepath;
			if (!isset($arr['disable'])) {
				$arr['disable'] = 1;
			} else {
				if ($arr['disable'] ==1 ) {
					$arr['disable'] = 0;
				} else {
					$arr['disable'] = 1;
				}
			}
			if (is_writable($filepath)) {
				file_put_contents($filepath, '<?php return '.var_export($arr, true).';?>');
			} else {
				dr_admin_msg(0,L('file_does_not_writable'), HTTP_REFERER);
			}
		} else {
			$arr = array('name'=>$style,'disable'=>1, 'dirname'=>$style);
			file_put_contents($filepath, '<?php return '.var_export($arr, true).';?>');
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	public function export() {
		$style = $this->input->get('style') && trim($this->input->get('style')) ? trim($this->input->get('style')) : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if (!preg_match('/([a-z0-9_\-]+)/i',$style)) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		}
		$filepath = $this->filepath.$style.DIRECTORY_SEPARATOR.'config.php';
		if (file_exists($filepath)) {
			$arr = include $filepath;
			if (pc_base::load_config('system', 'charset') == 'gbk') {
				$arr = array_iconv($arr);
			}
			$data = base64_encode(json_encode($arr));
		    header("Content-type: application/octet-stream");
		    header("Content-Disposition: attachment; filename=pc_template_".$style.'.txt');
		    exit($data);
		} else {
			dr_admin_msg(0,L('file_does_not_exists'), HTTP_REFERER);
		}
	}
	
	public function import() {
		if (IS_POST) {
			$type = $this->input->post('type') && trim($this->input->post('type')) ? trim($this->input->post('type')) : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
			if ($type == 1) {
				$filename = $this->input->post('filename');
				if (strtolower(substr((string)$filename, -3, 3)) != 'txt') {
					dr_admin_msg(0,L('only_allowed_to_upload_txt_files'), HTTP_REFERER);
				}
				$code = json_decode(base64_decode(file_get_contents($filename)), true);
				if (!preg_match('/([a-z0-9_\-]+)/i',$code['dirname'])) {
					dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
				}
				@unlink($filename);
			} elseif ($type == 2) {
				$code = $this->input->post('code') && trim($this->input->post('code')) ? json_decode(base64_decode(trim($this->input->post('code'))),true) : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
				if (!isset($code['dirname']) && !preg_match('/([a-z0-9_\-]+)/i',(string)$code['dirname'])) {
					dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
				}
			}
			if (pc_base::load_config('system', 'charset') == 'gbk') {
				$code = array_iconv($code, 'utf-8', 'gbk');
			}
			//echo $this->filepath.$code['dirname'].DIRECTORY_SEPARATOR.'config.php';
			if (!file_exists($this->filepath.$code['dirname'].DIRECTORY_SEPARATOR.'config.php')) {
				@mkdir($this->filepath.$code['dirname'].DIRECTORY_SEPARATOR, 0755, true);
				if (@is_writable($this->filepath.$code['dirname'].DIRECTORY_SEPARATOR)) {
					@file_put_contents($this->filepath.$code['dirname'].DIRECTORY_SEPARATOR.'config.php', '<?php return '.var_export($code, true).';?>');
					dr_admin_msg(1,L('operation_success'));
				} else {
					dr_admin_msg(0,L('template_directory_not_write'), HTTP_REFERER);
				}
			} else {
				dr_admin_msg(0,L('file_exists'), HTTP_REFERER);
			}
		} else {
			$show_header = true;
			include $this->admin_tpl('style_import');
		}
	}

	// 上传文件
	public function public_upload_index() {
		pc_base::load_sys_class('upload','',0);
		$upload = new upload();
		$rt = $upload->upload_file(array(
			'save_name' => 'null',
			'save_path' => CACHE_PATH.'temp/',
			'form_name' => 'file_data',
			'file_exts' => array('txt'),
			'file_size' => 100 * 1024 * 1024,
			'attachment' => array(
				'value' => array(
					'path' => 'null'
				)
			),
		));
		if (!$rt['code']) {
			exit(dr_array2string($rt));
		}
		dr_json(1, L('上传成功'), $rt['data']);
	}
	
	public function updatename() {
		$name = $this->input->post('name') ? $this->input->post('name') : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if (is_array($name)) {
			foreach ($name as $key=>$val) {
				$filepath = $this->filepath.$key.DIRECTORY_SEPARATOR.'config.php';
				if (file_exists($filepath)) {
					$arr = include $filepath;
					$arr['name'] = $val;
				} else {
					$arr = array('name'=>$val,'disable'=>0, 'dirname'=>$key);
				}
				@file_put_contents($filepath, '<?php return '.var_export($arr, true).';?>');
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		}
	}
}