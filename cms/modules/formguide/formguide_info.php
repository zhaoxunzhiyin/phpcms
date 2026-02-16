<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class formguide_info extends admin {
	
	private $input, $cache, $db, $f_db, $tablename, $form, $sitemodel, $form_cache, $field, $list_field;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->f_db = pc_base::load_model('sitemodel_model');
		if ($this->input->get('formid') && !empty($this->input->get('formid'))) {
			$formid = intval($this->input->get('formid'));
			$this->form = $this->f_db->get_one(array('modelid'=>$formid));
			$this->sitemodel = $this->cache->get('sitemodel');
			$this->form_cache = $this->sitemodel[$this->form['tablename']];
			$this->field = $this->form_cache['field'];
			$this->list_field = $this->form_cache['setting']['list_field'];
			$f_info = $this->f_db->get_one(array('modelid'=>$formid, 'siteid'=>$this->get_siteid()), 'tablename');
			$this->tablename = 'form_'.$f_info['tablename'];
			$this->db->table($this->tablename);
		}
	}
	
	/**
	 * 用户提交表单信息列表
	 */
	public function init() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		}
		$formid = intval($this->input->get('formid'));
		if (!$this->tablename) {
			$f_info = $this->f_db->get_one(array('modelid'=>$formid, 'siteid'=>$this->get_siteid()), 'tablename');
			$this->tablename = 'form_'.$f_info['tablename'];
			$this->db->table($this->tablename);
		}
		$field = $this->field;
		$list_field = $this->list_field;
		$page = max(intval($this->input->get('page')), 1);
		$total = $this->db->count();
		$this->f_db->update(array('items'=>$total), array('modelid'=>$formid));
		$pages = pages($total, $page, SYS_ADMIN_PAGESIZE);
		$offset = ($page-1)*SYS_ADMIN_PAGESIZE;
		$datas = $this->db->select(array(), '*', $offset.', '.SYS_ADMIN_PAGESIZE, $this->input->get('order') ? $this->input->get('order') : '`dataid` DESC');
		$clink = module_clink('form', 'form');
		$foot_tpl = '';
		$foot_tpl .= '<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>'.PHP_EOL;
		$foot_tpl .= '<label><button type="button" onclick="Dialog.confirm(\''.L('affirm_delete').'\',function(){document.myform.action=\'?m=formguide&c=formguide_info&a=public_delete&formid='.$formid.'\';$(\'#myform\').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> '.L('delete').'</button></label>'.PHP_EOL;
		$cbottom = module_cbottom('form', 'form');
		if ($cbottom) {
			$foot_tpl .= '<label>
				<div class="btn-group dropup">
					<a class="btn blue btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false" href="javascript:;"><i class="fa fa-cogs"></i> '.L('批量操作').'
						<i class="fa fa-angle-up"></i>
					</a>
					<ul class="dropdown-menu">';
			foreach ($cbottom as $i => $a) {
				$foot_tpl .= '<li><a href="'.str_replace(['{formid}', '{siteid}', '{m}'], [$formid, $this->get_siteid(), ROUTE_M], urldecode($a['url'])).'"> <i class="'.$a['icon'].'"></i> '.$a['name'].' </a></li>';
				if ($i) {
					$foot_tpl .= '<div class="dropdown-line"></div>';
				}
			}
			$foot_tpl .= '</ul>
				</div>
			</label>';
		}
		include $this->admin_tpl('formguide_info_list');
	}
	
	/**
	 * 查看
	 */
	public function public_view() {
		if (!$this->tablename || !$this->input->get('did') || empty($this->input->get('did'))) dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		$did = intval($this->input->get('did'));
		$formid = intval($this->input->get('formid'));
		$info = $this->db->get_one(array('dataid'=>$did));
		pc_base::load_sys_class('form', '', '');
		define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
		require CACHE_MODEL_PATH.'formguide_output.class.php';
		$formguide_output = new formguide_output($formid);
		$forminfos_data = $formguide_output->get($info);
		$fields = $formguide_output->fields;
		include $this->admin_tpl('formguide_info_view');
	}
	
	/**
	 * 删除
	 */
	public function public_delete() {
		$formid = intval($this->input->get('formid'));
		if ($this->input->get('did') && !empty($this->input->get('did'))) {
			$did = intval($this->input->get('did'));
			$this->db->delete(array('dataid'=>$did));
			$this->f_db->update(array('items'=>'-=1'), array('modelid'=>$formid));
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else if(is_array($this->input->post('did')) && !empty($this->input->post('did'))) {
			foreach ($this->input->post('did') as $did) {
				$did = intval($did);
				$this->db->delete(array('dataid'=>$did));
				$this->f_db->update(array('items'=>'-=1'), array('modelid'=>$formid));
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		}
	}
}
?>