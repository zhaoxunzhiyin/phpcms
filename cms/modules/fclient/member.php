<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('foreground', 'member');
pc_base::load_sys_class('format');
pc_base::load_sys_class('form');
class member extends foreground {
	private $input,$sync,$db2,$siteinfo,$menu,$grouplist,$member_model;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->sync = pc_base::load_app_class('sync');
		$this->db = pc_base::load_model('fclient_model');
		$this->db2 = pc_base::load_model('member_model');
		$this->siteinfo = siteinfo($this->memberinfo['siteid']);
		$this->menu_db = pc_base::load_model('member_menu_model');
		$this->menu = $this->menu_db->select(array('display'=>1, 'parentid'=>0), '*', 20, 'listorder');
		$this->grouplist = getcache('grouplist');
		$this->member_model = getcache('member_model', 'commons');
		$this->memberinfo['groupname'] = $this->grouplist[$this->memberinfo['groupid']]['name'];
		$this->memberinfo['grouppoint'] = $this->grouplist[$this->memberinfo['groupid']]['point'];
		pc_base::load_sys_class('service')->assign([
			'memberinfo' => $this->memberinfo,
			'grouplist' => $this->grouplist,
			'member_model' => $this->member_model,
			'siteinfo' => $this->siteinfo,
			'menu' => $this->menu,
		]);
	}

	// 查看列表
	public function init() {
 		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$where = 'uid = '.$this->memberinfo['userid'].'';
		$datas = $this->db->listinfo($where,$order = 'id DESC',$page, $pages = '10');
		$pages = $this->db->pages;
		$user = $this->db2->listinfo('',$order = 'userid DESC');
		$user = new_html_special_chars($user);
 		$user_arr = array ();
 		foreach($user as $userid=>$user){
			$user_arr[$user['userid']] = $user['username'];
		}
		pc_base::load_sys_class('service')->assign([
			'datas' => $datas,
			'pages' => $pages,
		]);
		pc_base::load_sys_class('service')->display('fclient', 'index');
	}
	
	// 修改内容
	public function edit() {
		if(IS_POST){
 			$id = intval($this->input->get('id'));
			if($id < 1) return false;
			$fclient = $this->input->post('fclient');
			if(!is_array($fclient) || empty($fclient)) return false;
			if((!$fclient['name']) || empty($fclient['name'])) showmessage(L('sitename_noempty'));
			if (!$fclient['domain']) showmessage(L('domain_noempty'));
			if (strpos($fclient['domain'], 'http') !== 0) showmessage(L('domain_http'));
			if (substr_count(trim($fclient['domain'], '/'), '/') > 2) showmessage(L('domain_contains'));
			if ($fclient['inputtime']) {
				$fclient['inputtime'] = strtotime($fclient['inputtime']);
			}
			if ($fclient['endtime']) {
				$fclient['endtime'] = strtotime($fclient['endtime']);
			}
			$this->db->update($fclient,array('id'=>$id));
			showmessage(L('operation_success'),'?m=fclient&c=member');
			
		}else{
			$id = intval($this->input->get('id'));
			$data = $this->_Data($id);
			if (!$data) {
				showmessage(L('no_site'));
			} elseif ($data['uid'] != $this->memberinfo['userid']) {
				showmessage(L('no_user_site'));
			} elseif (in_array($data['status'], [1])) {
				showmessage(L('no_site_check'));
			}
			pc_base::load_sys_class('service')->assign('show_validator', true);
			pc_base::load_sys_class('service')->assign('show_scroll', true);
			pc_base::load_sys_class('service')->assign('show_header', true);
			pc_base::load_sys_class('service')->assign($data);
 			pc_base::load_sys_class('service')->display('fclient', 'edit');
		}

	}
	
	public function sync() {

		$id = intval($this->input->get('id'));
		$data = $this->_Data($id);
		if (!$data) {
			showmessage(L('no_site'));
		} elseif ($data['uid'] != $this->memberinfo['userid']) {
			showmessage(L('no_user_site'));
		}
		pc_base::load_sys_class('service')->assign($data);
		pc_base::load_sys_class('service')->display('fclient', 'sync');
	}
	
	function sync_web() {
		$id = intval($this->input->get('id'));
		$data = $this->_Data($id);
		if (!$data) {
			dr_json(0, L('no_site'));
		} elseif (in_array($data['status'], [1])) {
			dr_json(0, L('no_site_check'));
		}

		$rt = $this->sync->sync_test($data);
        dr_json($rt['code'], $rt['msg']);
	}

	public function sync_admin() {
		$id = intval($this->input->get('id'));
		$data = $this->_Data($id);
		if (!$data) {
			showmessage(L('no_site'));
		} elseif (in_array($data['status'], [1])) {
			showmessage(L('no_site_check'));
		}

		$url = $this->sync->sync_admin_url($data);

		showmessage(L('admin_check'),$url,'3000');
	}
	
	public function pay() {
		$memberinfo = $this->memberinfo;
		if(IS_POST){
 			$id = intval($this->input->post('id'));
			if($id < 1) return false;
			$data = $this->db->get_one(array('id'=>$id));
			if ($data['inputtime']) {
				$inputtime = $data['inputtime'];
			}else{
				$inputtime = SYS_TIME;
			}
			if ($data['endtime']) {
				$endtime = $data['endtime'];
			}else{
				$endtime = SYS_TIME;
			}
			if ($this->memberinfo['amount'] < $data['money']) {
				showmessage(L('no_user_money'));
			}else{
				$this->db2->update(array('amount'=>$this->memberinfo['amount'] - $data['money']),array('userid'=>$data['uid']));
				$this->db->update(array('status'=>2,'inputtime'=>$inputtime,'endtime'=>strtotime(date('Y-m-d',$endtime+365*24*60*60))),array('id'=>$id));
			}
			showmessage(L('operation_success'),'?m=fclient&c=member');
		}else{
			$id = intval($this->input->get('id'));
			$data = $this->_Data($id);
			if (!$data) {
				showmessage(L('no_site'));
			} elseif ($data['uid'] != $this->memberinfo['userid']) {
				showmessage(L('no_user_site'));
			} elseif (in_array($data['status'], [1])) {
				showmessage(L('no_site_check'));
			}
			pc_base::load_sys_class('service')->assign($data);
			pc_base::load_sys_class('service')->display('fclient', 'pay');
		}
	}
	
	function down() {
		$id = intval($this->input->get('id'));
		$data = $this->_Data($id);
		if (!$data) {
			showmessage(L('no_site'));
		} elseif (in_array($data['status'], [1])) {
			showmessage(L('no_site_check'));
		}

		$rt = $this->sync->down_zip($data);
        !$rt['code'] && dr_json($rt['code'], $rt['msg']);
	}
	
	/**
	 * 获取内容
	 * $id      内容id,新增为0
	 * */
	protected function _Data($id = 0) {
		$row = $this->db->get_one(array('id'=>$id));
		// 这里可以对内容进行格式化显示操处理
		$row['setting'] = dr_string2array($row['setting']);
		return $row;
	}
	
	/**
	 * 生成来路随机字符
	 */
	public function asckey() {
		$s = strtoupper(base64_encode(md5(SYS_TIME).md5(rand(0, 2015).md5(rand(0, 2015)))).md5(rand(0, 2009)));
		echo substr('CMS'.str_replace('=', '', $s), 0, 23);exit;
	}
}
?>