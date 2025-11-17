<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_app_func('global', 'special');

class template extends admin {
	private $input,$db;
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('special_model');
	}
	
	/**
	 * 编辑专题首页模板
	 */
	public function init() {
		echo '<!DOCTYPE html>';
		$specialid = $this->input->get('specialid') && intval($this->input->get('specialid')) ? intval($this->input->get('specialid')) : dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		if (!$specialid) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		
		$info = $this->db->get_one(array('id'=>$specialid, 'disabled'=>'0', 'siteid'=>$this->get_siteid()));
		if (!$info['id']) dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$id = $specialid;
		if($info['css']) $css_param = unserialize($info['css']);
		if(!$info['ispage']) {
			$type_db = pc_base::load_model('type_model');
			$types = $type_db->select(array('module'=>'special', 'parentid'=>$id), '*', '', '`listorder` ASC, `typeid` ASC');
		}
		extract($info);
		$css = get_css($css_param);
		$template = $info['index_template'] ? $info['index_template'] : 'index';
		pc_base::load_app_func('global', 'template');
		ob_start();
		include template('special', $template);
		$html = ob_get_contents();
		ob_clean();
		$html = visualization($html, 'default', 'test', 'block.html');
		include $this->admin_tpl('template_edit');
	}
	
	/**
	 * css编辑预览
	 */
	public function preview() {
		define('HTML', true);
		if (!$this->input->get('specialid')) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		$info = $this->db->get_one(array('id'=>$this->input->get('specialid'), 'disabled'=>'0', 'siteid'=>$this->get_siteid()));
		if (!$info['id']) dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$css = get_css($this->input->post('info'));
		$template = $info['index_template'] ? $info['index_template'] : 'index';
		include template('special', $template);
	}
	
	/**
	 * css添加
	 */
	public function add() {
		if (!$this->input->get('specialid')) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		$info = $this->db->get_one(array('id'=>$this->input->get('specialid'), 'disabled'=>'0', 'siteid'=>$this->get_siteid()));
		if (!$info['id']) dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$data = serialize($this->input->post('info'));
		$this->db->update(array('css'=>$data), array('id'=>$info['id']));
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
}
?>