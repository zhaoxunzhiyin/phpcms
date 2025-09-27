<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
class role extends admin {
	private $input, $db, $priv_db, $op, $cache_api, $menu_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('admin_role_model');
		$this->priv_db = pc_base::load_model('admin_role_priv_model');
		$this->op = pc_base::load_app_class('role_op');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}
	
	/**
	 * 角色管理列表
	 */
	public function init() {
		$infos = $this->db->select('', '*', '', 'listorder DESC, roleid DESC');
		include $this->admin_tpl('role_list');
	}
	
	/**
	 * 添加角色
	 */
	public function add() {
		if(IS_AJAX_POST) {
			$info = $this->input->post('info');
			if(!is_array($info) || empty($info['rolename'])){
				dr_json(0,L('role_name').L('not_empty'), array('field' => 'rolename'));
			}
			if($this->op->checkname($info['rolename'])){
				dr_json(0,L('role_duplicate'));
			}
			$insert_id = $this->db->insert($info,true);
			$this->_cache();
			if($insert_id){
				dr_json(1,L('operation_success'));
			}
		}
		$reply_url = '?m=admin&c=role&a=init&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
		include $this->admin_tpl('role_add');
	}
	
	/**
	 * 编辑角色
	 */
	public function edit() {
		if(IS_AJAX_POST) {
			$roleid = intval($this->input->post('roleid'));
			$info = $this->input->post('info');
			if(!is_array($info) || empty($info['rolename'])){
				dr_json(0,L('role_name').L('not_empty'), array('field' => 'rolename'));
			}
			$this->db->update($info,array('roleid'=>$roleid));
			$this->_cache();
			dr_json(1,L('operation_success'));
		}
		$info = $this->db->get_one(array('roleid'=>$this->input->get('roleid')));
		extract($info);
		$post_url = '?m=admin&c=role&a=add&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
		$reply_url = '?m=admin&c=role&a=init&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
		include $this->admin_tpl('role_edit');		
	}
	
	/**
	 * 删除角色
	 */
	public function delete() {
		$roleid = intval($this->input->get('roleid'));
		if($roleid == '1') dr_admin_msg(0,L('this_object_not_del'), HTTP_REFERER);
		$this->db->delete(array('roleid'=>$roleid));
		$this->priv_db->delete(array('roleid'=>$roleid));
		$this->_cache();
		dr_admin_msg(1,L('role_del_success'));
	}
	/**
	 * 更新角色排序
	 */
	public function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $roleid => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('roleid'=>$roleid));
				}
			}
			dr_admin_msg(1,L('operation_success'));
		} else {
			dr_admin_msg(0,L('operation_failure'));
		}
	}
	
	/**
	 * 角色权限设置
	 */
	public function role_priv() {
		$this->menu_db = pc_base::load_model('menu_model');
		$siteid = $siteid ? $siteid : self::get_siteid(); 
		if(IS_POST){
			if (is_array($this->input->post('menuid')) && count($this->input->post('menuid')) > 0) {
			
				$this->priv_db->delete(array('roleid'=>$this->input->post('roleid'),'siteid'=>$this->input->post('siteid')));
				$menuinfo = $this->menu_db->select('','`id`,`m`,`c`,`a`,`data`');
				foreach ($menuinfo as $_v) $menu_info[$_v['id']] = $_v;
				foreach($this->input->post('menuid') as $menuid){
					$info = array();
					$info = $this->op->get_menuinfo(intval($menuid),$menu_info);
					$info['roleid'] = $this->input->post('roleid');
					$info['siteid'] = $this->input->post('siteid');
					$this->priv_db->insert($info);
				}
			} else {
				$this->priv_db->delete(array('roleid'=>$this->input->post('roleid'),'siteid'=>$this->input->post('siteid')));
			}
			$this->_cache();	
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=role&a=init', '', 'edit');

		} else {
			$siteid = intval($this->input->get('siteid'));
			$roleid = intval($this->input->get('roleid'));
			if ($siteid) {
				$menu = pc_base::load_sys_class('tree');
				$result = $this->menu_db->select();
				$priv_rs = $this->priv_db->select(); //获取权限表数据
				foreach ($priv_rs as $n=>$t) {
					if ($t['menuid']>290) {
						unset($t['menuid']);
					}
					$priv_data[] = $t;
				}
				$modules = 'admin,system';
				foreach ($result as $n=>$t) {
					$result[$n]['cname'] = L($t['name'],'',$modules);
					$result[$n]['checked'] = ($this->op->is_checked($t,$this->input->get('roleid'),$siteid, $priv_data))? ' checked' : '';
					$result[$n]['level'] = $this->op->get_level($t['id'],$result);
					$result[$n]['parentid_node'] = ($t['parentid'])? ' class="child-of-node-'.$t['parentid'].'"' : '';
				}
				$str  = "<tr id='node-\$id' \$parentid_node>
							<td class='myselect'>\$spacer<label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$cname <span></span></label></td>
						</tr>";
			
				$menu->init($result);
				$categorys = $menu->get_tree(0, $str);
			}
			$show_header = $show_scroll = true;
			include $this->admin_tpl('role_priv');
		}
	}
	
	public function priv_setting() {
		$sites = pc_base::load_app_class('sites', 'admin');
		$sites_list = $sites->get_list();
		$roleid = intval($this->input->get('roleid'));
		include $this->admin_tpl('role_priv_setting');
		
	}

	/**
	 * 更新角色状态
	 */
	public function change_status(){
		$roleid = intval($this->input->get('roleid'));
		$disabled = intval($this->input->get('disabled'));
		$this->db->update(array('disabled'=>$disabled),array('roleid'=>$roleid));
		$this->_cache();
		dr_admin_msg(1,L('operation_success'),'?m=admin&c=role');
	}
		
	/**
	 * 设置栏目权限
	 */
	public function setting_cat_priv() {
		$roleid = $this->input->get('roleid') && intval($this->input->get('roleid')) ? intval($this->input->get('roleid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$op = $this->input->get('op') && intval($this->input->get('op')) ? intval($this->input->get('op')) : '';
		switch ($op) {
			case 1:
			$siteid = $this->input->get('siteid') && intval($this->input->get('siteid')) ? intval($this->input->get('siteid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			pc_base::load_app_class('role_cat', '', 0);
			$category = role_cat::get_category($siteid);
			//获取角色当前权限设置
			$priv = role_cat::get_roleid($roleid, $siteid);
			//加载tree
			$tree = pc_base::load_sys_class('tree');
			$categorys = array();
			foreach ($category as $k=>$v) {
				if ($v['type'] == 1) {
					$v['disabled'] = 'disabled';
					$v['init_check'] = '';
					$v['add_check'] = '';
					$v['delete_check'] = '';
					$v['listorder_check'] = '';
					$v['push_check'] = '';
					$v['copy_check'] = '';
					$v['move_check'] = '';
					$v['recycle_init_check'] = '';
					$v['recycle_check'] = '';
					$v['update_check'] = '';
				} else {
					$v['disabled'] = '';
					
					$v['add_check'] = isset($priv[$v['catid']]['add']) ? 'checked' : '';
					$v['delete_check'] = isset($priv[$v['catid']]['delete']) ? 'checked' : '';
					$v['listorder_check'] = isset($priv[$v['catid']]['listorder']) ? 'checked' : '';
					$v['push_check'] = isset($priv[$v['catid']]['push']) ? 'checked' : '';
					$v['copy_check'] = isset($priv[$v['catid']]['copy']) ? 'checked' : '';
					$v['move_check'] = isset($priv[$v['catid']]['remove']) ? 'checked' : '';
					$v['edit_check'] = isset($priv[$v['catid']]['edit']) ? 'checked' : '';
					$v['recycle_init_check'] = isset($priv[$v['catid']]['recycle_init']) ? 'checked' : '';
					$v['recycle_check'] = isset($priv[$v['catid']]['recycle']) ? 'checked' : '';
					$v['update_check'] = isset($priv[$v['catid']]['update']) ? 'checked' : '';
				}
				$v['init_check'] = isset($priv[$v['catid']]['init']) ? 'checked' : '';
				$category[$k] = $v;
			}
			$show_header = true;
			$str = "<tr>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='group-checkable' value='1' onclick='select_all(\$catid, this)'><span></span></label></td>
				  <td>\$spacer\$catname</td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$init_check value='init'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$add_check value='add'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$edit_check value='edit'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$delete_check  value='delete'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$listorder_check value='listorder'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$push_check value='push'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$move_check value='remove'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$copy_check value='copy'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$recycle_init_check value='recycle_init'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$recycle_check value='recycle'><span></span></label></td>
				  <td class='myselect' align='center'><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='checkboxes' name='priv[\$catid][]' \$disabled \$update_check value='update'><span></span></label></td>
			  </tr>";
			
			$tree->init($category);
			$categorys = $tree->get_tree(0, $str);
			include $this->admin_tpl('role_cat_priv_list');
		break;
		
		case 2:
			$siteid = $this->input->get('siteid') && intval($this->input->get('siteid')) ? intval($this->input->get('siteid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			pc_base::load_app_class('role_cat', '', 0);
			role_cat::updata_priv($roleid, $siteid, $this->input->post('priv'));
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=role&a=init', '', 'edit');
			break;
		
		default:
			$sites = pc_base::load_app_class('sites', 'admin');
			$sites_list = $sites->get_list();
			include $this->admin_tpl('role_cat_priv');
		break;
		}
	}	
	/**
	 * 角色缓存
	 */
	private function _cache() {
		$this->cache_api->cache('admin_role');
	}
	
}
?>