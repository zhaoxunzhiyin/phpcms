<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class data extends admin {
	private $input,$db;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('datacall_model');
	}
	
	public function init() {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$list = $this->db->listinfo('','id desc', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('data_list');
	}
	
	public function add() {
		pc_base::load_app_func('global');
		if (IS_POST) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('name').L('empty'), array('field' => 'name'));
			$dis_type = $this->input->post('dis_type') && intval($this->input->post('dis_type')) ? intval($this->input->post('dis_type')) : 1;
			$cache = intval($this->input->post('cache'));
			$num = intval($this->input->post('num'));
			$type = intval($this->input->post('type'));
			//检查名称是否已经存在
			if ($this->db->count(array('name'=>$name)))  {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			$sql = array();
			if ($type == '1') { //自定义SQL
				$data = $this->input->post('data') && trim($this->input->post('data')) ? trim($this->input->post('data')) : dr_admin_msg(0,L('custom_sql').L('empty'));
				$sql = array('data'=>$data);
			} else { //模型配置方式
				$module = $this->input->post('module') && trim($this->input->post('module')) ? trim($this->input->post('module')) : dr_admin_msg(0,L('please_select_model'));
				$action = $this->input->post('action') && trim($this->input->post('action')) ? trim($this->input->post('action')) : dr_admin_msg(0,L('please_select_action'));
				$html = pc_tag_class($module);
				$data = array();
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
						$data[$key] = $$key;
					}
				}
				$sql = array('data'=>array2string($data), 'module'=>$module, 'action'=>$action);
			}
			
			if ($dis_type == 3) {
				$sql['template'] = $this->input->post('template') && trim($this->input->post('template')) ? trim($this->input->post('template')) : '';
			}
			//初始化数据
			$sql['name'] = $name;
			$sql['type'] = $type;
			$sql['dis_type'] = $dis_type;
			$sql['cache'] = $cache;
			$sql['num'] = $num;
			if ($id = $this->db->insert($sql,true)) {
				//当为JS时，输出模板文件
				if ($dis_type == 3) {
					$tpl = pc_base::load_sys_class('template_cache');
					$str = $this->db->get_one(array('id'=>$id), 'template');
					$str = $tpl->template_parse($str['template']);
					$filepath = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.'dbsource'.DIRECTORY_SEPARATOR;
					if(!is_dir($filepath)) {
						mkdir($filepath, 0777, true);
				    }
					@file_put_contents($filepath.$id.'.php', $str);
				}
				
				dr_admin_msg(1,L('operation_success'), '', '', 'add');
			} else {
				dr_admin_msg(0,L('operation_failure'));
			}
		} else {
			pc_base::load_sys_class('form','',0);
			$modules = array_merge(array(''=>L('please_select')),pc_base::load_config('modules'));
			$show_header = $show_validator = true;
			$type = intval($this->input->get('type'));
			$module = $this->input->get('module') && trim($this->input->get('module')) ? trim($this->input->get('module')) : '';
			$action = $this->input->get('action') && trim($this->input->get('action')) ? trim($this->input->get('action')) : '';
			if ($module) $html = pc_tag_class($module);
			pc_base::load_app_func('global','template');
			include $this->admin_tpl('data_add');
		}
	}
	
	public function edit() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		if (!$edit_data = $this->db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('notfound'));
		}
		pc_base::load_app_func('global');
		if (IS_POST) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('name').L('empty'), array('field' => 'name'));
			$dis_type = $this->input->post('dis_type') && intval($this->input->post('dis_type')) ? intval($this->input->post('dis_type')) : 1;
			$cache = intval($this->input->post('cache'));
			$num = intval($this->input->post('num'));
			$type = intval($this->input->post('type'));
			//检查名称是否已经存在
		if ($edit_data['name'] != $name) {
				if ($this->db->get_one(array('name'=>$name), 'id'))  {
					dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
				}
			}
			$sql = array();
			if ($type == '1') { //自定义SQL
				$data = $this->input->post('data') && trim($this->input->post('data')) ? trim($this->input->post('data')) : dr_admin_msg(0,L('custom_sql').L('empty'));
				$sql = array('data'=>$data);
			} else { //模型配置方式
				$module = $this->input->post('module') && trim($this->input->post('module')) ? trim($this->input->post('module')) : dr_admin_msg(0,L('please_select_model'));
				$action = $this->input->post('action') && trim($this->input->post('action')) ? trim($this->input->post('action')) : dr_admin_msg(0,L('please_select_action'));
				$html = pc_tag_class($module);
				$data = array();
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
						$data[$key] = $$key;
					}
				}
				$sql = array('data'=>array2string($data), 'module'=>$module, 'action'=>$action);
			}
			
			if ($dis_type == 3) {
				$sql['template'] = $this->input->post('template') && trim($this->input->post('template')) ? trim($this->input->post('template')) : '';
			}
			//初始化数据
			$sql['name'] = $name;
			$sql['type'] = $type;
			$sql['dis_type'] = $dis_type;
			$sql['cache'] = $cache;
			$sql['num'] = $num;
			if ($this->db->update($sql,array('id'=>$id))) {
				//当为JS时，输出模板文件
				if ($dis_type == 3) {
					$tpl = pc_base::load_sys_class('template_cache');
					$str = $this->db->get_one(array('id'=>$id), 'template');
					$str = $tpl->template_parse($str['template']);
					$filepath = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.'dbsource'.DIRECTORY_SEPARATOR;
					if(!is_dir($filepath)) {
						mkdir($filepath, 0777, true);
				    }
					@file_put_contents($filepath.$id.'.php', $str);
				}
				
				dr_admin_msg(1,L('operation_success'), '', '', 'edit');
			} else {
				dr_admin_msg(0,L('operation_failure'));
			}
		} else {
			pc_base::load_sys_class('form','',0);
			$modules = array_merge(array(''=>L('please_select')),pc_base::load_config('modules'));
			$show_header = $show_validator = true;
			$type = $this->input->get('type') ? intval($this->input->get('type')) : $edit_data['type'];
			$module = $this->input->get('module') && trim($this->input->get('module')) ? trim($this->input->get('module')) : $edit_data['module'];
			$action = $this->input->get('action') && trim($this->input->get('action')) ? trim($this->input->get('action')) : $edit_data['action'];
			if ($edit_data['type'] == 0) $form_data = string2array($edit_data['data']);
			if ($module) $html = pc_tag_class($module);
			pc_base::load_app_func('global','template');
			include $this->admin_tpl('data_edit');
		}
	}
	
	public function del() {
		$id = $this->input->get('id') ? $this->input->get('id') : '';
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
			if(empty($id)) dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			if ($this->db->delete(array('id'=>$id))) {
				dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
			} else {
				dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
			}
		}
	}
	
	public function public_name() {
		$name = $this->input->get('name') && trim($this->input->get('name')) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('name'))) : trim($this->input->get('name'))) : exit('0');
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : '';
 		$data = array();
		if ($id) {
			$data = $this->db->get_one(array('id'=>$id), 'name');
			if (!empty($data) && $data['name'] == $name) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('name'=>$name), 'id')) {
			exit('0');
		} else {
			exit('1');
		}
	}
}