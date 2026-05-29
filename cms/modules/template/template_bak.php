<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class template_bak extends admin {
	private $input, $db, $style, $dir, $filename, $filepath, $fileid;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->style = $this->input->get('style') && trim($this->input->get('style')) ? str_replace(array('..\\', '../', './', '.\\', '/', '\\'), '', trim($this->input->get('style'))) : dr_admin_msg(0,L('illegal_operation'));
		$this->dir = $this->input->get('dir') && trim($this->input->get('dir')) ? trim(urldecode($this->input->get('dir'))) : dr_admin_msg(0,L('illegal_operation'));
		$this->dir = safe_replace($this->dir);
		$this->filename = $this->input->get('filename') && trim($this->input->get('filename')) ? trim($this->input->get('filename')) : dr_admin_msg(0,L('illegal_operation'));
		if (empty($this->style) || empty($this->dir) || empty($this->filename)) {
			dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		}
		$this->filepath = TPLPATH.$this->style.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.$this->filename;
		$this->fileid = $this->style.'_'.$this->dir.'_'.$this->filename;
		$this->db = pc_base::load_model('template_bak_model');
	}
	
	public function init() {
		if(!IS_EDIT_TPL){
			dr_admin_msg(0,L('tpl_edit'),'close',3,1);
		}
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$list = $this->db->listinfo(array('fileid'=>$this->fileid), 'creat_at desc', $page, SYS_ADMIN_PAGESIZE);
		if (!$list) {
			dr_admin_msg(0,L('not_exist_versioning'),'close',3,1);
		}
		$pages = $this->db->pages;
		$show_header = true;
		pc_base::load_sys_class('format', '', 0);
		include $this->admin_tpl('template_bak_list');
	}
	
	public function restore() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('id'=>$id))) {
			if (!is_writable($this->filepath)) {
				dr_admin_msg(0,L("file_does_not_writable"), HTTP_REFERER);
			}
			if (@file_put_contents($this->filepath, new_stripslashes($data['template']))) {
				dr_admin_msg(1,L('operation_success'), HTTP_REFERER, '', 'history');
			} else {
				dr_admin_msg(0,L('operation_success'), HTTP_REFERER, '', 'history');
			}
			
		} else {
			dr_admin_msg(0,L('notfound'), HTTP_REFERER);
		}
	}
	
	public function del() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('id'=>$id))) {
			$this->db->delete(array('id'=>$id));
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('notfound'), HTTP_REFERER);
		}
	}
}
?>