<?php
defined('IN_CMS') or exit('No permission resources.'); 
pc_base::load_app_class('admin', 'admin', 0);
class block_admin extends admin {
	private $input, $db, $siteid, $priv_db, $history_db, $roleid;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('block_model');
		$this->priv_db = pc_base::load_model('block_priv_model');
		$this->history_db = pc_base::load_model('block_history_model');
		$this->roleid = param::get_session('roleid');
		$this->siteid = $this->get_siteid();
	}
	
	public function init() {
		$show_header = $show_dialog  = $show_pc_hash = true;
		include $this->admin_tpl('block_quick');
	}
	
	public function public_init() {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		if (!cleck_admin($this->roleid)) {
			$offset = ($page-1) * SYS_ADMIN_PAGESIZE;
			$r = $this->priv_db->select(array('roleid'=>is_array(dr_string2array($this->roleid)) ? dr_string2array($this->roleid) : $this->roleid, 'siteid'=>$this->siteid),'blockid', $offset.','.SYS_ADMIN_PAGESIZE);
			$blockid_list = array();
			foreach ($r as $key=>$v) {
				$blockid_list[$key] = $v['blockid'];
			}
			$sql =  implode('\',\'', $blockid_list);
			$list = $this->db->listinfo("id in ('$sql')", '', $page, SYS_ADMIN_PAGESIZE);
		} else {
			$list = $this->db->listinfo(array('siteid'=>$this->siteid), '', $page, SYS_ADMIN_PAGESIZE);
		}
		$pages = $this->db->pages;
		include $this->admin_tpl('block_list');
	}
	
	public function add() {
		$pos = $this->input->get('pos') && trim($this->input->get('pos')) ? trim($this->input->get('pos')) : dr_admin_msg(0,L('illegal_operation'));
		if (IS_POST) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('illegal_operation'), array('field' => 'name'));
			$type = $this->input->post('type') && intval($this->input->post('type')) ? intval($this->input->post('type')) : 1;
			//判断名称是否已经存在
			if ($this->db->get_one(array('name'=>$name))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			if ($id = $this->db->insert(array('name'=>$name, 'pos'=>$pos, 'type'=>$type, 'siteid'=>$this->siteid), true)) {
				//设置权限
				$priv = $this->input->post('priv') ? $this->input->post('priv') : '';
				if (!empty($priv)) {
					if (is_array($priv)) foreach ($priv as $v) {
						if (empty($v)) continue;
						$this->priv_db->insert(array('roleid'=>$v, 'blockid'=>$id, 'siteid'=>$this->siteid));
					}
				}
				dr_admin_msg(1,L('operation_success'), '?m=block&c=block_admin&a=block_update&id='.$id);
			} else {
				dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
			}
		} else {
			$show_header = $show_validator = true;
			pc_base::load_sys_class('form');
			$administrator = getcache('role', 'commons');
			unset($administrator[1]);
			include $this->admin_tpl('block_add_edit');
		}
	}
	
	public function edit() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) :  dr_admin_msg(0,L('illegal_operation'));
		if (!$data = $this->db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('nofound'));
		}
		if (IS_POST) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('illegal_operation'), array('field' => 'name'));
			if ($data['name'] != $name) {
				if ($this->db->get_one(array('name'=>$name))) {
					dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
				}
			}
			if ($this->db->update(array('name'=>$name, 'siteid'=>$this->siteid), array('id'=>$id))) {
				//设置权限
				$priv = $this->input->post('priv') ? $this->input->post('priv') : '';
				$this->priv_db->delete(array('blockid'=>$id, 'siteid'=>$this->siteid));
				if (!empty($priv)) {
					if (is_array($priv)) foreach ($priv as $v) {
						if (empty($v)) continue;
						$this->priv_db->insert(array('roleid'=>$v, 'blockid'=>$id, 'siteid'=>$this->siteid));
					}
				}
				dr_admin_msg(1,L('operation_success'), '', '' ,'edit');
			} else {
				dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
			}
		}
		$show_header = $show_validator = true;
		pc_base::load_sys_class('form');
		$administrator = getcache('role', 'commons');
		unset($administrator[1]);
		$r = $this->priv_db->select(array('blockid'=>$id, 'siteid'=>$this->siteid),'roleid');
		$priv_list = array();
		foreach ($r as $v) {
			if($v['roleid']) $priv_list[] = $v['roleid'];
		}
		include $this->admin_tpl('block_add_edit');
	}
	
	public function del() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) :  dr_admin_msg(0,L('illegal_operation'));
		if (!$data = $this->db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('nofound'));
		}
		if ($this->db->delete(array('id'=>$id)) && $this->history_db->delete(array('blockid'=>$id)) && $this->priv_db->delete(array('blockid'=>$id))) {
			if (SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$keyid = 'block-'.$id;
				$this->attachment_db->api_delete($keyid);
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}
	
	public function block_update() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) :  dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		//进行权限判断
		if (!cleck_admin($this->roleid)) {
			if (!$this->priv_db->get_one(array('blockid'=>$id, 'roleid'=>is_array(dr_string2array($this->roleid)) ? dr_string2array($this->roleid) : $this->roleid, 'siteid'=>$this->siteid))) {
				dr_admin_msg(0,L('not_have_permissions'));
			}
		}
		if (!$data = $this->db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('nofound'));
		}
		if (IS_POST) {
			$sql = array();
			if ($data['type'] == 2) {
				$title = $this->input->post('title') ? $this->input->post('title') : '';
				$url = $this->input->post('url') ? $this->input->post('url') : '';
				$thumb = $this->input->post('thumb') ? $this->input->post('thumb') : '';
				$desc = $this->input->post('desc') ? $this->input->post('desc') : '';
				$template = $this->input->post('template') && trim($this->input->post('template')) ? trim($this->input->post('template')) : '';
				$datas = array();
				foreach ($title as $key=>$v) {
					if (empty($v) || !isset($url[$key]) ||empty($url[$key])) continue;
					$datas[$key] = array('title'=>$v, 'url'=>$url[$key], 'thumb'=>$thumb[$key], 'desc'=>str_replace(array(chr(13), chr(43)), array('<br />', '&nbsp;'), $desc[$key]));
				}
				if ($template) {
					$block = pc_base::load_app_class('block_tag');
					$block->template_url($id, $template);
				}
				if (is_array($thumb) && !empty($thumb)) {
					if(SYS_ATTACHMENT_STAT) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_update($thumb, 'block-'.$id, 1);
					}
				}
				$sql = array('data'=>array2string($datas), 'template'=>$template);
			} elseif ($data['type'] == 1) {
				$datas = $this->input->post('data') && trim($this->input->post('data')) ? trim($this->input->post('data')) : '';
				$sql = array('data'=>$datas);
			}
			if ($this->db->update($sql, array('id'=>$id))) {
				//添加历史记录
				$this->history_db->insert(array('blockid'=>$data['id'], 'data'=>array2string($data), 'creat_at'=>SYS_TIME, 'userid'=>param::get_cookie('userid'), 'username'=>param::get_cookie('admin_username')));
				dr_admin_msg(1,L('operation_success'));
			} else {
				dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
			}
		} else {
			if (!empty($data['data'])) {
				if ($data['type'] == 2) $data['data'] = string2array($data['data']);
				$total = dr_count($data['data']);
			}
			pc_base::load_sys_class('form');
			pc_base::load_sys_class('format');
			$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) :  1;
			$history_list = $this->history_db->listinfo(array('blockid'=>$id), '', $page, SYS_ADMIN_PAGESIZE);
			$pages = $this->history_db->pages;
			$show_header = $show_validator = $show_dialog = true;
			include $this->admin_tpl('block_update');
		}
	}
	
	public function public_visualization() {
		$catid = $this->input->get('catid') && intval($this->input->get('catid')) ? intval($this->input->get('catid')) : 0;
		$type = $this->input->get('type') && trim($this->input->get('type')) ? trim($this->input->get('type')) : 'list';
		$siteid = $GLOBALS['siteid'] = $this->get_siteid();
		if (!empty($catid)) {
			$CATEGORY = get_category($siteid);
			if (!isset($CATEGORY[$catid])) {
				dr_admin_msg(0,L('notfound'));
			}
			$cat = dr_cat_value($catid);
			$cat['setting'] = dr_string2array($cat['setting']);
		}
		if($cat['type']==2) dr_admin_msg(0,L('link_visualization_not_exists'));
		$file = '';
		$style = $cat['setting']['template_list'];
		switch ($type) {
			case 'category':
				if($cat['type']==1) {
					$file = $cat['setting']['page_template'];
				} else {
					$file = $cat['setting']['category_template'];
				}
				break;
				
			case 'list':
				if($cat['type']==1) {
					$file = $cat['setting']['page_template'];
				} else {
					$file = $cat['setting']['list_template'];
				}
				break;
				
			case 'show':
				$file = $cat['setting']['show_template'];
				break;
			
			case 'index':
				$sites = pc_base::load_app_class('sites', 'admin');
				$sites_info = $sites->get_by_id($this->siteid);
				$file = 'index';
				$style = $sites_info['default_style'];
				break;
				
			case 'page':
				$file = $cat['setting']['page_template'];
				break;
		}
		
		pc_base::load_app_func('global','template');
		ob_start();
		include template('content', $file, $style);
		$html = ob_get_contents();
		ob_clean();
		echo visualization($html, $style, 'content', $file.'.html');
	}
	
	public function public_view() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) :  exit('0');
		if (!$data = $this->db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('nofound'));
		}
		if ($data['type'] == 1) {
			exit('<script type="text/javascript">parent.showblock('.$id.', \''.str_replace("\r\n", '', $this->input->post('data')).'\')</script>');
		} elseif ($data['type'] == 2) {
			extract($data);
			unset($data);
			$title = $this->input->post('title') ? $this->input->post('title') : '';
			$url = $this->input->post('url') ? $this->input->post('url') : '';
			$thumb = $this->input->post('thumb') ? $this->input->post('thumb') : '';
			$desc = $this->input->post('desc') ? $this->input->post('desc') : '';
			$template = $this->input->post('template') && trim($this->input->post('template')) ? trim($this->input->post('template')) : '';
			$data = array();
			foreach ($title as $key=>$v) {
				if (empty($v) || !isset($url[$key]) ||empty($url[$key])) continue;
				$data[$key] = array('title'=>$v, 'url'=>$url[$key], 'thumb'=>$thumb[$key], 'desc'=>str_replace(array(chr(13), chr(43)), array('<br />', '&nbsp;'), $desc[$key]));
			}
			$tpl = pc_base::load_sys_class('template_cache');
			$str = $tpl->template_parse(new_stripslashes($template));
			$filepath = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.'block'.DIRECTORY_SEPARATOR.'tmp_'.$id.'.php';
			$dir = dirname($filepath);
			if(!is_dir($dir)) {
				@mkdir($dir, 0777, true);
		    }
		    if (@file_put_contents($filepath,$str)) {
		    	 ob_start();
		   		 include $filepath;
		   		 $html = ob_get_contents();
		   		 ob_clean();
		   		 @unlink($filepath);
		    }
		   
			exit('<script type="text/javascript">parent.showblock('.$id.', \''.str_replace("\r\n", '', $html).'\')</script>');
		}
	}
	
	public function public_name() {
		$name = $this->input->get('name') && trim($this->input->get('name')) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('name'))) : trim($this->input->get('name'))) : exit('0');
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : '';
		$name = safe_replace($name);
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
	
	public function history_restore() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) :  dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if (!$data = $this->history_db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('nofound'), HTTP_REFERER);
		}
		$data['data'] = string2array($data['data']);
		$this->db->update(array('data'=>$data['data']['data'], 'template'=>$data['data']['template']), array('id'=>$data['blockid']));
		if ($data['data']['type'] == 2) {
			$block = pc_base::load_app_class('block_tag');
			$block->template_url($data['blockid'], $data['data']['template']);
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	public function history_del() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) :  dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if (!$data = $this->history_db->get_one(array('id'=>$id))) {
			dr_admin_msg(0,L('nofound'), HTTP_REFERER);
		}
		$this->history_db->delete(array('id'=>$id));
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	public function public_search_content() {
		$catid = $this->input->get('catid') && intval($this->input->get('catid')) ? intval($this->input->get('catid')) :  '';
		$posids = $this->input->get('posids') && intval($this->input->get('posids')) ? intval($this->input->get('posids')) :  0;
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) :  1;
		$searchtype = $this->input->get('searchtype') && intval($this->input->get('searchtype')) ? intval($this->input->get('searchtype')) :  0;
		$end_time = $this->input->get('end_time') && trim($this->input->get('end_time')) ? strtotime(trim($this->input->get('end_time'))) :  '';
		$start_time = $this->input->get('start_time') && trim($this->input->get('start_time')) ? strtotime(trim($this->input->get('start_time'))) :  '';
		$keyword = $this->input->get('keyword') && trim($this->input->get('keyword')) ? trim($this->input->get('keyword')) :  '';
		if ($this->input->get('dosubmit') && !empty($catid)) {
			if (!empty($start_time) && empty($end_time)) {
				$end_time = SYS_TIME;
			}
			if ($end_time < $start_time) {
				dr_admin_msg(0,L('end_of_time_to_time_to_less_than'));
			}
			if (!empty($end_time) && empty($start_time)) {
				dr_admin_msg(0,L('please_set_the_starting_time'));
			}
			$sql = "`catid` = '$catid' AND `posids` = '$posids'";
			if (!empty($start_time) && !empty($end_time)) $sql .= " AND `inputtime` BETWEEN '$start_time' AND '$end_time' ";
			if (!empty($searchtype) && !empty($keyword)) {
				switch ($searchtype) {
					case '1'://标题搜索
						$sql .= " AND `title` LIKE '%".$this->db->escape($keyword)."%' ";
						break;
					case '2'://简介搜索
						$sql .= " AND `description` LIKE '%".$this->db->escape($keyword)."%' ";
						break;
					case '3'://用户名
						$sql .= " AND `username` = '".$this->db->escape($keyword)."' ";
						break;
					case '4'://ID搜索
						$sql .= " AND `id` = '".$this->db->escape($keyword)."' ";
						break;
				}
			}
			$content_db = pc_base::load_model('content_model');
			$content_db->set_catid($catid);
			$data = $content_db->listinfo($sql, 'id desc', $page, SYS_ADMIN_PAGESIZE);
			$pages = $content_db->pages;
		}
		pc_base::load_sys_class('form');
		$show_header = $show_validator = $show_dialog = true;
		include $this->admin_tpl('search_content');
	}
}