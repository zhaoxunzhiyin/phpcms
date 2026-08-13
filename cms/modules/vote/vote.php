<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class vote extends admin {
	private $input, $setting, $db, $db2, $cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->setting = new_html_special_chars(getcache('vote', 'commons'));
		$this->db = pc_base::load_model('vote_subject_model');
		$this->db2 = pc_base::load_model('vote_option_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}

	public function init() {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo(array('siteid'=>$this->get_siteid()),'subjectid DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('vote_list'); 
 	}
	
	/*
	 *判断结束时间是否比当前时间小  
	 */
	public function checkdate() {
		$nowdate = date('Y-m-d',SYS_TIME);
		$todate = $this->input->get('todate');
		if($todate > $nowdate){
			exit('1');
		}else {
			exit('0');
		}
	}
	
	/**
	 * 添加投票
	 */
	public function add() {
		//读取配置文件
		$data = array();
		$data = $this->setting;
		$siteid = $this->get_siteid();//当前站点
		if(IS_POST) {
			$subject = $this->input->post('subject');
			$option = $this->input->post('option');
			$vote_subject = $this->input->post('vote_subject');
			if((!$subject['subject']) || empty($subject['subject'])) dr_admin_msg(0,L('input').L('vote_title'),array('field' => 'title'));
			if(!$option[0]) dr_admin_msg(0,L('input').L('vote_option'),array('field' => 'option1'));
			if(!$option[1]) dr_admin_msg(0,L('input').L('vote_option'),array('field' => 'option2'));
			if(!$vote_subject['style']) dr_admin_msg(0,L('select_style'),array('field' => 'style'));
			if ($this->db->count(array('subject'=>$subject['subject']))) {
				dr_admin_msg(0,L('vote_title').L('exists'),array('field' => 'title'));
			}
			$subject['addtime'] = SYS_TIME;
			if(!$subject['fromdate']) $subject['fromdate'] = dr_date(SYS_TIME, 'Y-m-d');
			if(!$subject['todate']) $subject['todate'] = dr_date(SYS_TIME, 'Y-m-d');
			$subject['siteid'] = $this->get_siteid();
			if(empty($subject['subject'])) {
				dr_admin_msg(0,L('vote_title_noempty'),'?m=vote&c=vote&a=add');
			}
 			//记录选项条数 optionnumber 
			$subject['optionnumber'] = dr_count($option);
			$subject['template'] = $vote_subject['vote_tp_template'];
			
			$subjectid = $this->db->insert($subject,true);
			if(!$subjectid) return FALSE; //返回投票ID值, 以备下面添加对应选项用,不存在返回错误
			//添加选项操作
			$this->db2->add_options($option,$subjectid,$this->get_siteid());
			//生成JS文件
			$this->update_votejs($subjectid);
			if($this->input->post('from_api')) {
				dr_admin_msg(1,L('operation_success'),'?m=vote&c=vote&a=add','', '',"$(function(){dialogOpener.$('#voteid').val('".$subjectid."');ownerDialog.close();})");
			} else {
				dr_admin_msg(1,L('operation_success'),'?m=vote&c=vote','','add');
 			}
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			@extract($data[$siteid]);
			//模版
			pc_base::load_app_func('global', 'admin');
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			include $this->admin_tpl('vote_add');
		}

	}

	/**
	 * 编辑投票
	 */
	public function edit() {

		if(IS_POST){
			//验证数据正确性
			$subjectid = intval($this->input->get('subjectid'));
			if($subjectid < 1) return false;
			$subject = $this->input->post('subject');
			$option = $this->input->post('option');
			$newoption = $this->input->post('newoption');
			$vote_subject = $this->input->post('vote_subject');
			if((!$subject['subject']) || empty($subject['subject'])) dr_admin_msg(0,L('input').L('vote_title'),array('field' => 'title'));
			if(!$vote_subject['style']) dr_admin_msg(0,L('select_style'),array('field' => 'style'));
			if ($this->db->count(array('subjectid<>'=>$subjectid, 'subject'=>$subject['subject']))) {
				dr_admin_msg(0,L('vote_title').L('exists'),array('field' => 'title'));
			}
			$this->db2->update_options($option);//先更新已有 投票选项,再添加新增加投票选项
			if(is_array($newoption)&&!empty($newoption)){
				$siteid = $this->get_siteid();//新加选项站点ID
				$this->db2->add_options($newoption,$subjectid,$siteid);
			}
			//模版 
			$subject['template'] = $vote_subject['vote_tp_template'];
			if ($newoption) {
				$subject['optionnumber'] = dr_count($option)+dr_count($newoption);
			}
			$this->db->update($subject,array('subjectid'=>$subjectid));//更新投票选项总数
			$this->update_votejs($subjectid);//生成JS文件
			dr_admin_msg(1,L('operation_success'),'?m=vote&c=vote&a=edit','', 'edit');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			
			//解出投票内容
			$info = $this->db->get_one(array('subjectid'=>$this->input->get('subjectid')));
			if(!$info) dr_admin_msg(0,L('operation_success'));
			extract($info);
				
			//解出投票选项
			$options = $this->db2->get_options($this->input->get('subjectid'));
			
			//模版
			pc_base::load_app_func('global', 'admin');
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}

			include $this->admin_tpl('vote_edit');
		}

	}

	/**
	 * 删除投票 
	 * @param	intval	$sid	投票的ID，递归删除
	 */
	public function delete() {
		if((!$this->input->get('subjectid') || empty($this->input->get('subjectid'))) && (!$this->input->post('subjectid') || empty($this->input->post('subjectid')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('subjectid'))){
				foreach($this->input->post('subjectid') as $subjectid_arr) {
					//删除对应投票的选项
					$this->db2->del_options($subjectid_arr);
					$this->db->delete(array('subjectid'=>$subjectid_arr));
				}
				dr_admin_msg(1,L('operation_success'),'?m=vote&c=vote');
			}else{
				$subjectid = intval($this->input->get('subjectid'));
				if($subjectid < 1) return false;
				//删除对应投票的选项
				$this->db2->del_options($subjectid);

				//删除投票
				$this->db->delete(array('subjectid'=>$subjectid));
				$result = $this->db->delete(array('subjectid'=>$subjectid));
				if($result)
				{
					dr_admin_msg(1,L('operation_success'),'?m=vote&c=vote');
				}else {
					dr_admin_msg(0,L("operation_failure"),'?m=vote&c=vote');
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	/**
	 * 说明:删除对应投票选项
	 * @param  $optionid
	 */
	public function del_option() {
		$result = $this->db2->del_option($this->input->get('optionid'));
		if($result) {
			echo 1;
		} else {
			echo 0;
		}
	} 
	
	
	/**
	 * 投票模块配置
	 */
	public function setting() {
		//读取配置文件
		$data = array();
 		$siteid = $this->get_siteid();//当前站点 
		//更新模型数据库,重设setting 数据. 
		$m_db = pc_base::load_model('module_model');
		$data = $m_db->select(array('module'=>'vote'));
		$setting = string2array($data[0]['setting']);
		$now_seting = $setting[$siteid]; 
 		if(IS_POST) {
			//多站点存储配置文件
			$siteid = $this->get_siteid();//当前站点
			$setting[$siteid] = $this->input->post('setting');
			//更新模型数据库,重设setting 数据. 
 			$set = array2string($setting);
			$m_db->update(array('setting'=>$set), array('module'=>ROUTE_M));
			$this->cache_api->cache('vote_setting');
			dr_json(1, L('setting_updates_successful'));
		} else {
			@extract($now_seting);
			pc_base::load_sys_class('form', '', 0);
			//模版
			pc_base::load_app_func('global', 'admin');
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			include $this->admin_tpl('setting');
		}
	}


	/**
	 * 检查表单数据
	 * @param	Array	$data	表单传递过来的数组
	 * @return Array	检查后的数组
	 */
	private function check($data = array()) {
		if($data['name'] == '') dr_admin_msg(0,L('name_plates_not_empty'));
		if(!isset($data['width']) || $data['width']==0) {
			dr_admin_msg(0,L('plate_width_not_empty'), HTTP_REFERER);
		} else {
			$data['width'] = intval($data['width']);
		}
		if(!isset($data['height']) || $data['height']==0) {
			dr_admin_msg(0,L('plate_height_not_empty'), HTTP_REFERER);
		} else {
			$data['height'] = intval($data['height']);
		}
		return $data;
	}
		
	/**
	 * 投票结果统计
	 */
	public function statistics() {
		$subjectid = intval($this->input->get('subjectid'));
		if(!$subjectid){
			dr_admin_msg(0,L('illegal_operation'));
		}
		$show_validator = $show_scroll = $show_header = true;
		//获取投票信息
		$sdb = pc_base::load_model('vote_data_model'); //加载投票统计的数据模型
		$infos = $sdb->select("subjectid = $subjectid",'data');	
		//新建一数组用来存新组合数据
		$total = 0;
		$vote_data =array();
		$vote_data['total'] = 0;//所有投票选项总数
		$vote_data['votes'] = 0;//投票人数
		//循环每个会员的投票记录
		foreach($infos as $subjectid_arr) {
				extract($subjectid_arr);
				$arr = string2array($data);
				foreach($arr as $key => $values){
					$vote_data[$key]+=1;
				}
				$total += array_sum($arr);
				$vote_data['votes']++;
		}
		$vote_data['total'] = $total;
		//取投票选项
		$options = $this->db2->get_options($subjectid);	
		include $this->admin_tpl('vote_statistics');	
	}
	
	/**
	 * 投票会员统计
	 */
	public function statistics_userlist() {
		$subjectid = $this->input->get('subjectid');
		if(empty($subjectid)) return false;
		$show_validator = $show_scroll = $show_header = true;
		$where = array ("subjectid" => $subjectid);
		$sdb = pc_base::load_model('vote_data_model'); //调用统计的数据模型
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $sdb->listinfo($where,'time DESC',$page,SYS_ADMIN_PAGESIZE);
		$pages = $sdb->pages;
		include $this->admin_tpl('vote_statistics_userlist');
	}
	
	/**
	 * 说明:生成JS投票代码
	 * @param $subjectid 投票ID
	 */
	function update_votejs($subjectid){
		if(!isset($subjectid)||intval($subjectid) < 1) return false;
		//解出投票内容
		$info = $this->db->get_subject($subjectid);
		if(!$info) dr_admin_msg(0,L('not_vote'));
		extract($info);
		//解出投票选项
		$options = $this->db2->get_options($subjectid);
		ob_start();
		include template('vote', $template);
		$voteform = ob_get_contents();
		ob_clean();
		@file_put_contents(CACHE_PATH.'vote_js/vote_'.$subjectid.'.js', $this->format_js($voteform));
	}
	
	/**
	 * 更新js
	 */
	public function create_js() {
 		$infos = $this->db->select(array('siteid'=>$this->get_siteid()), '*');
		if(is_array($infos)){
			foreach($infos as $subjectid_arr) {
				$this->update_votejs($subjectid_arr['subjectid']);
			}
		}
		dr_admin_msg(1,L('operation_success'));
	}
	
	/**
	 * 说明:对字符串进行处理
	 * @param $string 待处理的字符串
	 * @param $isjs 是否生成JS代码
	 */
	function format_js($string, $isjs = 1){
		return format_js($string, $isjs);
	}
	
	/**
	 * 投票调用代码
	 * 
	 */ 
 	public function public_call() {
 		$subjectid = intval($this->input->get('subjectid'));
		if(!$subjectid) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER, '', 'call');
		$r = $this->db->get_one(array('subjectid'=>$subjectid));
		include $this->admin_tpl('vote_call');
	}
	/**
	 * 信息选择投票接口
	 */
	public function public_get_votelist() {
		$show_header = true;
		$infos = $this->db->listinfo(array('siteid'=>$this->get_siteid()),'subjectid DESC',$page,SYS_ADMIN_PAGESIZE);
		$target = $this->input->get('target') ? $this->input->get('target') : '';
		include $this->admin_tpl('get_votelist');
	}
	
}
?>