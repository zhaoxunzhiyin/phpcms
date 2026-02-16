<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', '');

class formguide extends admin {
	
	private $input, $cache, $cache_api, $db, $tablename, $m_db, $setting, $sitemodel_field_db, $field;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->tablename = '';
		$setting = new_html_special_chars(getcache('formguide', 'commons'));
		$this->setting = $setting[$this->get_siteid()];
		if (!$this->setting) {
			$this->setting = array();
		}
		
		$this->db = pc_base::load_model('sitemodel_model');
	}
	
	//表单向导列表
	public function init() {
		$page = max(intval($this->input->get('page')), 1);
		$data = $this->db->listinfo(array('type' => 3, 'siteid'=>$this->get_siteid()), 'sort,modelid', $page, SYS_ADMIN_PAGESIZE);
		$this->cache();
		include $this->admin_tpl('formguide_list');
	}
	
	/**
	 * 添加表单向导
	 */
	public function add() {
		if (IS_POST) {
			$setting = $this->input->post('setting');
			!$setting['list_field'] && $setting['list_field'] = array(
				'dataid' => array(
					'use' => 1,
					'name' => L('Id'),
					'width' => '',
					'func' => '',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '',
					'func' => 'author',
				),
				'ip' => array(
					'use' => 1,
					'name' => L('用户ip'),
					'width' => '140',
					'func' => 'ip',
				),
				'datetime' => array(
					'use' => 1,
					'name' => L('时间'),
					'width' => '160',
					'func' => 'datetime',
				),
			);
			if ($setting['starttime']) {
				$setting['starttime'] = strtotime($setting['starttime']);
			}
			if ($setting['endtime']) {
				$setting['endtime'] = strtotime($setting['endtime']);
			}
			$info = $this->check_info($this->input->post('info'));
			$info['setting'] = array2string($setting);
			$info['siteid'] = $this->get_siteid();
			$info['addtime'] = SYS_TIME;
			$info['js_template'] = $info['show_js_template'];
			$info['type'] = 3;
			unset($info['show_js_template']);
			$this->tablename = $info['tablename'];
			$formid = $this->db->insert($info, true);
			define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'formguide'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
			$create_sql = file_get_contents(MODEL_PATH.'create.sql');
			$this->m_db = pc_base::load_model('sitemodel_field_model');
			$create_sql = str_replace("cms_form_table", $this->m_db->db_tablepre.'form_'.$this->tablename, $create_sql);
			$this->db->sql_execute($create_sql);
			$form_public_field_array = getcache('form_public_field_array', 'model');
			if (is_array($form_public_field_array)) {
				foreach ($form_public_field_array as $k => $v) {
					$v['info']['modelid'] = $formid;
					$this->m_db->insert($v['info']);
					$sql = str_replace('formguide_table', $this->m_db->db_tablepre.'form_'.$info['tablename'], $v['sql']);
					$this->m_db->query($sql);
				}
			}
			$this->cache();
			dr_admin_msg(1,L('add_success'), '?m=formguide&c=formguide_field&a=init&formid='.$formid, '', 'add');
		} else {
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$formid = intval($this->input->get('formid'));
			pc_base::load_sys_class('form', '', false);
			$show_header = $show_validator = $show_scroll = true;
			include $this->admin_tpl('formguide_add');
		}
	}
	
	/**
	 * 编辑表单向导
	 */
	public function edit() {
		if (IS_AJAX_POST) {
			$formid = intval($this->input->post('formid'));
			$setting = $this->input->post('setting');
			if ($setting['list_field']) {
				foreach ($setting['list_field'] as $t) {
					if ($t['func']
						&& !method_exists(pc_base::load_sys_class('function_list'), $t['func']) && !function_exists($t['func'])) {
						dr_json(0, L('列表回调函数['.$t['func'].']未定义'));
					}
				}
			}
			$setting['list_field'] = dr_list_field_order($setting['list_field']);
			!$setting['list_field'] && $setting['list_field'] = array(
				'dataid' => array(
					'use' => 1,
					'name' => L('Id'),
					'width' => '',
					'func' => '',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '',
					'func' => 'author',
				),
				'ip' => array(
					'use' => 1,
					'name' => L('用户ip'),
					'width' => '140',
					'func' => 'ip',
				),
				'datetime' => array(
					'use' => 1,
					'name' => L('时间'),
					'width' => '160',
					'func' => 'datetime',
				),
			);
			if ($setting['starttime']) {
				$setting['starttime'] = strtotime($setting['starttime']);
			}
			if ($setting['endtime']) {
				$setting['endtime'] = strtotime($setting['endtime']);
			}
			$info = $this->check_info($this->input->post('info'), $formid);
			$info['setting'] = array2string($setting);
			$info['js_template'] = $info['show_js_template'];
			unset($info['show_js_template']);
			$this->db->update($info, array('modelid'=>$formid));
			$this->cache();
			dr_json(1, L('update_success'), array('url' => '?m=formguide&c=formguide&a=init&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
				dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
			}
			$formid = intval($this->input->get('formid'));
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$this->m_db = pc_base::load_model('sitemodel_field_model');
			$this->field = $this->m_db->select(array('siteid'=>$siteid, 'modelid'=>$formid, 'disabled'=>0),'*','','listorder ASC,fieldid ASC');
			$sys_field = sys_field(array('dataid', 'userid', 'username', 'datetime', 'ip'));
			$data = $this->db->get_one(array('modelid'=>$formid));
			$data['setting'] = string2array($data['setting']);
			!$data['setting']['list_field'] && $data['setting']['list_field'] = array(
				'dataid' => array(
					'use' => 1,
					'name' => L('Id'),
					'width' => '',
					'func' => '',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '',
					'func' => 'author',
				),
				'ip' => array(
					'use' => 1,
					'name' => L('用户ip'),
					'width' => '140',
					'func' => 'ip',
				),
				'datetime' => array(
					'use' => 1,
					'name' => L('时间'),
					'width' => '160',
					'func' => 'datetime',
				),
			);
			$field = dr_list_field_value($data['setting']['list_field'], $sys_field, $this->field);
			$page = intval($this->input->get('page'));
			pc_base::load_sys_class('form', '', false);
			$show_validator = $show_scroll = true;
			include $this->admin_tpl('formguide_edit');
		}
	}
	
	/**
	 * 表单向导禁用、开启
	 */
	public function disabled() {
		$formid = intval($this->input->get('formid'));
		if (!$formid || empty($formid)) {
			dr_admin_msg(0,L('illegal_operation'));
		}
		$r = $this->db->get_one(array('modelid'=>$formid,'siteid'=>$this->get_siteid()));
		$value = $r['disabled'] ? '0' : '1';
		$this->db->update(array('disabled'=>$value), array('modelid'=>$formid, 'siteid'=>$this->get_siteid()));
		$this->cache();
		dr_json(1, L($value ? '禁用成功' : '启用成功'), array('value' => $value));
	}
	// 排序
	public function public_order_edit() {
		$formid = intval($this->input->get('formid'));
		// 查询数据
		$r = $this->db->get_one(array('modelid'=>$formid,'siteid'=>$this->get_siteid()));
		if (!$r) {
			dr_json(0, L('数据#'.$formid.'不存在'));
		}
		$value = (int)$this->input->get('value');
		$this->db->update(array('sort'=>$value),array('modelid'=>$formid,'siteid'=>$this->get_siteid()));
		$this->cache();
		dr_json(1, L('操作成功'));
	}
	
	/**
	 * ajax 检测表是重复
	 */
	public function public_checktable() {
		if ($this->input->get('formid') && !empty($this->input->get('formid'))) {
			$formid = intval($this->input->get('formid'));
		}
		$r = $this->db->get_one(array('tablename'=>$this->input->get('tablename')), 'tablename, modelid');
		if (!$r['modelid']) {
			exit('1');
		} elseif ($r['modelid'] && ($r['modelid']==$formid)) {
			exit('1');
		} else {
			exit('0');
		}
	}
	
	/**
	 * 判断表单数据合法性
	 * @param array $data 表单数组
	 * @param intval $formid 表单id
	 */
	private function check_info($data = array(), $formid = 0) {
		if (empty($data) || $data['name']=='') {
			dr_admin_msg(0,L('input_form_title'), HTTP_REFERER);
		}
		if ($data['tablename']=='') {
			dr_admin_msg(0,L('please_input_tallename'), HTTP_REFERER);
		}
		$r = $this->db->get_one(array('tablename'=>$data['tablename']), 'tablename, modelid');
		if ($r['modelid'] && (($r['modelid']!=$formid) || !$formid)) {
			dr_admin_msg(0,L('tablename_existed'), HTTP_REFERER);
		}
		return $data;
	}
	
	/**
	 * 删除表单向导
	 */
	public function delete() {
		$siteid = $this->get_siteid();
		if ($this->input->get('formid') && !empty($this->input->get('formid'))) {
			$formid = intval($this->input->get('formid'));
			$m_db = pc_base::load_model('sitemodel_field_model');
			$m_db->delete(array('modelid'=>$formid, 'siteid'=>$siteid));
			$m_info = $this->db->get_one(array('modelid'=>$formid), 'tablename');
			$tablename = $m_db->db_tablepre.'form_'.$m_info['tablename'];
			$m_db->query("DROP TABLE `$tablename`");
			$this->db->delete(array('modelid'=>$formid, 'siteid'=>$siteid));
			$this->cache();
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} elseif ($this->input->post('formid') && !empty($this->input->post('formid'))) {
			$m_db = pc_base::load_model('sitemodel_field_model');
			$m_db->delete(array('modelid'=>$formid, 'siteid'=>$siteid));
			if (is_array($this->input->post('formid'))) {
				foreach ($this->input->post('formid') as $fid) {
					$m_info = $this->db->get_one(array('modelid'=>$fid), 'tablename');
					$tablename = $m_db->db_tablepre.'form_'.$m_info['tablename'];
					$m_db->query("DROP TABLE `$tablename`");
					$this->db->delete(array('modelid'=>$fid, 'siteid'=>$siteid));
				}
			}
			$this->cache();
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		}
	}
	
	/**
	 * 统计
	 */
	public function stat() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		}
		$formid = intval($this->input->get('formid'));
		$fields = getcache('model_field_'.$formid, 'model');
		$f_info = $this->db->get_one(array('modelid'=>$formid, 'siteid'=>$this->get_siteid()), 'tablename');
		$tablename = 'form_'.$f_info['tablename'];
		$m_db = pc_base::load_model('sitemodel_field_model');
		$result = $m_db->select(array('modelid'=>$formid, 'formtype'=>'box'), 'field, setting');
		$m_db->table($tablename);
		$datas = $m_db->select(array(), '*');
		$total = dr_count($datas);
		include $this->admin_tpl('formguide_stat');
	}
	
	/**
	 * 模块配置
	 */
	public function setting() {
		if (IS_POST) {
			$post = $this->input->post('setting');
			$setting = getcache('formguide', 'commons');
			!$setting && $setting = array();
			$setting[$this->get_siteid()] = $post;
			setcache('formguide', $setting, 'commons'); //设置缓存
			$m_db = pc_base::load_model('module_model'); //调用模块数据模型
			if ($post['code'] && (intval($post['codelen'])<2 || intval($post['codelen'])>8)) {
				dr_admin_msg(0, L('setting_noe_code_len'));
			}
			$setting = array2string($post);
			$m_db->update(array('setting'=>$setting), array('module'=>ROUTE_M)); //将配置信息存入数据表中
			
			dr_admin_msg(1,L('setting_updates_successful'), HTTP_REFERER, '', 'setting');
		} else {
			if ($this->setting) {
				@extract($this->setting); 
			}
			include $this->admin_tpl('setting');
		}
	}

	// 缓存
	public function cache() {
		$this->cache_api->cache('formguidemodel');
		$this->cache_api->cache('sitemodel');
		$this->cache_api->del_file();
	}
}
?>