<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class mood_admin extends admin {
	private $input;
	public $siteid;
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->siteid = $this->get_siteid();
	}
	
	//排行榜查看
	public function init() {
		$mood_program = getcache('mood_program', 'commons');
		$mood_program = isset($mood_program[$this->siteid]) ? $mood_program[$this->siteid] : array();
		$mood_db = pc_base::load_model('mood_model');
		$catid = $this->input->get('catid') && intval($this->input->get('catid')) ? intval($this->input->get('catid')) : '';
		$datetype = $this->input->get('datetype') && intval($this->input->get('datetype')) ? intval($this->input->get('datetype')) : 0;
		$order = $this->input->get('order') && intval($this->input->get('order')) ? intval($this->input->get('order')) : 0;
		$sql = '';
		if ($catid) {
			$sql = "`catid` = '$catid' AND `siteid` = '".$this->siteid."'";
			switch ($datetype) {
				case 1://今天
					$sql .= " AND `lastupdate` BETWEEN '".(strtotime(date('Y-m-d')." 00:00:00"))."' AND '".(strtotime(date('Y-m-d')." 23:59:59"))."'";
					break;
					
				case 2://昨天
					$sql .= " AND `lastupdate` BETWEEN '".(strtotime(date('Y-m-d')." 00:00:00")-86400)."' AND '".(strtotime(date('Y-m-d')." 23:59:59")-86400)."'";
					break;
					
				case 3://本周
					$week = date('w');
					if (empty($week)) $week = 7;
					$sql .= " AND `lastupdate` BETWEEN '".(strtotime(date('Y-m-d')." 23:59:59")-86400*$week)."' AND '".(strtotime(date('Y-m-d')." 23:59:59")+(86400*(7-$week)))."'";
					break;
				
				case 4://本月
					$day = date('t');
					$sql .= " AND `lastupdate` BETWEEN '".strtotime(date('Y-m-1')." 00:00:00")."' AND '".strtotime(date('Y-m-'.$day)." 23:59:59")."'";
					break;
					
				case 5://所有
					$sql .= " AND `lastupdate` <= '".SYS_TIME."'";
					break;
			}
			$sql_order = '';
			if ($order == '-1') {
				$sql_order = " `total` desc";
			} elseif ($order) {
				$sql_order = "`n$order` desc";
			}
			$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
			$data = $mood_db->listinfo($sql, $sql_order, $page, SYS_ADMIN_PAGESIZE);
			$content_db = pc_base::load_model('content_model'); 
			$contentid = '';
			foreach ($data as $v) {
				$contentid .= $contentid ? "','".$v['contentid'] : $v['contentid'];
			}
			$content_db->set_catid($catid);
			$content_data = $content_db->select("`id` IN ('$contentid')", 'id,url,title');
			foreach ($content_data as $k=>$v) {
				$content_data[$v['id']] = array('title'=>$v['title'], 'url'=>$v['url']);
				unset($content_data[$k]);
			}
			$pages = $mood_db->pages;
		}
		$order_list = array('-1'=>L('total'));
		foreach ($mood_program as $k=>$v) {
			if ($v['use']) {
				$order_list[$k]=$v['name'];
			}
		}
		pc_base::load_sys_class('form', '', 0);
		include $this->admin_tpl('mood_list');
	}
	
	//配置
	public function setting() {
		$mood_program = getcache('mood_program', 'commons');
		if (IS_POST) {
			$use = $this->input->post('use') ? $this->input->post('use') : '';
			$name = $this->input->post('name') ? $this->input->post('name') : '';
			$pic = $this->input->post('pic') ? $this->input->post('pic') : '';
			$data = array();
			foreach ($name as $k=>$v) {
				$data[$k] = array('use'=>$use[$k], 'name'=>$v, 'pic'=>$pic[$k]);
			}
			$mood_program[$this->siteid] = $data;
			setcache('mood_program', $mood_program, 'commons');
			dr_admin_msg(1,L('operation_success'), array('url'=>HTTP_REFERER));
		} else {
			$mood_program = isset($mood_program[$this->siteid]) ? $mood_program[$this->siteid] : array();
			include $this->admin_tpl('mood_setting');
		}
	}
}