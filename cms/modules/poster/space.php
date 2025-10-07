<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);

class space extends admin {
	private $input, $setting, $db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		pc_base::load_app_func('global','poster');
		$setting = new_html_special_chars(getcache('poster', 'commons'));
		if ($this->setting) {
			$this->setting = $setting[$this->get_siteid()];
		} else {
			$this->setting = array();
		}
		$this->db = pc_base::load_model('poster_space_model');
	}
	
	public function init() {
		$TYPES = $this->template_type();
		$page = max(intval($this->input->get('page')), 1);
		$infos = $this->db->listinfo(array('siteid'=>$this->get_siteid()), '`spaceid`', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('space_list');
	}
	
	/**
	 * 添加广告版块
	 */
	public function add() {
		if (IS_POST) {
			$space = $this->check($this->input->post('space'));
			$space['setting'] = array2string($this->input->post('setting'));
			$space['siteid'] = $this->get_siteid();
			$spaceid = $this->db->insert($space, true);
			if ($spaceid) {
				if ($space['type']=='code') {
					$path = '{show_ad('.$space['siteid'].', '.$spaceid.')}';
				} else {
					$path = 'poster_js/'.$spaceid.'.js';
				}
				$this->db->update(array('path'=>$path), array('siteid'=>$this->get_siteid(), 'spaceid'=>$spaceid));
				dr_admin_msg(1,L('added_successful'), '?m=poster&c=space', '', 'add');
			}
		} else {
			$TYPES = $this->template_type();
			$poster_template = poster_template();
			$show_header = $show_validator = true;
			include $this->admin_tpl('space_add');
		}
	}
	
	/**
	 * 编辑广告版位
	 */
	public function edit() {
		$spaceid = intval($this->input->get('spaceid'));
		if (!$spaceid) dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if (IS_POST) {
			$space = $this->check($this->input->post('space'));
			$space['setting'] = array2string($this->input->post('setting'));
			if ($space['type']=='code') {
				$space['path'] = '{show_ad('.$this->get_siteid().', '.$spaceid.')}';
			} else {
				$space['path'] = 'poster_js/'.$spaceid.'.js';
			}
			if ($this->input->post('old_type') && $this->input->post('old_type')!=$space['type']) {
				$poster_db = pc_base::load_model('poster_model');
				$poster_db->delete(array('spaceid'=>$spaceid));
				$space['items'] = 0;
			}
			if ($this->db->update($space, array('spaceid'=>$spaceid))) dr_admin_msg(1,L('edited_successful'), '?m=poster&c=space', '', 'testIframe'.$spaceid);
		} else {
			$info = $this->db->get_one(array('spaceid' => $spaceid));
			$setting = string2array($info['setting']);
			$TYPES = $this->template_type();
			$poster_template = poster_template();
			$show_header = $show_validator = true;
			include $this->admin_tpl('space_edit');
		}
	}
	
	/**
	 * 广告版位调用代码
	 */
	public function public_call() {
		$sid = intval($this->input->get('sid'));
		if (!$sid) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER, '', 'call');
		$r = $this->db->get_one(array('spaceid'=>$sid, 'siteid'=>$this->get_siteid()));
		include $this->admin_tpl('space_call');
	}
	
	/**
	 * 广告预览
	 */
	public function public_preview() {
		$spaceid = intval($this->input->get('spaceid'));
		if (is_numeric($spaceid)) {
			$r = $this->db->get_one(array('spaceid'=>$spaceid, 'siteid'=>$this->get_siteid()));
			$scheme = SITE_PROTOCOL;
			if ($r['type']=='code') {
				$db = pc_base::load_model('poster_model');
				$rs = $db->get_one(array('spaceid'=>$r['spaceid'], 'siteid'=>$this->get_siteid()), 'setting', '`id` ASC');
				if ($rs['setting']) {
					$d = string2array($rs['setting']);
					$data = $d['code'];
				}
			} else {
				$path = APP_PATH.'index.php?m=poster&c=index&a=show_poster&id='.$r['spaceid'];
			}
			include $this->admin_tpl('space_preview');
		}
	}
	
	private function template_type() {
		return get_types();
	}
	
	/**
	 * 删除广告版位 
	 * @param	intval	$sid	广告版位的ID，当批量删除时系统会递归删除
	 */
	public function delete() {
		if ((!$this->input->get('spaceid') || empty($this->input->get('spaceid'))) && (!$this->input->post('spaceid') || empty($this->input->post('spaceid')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if (is_array($this->input->post('spaceid'))) {
				$ids = $this->input->post('spaceid');
				foreach($ids as $id) {
					$this->_del($id); //如果是批量操作，则递归数组
				}
			} elseif($this->input->get('spaceid')) {
				$spaceid = intval($this->input->get('spaceid'));
				$db = pc_base::load_model('poster_model');
				$db->delete(array('siteid'=>$this->get_siteid(), 'spaceid'=>$spaceid));
				$this->db->delete(array('siteid'=>$this->get_siteid(), 'spaceid' => $spaceid));
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	
	/**
	 * 广告位删除
	 * @param intval $spaceid 专题ID
	 */
	private function _del($spaceid = 0) {
		$spaceid = intval($spaceid);
		if (!$spaceid) return false;
		$db = pc_base::load_model('poster_model');
		$db->delete(array('siteid'=>$this->get_siteid(), 'spaceid'=>$spaceid));
		$this->db->delete(array('siteid'=>$this->get_siteid(), 'spaceid' => $spaceid));
		return true;
	}
	
	/**
	 * 广告模块配置
	 */
	public function setting() {
		if (IS_POST) {
			$setting = getcache('poster', 'commons');
			!$setting && $setting = array();
			$setting[$this->get_siteid()] = $this->input->post('setting');
			setcache('poster', $setting, 'commons'); //设置缓存
			$m_db = pc_base::load_model('module_model'); //调用模块数据模型
			$setting = array2string($this->input->post('setting'));
			$m_db->update(array('setting'=>$setting), array('module'=>ROUTE_M)); //将配置信息存入数据表中
			dr_admin_msg(1,L('setting_updates_successful'), HTTP_REFERER, '', 'setting');
		} else {
			$show_dialog = $show_header = true;
			@extract($this->setting); 
    		include $this->admin_tpl('setting');
		}
	}
	
	/**
	 * 更新js
	 */
	public function create_js($page = 0) {
		$page = max(intval($this->input->get('page')), 1);
		if ($page==1) {
			$total = $this->db->count(array('disabled'=>0, 'siteid'=>get_siteid()));
			if ($total) {
				$pages = ceil($total/SYS_ADMIN_PAGESIZE);
			}
		} else {
			$pages = $this->input->get('pages') ? intval($this->input->get('pages')) : 0;
		}
		$offset = ($page-1)*SYS_ADMIN_PAGESIZE;
		$data = $this->db->listinfo(array('disabled'=>0, 'siteid'=>get_siteid()), 'spaceid ASC', $page, SYS_ADMIN_PAGESIZE);
		$html = pc_base::load_app_class('html');
		foreach ($data as $d) {
			if ($d['type']!='code') {
				$html->create_js($d['spaceid']);
			} else {
				continue;
			}
		}
		$page++;
		if ($page>$pages) {
			dr_admin_msg(1,L('update_js_success'), '?m=poster&c=space&a=init&menuid='.$this->input->get('menuid'));
		} else {
			dr_admin_msg(1,L('update_js').'<font style="color:red">'.($page-1).'/'.$pages.'</font>', '?m=poster&c=space&a=create_js&menuid='.$this->input->get('menuid').'&page='.$page.'&pages='.$pages);
		}
	}
	
	/**
	 * 检测版位名称是否存在
	 */
	public function public_check_space() {
		if (!$this->input->get('name')) exit(0);
		$name = $this->input->get('name');
		if (CHARSET=='gbk') {
			$name = iconv('UTF-8', 'GBK', $name);
		}
		if ($this->input->get('spaceid')) {
			$spaceid = intval($this->input->get('spaceid'));
			$r = $this->db->get_one(array('spaceid' => $spaceid, 'siteid'=>$this->get_siteid()));
			if ($r['name'] == $name) {
				exit('1');
			}
		} 
		$r = $this->db->get_one(array('siteid' => $this->get_siteid(), 'name' => $name), 'spaceid');
		if ($r['spaceid']) {
			exit('0');
		} else {
			exit('1');
		}
	}
	
	/**
	 * 检查表单数据
	 * @param	Array	$data	表单传递过来的数组
	 * @return Array	检查后的数组
	 */
	private function check($data = array()) {
		if ($data['name'] == '') dr_admin_msg(0,L('name_plates_not_empty'), array('field' => 'name'));
		if(!$data['type']) dr_admin_msg(0,L('choose_space_type'), array('field' => 'type'));
		$info = $this->db->get_one(array('name'=>$data['name'], 'siteid'=>$this->get_siteid()), 'spaceid');
		if (($info['spaceid'] && $info['spaceid']!=$this->input->get('spaceid')) || ($info['spaceid'] && !$this->input->get('spaceid'))) {
			dr_admin_msg(0,L('space_exist'));
		}
		if ((!$data['width'] || intval($data['width'])==0) && in_array($data['type'], array('banner', 'fixure', 'float', 'couplet', 'imagechange', 'imagelist'))) {
			dr_admin_msg(0,L('plate_width_not_empty'), array('field' => 'width'));
		} else {
			$data['width'] = intval($data['width']);
		}
		if ((!$data['height'] || intval($data['height'])==0) && in_array($data['type'], array('banner', 'fixure', 'float', 'couplet', 'imagechange', 'imagelist'))) {
			dr_admin_msg(0,L('plate_height_not_empty'), array('field' => 'height'));
		} else {
			$data['height'] = intval($data['height']);
		}
		$TYPES = $this->template_type();
		return $data;
	}
}
?>