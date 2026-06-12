<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_app_func('global', 'poster');

class poster extends admin {
	private $input, $db, $s_db, $setting, $attachment_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->s_db = pc_base::load_model('poster_space_model');
		$this->db = pc_base::load_model('poster_model');
		$setting = new_html_special_chars(getcache('poster', 'commons'));
		if ($this->setting) {
			$this->setting = $setting[$this->get_siteid()];
		} else {
			$this->setting = array();
		}
	}
	
	/**
	 * 广告列表
	 */
	public function init() {
		$spaceid = $this->input->get('spaceid') ? intval($this->input->get('spaceid')) : 0;
		if (!isset($spaceid) || empty($spaceid)) {
			dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		}
		$page = max($this->input->get('page'), 1);
		$infos = $this->db->listinfo(array('spaceid'=>$spaceid, 'siteid'=>$this->get_siteid()), '`listorder` ASC, `id` DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		pc_base::load_sys_class('format', '', 0);
		$types = array('images'=>L('photo'), 'flash'=>L('flash'), 'text'=>L('title'));
		$show_dialog = true;
		include $this->admin_tpl('poster_list');
	}
	
	/**
	 * 添加广告
	 */
	public function add() {
		if (IS_POST) {
			$poster = $this->check($this->input->post('poster'));
			$setting = $this->check_setting($this->input->post('setting'), $poster['type']);
			$poster['siteid'] = $this->get_siteid();
			$sinfo = $this->s_db->get_one(array('spaceid' => $poster['spaceid'], 'siteid'=>$poster['siteid']), 'name, type');
			$settings = $this->get_setting($sinfo['type']);
			!$poster['name'] && dr_admin_msg(0,L('please_input_name'), array('field' => 'name'));
			if ($this->db->count(array('name'=>$poster['name'], 'siteid'=>$poster['siteid']))) {
				dr_admin_msg(0,L('poster_title').L('exists'), array('field' => 'name'));
			}
			!$poster['type'] && dr_admin_msg(0,L('choose_ads_type'), array('field' => 'type'));
			if(array_key_exists('text', $settings['type'])) {
				if($sinfo['type']=='text') {
					!$setting[1]['linkurl'] && dr_admin_msg(0,L('link_content'), array('field' => 'linkurl'));
				} elseif($sinfo['type']=='code') {
					!$setting['code'] && dr_admin_msg(0,L('input_code'), array('field' => 'code'));
				}
			}
			$poster['setting'] = array2string($setting);
			$poster['addtime'] = SYS_TIME;
			$id = $this->db->insert($poster, true);
			if ($id) {
				$this->s_db->update(array('items'=>'+=1'), array('spaceid'=>$poster['spaceid'], 'siteid'=>$this->get_siteid()));
				$this->create_js($poster['spaceid']);
				if(is_array($setting['images'])) {
					foreach ($setting['images'] as $im) {
						$imgs[] = $im['imageurl'];
					}
				}
				if (SYS_ATTACHMENT_STAT) {
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_update($imgs, 'poster-'.$id, 1);
				}
				dr_admin_msg(1,L('add_ads_success'), array('url'=>SELF.'?m=poster&c=space&a=init&s=3&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
			} else {
				dr_admin_msg(0,L('operation_failure'), array('url'=>SELF.'?m=poster&c=space&a=init&s=3&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
			}
		} else {
			$spaceid = intval($this->input->get('spaceid'));
			$sinfo = $this->s_db->get_one(array('spaceid' => $spaceid, 'siteid'=>$this->get_siteid()), 'name, type');
			$setting = $this->get_setting($sinfo['type']);
			$TYPES = get_types();
			$default = dr_count($setting)>0 ? L('please_select').'&nbsp;&nbsp;&nbsp;&nbsp;' : '';
		}
		pc_base::load_sys_class('form', '', 0);
		include $this->admin_tpl('poster_add');
	}
	
	/**
	 * 广告修改
	 */
	public function edit() {
		if (!intval($this->input->get('id'))) dr_admin_msg(0,L('illegal_action'));
		if (IS_POST) {
			$poster = $this->check($this->input->post('poster'));
			$setting = $this->check_setting($this->input->post('setting'), $poster['type']);
			$sinfo = $this->s_db->get_one(array('spaceid' => intval($this->input->get('spaceid')), 'siteid'=>$this->get_siteid()), 'name, type');
			$settings = $this->get_setting($sinfo['type']);
			!$poster['name'] && dr_admin_msg(0,L('please_input_name'), array('field' => 'name'));
			if ($this->db->count(array('id<>'=>intval($this->input->get('id')), 'name'=>$poster['name'], 'siteid'=>$this->get_siteid()))) {
				dr_admin_msg(0,L('poster_title').L('exists'), array('field' => 'name'));
			}
			!$poster['type'] && dr_admin_msg(0,L('choose_ads_type'), array('field' => 'type'));
			if(array_key_exists('text', $settings['type'])) {
				if($sinfo['type']=='text') {
					!$setting[1]['linkurl'] && dr_admin_msg(0,L('link_content'), array('field' => 'linkurl'));
				} elseif($sinfo['type']=='code') {
					!$setting['code'] && dr_admin_msg(0,L('input_code'), array('field' => 'code'));
				}
			}
			$poster['setting'] = array2string($setting);
			$this->db->update($poster, array('id'=>intval($this->input->get('id')), 'siteid'=>$this->get_siteid()));
			$this->create_js(intval($this->input->get('spaceid')));
			if(is_array($setting['images'])) {
				foreach ($setting['images'] as $im) {
					$imgs[] = $im['imageurl'];
				}
			}
			if(SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($imgs, 'poster-'.$this->input->get('id'), 1);
			}
			dr_admin_msg(1,L('edit_ads_success'), array('url'=>SELF.'?m=poster&c=poster&a=init&spaceid='.$this->input->get('spaceid').'&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			$info = $this->db->get_one(array('id'=>$this->input->get('id'), 'siteid'=>$this->get_siteid()));
			$sinfo = $this->s_db->get_one(array('spaceid' => $info['spaceid'], 'siteid'=>$this->get_siteid()), 'name, type');
			$setting = $this->get_setting($sinfo['type']);
			$TYPES = get_types();
			$info['setting'] = string2array($info['setting']);
			$default = dr_count($setting)>0 ? L('please_select').'&nbsp;&nbsp;&nbsp;&nbsp;' : '';
			pc_base::load_sys_class('form', '', 0);
			include $this->admin_tpl('poster_edit');
		}
	}
	
	/**
	 * 广告排序
	 */
	public function listorder() {
		if ($this->input->post('listorder') && is_array($this->input->post('listorder'))) {
			$listorder = $this->input->post('listorder');
			foreach ($listorder as $k => $v) {
				
				$this->db->update(array('listorder'=>$v), array('id'=>$k));
			}
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 * 生成广告js文件
	 * @param intval $id 广告版位ID
	 * @return boolen 成功返回true
	 */
	private function create_js($id = 0) {
		
		$html = pc_base::load_app_class('html');
		if (!$html->create_js($id)) showmessge($html->msg, HTTP_REFERER);
		return true;
	}
	
	/**
	 * 启用、停用广告。此方法不真正执行操作，调用真正的操作方法
	 * @param intval $id 广告ID
	 */
	public function public_approval() {
		if (!$this->input->post('id') || !is_array($this->input->post('id'))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			$ids = $this->input->post('id');
			foreach($ids as $id) {
				$this->_approval($id);
			}
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	private function _approval($id = 0) {
		$id = intval($id);
		if (!$id) return false;
		$this->db->update(array('disabled'=>intval($this->input->get('passed'))), array('id'=>$id, 'siteid'=>$this->get_siteid()));
		return true;
	}
	
	/**
	 * 删除广告 此方法不真正执行删除操作，调用真正的删除操作方法
	 * @param invtal $id 广告ID
	 */
	public function delete() {
		if (!$this->input->post('id') || !is_array($this->input->post('id'))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			$ids = $this->input->post('id');
			foreach($ids as $id) {
				$this->_del($id);
			}
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/***
	 * 广告删除
	 */
	private function _del($id = 0) {
		$id = intval($id);
		if (!$id) return false;
		$r = $this->db->get_one(array('id'=>$id, 'siteid'=>$this->get_siteid()), 'spaceid');
		$this->s_db->update(array('items'=>'-=1'), array('spaceid'=>$r['spaceid'], 'siteid'=>$this->get_siteid()));
		$this->db->delete(array('id'=>$id, 'siteid'=>$this->get_siteid()));
		if (SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
			$this->attachment_db = pc_base::load_model('attachment_model');
			$keyid = 'poster-'.$id;
			$this->attachment_db->api_delete($keyid);
		}
		return true;
	}
	
	/**
	 * 广告统计
	 */
	public function stat() {
		if (!$this->input->get('id')) dr_admin_msg(0,L('illegal_operation'));
		$info = $this->db->get_one(array('id'=>intval($this->input->get('id'))), 'spaceid');
		/** 
		 *如果设置了日期查询，设置查询的开始时间和结束时间
		 */
		$sdb = pc_base::load_model('poster_stat_model'); //调用广告统计的数据模型
		$year = date('Y', SYS_TIME);
        $month = date('m', SYS_TIME);
        $day = date('d', SYS_TIME);
        $where = $group = $order = '';
        $fields = '*';
        $where = "`pid`='".$this->input->get('id')."' AND `siteid`='".$this->get_siteid()."'";
		if ($this->input->get('range') == 2) { //昨天的统计
            $fromtime = mktime(0, 0, 0, $month, $day-2, $year);
            $totime = mktime(0, 0, 0, $month, $day-1, $year);
            $where .= " AND `clicktime`>='".$fromtime."'";
            $where .= " AND `clicktime`<='".$totime."'";
        } elseif(is_numeric($this->input->get('range'))) { //如果设置了查询的天数
            $fromtime = mktime(0, 0, 0, $month, $day-$this->input->get('range'), $year);
            $where .= " AND `clicktime`>='".$fromtime."'";
        }
        $order = '`clicktime` DESC';
        
        //如果设置了按点击、展示统计
        $click = $this->input->get('click') ? intval($this->input->get('click')) : 0;
        if (is_numeric($click)) {
        	$where .= " AND `type`='".$click."'";
        	
        	//如果设置了按地区或者按ip分类
	        if ($this->input->get('group')) {
	        	$group = " `".preg_replace('#`#', '', $this->input->get('group'))."`";
	        	$fields = "*, COUNT(".$group.") AS num";
	        	$order = " `num` DESC";
	        } 
	        $r = $sdb->get_one($where, 'COUNT(*) AS num', '', $group); //取得总数
        } else {
        	$r = $sdb->get_one($where, 'COUNT(*) AS num');
        }
		$page = max(intval($this->input->get('page')), 1);
		$curr_page = SYS_ADMIN_PAGESIZE;
		$limit = ($page-1)*$curr_page.','.$curr_page;
		$pages = pages($r['num'], $page, SYS_ADMIN_PAGESIZE); //生成分页
		$data = $sdb->select($where, $fields, $limit, $order, $group);
		$selectstr = $sdb->get_list($this->input->get('year')); //取得历史查询下拉框，有历史数据查询时，会自动换表
		pc_base::load_sys_class('format', '', 0);
		$show_header = true;
		unset($r);
		include $this->admin_tpl('poster_stat');
	}
	
	/**
	 * 根据版位的类型，得到版位的配置信息。如广告类型等
	 * @param string  $type 版位的类型,默认情况下是一张图片或者动画
	 * return boolean  
	 */
	private function get_setting($type) {
		$data = $poster_template = array();
		$poster_template = poster_template();
		if (is_array($poster_template) && !empty($poster_template)) {
			$data = $poster_template[$type];
		}
		return $data;
	}
	
	/**
	 * 检查广告属性信息
	 * @param array $data
	 * return array
	 */
	private function check($data) {
		if (!isset($data['name']) || empty($data['name'])) dr_admin_msg(0,L('adsname_no_empty'), HTTP_REFERER);
		if (!isset($data['type']) || empty($data['type'])) dr_admin_msg(0,L('no_ads_type'), HTTP_REFERER);
		$data['startdate'] = $data['startdate'] ? strtotime($data['startdate']) : SYS_TIME;
		$data['enddate'] = $data['enddate'] ? strtotime($data['enddate']) : strtotime('next month', $data['startdate']);
		if($data['startdate']>=$data['enddate']) $data['enddate'] = strtotime('next month', $data['startdate']);
		return $data;
	}
	
	/**
	 * 检查广告的内容信息，如图片、flash、文字
	 * @param array $setting
	 * @param string $type 广告的类型
	 * @return array
	 */
	private function check_setting($setting = array(), $type = 'images') {
		switch ($type) {
			case 'images':
				unset($setting['flash'], $setting['text']);
				if(is_array($setting['images'])) {
					$tag = 0;
					foreach ($setting['images'] as $k => $s) {
						if($s['linkurl']=='http://') {
							$setting['images'][$k]['linkurl'] = '';
						}
						if (!$s['imageurl']) unset($setting['images'][$k]);
						else $tag = 1;
					}
					if (!$tag) dr_admin_msg(0,L('no_setting_photo'));
				}
				break;
				
			case 'flash':
				unset($setting['images'], $setting['text']);
				if (is_array($setting['flash'])) {
					$tag = 0;
					foreach ($setting['flash'] as $k => $s) {
						if (!$s['flashurl']) unset($setting['flash'][$k]);
						else $tag = 1;
					}
					if (!$tag) dr_admin_msg(0,L('no_flash_path'));
				}
				break;
			
			case 'text':
				unset($setting['images'], $setting['flash']);
				if ((!isset($setting['text'][1]['title']) || empty($setting['text'][1]['title'])) && (!isset($setting['text']['code']) || empty($setting['text']['code']))) {
					dr_admin_msg(0,L('no_title_info'));
				}
				break;
		}
		return $setting[$type];
	}
	
	/**
	 * ajax检查广告名的合法性
	 */
	public function public_check_poster() {
		if (!$this->input->get('name')) exit(0);
		$name = $this->input->get('name');
		if (CHARSET=='gbk') {
			$name = safe_replace(iconv('UTF-8', 'GBK', $name));
		}
		if ($this->input->get('id')) {
			$spaceid = intval($this->input->get('spaceid'));
			$r = $this->db->get_one(array('id' => $id));
			if($r['name'] == $name) {
				exit('1');
			}
		} 
		$r = $this->db->get_one(array('siteid' => $this->get_siteid(), 'name' => $this->input->get('name')), 'id');
		if ($r['id']) {
			exit('0');
		} else {
			exit('1');
		}
	}
}
?>