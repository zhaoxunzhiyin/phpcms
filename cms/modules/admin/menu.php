<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class menu extends admin {
	private $input,$db,$module_db,$installdir;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('menu_model');
		$this->module_db = pc_base::load_model('module_model');
	}
	
	function init() {
		$list = $this->db->select('','*','','listorder ASC,id ASC');
		$array = array();
		foreach($list as $r) {
			$rs['id'] = $r['id'];
			$rs['title'] = '<i class="'.$r['icon'].'"></i> '.L($r['name']);
			$rs['parentid'] = $r['parentid'];
			$rs['tid'] = $this->parentid($r['parentid']);
			$rs['display'] = $r['display'];
			$rs['listorder'] = $r['listorder'];
			if ($r['parentid'] == 0) {
				$rs['type'] = '<span class="btn btn-xs yellow">'.L('目录').'</span>';
			} else {
				if ($r['display'] == 0) {
					$rs['type'] = '<span class="btn btn-xs btn-default">'.L('按钮').'</span>';
				} else {
					$rs['type'] = '<span class="btn btn-xs dark">'.L('菜单').'</span>';
				}
			}
			$array[] = $rs;
		}
		$tree = pc_base::load_sys_class('tree');
		$array = $tree->get($array);
		include $this->admin_tpl('menu');
	}
	function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$language = $this->input->post('language');
			if (!$language) {
				dr_admin_msg(0,L('input').L('chinese_name'), array('field' => 'language'));
			}
			if (!$info['name']) {
				dr_admin_msg(0,L('input').L('menu_name'), array('field' => 'name'));
			}
			if (!$info['m']) {
				dr_admin_msg(0,L('input').L('module_name'), array('field' => 'm'));
			}
			if (!$info['c']) {
				dr_admin_msg(0,L('input').L('file_name'), array('field' => 'c'));
			}
			if (!$info['a']) {
				dr_admin_msg(0,L('input').L('action_name'), array('field' => 'a'));
			}
			if (!$info['icon']) {
				dr_admin_msg(0,L('input').L('菜单图标'), array('field' => 'icon'));
			}
			$this->db->insert($info);
			//开发过程中用于自动创建语言包
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'system_menu.lang.php';
			if(file_exists($file)) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$key = $info['name'];
				$data = $content."\$LANG['$key'] = '".$language."';\r\n?>";
				file_put_contents($file,$data);
			} else {
				$key = $this->input->post('info')['name'];
				$data = "<?php\r\n\$LANG['$key'] = '".$language."';\r\n?>";
				file_put_contents($file,$data);
			}
			//结束
			dr_admin_msg(1,L('add_success'));
		} else {
			$show_validator = '';
			$tree = pc_base::load_sys_class('tree');
			$result = $this->db->select();
			$array = array();
			foreach($result as $r) {
				$r['cname'] = L($r['name']);
				$r['selected'] = $r['id'] == $this->input->get('parentid') ? 'selected' : '';
				$array[] = $r;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$reply_url = '?m=admin&c=menu&a=init&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('menu');
		}
	}
	function delete() {
		$ids = $this->input->get_post_ids();
		if (!$ids) {
		    dr_json(0, L('你还没有选择呢'));
        }
		foreach ($ids as $id) {
			$this->delete_child((int)$id);
			$this->db->delete(array('id'=>$id));
		}
		dr_json(1, L('operation_success'), ['ids' => $ids]);
	}
	/**
	 * 递归删除
	 * @param $id 要删除的id
	 */
	private function delete_child($id) {
		$id = intval($id);
		if (empty($id)) return false;
		$list = $this->db->select(array('parentid'=>$id));
		foreach($list as $r) {
			$this->delete_child($r['id']);
			$this->db->delete(array('id'=>$r['id']));
		}
		return true;
	}
	private function parentid($parentid) {
		$parentid = intval($parentid);
		if (empty($parentid)) return '';
		$r = $this->db->get_one(array('id'=>$parentid));
		if (!$r) return '';
		return ' checkboxes'.$r['id'].$this->parentid($r['parentid']);
	}
	
	function edit() {
		$id = intval($this->input->get('id'));
		if(IS_POST) {
			$info = $this->input->post('info');
			$language = $this->input->post('language');
			if (!$language) {
				dr_admin_msg(0,L('input').L('chinese_name'), array('field' => 'language'));
			}
			if (!$info['name']) {
				dr_admin_msg(0,L('input').L('menu_name'), array('field' => 'name'));
			}
			if (!$info['m']) {
				dr_admin_msg(0,L('input').L('module_name'), array('field' => 'm'));
			}
			if (!$info['c']) {
				dr_admin_msg(0,L('input').L('file_name'), array('field' => 'c'));
			}
			if (!$info['a']) {
				dr_admin_msg(0,L('input').L('action_name'), array('field' => 'a'));
			}
			if (!$info['icon']) {
				dr_admin_msg(0,L('input').L('菜单图标'), array('field' => 'icon'));
			}
			$this->db->update($info,array('id'=>$id));
			//修改语言文件
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'system_menu.lang.php';
			require $file;
			$key = $info['name'];
			if(!isset($LANG[$key])) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$data = $content."\$LANG['$key'] = '".$language."';\r\n?>";
				file_put_contents($file,$data);
			} elseif(isset($LANG[$key]) && $LANG[$key]!=$language) {
				$content = file_get_contents($file);
				$content = str_replace($LANG[$key],$language,$content);
				file_put_contents($file,$content);
			}
			//结束语言文件修改
			dr_admin_msg(1,L('operation_success'));
		} else {
			$show_validator = '';
			$tree = pc_base::load_sys_class('tree');
			$r = $this->db->get_one(array('id'=>$id));
			if($r) extract($r);
			$result = $this->db->select();
			foreach($result as $r) {
				$r['cname'] = L($r['name']);
				$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
				$array[] = $r;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$post_url = '?m=admin&c=menu&a=add&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			$reply_url = '?m=admin&c=menu&a=init&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('menu');
		}
	}
	
	/**
	 * 选择图标
	 */
	public function public_icon() {
		$show_header = $show_pc_hash = true;
		include $this->admin_tpl('menu_icon');
	}
	
	/**
	 * 初始化菜单
	 */
	public function public_init() {
		define('INSTALL', true);
		if(file_exists(TEMPPATH.'menu/menu.sql')) {
			$sql = file_get_contents(TEMPPATH.'menu/menu.sql');
			$this->_sql_execute($sql);
		}
		$modules = $this->module_db->select('', '*', '', '', '', 'module');
		foreach ($modules as $t) {
			if ($t['module']!='admin' && $t['module']!='member' && $t['module']!='pay' && $t['module']!='digg' && $t['module']!='special' && $t['module']!='content' && $t['module']!='search' && $t['module']!='scan' && $t['module']!='attachment' && $t['module']!='block' && $t['module']!='collection' && $t['module']!='dbsource' && $t['module']!='template' && $t['module']!='release') {
				$this->installdir = PC_PATH.'modules'.DIRECTORY_SEPARATOR.$t['module'].DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR;
				if (file_exists($this->installdir.'extention.inc.php')) {
					$menu_db = pc_base::load_model('menu_model');
					@include ($this->installdir.'extention.inc.php');
					$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.SYS_LANGUAGE.DIRECTORY_SEPARATOR.'system_menu.lang.php';
					if(file_exists($file)) {
						$content = file_get_contents($file);
						$content = substr($content,0,-2);
						$data = '';
						foreach ($language as $key => $l) {
							if (L($key, '', 'system_menu')==$key) {
								$data .= "\$LANG['".$key."'] = '".$l."';\r\n";
							}
						}
						$data = $content.$data."?>";
						file_put_contents($file,$data);
					} else {
						foreach ($language as $key =>$l) {
							if (L($key, '', 'system_menu')==$key) {
								$data .= "\$LANG['".$key."'] = '".$l."';\r\n";
							}
						}
						$data = "<?"."php\r\n\$data?>";
						file_put_contents($file,$data);
					}
				}
				if (file_exists($this->installdir.'languages'.DIRECTORY_SEPARATOR)) {
					pc_base::load_sys_class('file')->copy_dir($this->installdir.'languages'.DIRECTORY_SEPARATOR, $this->installdir.'languages'.DIRECTORY_SEPARATOR, PC_PATH.'languages'.DIRECTORY_SEPARATOR);
				}
				/*if(file_exists($this->installdir.'templates'.DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR)) {
					pc_base::load_sys_class('file')->copy_dir($this->installdir.'templates'.DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR, $this->installdir.'templates'.DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR, TPLPATH.SYS_TPL_NAME.DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR.$t['module'].DIRECTORY_SEPARATOR);
					if (file_exists($this->installdir.'templates'.DIRECTORY_SEPARATOR.'name.inc.php')) {
						$keyid = 'templates|'.SYS_TPL_NAME.'|pc|'.$t['module'];
						$file_explan[$keyid] = include $this->installdir.'templates'.DIRECTORY_SEPARATOR.'name.inc.php';
						$templatepath = TPLPATH.SYS_TPL_NAME.DIRECTORY_SEPARATOR;
						if (file_exists($templatepath.'config.php')) {
							$style_info = include $templatepath.'config.php';
							$style_info['file_explan'] = array_merge($style_info['file_explan'], $file_explan);
							@file_put_contents($templatepath.'config.php', '<?php return '.var_export($style_info, true).';?>');
						}
					}
				}
				if(file_exists($this->installdir.'templates'.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR)) {
					pc_base::load_sys_class('file')->copy_dir($this->installdir.'templates'.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR, $this->installdir.'templates'.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR, TPLPATH.SYS_TPL_NAME.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.$t['module'].DIRECTORY_SEPARATOR);
					if (file_exists($this->installdir.'templates'.DIRECTORY_SEPARATOR.'name.inc.php')) {
						$keyid = 'templates|'.SYS_TPL_NAME.'|mobile|'.$t['module'];
						$file_explan[$keyid] = include $this->installdir.'templates'.DIRECTORY_SEPARATOR.'name.inc.php';
						$templatepath = TPLPATH.SYS_TPL_NAME.DIRECTORY_SEPARATOR;
						if (file_exists($templatepath.'config.php')) {
							$style_info = include $templatepath.'config.php';
							$style_info['file_explan'] = array_merge($style_info['file_explan'], $file_explan);
							@file_put_contents($templatepath.'config.php', '<?php return '.var_export($style_info, true).';?>');
						}
					}
				}*/
			}
		}
		dr_json(1, L('refresh_menu_ok'));
	}
	
	// 隐藏或者启用
	function display_edit() {
		$id = intval($this->input->get('id'));
		$r = $this->db->get_one(array('id'=>$id));
		if (!$r) {
		    dr_json(0, L('数据#'.$id.'不存在'));
        }
		$value = (int)$r['display'] ? 0 : 1;
		$this->db->update(array('display'=>$value),array('id'=>$id));
		dr_json(1, L($value ? '此菜单已被启用' : '此菜单已被隐藏'), array('value' => $value));
	}
	
	// 保存数据
	public function listorder() {
		$id = intval($this->input->get('id'));
		$name = $this->input->get('name');
		$value = intval($this->input->get('value'));
		$this->db->update(array(dr_safe_replace($name)=>dr_safe_replace($value)),array('id'=>$id));
		dr_json(1, L('operation_success'));
	}
	
	/**
	 * 执行SQL
	 * @param string $sql 要执行的sql语句
	 */
 	private function _sql_execute($sql) {
	    $sqls = $this->_sql_split($sql);
		if(is_array($sqls)) {
			foreach($sqls as $sql) {
				if(trim($sql) != '') {
					$this->db->query($sql);
				}
			}
		} else {
			$this->db->query($sqls);
		}
		return true;
	}	
	
	/**
	 * 分割SQL语句
	 * @param string $sql 要执行的sql语句
	 */	
 	private function _sql_split($sql) {
		$database = pc_base::load_config('database');
		$db_charset = $database['default']['charset'];
		if($this->db->version() > '4.1' && $db_charset) {
			$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".$db_charset,$sql);
		}
		$sql = str_replace("\r", "\n", $sql);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach($queriesarray as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			$queries = array_filter($queries);
			foreach($queries as $query) {
				$str1 = substr($query, 0, 1);
				if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
			}
			$num++;
		}
		return($ret);
	}
}
?>