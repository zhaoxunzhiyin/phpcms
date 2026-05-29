<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
class position extends admin {
	private $input, $db, $db_data, $db_content, $sites, $cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('position_model');
		$this->db_data = pc_base::load_model('position_data_model');
		$this->db_content = pc_base::load_model('content_model');			
		$this->sites = pc_base::load_app_class('sites');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}
	
	public function init() {
		$infos = array();
		$where = '';
		$current_siteid = self::get_siteid();
		$model = getcache('model','commons');
		$where = "`siteid`='$current_siteid' OR `siteid`='0'";
		$page = $this->input->get('page') ? $this->input->get('page') : '1';
		$infos = $this->db->listinfo($where, $order = 'listorder DESC,posid DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$show_dialog = true;
		include $this->admin_tpl('position_list');
	}
	
	/**
	 * 推荐位添加
	 */
	public function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			if (dr_is_empty($info['name'])) dr_admin_msg(0, L('input').L('posid_name'), array('field' => 'name'));
			if (dr_is_empty($info['maxnum'])) dr_admin_msg(0, L('input').L('maxnum'), array('field' => 'maxnum'));
			$info['siteid'] = intval($info['modelid']) ? get_siteid() : 0;
			$info['listorder'] = intval($info['listorder']);
			$info['maxnum'] = intval($info['maxnum']);
			$info['thumb'] = $info['thumb'];
			$insert_id = $this->db->insert($info,true);
			$this->_set_cache();
			if($insert_id){
				dr_admin_msg(1,L('operation_success'), '', '', 'add');
			}
		} else {
			pc_base::load_sys_class('form');
			$sitemodel = $sitemodel = array();
			$sitemodel = getcache('model','commons');
			foreach($sitemodel as $value){
				if($value['siteid'] == get_siteid())$modelinfo[$value['modelid']]=$value['name'];
			}			
			$show_header = $show_validator = true;
			include $this->admin_tpl('position_add');
		}
		
	}
	
	/**
	 * 推荐位编辑
	 */
	public function edit() {
		if(IS_POST) {
			$posid = intval($this->input->post('posid'));
			$info = $this->input->post('info');
			if (!$info['name']) dr_admin_msg(0, L('input').L('posid_name'), array('field' => 'name'));
			if (!$info['maxnum']) dr_admin_msg(0, L('input').L('maxnum'), array('field' => 'maxnum'));
			$info['siteid'] = intval($info['modelid']) ? get_siteid() : 0;
			$info['listorder'] = intval($info['listorder']);
			$info['maxnum'] = intval($info['maxnum']);			
			$info['thumb'] = $info['thumb'];			
			$this->db->update($info,array('posid'=>$posid));
			$this->_set_cache();
			dr_admin_msg(1,L('operation_success'), '', '', 'edit');
		} else {
			$info = $this->db->get_one(array('posid'=>intval($this->input->get('posid'))));
			extract($info);
			pc_base::load_sys_class('form');
			$sitemodel = $sitemodel = array();
			$sitemodel = getcache('model','commons');
			foreach($sitemodel as $value){
				if($value['siteid'] == get_siteid())$modelinfo[$value['modelid']]=$value['name'];
			}
			$show_validator = $show_header = $show_scroll = true;
			include $this->admin_tpl('position_edit');
		}

	}
	
	/**
	 * 推荐位删除
	 */
	public function delete() {
		$posid = intval($this->input->get('posid'));
		$this->db->delete(array('posid'=>$posid));
		$this->_set_cache();
		dr_admin_msg(1,L('posid_del_success'),'?m=admin&c=position');
	}
	
	/**
	 * 推荐位排序
	 */
	public function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $posid => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('posid'=>$posid));
				}
			}
			$this->_set_cache();
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=position');
		} else {
			dr_admin_msg(0,L('operation_failure'),'?m=admin&c=position');
		}
	}
	
	/**
	 * 推荐位文章统计
	 * @param $posid 推荐位ID
	 */
	public function content_count($posid) {
		$posid = intval($posid);
		$where = array('posid'=>$posid);
		$infos = $this->db_data->get_one($where, $data = 'count(*) as count');
		return $infos['count'];
	}
	
	/**
	 * 推荐位文章列表
	 */
	public function public_item() {	
		if(IS_POST) {
			$items = dr_count($this->input->post('items')) > 0  ? $this->input->post('items') : dr_admin_msg(0,L('posid_select_to_remove'),HTTP_REFERER);
			if(is_array($items)) {
				$sql = array();
				foreach ($items as $item) {
					$_v = explode('-', $item);
					$sql['id'] = $_v[0];
					$sql['modelid']= $_v[1];
					$sql['posid'] = intval($this->input->post('posid'));
					$this->db_data->delete($sql);
					$this->content_pos($sql['id'],$sql['modelid']);		
				}
			}
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
		} else {
			$posid = intval($this->input->get('posid'));
			$MODEL = getcache('model','commons');
			$siteid = $this->get_siteid();
			$CATEGORY = get_category($siteid);
			$page = $this->input->get('page') ? $this->input->get('page') : '1';
			$pos_arr = $this->db_data->listinfo(array('posid'=>$posid,'siteid'=>$siteid),'listorder DESC', $page, SYS_ADMIN_PAGESIZE);
			$pages = $this->db_data->pages;
			$infos = array();
			foreach ($pos_arr as $_k => $_v) {
				$r = string2array($_v['data']);
				$r['catname'] = $CATEGORY[$_v['catid']]['catname'];
				$r['modelid'] = $_v['modelid'];
				$r['posid'] = $_v['posid'];
				$r['id'] = $_v['id'];
				$r['listorder'] = $_v['listorder'];
				$r['catid'] = $_v['catid'];
				$r['url'] = dr_go($_v['catid'], $_v['id']);
				$key = $r['modelid'].'-'.$r['id'];
				$infos[$key] = $r;
				
			}
			include $this->admin_tpl('position_items');			
		}
	}
	/**
	 * 推荐位文章管理
	 */
	public function public_item_manage() {
		if(IS_POST) {
			$posid = intval($this->input->post('posid'));
			$modelid = intval($this->input->post('modelid'));	
			$id= intval($this->input->post('id'));
			$pos_arr = $this->db_data->get_one(array('id'=>$id,'posid'=>$posid,'modelid'=>$modelid));
			$array = string2array($pos_arr['data']);
			$array['inputtime'] = strtotime($this->input->post('info')['inputtime']);
			$array['title'] = trim($this->input->post('info')['title']);
			$array['thumb'] = trim($this->input->post('info')['thumb']);
			$array['description'] = trim($this->input->post('info')['description']);
			$thumb = $this->input->post('info')['thumb'] ? 1 : 0;
			$array = array('data'=>array2string($array),'synedit'=>intval($this->input->post('synedit')),'thumb'=>$thumb);
			$this->db_data->update($array,array('id'=>$id,'posid'=>$posid,'modelid'=>$modelid));
			dr_admin_msg(1,L('operation_success'),'','','edit');
		} else {
			$posid = intval($this->input->get('posid'));
			$modelid = intval($this->input->get('modelid'));	
			$id = intval($this->input->get('id'));		
			if($posid == 0 || $modelid == 0) dr_admin_msg(0,L('linkage_parameter_error'), HTTP_REFERER);
			$pos_arr = $this->db_data->get_one(array('id'=>$id,'posid'=>$posid,'modelid'=>$modelid));
			extract(string2array($pos_arr['data']));
			$synedit = $pos_arr['synedit'];
			$show_validator = $show_header = true;		
			include $this->admin_tpl('position_item_manage');			
		}
	
	}
	/**
	 * 推荐位文章排序
	 */
	public function public_item_listorder() {
		if($this->input->post('posid')) {
			foreach($this->input->post('listorders') as $_k => $listorder) {
				$pos = array();
				$pos = explode('-', $_k);
				$this->db_data->update(array('listorder'=>$listorder),array('id'=>$pos[1],'catid'=>$pos[0],'posid'=>$this->input->post('posid')));
			}
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
			
		} else {
			dr_admin_msg(0,L('operation_failure'),HTTP_REFERER);
		}
	}
	/**
	 * 推荐位添加栏目加载
	 */
	public function public_category_load() {
		$modelid = intval($this->input->get('modelid'));
		pc_base::load_sys_class('form');
		$category = form::select_category('','','name="info[catid]"',L('please_select_parent_category'),$modelid);
		echo $category;
	}
	
	private function _set_cache() {
		$this->cache_api->cache('position');
	}
	
	private function content_pos($id,$modelid) {
		$id = intval($id);
		$modelid = intval($modelid);
		$MODEL = getcache('model','commons');
		$this->db_content->table_name = $this->db_content->db_tablepre.$MODEL[$modelid]['tablename'];		
		$posids = $this->db_data->get_one(array('id'=>$id,'modelid'=>$modelid)) ? 1 : 0;
		return $this->db_content->update(array('posids'=>$posids),array('id'=>$id));
	}
}
?>