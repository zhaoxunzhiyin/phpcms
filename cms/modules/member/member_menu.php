<?php
/**
 * 管理员后台会员中心菜单管理类
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class member_menu extends admin {
	private $input,$db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('member_menu_model');
	}
	
	function manage() {
		$tree = pc_base::load_sys_class('tree');
		$userid = param::get_session('userid');
		$admin_username = param::get_cookie('admin_username');

		$result = $this->db->select('','*','','listorder ASC,id DESC');

		foreach($result as $r) {
			$r['checkboxes'] = '<label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="checkboxes'.$this->parentid($r['parentid']).' group-checkable" data-set=".checkboxes'.$r['id'].'"  name="ids[]" value="'.$r['id'].'" /><span></span></label>';
			$r['listorder'] = '<input type="text" onblur="dr_ajax_save(this.value, \'?m=member&c=member_menu&a=listorder&id='.$r['id'].'&pc_hash=\'+pc_hash, \'listorder\')" value="'.$r['listorder'].'" class="displayorder form-control input-sm input-inline input-mini">';
			$r['cname'] = L($r['name'], '', 'member_menu');
			$r['str_manage'] = '<a class="btn btn-xs green" href="?m=member&c=member_menu&a=edit&id='.$r['id'].'&menuid='.$this->input->get('menuid').'"> <i class="fa fa-edit"></i> '.L('edit').'</a>';
			$array[] = $r;
		}

		$str  = "<tr>
					<td class='myselect'>\$checkboxes</td>
					<td align='center'>\$listorder</td>
					<td align='center'>\$id</td>
					<td >\$spacer\$cname</td>
					<td align='center'>\$str_manage</td>
				</tr>";
		$tree->init($array);
		$categorys = $tree->get_tree(0, $str);
		include $this->admin_tpl('member_menu');
	}
	function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$language = $this->input->post('language');
			if (!$language) {
				dr_admin_msg(0,L('input').L('chinese_name', '', 'admin'), array('field' => 'language'));
			}
			if (!$info['name']) {
				dr_admin_msg(0,L('input').L('menu_name'), array('field' => 'name'));
			}
			if(!$this->input->get('isurl')) {
				if (!$info['m']) {
					dr_admin_msg(0,L('input').L('module_name'), array('field' => 'm'));
				}
				if (!$info['c']) {
					dr_admin_msg(0,L('input').L('file_name'), array('field' => 'c'));
				}
				if (!$info['a']) {
					dr_admin_msg(0,L('input').L('action_name'), array('field' => 'a'));
				}
			}
			$this->db->insert($info);
			//开发过程中用于自动创建语言包
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'member_menu.lang.php';
			if(file_exists($file)) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$key = $info['name'];
				$data = $content."\$LANG['$key'] = '".$language."';\r\n?>";
				file_put_contents($file,$data);
			} else {
				
				$key = $info['name'];
				$data = "<?php\r\n\$LANG['$key'] = '".$language."';\r\n?>";
				file_put_contents($file,$data);
			}
			//结束
			dr_admin_msg(1,L('add_success'));
		} else {
			$show_validator = '';
			$tree = pc_base::load_sys_class('tree');
			$result = $this->db->select();
			foreach($result as $r) {
				$r['cname'] = L($r['name'], '', 'member_menu');
				$r['selected'] = $r['id'] == $this->input->get('parentid') ? 'selected' : '';
				$array[] = $r;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$reply_url = '?m=member&c=member_menu&a=manage&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('member_menu');
		}
	}
	function delete() {
		$ids = $this->input->get_post_ids();
		if (!$ids) {
		    dr_json(0, L('你还没有选择呢'));
        }
		foreach ($ids as $id) {
			$this->db->delete(array('id'=>$id));
			//删除member_menu语言包
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'member_menu.lang.php';
			require $file;
			$content = file_get_contents($file);
			$str = "\$LANG['".$menu['name']."'] = '".$LANG[$menu['name']]."';\r\n";
			$content = str_replace($str,'',$content);
			file_put_contents($file,$content);
		}
		dr_json(1, L('operation_success'), ['ids' => $ids]);
	}
	
	function edit() {
		$id = intval($this->input->get('id'));
		if(IS_POST) {
			$info = $this->input->post('info');
			$language = $this->input->post('language');
			if (!$language) {
				dr_admin_msg(0,L('input').L('chinese_name', '', 'admin'), array('field' => 'language'));
			}
			if (!$info['name']) {
				dr_admin_msg(0,L('input').L('menu_name'), array('field' => 'name'));
			}
			if(!$this->input->get('isurl')) {
				if (!$info['m']) {
					dr_admin_msg(0,L('input').L('module_name'), array('field' => 'm'));
				}
				if (!$info['c']) {
					dr_admin_msg(0,L('input').L('file_name'), array('field' => 'c'));
				}
				if (!$info['a']) {
					dr_admin_msg(0,L('input').L('action_name'), array('field' => 'a'));
				}
			}
			$this->db->update($info,array('id'=>$id));
			//修改语言文件
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'member_menu.lang.php';
			require $file;
			$key = $info['name'];
			if(!isset($LANG[$key])) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$data = $content."\$LANG['$key'] = '".$language."';\r\n?>";
				file_put_contents($file,$data);
			} elseif(isset($LANG[$key]) && $LANG[$key]!=$language) {
				$content = file_get_contents($file);
				$LANG[$key] = safe_replace($LANG[$key]);
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
				$r['cname'] = L($r['name'], '', 'member_menu');
				$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
				$array[] = $r;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$post_url = '?m=member&c=member_menu&a=add&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			$reply_url = '?m=member&c=member_menu&a=manage&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('member_menu');
		}
	}
	
	/**
	 * 排序
	 */
	public function listorder() {
		$id = intval($this->input->get('id'));
		$name = $this->input->get('name');
		$value = intval($this->input->get('value'));
		$this->db->update(array(dr_safe_replace($name)=>dr_safe_replace($value)),array('id'=>$id));
		dr_json(1, L('operation_success'));
	}
	private function parentid($parentid) {
		$parentid = intval($parentid);
		if (empty($parentid)) return '';
		$r = $this->db->get_one(array('id'=>$parentid));
		if (!$r) return '';
		return ' checkboxes'.$r['id'].$this->parentid($r['parentid']);
	}
}
?>