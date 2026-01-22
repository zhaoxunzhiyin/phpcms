<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class tag extends admin {
	private $input, $db, $dbsource;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('tag_model');
		$this->dbsource = pc_base::load_model('dbsource_model');
	}
	
	/**
	 * 标签向导列表
	 */
	public function init() {
		$page = $this->input->post('page') && intval($this->input->post('page')) ? intval($this->input->post('page')) : 1;
		$list = $this->db->listinfo('','id desc', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('tag_list');
	}

	/**
	 * 添加标签向导
	 */
	public function add() {
		pc_base::load_app_func('global', 'dbsource');
		if (IS_POST) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('name').L('empty'), array('field' => 'name'));
			$cache = $this->input->post('cache') && intval($this->input->post('cache')) ? intval($this->input->post('cache')) : 0;
			$num = $this->input->post('num') && intval($this->input->post('num')) ? intval($this->input->post('num')) : 0;
			$maxsize = $this->input->post('maxsize') && intval($this->input->post('maxsize')) ? intval($this->input->post('maxsize')) : 0;
			$type = $this->input->post('type') && intval($this->input->post('type')) ? intval($this->input->post('type')) : 0;
			$ac = $this->input->get('ac') && !empty($this->input->get('ac')) ? trim($this->input->get('ac')) : '';
			//检查名称是否已经存在
			if ($this->db->count(array('name'=>$name))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			$siteid = $this->get_siteid();
			if ($type == '1') { //自定义SQL
				$sql = $this->input->post('data') && trim($this->input->post('data')) ? trim($this->input->post('data')) : dr_admin_msg(0,L('custom_sql').L('empty'));
				$data['sql'] = $sql;
				$tag = '{pc:get sql="'.$sql.'" ';
				if ($cache) {
					$tag .= 'cache="'.$cache.'" ';
				}
				if ($this->input->post('page')) {
					$tag .= 'page="'.$this->input->post('page').'" ';
				}
				if ($this->input->post('dbsource')) {
					$data['dbsource'] = $this->input->post('dbsource');
					$tag .= 'dbsource= "'.$this->input->post('dbsource').'" ';
				}
				if ($this->input->post('return')) {
					$tag .= 'return="'.$this->input->post('return').'"';
				}
				$tag .= '}';
			} elseif ($type == 0) { //模型配置
				$module = $this->input->post('module') && trim($this->input->post('module')) ? trim($this->input->post('module')) : dr_admin_msg(0,L('please_select_model'));
				$action = $this->input->post('action') && trim($this->input->post('action')) ? trim($this->input->post('action')) : dr_admin_msg(0,L('please_select_action'));
				$html = pc_tag_class($module);
				$data = array();
				$tag = '{pc:'.$module.' action="'.$action.'" ';
				if (isset($html[$action]) && is_array($html[$action])) {
					foreach ($html[$action] as $key=>$val) {
						$val['validator']['reg_msg'] = $val['validator']['reg_msg'] ? $val['validator']['reg_msg'] : $val['name'].L('inputerror');
						$$key = $this->input->post($key) && trim($this->input->post($key)) ? trim($this->input->post($key)) : '';
						if (!empty($val['validator'])) {
							if (isset($val['validator']['min']) && strlen($$key) < $val['validator']['min']) {
								dr_admin_msg(0,$val['name'].L('should').L('is_greater_than').$val['validator']['min'].L('lambda'));
							} 
							if (isset($val['validator']['max']) && strlen($$key) > $val['validator']['max']) {
								dr_admin_msg(0,$val['name'].L('should').L('less_than').$val['validator']['max'].L('lambda'));
							} 
							if (!preg_match('/'.$val['validator']['reg'].'/'.$val['validator']['reg_param'], $$key)) {
								dr_admin_msg(0,$val['name'].$val['validator']['reg_msg']);
							}
						}
						$tag .= $key.'="'.$$key.'" ';
						$data[$key] = $$key;
					}
				}
				if ($this->input->post('page')) {
					$tag .= 'page="'.$this->input->post('page').'" ';
				}
				if ($num) {
					$tag .= ' num="'.$num.'" ';
				}
				if ($maxsize) {
					$tag .= ' maxsize="'.$maxsize.'" ';
				}
				if ($this->input->post('return')) {
					$tag .= ' return="'.$this->input->post('return').'" ';
				}
				if ($cache) {
					$tag .= ' cache="'.$cache.'" ';
				}
				$tag .= '}';
			} else { //碎片
				$data = $this->input->post('block') && trim($this->input->post('block')) ? trim($this->input->post('block')) : dr_admin_msg(0,L('block_name_not_empty'));
				$tag = '{pc:block pos="'.$data.'"}';
			}
			$tag .= "\n".'{loop $data $n $r}'."\n".'<li><a href="{$r[\'url\']}" title="{$r[\'title\']}">{$r[\'title\']}</a></li>'."\n".'{/loop}'."\n".'{/pc}';
			$data = is_array($data) ? array2string($data) : $data;
			$this->db->insert(array('siteid'=>$siteid, 'tag'=>$tag, 'name'=>$name, 'type'=>$type, 'module'=>$module, 'action'=>$action, 'data'=>$data, 'page'=>$this->input->post('page'), 'return'=>$this->input->post('return'), 'cache'=>$cache, 'num'=>$num, 'maxsize'=>$maxsize));
			if ($ac=='js') {
				include $this->admin_tpl('tag_show');
			} else {
				dr_admin_msg(1, L('operation_success'));
			}
		} else {
			pc_base::load_sys_class('form','',0);
			$modules = array_merge(array(''=>L('please_select')),pc_base::load_config('modules'));
			$show_header = $show_validator = true;
			$type = $this->input->get('type') && intval($this->input->get('type')) ? intval($this->input->get('type')) : 0;
			$siteid = $this->get_siteid();
			$dbsource_data = $dbsource = array();
			$dbsource[] = L('please_select');
			$dbsource_data = $this->dbsource->select(array('siteid'=>$siteid), 'name');
			foreach ($dbsource_data as $dbs) {
				$dbsource[$dbs['name']] = $dbs['name'];
			}
			$ac = $this->input->get('ac') && !empty($this->input->get('ac')) ? trim($this->input->get('ac')) : '';
			$module = $this->input->get('module') && trim($this->input->get('module')) ? trim($this->input->get('module')) : '';
			$action = $this->input->get('action') && trim($this->input->get('action')) ? trim($this->input->get('action')) : '';
			if ($module) $html = pc_tag_class($module);
			pc_base::load_app_func('global','template');
			include $this->admin_tpl('tag_add');
		}
	}

	/**
	 * 修改标签向导
	 */
	public function edit() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		if (!$edit_data = $this->db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('notfound'));
		}
		pc_base::load_app_func('global', 'dbsource');
		if (IS_POST) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('name').L('empty'), array('field' => 'name'));
			$cache = $this->input->post('cache') && intval($this->input->post('cache')) ? intval($this->input->post('cache')) : 0;
			$num = $this->input->post('num') && intval($this->input->post('num')) ? intval($this->input->post('num')) : 0;
			$maxsize = $this->input->post('maxsize') && intval($this->input->post('maxsize')) ? intval($this->input->post('maxsize')) : 0;
			$type = $this->input->post('type') && intval($this->input->post('type')) ? intval($this->input->post('type')) : 0;
			//检查名称是否已经存在
			if ($this->db->count(array('id<>'=>$id, 'name'=>$name))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			$siteid = $this->get_siteid();
			if ($type == '1') { //自定义SQL
				$sql = $this->input->post('data') && trim($this->input->post('data')) ? trim($this->input->post('data')) : dr_admin_msg(0,L('custom_sql').L('empty'));
				$data['sql'] = $sql;
				$tag = '{pc:get sql="'.$sql.'" ';
				if ($cache) {
					$tag .= 'cache="'.$cache.'" ';
				}
				if ($this->input->post('page')) {
					$tag .= 'page="'.$this->input->post('page').'" ';
				}
				if ($this->input->post('dbsource')) {
					$data['dbsource'] = $this->input->post('dbsource');
					$tag .= 'dbsource= "'.$this->input->post('dbsource').'" ';
				}
				if ($this->input->post('return')) {
					$tag .= 'return="'.$this->input->post('return').'"';
				}
				$tag .= '}';
			} elseif ($type == 0) { //模型配置
				$module = $this->input->post('module') && trim($this->input->post('module')) ? trim($this->input->post('module')) : dr_admin_msg(0,L('please_select_model'));
				$action = $this->input->post('action') && trim($this->input->post('action')) ? trim($this->input->post('action')) : dr_admin_msg(0,L('please_select_action'));
				$html = pc_tag_class($module);
				$data = array();
				$tag = '{pc:'.$module.' action="'.$action.'" ';
				if (isset($html[$action]) && is_array($html[$action])) {
					foreach ($html[$action] as $key=>$val) {
						$val['validator']['reg_msg'] = $val['validator']['reg_msg'] ? $val['validator']['reg_msg'] : $val['name'].L('inputerror');
						$$key = $this->input->post($key) && trim($this->input->post($key)) ? trim($this->input->post($key)) : '';
						if (!empty($val['validator'])) {
							if (isset($val['validator']['min']) && strlen($$key) < $val['validator']['min']) {
								dr_admin_msg(0,$val['name'].L('should').L('is_greater_than').$val['validator']['min'].L('lambda'));
							} 
							if (isset($val['validator']['max']) && strlen($$key) > $val['validator']['max']) {
								dr_admin_msg(0,$val['name'].L('should').L('less_than').$val['validator']['max'].L('lambda'));
							} 
							if (!preg_match('/'.$val['validator']['reg'].'/'.$val['validator']['reg_param'], $$key)) {
								dr_admin_msg(0,$val['name'].$val['validator']['reg_msg']);
							}
						}
						$tag .= $key.'="'.$$key.'" ';
						$data[$key] = $$key;
					}
				}
				if ($this->input->post('page')) {
					$tag .= 'page="'.$this->input->post('page').'" ';
				}
				if ($num) {
					$tag .= ' num="'.$num.'" ';
				}
				if ($maxsize) {
					$tag .= ' maxsize="'.$maxsize.'" ';
				}
				if ($this->input->post('return')) {
					$tag .= ' return="'.$this->input->post('return').'" ';
				}
				if ($cache) {
					$tag .= ' cache="'.$cache.'" ';
				}
				$tag .= '}';
			} else { //碎片
				$data = $this->input->post('block') && trim($this->input->post('block')) ? trim($this->input->post('block')) : dr_admin_msg(0,L('block_name_not_empty'));
				$tag = '{pc:block pos="'.$data.'"}';
			}
			$tag .= "\n".'{loop $data $n $r}'."\n".'<li><a href="{$r[\'url\']}" title="{$r[\'title\']}">{$r[\'title\']}</a></li>'."\n".'{/loop}'."\n".'{/pc}';
			$data = is_array($data) ? array2string($data) : $data;
			$this->db->update(array('siteid'=>$siteid, 'tag'=>$tag, 'name'=>$name, 'type'=>$type, 'module'=>$module, 'action'=>$action, 'data'=>$data, 'page'=>$this->input->post('page'), 'return'=>$this->input->post('return'), 'cache'=>$cache, 'num'=>$num, 'maxsize'=>$maxsize), array('id'=>$id));
			dr_admin_msg(1, L('operation_success'));
		} else {
			pc_base::load_sys_class('form','',0);
			$modules = array_merge(array(''=>L('please_select')),pc_base::load_config('modules'));
			$show_header = $show_validator = true;
			$type = $this->input->get('type') && intval($this->input->get('type')) ? intval($this->input->get('type')) : $edit_data['type'];
			$siteid = $this->get_siteid();
			$dbsource_data = $dbsource = array();
			$dbsource[] = L('please_select');
			$dbsource_data = $this->dbsource->select(array('siteid'=>$siteid), 'name');
			foreach ($dbsource_data as $dbs) {
				$dbsource[$dbs['name']] = $dbs['name'];
			}
			$module = $this->input->get('module') && trim($this->input->get('module')) ? trim($this->input->get('module')) : $edit_data['module'];
			$action = $this->input->get('action') && trim($this->input->get('action')) ? trim($this->input->get('action')) : $edit_data['action'];
			if ($edit_data['type'] == 0 || $edit_data['type'] == 1) $form_data = string2array($edit_data['data']);
			if ($module) $html = pc_tag_class($module);
			pc_base::load_app_func('global','template');
			include $this->admin_tpl('tag_edit');
		}
	}
	
	/**
	 * 标签向导列表
	 */
	public function lists() {
		$show_header = true;
		$page = $this->input->post('page') && intval($this->input->post('page')) ? intval($this->input->post('page')) : 1;
		$list = $this->db->listinfo('','id desc', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('tag_lists');
	}

	/**
	 * 删除标签向导
	 */
	public function del() {
		$id = $this->input->post('id') ? $this->input->post('id') : ($this->input->get('id') ? $this->input->get('id') : '');
		if(empty($id)) dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		if (is_array($id)) {
			foreach ($id as $key => $v) {
				if (intval($v)) {
					$id[$key] = intval($v);
				} else {
					unset($id[$key]);
				}
			}
			$sql = implode('\',\'', $id);
			$this->db->delete("id in ('$sql')");
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			$id = intval($id);
			if ($this->db->delete(array('id'=>$id))) {
				dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
			} else {
				dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
			}
		}
	}
}
?>