<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class fclient extends admin {
	private $input,$sync,$db,$db2;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->sync = pc_base::load_app_class('sync');
		$this->db = pc_base::load_model('fclient_model');
		$this->db2 = pc_base::load_model('member_model');
	}

	// 查看列表
	public function init() {
 		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$field = $this->input->get('field');
		$keyword = clearhtml(trim($this->input->get('keyword')));
		$where = '';
		if($field=='name' && $keyword!=''){
			$where = " `$field` like '%".$this->db->escape($keyword)."%'";
		}elseif($field!='' && $keyword!=''){
			$where = array($field=>$keyword);
		}
		$infos = $this->db->listinfo($where,$order = 'id DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$user = $this->db2->select();
		$user = new_html_special_chars($user);
 		$user_arr = array ();
 		foreach($user as $userid=>$user){
			$user_arr[$user['userid']] = $user['username'];
		}
		include $this->admin_tpl('fclient_list');
	}

	/*
	 *判断标题重复和验证 
	 */
	public function public_name() {
		$fclient_title = $this->input->get('fclient_name') && trim($this->input->get('fclient_name')) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('fclient_name'))) : trim($this->input->get('fclient_name'))) : exit('0');
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : '';
		$data = array();
		if ($id) {

			$data = $this->db->get_one(array('id'=>$id), 'name');
			if (!empty($data) && $data['name'] == $fclient_title) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('name'=>$fclient_title), 'id')) {
			exit('0');
		} else {
			exit('1');
		}
	}
	
	//添加客户站群
 	public function add() {
 		if(IS_POST) {
			$fclient = $this->input->post('fclient');
			if(empty($fclient['name'])) {
				dr_admin_msg(0,L('sitename_noempty'), array('field' => 'name'));
			} else {
				$fclient['name'] = safe_replace($fclient['name']);
			}
			if ($this->db->count(array('name'=>$fclient['name']))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			if (!$fclient['username']) dr_admin_msg(0,L('username_noempty'), array('field' => 'username'));
			if (!$fclient['domain']) dr_admin_msg(0,L('domain_noempty'), array('field' => 'domain'));
			if (strpos($fclient['domain'], 'http') !== 0) dr_admin_msg(0,L('domain_http'), array('field' => 'domain'));
			if (substr_count(trim($fclient['domain'], '/'), '/') > 2) dr_admin_msg(0,L('domain_contains'), array('field' => 'domain'));
			$data = $this->db2->get_one(array('username'=>$fclient['username']));
			if (!$data) {
				dr_admin_msg(0,L('uid_noempty'));
			}
			$fclient['uid'] = $data['userid'];
			if((!$fclient['sn']) || empty($fclient['sn'])) dr_admin_msg(0,L('sn_noempty'), array('field' => 'sn'));
			$fclient['setting'] = dr_array2string($fclient['setting']);
			if ($fclient['inputtime']) {
				$fclient['inputtime'] = strtotime($fclient['inputtime']);
			}
			if ($fclient['endtime']) {
				$fclient['endtime'] = strtotime($fclient['endtime']);
			}
			$id = $this->db->insert($fclient,true);
			if(!$id) return FALSE;
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER,'', 'add');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
 			include $this->admin_tpl('fclient_add');
		}
	}
	
	//修改客户站群
	public function edit() {
		if(IS_POST){
 			$id = intval($this->input->get('id'));
			$fclient = $this->input->post('fclient');
			if($id < 1) return false;
			if(!is_array($fclient) || empty($fclient)) return false;
			if((!$fclient['name']) || empty($fclient['name'])) dr_admin_msg(0,L('sitename_noempty'), array('field' => 'name'));
			if ($this->db->count(array('id<>'=>$id, 'name'=>$name))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			if((!$fclient['username']) || empty($fclient['username'])) dr_admin_msg(0,L('username_noempty'), array('field' => 'username'));
			if (!$fclient['domain']) dr_admin_msg(0,L('domain_noempty'), array('field' => 'domain'));
			if (strpos($fclient['domain'], 'http') !== 0) dr_admin_msg(0,L('domain_http'), array('field' => 'domain'));
			if (substr_count(trim($fclient['domain'], '/'), '/') > 2) dr_admin_msg(0,L('domain_contains'), array('field' => 'domain'));
			$data = $this->db2->get_one(array('username'=>$fclient['username']));
			if (!$data) {
				dr_admin_msg(0,L('uid_noempty'));
			}
			if((!$fclient['sn']) || empty($fclient['sn'])) dr_admin_msg(0,L('sn_noempty'), array('field' => 'sn'));
			$fclient['uid'] = $data['userid'];
			$fclient['setting'] = dr_array2string($fclient['setting']);
			if ($fclient['inputtime']) {
				$fclient['inputtime'] = strtotime($fclient['inputtime']);
			}
			if ($fclient['endtime']) {
				$fclient['endtime'] = strtotime($fclient['endtime']);
			}
			$this->db->update($fclient,array('id'=>$id));
			dr_admin_msg(1,L('operation_success'),'?m=fclient&c=fclient&a=edit','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			//解出客户站群内容
			$id = intval($this->input->get('id'));
			$data = $this->_Data($id);
			extract($data);
 			include $this->admin_tpl('fclient_edit');
		}

	}

	/**
	 * 删除客户站群  
	 * @param	intval	$sid	客户站群ID，递归删除
	 */
	public function delete() {
  		if((!$this->input->get('id') || empty($this->input->get('id'))) && (!$this->input->post('id') || empty($this->input->post('id')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('id'))){
				foreach($this->input->post('id') as $id_arr) {
 					//批量删除客户站群
					$this->db->delete(array('id'=>$id_arr));
				}
				dr_admin_msg(1,L('operation_success'),'?m=fclient&c=fclient');
			}else{
				$id = intval($this->input->get('id'));
				if($id < 1) return false;
				//删除客户站群
				$result = $this->db->delete(array('id'=>$id));
				if($result){
					dr_admin_msg(1,L('operation_success'),'?m=fclient&c=fclient');
				}else {
					dr_admin_msg(0,L("operation_failure"),'?m=fclient&c=fclient');
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	 
	/**
	 * 配置
	 */
	public function setting() {
		//读取配置文件
		$page = intval($this->input->get('page'));
		$data = array();
 		$siteid = $this->get_siteid();//当前站点 
		//更新模型数据库,重设setting 数据. 
		$m_db = pc_base::load_model('module_model');
		$data = $m_db->select(array('module'=>'fclient'));
		$setting = string2array($data[0]['setting']);
		$now_seting = $setting[$siteid]; //当前站点配置
		if(IS_POST) {
			//多站点存储配置文件
 			$setting[$siteid] = $this->input->post('setting');
  			setcache('fclient', $setting, 'commons');  
			//更新模型数据库,重设setting 数据. 
  			$m_db = pc_base::load_model('module_model'); //调用模块数据模型
			$set = array2string($setting);
			$m_db->update(array('setting'=>$set), array('module'=>ROUTE_M));
			dr_admin_msg(1,L('setting_updates_successful'));
		} else {
			if ($now_seting) {
				@extract($now_seting);
			}
 			include $this->admin_tpl('setting');
		}
	}
	
	// 通信测试
	public function sync_web() {
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

	// 登录后台
	public function sync_admin() {
		$id = intval($this->input->get('id'));
		$data = $this->_Data($id);
		if (!$data) {
			dr_admin_msg(0,L('no_site'));
		} elseif (in_array($data['status'], [1])) {
			dr_admin_msg(0,L('no_site_check'));
		}

		$url = $this->sync->sync_admin_url($data);

		dr_admin_msg(1,L('admin_check'),$url);
	}
	
	// 下载安装包
	public function down() {
		$id = intval($this->input->get('id'));
		$data = $this->_Data($id);
		if (!$data) {
			dr_admin_msg(0,L('no_site'));
		} elseif (in_array($data['status'], [1])) {
			dr_admin_msg(0,L('no_site_check'));
		}

		$rt = $this->sync->down_zip($data);
        !$rt['code'] && dr_json($rt['code'], $rt['msg']);
	}

    // 升级版本
    public function update() {

        $id = intval($this->input->get('id'));
		$data = $this->_Data($id);
        if (!$data) {
            dr_admin_msg(0,L('no_site'));
        } elseif (!$data['setting']['mode']) {
            dr_admin_msg(0,L('not_local_site'));
        } elseif (!$data['setting']['webpath']) {
            dr_admin_msg(0,L('not_web_path'));
        }

        $path = dr_get_dir_path($data['setting']['webpath']);
        if (is_dir($path)) {
            if (is_file($path.'index.php')) {
                if (!is_file(CACHE_PATH.'cms.zip')) {
                    dr_admin_msg(0,L('not_web_path_cms'));
                }
                $this->sync->unzip(CACHE_PATH.'cms.zip', $path);
                dr_admin_msg(0,L('not_web_path_cms_ok'));
            } else {
                dr_admin_msg(0,L('not_web_path_not_cms'));
            }
        } else {
            dr_admin_msg(0,str_replace('{path}',$path,L('not_path')));
        }
    }
	
	/**
	 * 测试目录是否可用
	 */
	public function public_test_dir() {

		$v = $this->input->get('v');
		if (!$v) {
			dr_json(0, L('no_path'));
		} elseif (strpos($v, ' ') === 0) {
			dr_json(0, L('not_space'));
		}

		$path = dr_get_dir_path($v);
		if (is_dir($path)) {
			if (is_file($path.'index.php')) {
				dr_json(1, L('path_normal'));
			}
			dr_json(0, L('not_web_path_not_cms'));
		} else {
			dr_json(0, str_replace('{path}',$path,L('not_path')));
		}
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
	public function public_asckey() {
		$s = strtoupper(base64_encode(md5(SYS_TIME).md5(rand(0, 2015).md5(rand(0, 2015)))).md5(rand(0, 2009)));
		echo substr('CMS'.str_replace('=', '', $s), 0, 23);exit;
	}
}
?>