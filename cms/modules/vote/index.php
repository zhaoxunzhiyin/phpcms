<?php
defined('IN_CMS') or exit('No permission resources.');
class index {
	private $input,$vote,$vote_option,$vote_data,$username,$userid,$groupid,$ip;
	
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->vote = pc_base::load_model('vote_subject_model');//投票标题
		$this->vote_option = pc_base::load_model('vote_option_model');//投票选项
		$this->vote_data = pc_base::load_model('vote_data_model'); //投票统计的数据模型
		$this->username = param::get_cookie('_username');
		$this->userid = param::get_cookie('_userid'); 
		$this->groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		$this->ip = ip();
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		define("SITEID",$siteid);
	}

	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = SITEID;
		$page = intval($this->input->get('page'));
		$page = max($page,1);
		pc_base::load_sys_class('service')->assign([
			'siteid' => $siteid,
			'page' => $page,
		]);
		pc_base::load_sys_class('service')->display('vote', 'list_new');
	}

	 /**
	 *	投票列表页
	 */
	public function lists() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = SITEID;
		$page = intval($this->input->get('page'));
		$page = max($page,1);
		pc_base::load_sys_class('service')->assign([
			'siteid' => $siteid,
			'page' => $page,
		]);
		pc_base::load_sys_class('service')->display('vote', 'list_new');
	}

	/**
	 * 投票显示页
	 */
	public function show(){
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$type = intval($this->input->get('type'));//调用方式ID
		$subjectid = abs(intval($this->input->get('subjectid')));
		if(!$subjectid) showmessage(L('vote_novote'),'blank');
		//取出投票标题
		$subject_arr = $this->vote->get_subject($subjectid);
		
		$siteid = $subject_arr['siteid'];

		//增加判断，防止模板调用不存在投票时js报错 wangtiecheng
		if(!is_array($subject_arr)) {
			if($this->input->get('action') && $this->input->get('action') == 'js') {
				exit;
			} else {
				showmessage(L('vote_novote'),'blank');
			}
		}
		extract($subject_arr);
		//显示模版
		$template = $template ? $template: 'vote_tp';
		//获取投票选项
		$options = $this->vote_option->get_options($subjectid);
		
		//新建一数组用来存新组合数据
		$total = 0;
		$vote_data =array();
		$vote_data['total'] = 0;//所有投票选项总数
		$vote_data['votes'] = 0;//投票人数
		
		//获取投票结果信息
		$infos = $this->vote_data->select(array('subjectid'=>$subjectid),'data');	
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
		
		
		//取出投票时间，如果当前时间不在投票时间范围内，则选项变灰不可选
		if(date("Y-m-d",SYS_TIME)>$todate || date("Y-m-d",SYS_TIME)<$fromdate){
			$check_status = 'disabled';
			$display = 'display:none;';
		}else {
			$check_status = '';
		}
		
		pc_base::load_sys_class('service')->assign($subject_arr);
		pc_base::load_sys_class('service')->assign([
			'subjectid' => $subjectid,
			'vote_data' => $vote_data,
			'options' => $options,
			'display' => $display,
			'check_status' => $check_status,
		]);
		//JS调用 
		if($this->input->get('action')=='js'){
			if(!function_exists('ob_gzhandler')) ob_clean();
			ob_start();
			//$template = 'submit';
			$template = $subject_arr['template'];
			//根据TYPE值，判断调用模版
			switch ($type){
				case 3://首页、栏目页调用
				$true_template = 'vote_tp_3';
				break; 
				case 2://内容页调用
				$true_template = 'vote_tp_2';	
				break;
				default:
				$true_template = $template;
			}
			pc_base::load_sys_class('service')->display('vote',$true_template);
			$data=ob_get_contents();
			ob_clean();
			exit(format_js($data));
		}
		
		//SEO设置 
		$SEO = seo(SITEID, '', $subject, $description, $subject);
		//前台投票列表调用默认页面,以免页面样式错乱.
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid ? $siteid : SITEID,
		]);
		if($this->input->get('show_type')==1){
			pc_base::load_sys_class('service')->display('vote', 'vote_tp');
		}else {
			pc_base::load_sys_class('service')->display('vote', $template);
		}
		
	} 

	/**
	 * 处理投票
	 */
	public function post(){
		$subjectid = intval($this->input->post('subjectid'));
		if(!$subjectid)	showmessage(L('vote_novote'),'blank');
		//当前站点
		$siteid = SITEID;
		//判断是否已投过票,或者尚未到第二次投票期
		$return = $this->check($subjectid);
		switch ($return) {
		case 0:
			showmessage(L('vote_voteyes'),"?m=vote&c=index&a=result&subjectid=$subjectid&siteid=$siteid");
			break;
		case -1:
			showmessage(L('vote_voteyes'),"?m=vote&c=index&a=result&subjectid=$subjectid&siteid=$siteid");
			break;
		}
		if(!is_array($this->input->post('radio'))) showmessage(L('vote_nooption'),'blank');
		$time = SYS_TIME;
		
		$data_arr = array();
		foreach($this->input->post('radio') as $radio){
			$radio = intval($radio);
			$data_arr[$radio]='1';
		}
		$new_data = array2string($data_arr);//转成字符串存入数据库中
		//添加到数据库
		$this->vote_data->insert(array('userid'=>$this->userid,'username'=>$this->username,'subjectid'=>$subjectid,'time'=>$time,'ip'=>$this->ip,'data'=>$new_data));
		//查询投票奖励点数，并更新会员点数
		$vote_arr = $this->vote->get_one(array('subjectid'=>$subjectid));
		pc_base::load_app_class('receipts','pay',0);
		receipts::point($vote_arr['credit'],$this->userid, $this->username, '','selfincome',L('vote_post_point'));
		//更新投票人数 
		$this->vote->update(array('votenumber'=>'+=1'),array('subjectid'=>$subjectid));
		showmessage(L('vote_votesucceed'), "?m=vote&c=index&a=result&subjectid=$subjectid&siteid=$siteid");
	}

	/**
	 * 
	 * 投票结果显示 
	 */
	public function result(){
		$siteid = SITEID;
		$subjectid = abs(intval($this->input->get('subjectid')));
		if(!$subjectid)	showmessage(L('vote_novote'),'blank');
		//取出投票标题
		$subject_arr = $this->vote->get_subject($subjectid);
		if(!is_array($subject_arr)) showmessage(L('vote_novote'),'blank');
		extract($subject_arr);
		//获取投票选项
		$options = $this->vote_option->get_options($subjectid);
		
		//新建一数组用来存新组合数据
		$total = 0;
		$vote_data =array();
		$vote_data['total'] = 0;//所有投票选项总数
		$vote_data['votes'] = 0;//投票人数
		
		//获取投票结果信息
		$infos = $this->vote_data->select(array('subjectid'=>$subjectid),'data');	
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
		//SEO设置 
		$SEO = seo(SITEID, '', $subject, $description, $subject);
		pc_base::load_sys_class('service')->assign($subject_arr);
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'subjectid' => $subjectid,
			'vote_data' => $vote_data,
			'options' => $options,
		]);
		pc_base::load_sys_class('service')->display('vote','vote_result');
	}

	/**
	 * 
	 * 投票前检测
	 * @param $subjectid 投票ID 
	 * @return 返回值 (1:可投票  0: 多投,时间段内不可投票  -1:单投,已投票,不可重复投票)
	 */
	public function check($subjectid){
		//查询本投票配置
		$siteid = SITEID;
		$subject_arr = $this->vote->get_subject($subjectid);
		if($subject_arr['enabled']==0){
			showmessage(L('vote_votelocked'),"?m=vote&c=index&a=result&subjectid=$subjectid&siteid=$siteid");
		}
		if(date("Y-m-d",SYS_TIME)>$subject_arr['todate']){
			showmessage(L('vote_votepassed'),"?m=vote&c=index&a=result&subjectid=$subjectid&siteid=$siteid");
		}
		//游客是否可以投票
		if($subject_arr['allowguest']==0 ){
			if(!$this->username){
				showmessage(L('vote_votenoguest'),"?m=vote&c=index&a=result&subjectid=$subjectid&siteid=$siteid");
			}elseif($this->groupid == '7'){
				showmessage('对不起，不允许邮件待验证用户投票！',"?m=vote&c=index&a=result&subjectid=$subjectid&siteid=$siteid");
			}
		}
		
		//是否有投票记录 
		$user_info = $this->vote_data->select(array('subjectid'=>$subjectid,'ip'=>$this->ip,'username'=>$this->username),'*','1',' time DESC'); 
		if(!$user_info){
			return 1;
		} else {
			if($subject_arr['interval']==0){
				return -1;
			}
			if($subject_arr['interval']>0){ 
				$condition = (SYS_TIME - $user_info[0]['time'])/(24*3600)> $subject_arr['interval'] ? 1	: 0;
				return $condition;
			}
		}
	}

}
?>