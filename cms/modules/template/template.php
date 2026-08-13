<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class template extends admin {
	//模板文件夹
	private $input,$root_path,$mobile_path,$backups_dir,$exclude_dir,$backups_path,$dir;
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->root_path = CMS_PATH.'statics/';
		$this->mobile_path = CMS_PATH.'mobile/statics/';
		$this->backups_dir = 'theme';
		$this->exclude_dir = [];
		$this->backups_path = CACHE_PATH.'bakup/'.$this->backups_dir.'/';
		$this->dir = $this->_safe_path($this->input->get('dir'));
	}
	
	public function init(){
		pc_base::load_app_func('global', 'admin');
		list($path, $list) = $this->_map_file($this->dir, $this->root_path);
		$big_menu = array('javascript:artdialog(\'add\',\'?m=template&c=template&a=add&dir='.$this->dir.'&is_iframe=1\',\''.L('create_directory_file').'\',500,100);void(0);', L('create_directory_file'));
		$delete = '?m=template&c=template&a=delete&dir='.$this->dir;
		include $this->admin_tpl('tpl_index');
	}
	
	public function add(){
		$this->_Add(0);
	}
	
	public function edit(){
		$this->_Edit(0);
	}
	
	public function delete(){
		$this->_Del(0);
	}
	
	public function mobile(){
		pc_base::load_app_func('global', 'admin');
		list($path, $list) = $this->_map_file($this->dir, $this->mobile_path, 1);
		$big_menu = array('javascript:artdialog(\'add\',\'?m=template&c=template&a=mobile_add&dir='.$this->dir.'&is_iframe=1\',\''.L('create_directory_file').'\',500,100);void(0);', L('create_directory_file'));
		$delete = '?m=template&c=template&a=mobile_delete&dir='.$this->dir;
		include $this->admin_tpl('tpl_index');
	}
	
	public function mobile_add(){
		$this->_Add(1);
	}
	
	public function mobile_edit(){
		$this->_Edit(1);
	}
	
	public function mobile_delete(){
		$this->_Del(1);
	}
	
	public function public_image_index(){

		$mobile = intval($this->input->get('mobile'));
		$file = $this->_safe_path($this->input->get('file'));
		$filename = ($mobile ? $this->mobile_path : $this->root_path).$file;
		if (!is_file($filename)) {
			exit(L('文件'.$file.'不存在'));
		}

		$vals = getimagesize($filename);
		$types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
		$mime = (isset($types[$vals[2]])) ? 'image/'.$types[$vals[2]] : 'image/jpg';

		switch ($vals[2]) {

			case 1:
				$resource = imagecreatefromgif($filename);
				break;

			case 2:
				$resource = imagecreatefromjpeg($filename);
				break;

			case 3:
				$resource = imagecreatefrompng($filename);
				break;
		}

		header('Content-Disposition: filename='.$filename.';');
		header('Content-Type: '.$mime);
		header('Content-Transfer-Encoding: binary');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');

		switch ($vals[2])
		{
			case 1	:	imagegif($resource);
				break;
			case 2	:	imagejpeg($resource, NULL, 99);
				break;
			case 3	:
				imagepng($resource);
				break;
		}

		exit;
	}

	public function public_clear_del(){

		$mobile = intval($this->input->get('mobile'));
		$file = $this->_safe_path($this->input->get('file'));
		$filename = ($mobile ? $this->mobile_path : $this->root_path).$file;
		if (!is_file($filename)) {
			dr_json(0, L('文件'.$file.'不存在'));
		}

		$dir = md5($filename);
		pc_base::load_sys_class('cache')->del_all('bakup/'.$this->backups_dir.'/'.$dir.'/');
		@rmdir($this->backups_path.$dir.'/');

		dr_json(1, L('操作成功'));
	}

	protected function _Add($mobile) {

		if (IS_AJAX_POST) {

			if (!IS_EDIT_TPL) {
				dr_json(0, L('系统不允许创建和修改模板文件'), ['field' => 'name']);
			}

			$name = dr_safe_filename($this->input->post('name'));
			if (!$name) {
				dr_json(0, L('文件名称不能为空'), ['field' => 'name']);
			}

			$path = ($mobile ? $this->mobile_path : $this->root_path).($this->dir ? $this->dir : trim($this->dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			$file = $path.$name;

			if (strpos($name, '.') !== false) {
				// 文件
				$ext = trim(strtolower(strrchr($file, '.')), '.');
				strpos($ext, 'php') !== false && dr_json(0, L('文件不允许'));
				file_put_contents($file, '') === false && dr_json(0, L('文件创建失败'), ['field' => 'name']);
			} else {
				dr_mkdirs($file);
			}

			dr_json(1, L('操作成功'));
		}

		$name = '';
		include $this->admin_tpl('tpl_dirname');
		exit;
	}
	
	protected function _Edit($mobile) {

		$file = $this->_safe_path($this->input->get('file'));
		$fileext = trim(strtolower(strrchr($file, '.')), '.');
		$fileext == 'php' && dr_json(0, L('文件不允许'));
		$filename = ($mobile ? $this->mobile_path : $this->root_path).$file;

		switch ($this->input->get('mtype')) {

			case 'dir':
				// 目录修改名称
				// 目录修改
				if (IS_AJAX_POST) {

					$name = dr_safe_filename($this->input->post('name'));
					if (!IS_EDIT_TPL) {
						dr_json(0, L('系统不允许创建和修改模板文件'), ['field' => 'name']);
					} elseif (!is_dir($filename)) {
						dr_json(0, L('此目录不存在'));
					} elseif (!$name) {
						dr_json(0, L('目录名称不能为空'), ['field' => 'name']);
					}

					// 开始修改
					if ( basename($filename) != $name && !rename($filename, dirname($filename).'/'.$name)) {
						dr_json(0, L('重命名失败'), ['field' => 'name']);
					}

					dr_json(1, L('操作成功'));
				}

				$name = basename($file);
				include $this->admin_tpl('tpl_dirname');
				exit;
				break;

			case 'filecname':
				// 重命名文件

				$fname = str_replace('.'.$fileext, '', basename($file));

				if (IS_AJAX_POST) {

					$name = dr_safe_filename($this->input->post('name'));
					if (!IS_EDIT_TPL) {
						dr_json(0, L('系统不允许创建和修改模板文件'), ['field' => 'name']);
					} elseif (!$name) {
						dr_json(0, L('文件名称不能为空'), ['field' => 'name']);
					}

					// 开始修改
					if ($fname != $name && !rename($filename, dirname($filename).'/'.$name.'.'.$fileext)) {
						dr_json(0, L('重命名失败'), ['field' => 'name']);
					}

					dr_json(1, L('操作成功'));
				}

				$name = basename($fname);
				include $this->admin_tpl('tpl_dirname');
				exit;
				break;

			case 'cname':
				// 重命名文件别名

				$cname = $this->_get_name_ini($file, ($mobile ? $this->mobile_path : $this->root_path));

				if (IS_AJAX_POST) {

					$name = dr_safe_filename($this->input->post('name'));
					if (!IS_EDIT_TPL) {
						dr_json(0, L('系统不允许创建和修改模板文件'), ['field' => 'name']);
					} elseif (!$name) {
						dr_json(0, L('文件名称不能为空'), ['field' => 'name']);
					}

					// 开始修改
					$this->_save_name_ini($file, $name, ($mobile ? $this->mobile_path : $this->root_path));

					dr_json(1, L('操作成功'));
				}

				$name = basename($cname);
				include $this->admin_tpl('tpl_dirname');
				exit;
				break;

			case 'file':

				// 文件修改
				if (!is_file($filename)) {
					dr_admin_msg(0, L('文件'.$file.'不存在'));
				}

				if (in_array($fileext, ['html', 'htm', 'css', 'js', 'map', 'ini', 'php'])) {
					// 文件内容编辑模式
					$dir = md5($filename);
					$bfile = intval($this->input->get('bfile'));
					if ($bfile && is_file($this->backups_path.$dir.'/'.$bfile)) {
						$name = L('对比历史文件：'.dr_date($bfile, null, 'red').'（左边是当前文件；右边是历史文件）');
						$is_diff = 1;
						$diff_content = file_get_contents($this->backups_path.$dir.'/'.$bfile);
					} else {
						$name = L('文件修改');
						$is_diff = 0;
						$diff_content = '';
					}

					$content = file_get_contents($filename);
					$backups = dr_file_map($this->backups_path.$dir.'/');

					if (IS_AJAX_POST) {

						$code = $this->input->post('code', false);
						if (!IS_EDIT_TPL) {
							dr_json(0, L('系统不允许创建和修改模板文件'));
						} elseif (!$code) {
							dr_json(0, L('内容不能为空'));
						}

						// 备份数据
						if ($content != $code && $is_diff == 0) {
							!is_dir($this->backups_path.$dir.'/') && dr_mkdirs($this->backups_path.$dir.'/');
							$size = file_put_contents($this->backups_path.$dir.'/'.SYS_TIME, $content);
							if ($size === false) {
								dr_json(0, L('备份目录/cache/backups/无法存储'));
							}
						}

						// 替换现有的文件
						$size = file_put_contents($filename, $code);
						if ($size === false) {
							dr_json(0, L('模板目录无法写入'));
						}

						$cname = $this->input->post('cname');
						$cname && $this->_save_name_ini($filename, $cname, ($mobile ? $this->mobile_path : $this->root_path));

						dr_json(1, L('操作成功'));
					}

					switch ($fileext) {

						case 'css':
							$file_ext = 'css';
							$file_js = 'css/css.js';
							break;

						default:
							$file_ext = 'javascript';
							$file_js = 'javascript/javascript.js';
							break;
					}

					$code = $this->_get_code($content, $fileext);
					$path = $filename;
					$cname = $this->_get_name_ini($filename, ($mobile ? $this->mobile_path : $this->root_path));
					$diff_code = htmlentities($diff_content,ENT_COMPAT,'UTF-8');
					$reply_url = '?m=template&c=template&a='.($mobile ? 'mobile' : 'init').'&menuid='.$this->input->get('menuid').'&dir='.dirname($file);
					$backups_url = '?m=template&c=template&a='.($mobile ? 'mobile_edit' : 'edit').'&menuid='.$this->input->get('menuid').'&mtype=file&file='.$file;
					$backups_del = '?m=template&c=template&a=public_clear_del'.($mobile ? '&mobile=1' : '').'&menuid='.$this->input->get('menuid').'&file='.$file;
					include $this->admin_tpl($is_diff ? 'tpl_diff' : 'tpl_edit');

				} else {

					$reply_url = '?m=template&c=template&a='.($mobile ? 'mobile' : 'init').'&menuid='.$this->input->get('menuid').'&dir='.dirname($file);
					if (IS_POST) {
						if (!IS_EDIT_TPL) {
							dr_json(0, L('系统不允许创建和修改模板文件'));
						}
						pc_base::load_sys_class('upload','',0);
						$upload = new upload();
						$rt = $upload->update_file([
							'file_name' => $filename,
							'form_name' => 'file',
							'file_exts' => [$fileext],
						]);
						!$rt['code'] && dr_admin_msg(0, $rt['msg']);
						dr_admin_msg(1, L('操作成功'), $reply_url);
					}

					$preview = '<img src="'.WEB_PATH.'api.php?op=icon&fileext='.$fileext.'" width="64">';
					dr_is_image($fileext) && $preview = '<a href="javascript:dr_preview_image(\'?m=template&c=template&a=public_image_index'.($mobile ? '&mobile=1' : '').'&menuid='.$this->input->get('menuid').'&file='.$file.'\');">'.$preview.'</a>';

					// 文件上传模式
					$path = $filename;
					include $this->admin_tpl('tpl_file');
				}

				break;

			case 'zip':

				if (!IS_EDIT_TPL) {
					dr_json(0, L('系统不允许创建和修改模板文件'));
				} elseif ($fileext != 'zip') {
					dr_json(0, L('不是zip压缩文件'));
				} elseif (!pc_base::load_sys_class('file')->unzip($filename)) {
					// 解压zip
					dr_json(0, L('zip解压失败'));
				}

				dr_json(1, L('解压成功'));
				break;

		}
	}
	
	protected function _Del($mobile){

		$ids = $this->input->post('ids');
		if (!$ids) {
			dr_json(0, L('还没有选择呢'));
		} elseif (!IS_EDIT_TPL) {
			dr_json(0, L('系统不允许创建和修改模板文件'));
		}

		$path = ($mobile ? $this->mobile_path : $this->root_path).($this->dir ? $this->dir : trim($this->dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

		foreach ($ids as $file) {
			$file = $this->_safe_path($file);
			if ($file) {
				if (is_dir($path.$file)) {
					dr_file_delete($path.$file, TRUE);
					@rmdir($path.$file);
				} else {
					@unlink($path.$file);
				}
			}

		}

		dr_json(1, L('操作成功'));
	}

	/**
	 * 文件图标
	 */
	protected function _file_icon($file) {
		return WEB_PATH.'api.php?op=icon&fileext='.trim(strtolower(strrchr($file, '.')), '.');
	}

	/**
	 * 安全目录
	 */
	protected function _safe_path($string) {
		return trim(str_replace('..', '', str_replace(
			[".//.", '\\', ' ', '<', '>', "{", '}', '..', "//"],
			'',
			$string
		)), '/');
	}

	/**
	 * 目录扫描
	 */
	protected function _map_file($dir, $root_path, $mobile = 0) {

		$file_data = $dir_data = [];
		$dir && $dir_data = [
			[
				'id' => 0,
				'name' => '..',
				'icon' => IMG_PATH.'folder.png',
				'size' => ' - ',
				'time' => '',
				'edit' => '',
				'file' => '',
				'cname' => '',
				'zip' => '',
				'url' => '?m=template&c=template&a='.($mobile ? 'mobile' : 'init').'&menuid='.$this->input->get('menuid').'&dir='.trim(dirname($this->dir), '.'),
			]
		];

		$source_dir	= dr_rp($root_path.($dir ? $dir : trim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR), ['//', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR], ['/', DIRECTORY_SEPARATOR]);

		if ($fp = @opendir($source_dir)) {
			$list = [];
			while (FALSE !== ($file = readdir($fp))) {
				if (in_array($file, ['.', '..', '.DS_Store', 'config.ini'])) {
					continue;
				} elseif (strtolower(strrchr($file, '.')) == '.php') {
					continue;
				} elseif ($this->not_root_path && dr_in_array($source_dir . $file, $this->not_root_path)) {
					continue;
				}
				$list[] = $file;
			}
			sort($list);
			foreach ($list as $file) {
				$edit = '?m=template&c=template&a='.($mobile ? 'mobile_edit' : 'edit').'&menuid='.$this->input->get('menuid').'&file='.$this->dir.'/'.$file;
				if (is_dir($source_dir.'/'.$file)) {
					if (!$dir && $this->exclude_dir && is_array($this->exclude_dir) && dr_in_array($file, $this->exclude_dir)) {
						continue;
					}
					$dir_data[] = [
						'id' => 0,
						'name' => $file,
						'cname' => $this->_get_name_ini($source_dir.'/'.$file, $root_path),
						'cname_edit' => 'javascript:dr_iframe(\''.L('文件别名').'\', \''.$edit.'&mtype=cname\', \'500px\',\'100px\');',
						'file' => $file,
						'icon' => IMG_PATH.'folder.png',
						'size' => ' - ',
						'time' => dr_date(filemtime($source_dir.'/'.$file), null, 'red'),
						'zip' => '',
						'edit' => 'javascript:dr_iframe(\''.L('目录/文件名称').'\', \''.$edit.'&mtype=dir\', \'500px\',\'100px\');',
						'url' => '?m=template&c=template&a='.($mobile ? 'mobile' : 'init').'&menuid='.$this->input->get('menuid').'&dir='.$this->dir.'/'.$file,
					];
				} else {
					$ext = trim(strtolower(strrchr($file, '.')), '.');
					$edit = '?m=template&c=template&a='.($mobile ? 'mobile_edit' : 'edit').'&menuid='.$this->input->get('menuid').'&file='.$this->dir.'/'.$file;
					$file_data[] = [
						'id' => md5($file),
						'name' => $file,
						'cname' => $this->_get_name_ini($source_dir.'/'.$file, $root_path),
						'cname_edit' => 'javascript:dr_iframe(\''.L('文件别名').'\', \''.$edit.'&mtype=cname\', \'500px\',\'100px\');',
						'file' => $file,
						'icon' => $this->_file_icon($file),
						'size' => format_file_size(filesize($source_dir.'/'.$file)),
						'time' => dr_date(filemtime($source_dir.'/'.$file), null, 'red'),
						'edit' => $edit.'&mtype=file',
						'url' => $edit.'&mtype=file',
						'zip' => $ext == 'zip' ? 'javascript:dr_admin_menu_ajax(\''.$edit.'&mtype=zip&pc_hash=\' + pc_hash, 1);' : '',
						//'edit' => 'javascript:dr_iframe(\''.L('目录/文件名称').'\', \''.$edit.'&mtype=filecname\', \'500px\',\'100px\');',
					];
				}
			}

			closedir($fp);
		}

		return [$source_dir, $dir_data && $file_data ? array_merge($dir_data, $file_data) : $dir_data];
	}

	/**
	 * 存储文件别名
	 */
	protected function _save_name_ini($file, $value, $root_path) {

		list($dir, $path) = $this->_get_one_dirname($file, $root_path);
		$id = md5($path);
		$ini = $root_path.$dir.'/config.ini';

		$data = json_decode(file_get_contents($ini), true);
		!$data && $data = [];
		$data[$id] = $value;

		file_put_contents($ini, json_encode($data));
	}

	/**
	 * 获取单个文件别名
	 */
	protected function _get_name_ini($file, $root_path) {

		list($dir, $path, $lsname) = $this->_get_one_dirname($file, $root_path);
		$id = md5($path);
		$ini = $root_path.$dir.'/config.ini';
		$data = json_decode(file_get_contents($ini), true);

		return isset($data[$id]) ? (string)$data[$id] : $lsname;
	}

	/**
	 * 获取第一个目录名称
	 */
	protected function _get_one_dirname($path, $root_path) {

		$dir = trim(str_replace(['/', '\\'], '*', str_replace($root_path, '', $path)), '*');
		if (strpos($dir, '*')) {
			//存在子目录
			list($a) = explode('*', $dir);
			return [$a, $dir, trim(strtolower(strrchr($dir, '*')), '*')];
		}

		return [$dir, $dir, $dir];
	}

	/**
	 * 格式化内容
	 */
	protected function _get_code($code, $ext) {

		if ($ext == 'js') {
			return $code;
		}

		return htmlentities($code,ENT_COMPAT,'UTF-8');
	}
}