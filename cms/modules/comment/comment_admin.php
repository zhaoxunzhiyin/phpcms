<?php
defined('IN_CMS') or exit('No permission resources.'); 
pc_base::load_app_class('admin', 'admin', 0);
class comment_admin extends admin {
	private $input,$comment_setting_db,$comment_data_db,$comment_db,$comment_check_db,$siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->comment_setting_db = pc_base::load_model('comment_setting_model');
		$this->comment_data_db = pc_base::load_model('comment_data_model');
		$this->comment_db = pc_base::load_model('comment_model');
		$this->comment_check_db = pc_base::load_model('comment_check_model');
		$this->siteid = $this->get_siteid();
	}
	
	public function init() {
		$data = $this->comment_setting_db->get_one(array('siteid'=>$this->siteid));
		if (IS_AJAX_POST) {
			$link = $this->input->post('link') && intval($this->input->post('link')) ? intval($this->input->post('link')) : 0;
			$guest = $this->input->post('guest') && intval($this->input->post('guest')) ? intval($this->input->post('guest')) : 0;
			$check = $this->input->post('check') && intval($this->input->post('check')) ? intval($this->input->post('check')) : 0;
			$code = $this->input->post('code') && intval($this->input->post('code')) ? intval($this->input->post('code')) : 0;
			$add_point = $this->input->post('add_point') && abs(intval($this->input->post('add_point'))) ? intval($this->input->post('add_point')) : 0;
			$del_point = $this->input->post('del_point') && abs(intval($this->input->post('del_point'))) ? intval($this->input->post('del_point')) : 0;
			$sql = array('link'=>$link, 'guest'=>$guest, 'check'=>$check, 'code'=>$code, 'add_point'=>$add_point, 'del_point'=>$del_point);
			if ($data) {
				$this->comment_setting_db->update($sql, array('siteid'=>$this->siteid));
			} else {
				$sql['siteid'] = $this->siteid;
				$this->comment_setting_db->insert($sql);
			}
			dr_json(1, L('operation_success'));
		} else {
			$show_header = true;
			include $this->admin_tpl('comment_setting');
		}
	}
	
	public function lists() {
		$show_header = true;
		$commentid =  $this->input->get('commentid') && trim($this->input->get('commentid')) ? trim($this->input->get('commentid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$hot = intval($this->input->get('hot'));
		$comment = $this->comment_db->get_one(array('commentid'=>$commentid, 'siteid'=>$this->siteid));
		if (empty($comment)) {
			$forward = $this->input->get('show_center_id') ? '' : HTTP_REFERER;
			dr_admin_msg(0,L('no_comment'), $forward);
		}
		pc_base::load_app_func('global');
		pc_base::load_sys_class('format','', 0);
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$pagesize = SYS_ADMIN_PAGESIZE;
		$offset = ($page-1)*$pagesize;
		$this->comment_data_db->table_name($comment['tableid']);
		$desc = 'id desc';
		if (!empty($hot)) {
			$desc = 'support desc, id desc';
		}
		$list = $this->comment_data_db->select(array('commentid'=>$commentid, 'siteid'=>$this->siteid, 'status'=>1), '*', $offset.','.$pagesize, $desc);
		$pages = pages($comment['total'], $page, $pagesize);
		include $this->admin_tpl('comment_data_list');
	}

	public function listinfo() {
		
		$r = $max_table = '';
		$max_table = intval($this->input->get('max_table'));
		if (!$max_table) {
			$r = $this->comment_db->get_one(array(), 'MAX(tableid) AS tableid');
			if (!$r['tableid']) {
				dr_admin_msg(0,L('no_comment'),'close');
			}
			$max_table = $r['tableid'];
		}
		$page = max(intval($this->input->get('page')), 1);
		$tableid = $this->input->get('tableid') ? intval($this->input->get('tableid')) : $max_table;
		if ($tableid > $max_table) {
			$tableid = $max_table;
		}
		if ($this->input->get('search')) {
			$where = $sql = $t = $comment_id = $order = '';
			$keywords = safe_replace($this->input->get('keyword'));
			$searchtype = intval($this->input->get('searchtype'));
			switch ($searchtype) {
				case '0':
					$sql = "SELECT `commentid` FROM `cms_comment` WHERE `siteid` = '$this->siteid' AND `title` LIKE '%".$this->comment_db->escape($keywords)."%' AND `tableid` = '$tableid' ";
				
					$this->comment_db->query($sql);	
					$data = $this->comment_db->fetch_array();
					if (!empty($data)) {
						foreach ($data as $d) {
							$comment_id .= $t.'\''.$d['commentid'].'\'';
							$t = ',';
						}
						$where = "`commentid` IN ($comment_id)";
					}
				break;

				case '1':
					$keywords = intval($keywords);
					$sql = "SELECT `commentid` FROM `cms_comment` WHERE `commentid` LIKE 'content_%-$keywords-%' ";
					$this->comment_db->query($sql);
					$data = $this->comment_db->fetch_array();
					if (!empty($data)) {
						foreach ($data as $d) {
							$comment_id .= $t.'\''.$d['commentid'].'\'';
							$t = ',';
						}
						$where = "`commentid` IN ($comment_id)";
					}
 				break;

				case '2':
					$where = "`username` = '".$this->comment_db->escape($keywords)."'";
				break;
			}
		}
 		$data = array();
		
		
		
		if ($this->input->get('search')) {
			if(!empty($where)){
				$where .= ' AND siteid='.$this->siteid;
			}else{
				pc_base::load_sys_class('format','', 0);
				$data= '';
				include $this->admin_tpl('comment_listinfo');
				exit;
			}
		}else{
			$where = 'siteid='.$this->siteid; 
 		}
 		
		$order = '`id` DESC';
		pc_base::load_sys_class('format','', 0);
		$this->comment_data_db->table_name($tableid);
		$data = $this->comment_data_db->listinfo($where, $order, $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->comment_data_db->pages;
		include $this->admin_tpl('comment_listinfo');
	}

	public function del() {
		if ($this->input->get('dosubmit')) {
			$ids = $this->input->get('ids');
			$tableid = intval($this->input->get('tableid'));
			$r = $this->comment_db->get_one(array(), 'MAX(tableid) AS tableid');
			$max_table = $r['tableid'];
			if (!$tableid || $max_table<$tableid) dr_admin_msg(0,L('illegal_operation'));
			$this->comment_data_db->table_name($tableid);
			$site = $this->comment_setting_db->site($this->siteid);
			if (is_array($ids)) {
				foreach ($ids as $id) {
					$comment_info = $this->comment_data_db->get_one(array('id'=>$id), 'commentid, userid, username');
					$total = $this->comment_data_db->count(array('commentid'=>$comment_info['commentid']));
 					$total = max($total-1,0);
					$this->comment_db->update(array('total'=>$total), array('commentid'=>$comment_info['commentid']));
					$this->comment_data_db->delete(array('id'=>$id));
					$this->comment_check_db->delete(array('id'=>$id));

					//当评论ID不为空，站点配置了删除的点数，支付模块存在的时候，删除用户的点数。
					if (!empty($comment_info['userid']) && !empty($site['del_point']) && module_exists('pay')) {
						pc_base::load_app_class('spend', 'pay', 0);
						$op_userid = param::get_cookie('userid');
						$op_username = param::get_cookie('admin_username');
						spend::point($site['del_point'], L('comment_point_del', '', 'comment'), $comment_info['userid'], $comment_info['username'], $op_userid, $op_username);
					}
				}
				$ids = implode(',', $ids);
			} elseif (is_numeric($ids)) {
				$id = intval($ids);
				$comment_info = $this->comment_data_db->get_one(array('id'=>$id), 'commentid, userid, username');
				$total = $this->comment_data_db->count(array('commentid'=>$comment_info['commentid']));
				$total = max($total-1,0);
				$this->comment_db->update(array('total'=>$total), array('commentid'=>$comment_info['commentid']));
				$this->comment_data_db->delete(array('id'=>$id));
				$this->comment_check_db->delete(array('id'=>$id));

				//当评论ID不为空，站点配置了删除的点数，支付模块存在的时候，删除用户的点数。
				if (!empty($comment_info['userid']) && !empty($site['del_point']) && module_exists('pay')) {
					pc_base::load_app_class('spend', 'pay', 0);
					$op_userid = param::get_cookie('userid');
					$op_username = param::get_cookie('admin_username');
					spend::point($site['del_point'], L('comment_point_del', '', 'comment'), $comment_info['userid'], $comment_info['username'], $op_userid, $op_username);
				}
			} else {
				dr_admin_msg(0,L('illegal_operation'));
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
}