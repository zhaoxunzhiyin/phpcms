<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', '');

class formguide extends admin {
	
	private $db, $tablename, $m_db, $setting;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->tablename = '';
		$setting = new_html_special_chars(getcache('formguide', 'commons'));
		$this->setting = $setting[$this->get_siteid()];
		$this->db = pc_base::load_model('sitemodel_model');
	}
	
	//表单向导列表
	public function init() {
		$page = max(intval($this->input->get('page')), 1);
		$data = $this->db->listinfo(array('type' => 3, 'siteid'=>$this->get_siteid()), '`modelid` DESC', $page);
		$big_menu = array('javascript:artdialog(\'add\',\'?m=formguide&c=formguide&a=add\',\''.L('formguide_add').'\',700,500);void(0);', L('formguide_add'));
		include $this->admin_tpl('formguide_list');
	}
	
	/**
	 * 添加表单向导
	 */
	public function add() {
		if ($this->input->post('dosubmit')) {
			$setting = $this->input->post('setting');
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
			$this->sql_execute($create_sql);
			$form_public_field_array = getcache('form_public_field_array', 'model');
			if (is_array($form_public_field_array)) {
				foreach ($form_public_field_array as $k => $v) {
					$v['info']['modelid'] = $formid;
					$this->m_db->insert($v['info']);
					$sql = str_replace('formguide_table', $this->m_db->db_tablepre.'form_'.$info['tablename'], $v['sql']);
					$this->m_db->query($sql);
				}
			}
			showmessage(L('add_success'), '?m=formguide&c=formguide_field&a=init&formid='.$formid, '', 'add');
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
			$show_header = $show_validator = $show_scroll = 1;
			include $this->admin_tpl('formguide_add');
		}
	}
	
	/**
	 * 编辑表单向导
	 */
	public function edit() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			showmessage(L('illegal_operation'), HTTP_REFERER);
		}
		$formid = intval($this->input->get('formid'));
		$setting = $this->input->post('setting');
		if ($this->input->post('dosubmit')) {
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
			showmessage(L('update_success'), '?m=formguide&c=formguide&a=init&formid='.$formid, '', 'edit');
		} else {
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$data = $this->db->get_one(array('modelid'=>$formid));
			$data['setting'] = string2array($data['setting']);
			pc_base::load_sys_class('form', '', false);
			$show_header = $show_validator = $show_scroll = 1;
			include $this->admin_tpl('formguide_edit');
		}
	}
	
	/**
	 * 表单向导禁用、开启
	 */
	public function disabled() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			showmessage(L('illegal_operation'), HTTP_REFERER);
		}
		$formid = intval($this->input->get('formid'));
		$val = $this->input->get('val') ? intval($this->input->get('val')) : 0;
		$this->db->update(array('disabled'=>$val), array('modelid'=>$formid, 'siteid'=>$this->get_siteid()));
		showmessage(L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 * 预览
	 */
	public function public_preview() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			showmessage(L('illegal_operation'), HTTP_REFERER);
		}
		$formid = intval($this->input->get('formid'));
		$f_info = $this->db->get_one(array('modelid'=>$formid, 'siteid'=>$this->get_siteid()), 'name');
		define('CACHE_MODEL_PATH',CMS_PATH.'caches'.DIRECTORY_SEPARATOR.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
		require CACHE_MODEL_PATH.'formguide_form.class.php';
		$formguide_form = new formguide_form($formid);
		$forminfos_data = $formguide_form->get();
		$show_header = 1;
		include $this->admin_tpl('formguide_preview');
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
			showmessage(L('input_form_title'), HTTP_REFERER);
		}
		if ($data['tablename']=='') {
			showmessage(L('please_input_tallename'), HTTP_REFERER);
		}
		$r = $this->db->get_one(array('tablename'=>$data['tablename']), 'tablename, modelid');
		if ($r['modelid'] && (($r['modelid']!=$formid) || !$formid)) {
			showmessage(L('tablename_existed'), HTTP_REFERER);
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
			showmessage(L('operation_success'), HTTP_REFERER);
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
			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
			showmessage(L('illegal_operation'), HTTP_REFERER);
		}
	}
	
	/**
	 * 统计
	 */
	public function stat() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			showmessage(L('illegal_operation'), HTTP_REFERER);
		}
		$formid = intval($this->input->get('formid'));
		$fields = getcache('formguide_field_'.$formid, 'model');
		$f_info = $this->db->get_one(array('modelid'=>$formid, 'siteid'=>$this->get_siteid()), 'tablename');
		$tablename = 'form_'.$f_info['tablename'];
		$m_db = pc_base::load_model('sitemodel_field_model');
		$result = $m_db->select(array('modelid'=>$formid, 'formtype'=>'box'), 'field, setting');
		$m_db->change_table($tablename);
		$datas = $m_db->select(array(), '*');
		$total = count($datas);
		include $this->admin_tpl('formguide_stat');
	}
	
	/**
	 * 模块配置
	 */
	public function setting() {
		if ($this->input->post('dosubmit')) {
			$setting = getcache('formguide', 'commons');
			$setting[$this->get_siteid()] = $this->input->post('setting');
			setcache('formguide', $setting, 'commons'); //设置缓存
			$m_db = pc_base::load_model('module_model'); //调用模块数据模型
			$setting = array2string($this->input->post('setting'));  
			$m_db->update(array('setting'=>$setting), array('module'=>ROUTE_M)); //将配置信息存入数据表中
			
			showmessage(L('setting_updates_successful'), HTTP_REFERER, '', 'setting');
		} else {
			@extract($this->setting); 
    		include $this->admin_tpl('setting');
		}
	}
	
	/**
	 * 执行sql文件，创建数据表等
	 * @param string $sql sql语句
	 */
	private function sql_execute($sql) {
	    $sqls = $this->sql_split($sql);

		if (is_array($sqls)) {
			foreach ($sqls as $sql) {
				if (trim($sql) != '') {
					$this->m_db->query($sql);
				}
			}
		} else {
			$this->m_db->query($sqls);
		}
		return true;
	}
	
	/**
	 * 处理sql语句，执行替换前缀都功能。
	 * @param string $sql 原始的sql，将一些大众的部分替换成私有的
	 */
	private function sql_split($sql) {
		$database = pc_base::load_config('database');
		$dbcharset = $database['default']['charset'];
		if($this->m_db->version() > '4.1' && $dbcharset) {
			$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".$dbcharset, $sql);
		}
		$sql = str_replace("cms_form_table", $this->m_db->db_tablepre.'form_'.$this->tablename, $sql);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach ($queriesarray as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			$queries = array_filter($queries);
			foreach ($queries as $query) {
				$str1 = substr($query, 0, 1);
				if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
			}
			$num++;
		}
		return $ret;
	}
}
?>