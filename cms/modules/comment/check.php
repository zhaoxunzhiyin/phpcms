<?php
defined('IN_CMS') or exit('No permission resources.'); 
pc_base::load_app_class('admin', 'admin', 0);
class check extends admin {
	//数据库连接
	private $input,$comment_check_db,$comment,$comment_data_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->comment_data_db = pc_base::load_model('comment_data_model');
		$this->comment_check_db = pc_base::load_model('comment_check_model');
		$this->comment = pc_base::load_app_class('comment');
	}
	
	public function checks() {
		$total = $this->comment_check_db->count(array('siteid'=>$this->get_siteid()));
		$comment_check_data = $this->comment_check_db->select(array('siteid'=>$this->get_siteid()), '*', '20', 'id desc');
		if (empty($comment_check_data)) dr_admin_msg(0,L('no_check_comments'));
		pc_base::load_sys_class('format','', 0);
		include $this->admin_tpl('comment_check');
	}
	
	public function ajax_checks() {
		$id =  $this->input->get('id') && $this->input->get('id') ? $this->input->get('id') : ($this->input->get('form') ? dr_admin_msg(0,L('please_chose_comment'), HTTP_REFERER) : exit('0'));
		$type =  $this->input->get('type') && intval($this->input->get('type')) ? intval($this->input->get('type')) : exit('0');
		$commentid = $this->input->get('commentid') && trim($this->input->get('commentid')) ? safe_replace(trim($this->input->get('commentid'))) : exit('0');
		if (is_array($id)) {
			foreach ($id as $v) {
				if (!$v = intval($v)) {
					continue;
				}
				$this->comment->status($commentid, $v, $type);
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			$id = intval($id) ? intval($id) : exit('0');
			$this->comment->status($commentid, $id, $type);
		}
		if ($comment->msg_code != 0) {
			exit($comment->get_error());
		} else {
			exit('1');
		}
	}
	
	public function public_get_one() {
		$total = $this->comment_check_db->count(array('siteid'=>$this->get_siteid()));
		$comment_check_data = $this->comment_check_db->select(array('siteid'=>$this->get_siteid()), '*', '19,1', 'id desc');
		$comment_check_data = $comment_check_data[0];
		$r = array();
		if (is_array($comment_check_data) && !empty($comment_check_data)) {
			$this->comment_data_db->table_name($comment_check_data['tableid']);
			$r = $this->comment_data_db->get_one(array('id'=>$comment_check_data['comment_data_id'], 'siteid'=>$this->get_siteid()));
			pc_base::load_sys_class('format','', 0);
			$r['creat_at'] = format::date($r['creat_at'], 1);
			if (CHARSET=='gbk') {
				foreach ($r as $k=>$v) {
					$r[$k] = iconv('gbk', 'utf-8', $v);
				}
			}
		}
		exit(dr_array2string(array('total'=>$total, 'data'=>$r)));
	}
}