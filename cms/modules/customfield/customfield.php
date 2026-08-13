<?php
/**
 * 自定义变量
 *
 */
defined('IN_CMS') or exit('No permission resources.'); 
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form','',0);
class customfield extends admin {
	private $input, $cache, $db, $siteid, $attachment_db;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('customfield_model');
		$this->siteid = intval($this->input->get('siteid')) ? intval($this->input->get('siteid')) : $this->get_siteid();
	}

	/* 字段管理 */
	public function init() {
		if(IS_AJAX_POST) {
			foreach($this->input->post('postdata') as $key => $post){
				$id = intval($post['id']);
				unset($post['id']);
				$this->db->update($post,array('id'=>$id));
			}
	 		//更新附件状态
			if(SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update('','customfield-'.$id,2);
			}
			//更新缓存
			$this->setFieldCache();
			dr_admin_msg(1,L('operation_success'));
		}
		$page = intval($this->input->get('page'));
		$root = $this->db->select(array('siteid'=>$this->siteid,'pid'=>0),"*","","listorder ASC");
		$j = 0;
		foreach ($root as $key =>$r) {
			$field = $this->db->select(array('pid'=>$r['id']),"*","","listorder ASC");
			foreach($field as $j => $f){
				$filed_list[$r['id']][] = $f;
				$filed_list[$r['id']][$j]['conf'] = string2array($f['conf']);
			}
		}
		include $this->admin_tpl('field_list');
	}

	//管理列表
	public function manage_list() {
		if(IS_AJAX_POST) {
			foreach($this->input->post('postdata') as $key => $post){
				$id = intval($post['id']);
				$options = intval($post['options']);
				if($options!=3 && $post['name'] == "") dr_admin_msg(0,L('cm_errmsg_value'));
				if($options!=3 && $post['description'] == "") dr_admin_msg(0,L('cm_errmsg_description'));
				unset($post['id']);
				unset($post['options']);
				$post['siteid'] = intval($this->input->post('siteid'));
				$post['pid'] = intval($post['pid']);
				$post['listorder'] = intval($post['listorder']);
				$post['conf']['type'] = (!$post['conf']['type']) ? 'text' : $post['conf']['type'];
				$post['conf']['status'] = (!$post['conf']['status']) ? 0 : $post['conf']['status'];
				$post['conf'] = array2string($post['conf']);
				switch($options){
					case 1: //修改
						if ($this->db->count(array('id<>'=>$id, 'name'=>$post['name'], 'siteid'=>intval($this->input->post('siteid')), 'pid'=>intval($post['pid'])))) {
							dr_admin_msg(0,L('cm_field').L('exists').L('cm_refresh'));
						}
						if ($this->db->count(array('id<>'=>$id, 'description'=>$post['description'], 'siteid'=>intval($this->input->post('siteid')), 'pid'=>intval($post['pid'])))) {
							dr_admin_msg(0,L('cm_description').L('exists').L('cm_refresh'));
						}
						$this->db->update($post,array('id'=>$id));
						break;
					case 2: //添加
						if ($this->db->count(array('name'=>$post['name'], 'siteid'=>$post['siteid'], 'pid'=>$post['pid']))) {
							dr_admin_msg(0,L('cm_field').L('exists').L('cm_refresh'));
						}
						if ($this->db->count(array('description'=>$post['description'], 'siteid'=>$post['siteid'], 'pid'=>$post['pid']))) {
							dr_admin_msg(0,L('cm_description').L('exists').L('cm_refresh'));
						}
						$this->db->insert($post);
						break;
					case 3: //删除
						$this->db->delete(array('id'=>$id));
						break;
					default:
						break;
				}
			}
			//更新缓存
			$this->setFieldCache();
			dr_admin_msg(1,L('operation_success'));
		}
		$page = intval($this->input->get('page'));
		$siteid = $this->siteid;
		$root = $this->db->select(array('siteid'=>$this->siteid,'pid'=>0),"*","","listorder ASC");
		$field = array();
		foreach($root as $key =>$r) {
			$field = $this->db->select(array('pid'=>$r['id'],'siteid'=>$siteid),'*','','listorder ASC');
			foreach($field as $j => $f){
				$filed_list[$r['id']][] = $f;
				$filed_list[$r['id']][$j]['conf'] = string2array($f['conf']);
			}
		}
		//获取站点信息
		$sitedb = pc_base::load_model('site_model');
		$sitelist = $sitedb->select("","siteid,name");
		include $this->admin_tpl('manage_list');
	}

	/* 分类管理 */
	public function category_list() {
		if(IS_AJAX_POST) {
			foreach($this->input->post('postdata') as $key => $post){
				$id = intval($post['id']);
				$options = intval($post['options']);
				if($options!=3 && $post['description'] == "") dr_admin_msg(0,L('cm_errmsg_name'));
				unset($post['id']); 	 //卸载id字段，防止更新覆盖
				unset($post['options']); //卸载options字段，数据库无此字段，防止出错。
				$post['pid'] = 0; 
				$post['listorder'] = intval($post['listorder']);
				$post['siteid'] = intval($this->input->post('siteid'));
				$post['conf']['status'] = (!$post['conf']['status']) ? 0 : $post['conf']['status'];
				$post['conf'] = array2string($post['conf']);
				switch($options){
					case 1: //修改
						if ($this->db->count(array('id<>'=>$id, 'description'=>$post['description'], 'siteid'=>$post['siteid'], 'pid'=>$post['pid']))) {
							dr_admin_msg(0,L('cm_cate_name').L('exists').L('cm_refresh'));
						}
						$this->db->update($post,array('id'=>$id));
						break;
					case 2: //添加
						if ($this->db->count(array('description'=>$post['description'], 'siteid'=>$post['siteid'], 'pid'=>$post['pid']))) {
							dr_admin_msg(0,L('cm_cate_name').L('exists').L('cm_refresh'));
						}
						$this->db->insert($post);
						break;
					case 3: //删除
						$vo = $this->db->count('pid = '.$id);
						if($vo > 0){dr_admin_msg(0,L('cm_errmsg_delete'));}
						else{$this->db->delete(array('id'=>$id));}
						break;
					default:
						break;
				}
			}
			dr_admin_msg(1,L('operation_success'));
		}
		$siteid = $this->siteid;
		$cate_list = $this->db->select(array('siteid'=>$siteid,'pid'=>0),"*","","listorder ASC");
		foreach($cate_list as $key => $cate){
			$cate_list[$key]['conf'] = string2array($cate['conf']);	
		}
		//获取站点信息
		$sitedb = pc_base::load_model('site_model');
		$sitelist = $sitedb->select("","siteid,name");
		include $this->admin_tpl('category_list');
	}

	/* 重写缓存 */
	public function setFieldCache(){
		$sitedb = pc_base::load_model('site_model');
		$sitelist = $sitedb->select('','siteid');
		foreach($sitelist as $slist){
			$fieldlist = $this->db->select(array('pid<>'=>0,'siteid'=>$slist['siteid']),'pid,val,name');
			foreach($fieldlist as $key => $flist){
				$category = $this->db->get_one(array('id'=>$flist['pid']));
				$caches['data'][$slist['siteid']][$category['description']][$flist['name']] = $flist['val'];
			}
		}
		$this->cache->set_file('fieldlist', $caches);
	}
}