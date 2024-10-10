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
			$r['cname'] = L($r['name'], '', 'member_menu');
			$r['str_manage'] = '<a class="btn btn-xs green" href="?m=member&c=member_menu&a=edit&id='.$r['id'].'&menuid='.$this->input->get('menuid').'">'.L('edit').'</a><a class="btn btn-xs red" href="javascript:confirmurl(\'?m=member&c=member_menu&a=delete&id='.$r['id'].'&menuid='.$this->input->get('menuid').'\',\''.L('confirm',array('message'=>$r['cname'])).'\')">'.L('delete').'</a> ';
			$array[] = $r;
		}

		$str  = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
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
			$this->db->insert($info);
			//开发过程中用于自动创建语言包
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'member_menu.lang.php';
			if(file_exists($file)) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$key = $info['name'];
				$data = $content."\$LANG['$key'] = '".$this->input->post('language')."';\r\n?>";
				file_put_contents($file,$data);
			} else {
				
				$key = $info['name'];
				$data = "<?php\r\n\$LANG['$key'] = '".$this->input->post('language')."';\r\n?>";
				file_put_contents($file,$data);
			}
			//结束
			dr_admin_msg(1,L('add_success'),array('url' => '?m=member&c=member_menu&a=manage&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
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
			
			include $this->admin_tpl('member_menu');
		}
	}
	function delete() {
		$id = intval($this->input->get('id'));
		$menu = $this->db->get_one(array("id"=>$id));
		if(!$menu)dr_admin_msg(0,'菜单不存在！请返回！',HTTP_REFERER);
		$this->db->delete(array('id'=>$id));
		//删除member_menu语言包
		$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'member_menu.lang.php';
		require $file;
		$content = file_get_contents($file);
 		$str = "\$LANG['".$menu['name']."'] = '".$LANG[$menu['name']]."';\r\n";
 		$content = str_replace($str,'',$content);
		file_put_contents($file,$content);
		
 		dr_admin_msg(1,L('operation_success'));
	}
	
	function edit() {
		if(IS_POST) {
			$id = intval($this->input->post('id'));
			$info = $this->input->post('info');
			$this->db->update($info,array('id'=>$id));
			//修改语言文件
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'member_menu.lang.php';
			require $file;
			$key = $info['name'];
			if(!isset($LANG[$key])) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$data = $content."\$LANG['$key'] = '".$this->input->post('language')."';\r\n?>";
				file_put_contents($file,$data);
			} elseif(isset($LANG[$key]) && $LANG[$key]!=$this->input->post('language')) {
				$content = file_get_contents($file);
				$LANG[$key] = safe_replace($LANG[$key]);
				$content = str_replace($LANG[$key],$this->input->post('language'),$content);
				file_put_contents($file,$content);
			}
			
			//结束语言文件修改
			dr_admin_msg(1,L('operation_success'),array('url' => '?m=member&c=member_menu&a=manage&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			$show_validator = '';
			$tree = pc_base::load_sys_class('tree');
			$id = intval($this->input->get('id'));
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
			include $this->admin_tpl('member_menu');
		}
	}
	
	/**
	 * 排序
	 */
	function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $id => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('id'=>$id));
				}
			}
			dr_admin_msg(1,L('operation_success'));
		} else {
			dr_admin_msg(0,L('operation_failure'));
		}
	}
}
?>