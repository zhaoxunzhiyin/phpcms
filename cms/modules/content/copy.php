<?php
defined('IN_CMS') or exit('No permission resources.');

pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('push_factory', '', 0);

class copy extends admin {
	private $input,$siteid,$push;
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->siteid = $this->get_siteid();
		//权限判断，根据栏目里面的权限设置检查
		if($this->input->get('catid') && !cleck_admin(param::get_session('roleid'))) {
			$catid = $this->input->get('catid') ? intval($this->input->get('catid')) : intval($this->input->post('catid'));
			$this->priv_db = pc_base::load_model('category_priv_model');
			$priv_datas = $this->priv_db->get_one(array('catid'=>$catid,'is_admin'=>1,'action'=>'copy'));
			if(!$priv_datas['catid']) dr_admin_msg(0,L('permission_to_operate'));
		}
		$module = ($this->input->get('module') && !empty($this->input->get('module'))) ? $this->input->get('module') : 'admin';
		if (in_array($module, array('admin', 'special', 'content'))) {
			$this->push = push_factory::get_instance()->get_api($module);
		} else {
			dr_admin_msg(0,L('not_exists_copy'));
		}
	}
	
	/**
	 * 推送选择界面
	 */
	public function init() {
		if (IS_POST) {
			$c = pc_base::load_model('content_model');
			$c->set_model($this->input->post('modelid'));
			$info = array();
			$ids = explode('|', $this->input->post('id'));

			if(is_array($ids)) {
				foreach($ids as $id) {
					$info[$id] = $c->get_content($this->input->post('catid'), $id);
				}
			}
			$add_action = $this->input->get('add_action') ? $this->input->get('add_action') : $this->input->get('action'); 
			$this->push->{$add_action}($info, $_POST);
			dr_admin_msg(1,L('success'), '', '', 'copy');
		} else {
			pc_base::load_app_func('global', 'template');
			if (method_exists($this->push, $this->input->get('action'))) {
				$html = $this->push->{$this->input->get('action')}(array('modelid'=>$this->input->get('modelid'), 'catid'=>$this->input->get('catid')));
				include $this->admin_tpl('copy_to_category');
			} else {
				dr_admin_msg(0,'CLASS METHOD NO EXISTS!');
			}
		}
	}
	
	public function public_ajax_get() {
		if (method_exists($this->push, $this->input->get('action'))) {
			$html = $this->push->{$this->input->get('action')}($this->input->get('html'));
			echo $html;
		} else {
			echo 'CLASS METHOD NO EXISTS!';
		}
	}
}
?>