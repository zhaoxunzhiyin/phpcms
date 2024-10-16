<?php
/**
 * 自定义变量
 *
 */
defined('IN_CMS') or exit('No permission resources.'); 
pc_base::load_app_class('admin', 'admin', 0);
class customfield extends admin {
	private $input, $cache, $db, $siteid;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('customfield_model');
		$this->siteid = intval($this->input->get('siteid')) ? intval($this->input->get('siteid')) : $this->get_siteid();
		parent::__construct();
	}
	
	/* 字段管理（普通管理员） */
	public function init() {
        $page = intval($this->input->get('page'));
        $root = $this->db->select(array('siteid'=>$this->siteid,'pid'=>0),"*","","listorder ASC");
        $j = 0;
        foreach ($root as $key =>$r) {
            $field = $this->db->select(array('pid'=>$r['id']),"*","","listorder ASC");
            foreach($field as $j => $f){
                $filed_list[$r['id']][] = $f;
            	$filed_list[$r['id']][$j]['val'] = str_replace("<br />",'',$filed_list[$r['id']][$j]['val']);
                $filed_list[$r['id']][$j]['conf'] = string2array($f['conf']);
           }
        }
		include $this->admin_tpl('field_list');
	}

	/* 字段保存（普通管理员） */
	public function field_save() {
		foreach($this->input->post('postdata') as $key => $post){
			$id = intval($post['id']);
			$post['val'] = nl2br($post['val']);
			unset($post['id']);
			$this->db->update($post,array('id'=>$id));
			$caches[$post['name']] = $post['val'];
		}
		//更新缓存
		$this->setFieldCache();
		dr_admin_msg(1,L('operation_success'), array('url' => '?m=customfield&c=customfield&a=init&page='.(int)$this->input->post('page').'&pc_hash='.dr_get_csrf_token()));
	}

	//管理列表（超级管理员）
	public function manage_list() {
        $page = intval($this->input->get('page'));
        $siteid = $this->siteid;
        $root = $this->db->select(array('siteid'=>$this->siteid,'pid'=>0),"*","","listorder ASC");
        $field = array();
        foreach($root as $key =>$r) {
            $field = $this->db->select(array('pid'=>$r['id'],'siteid'=>$siteid),'*','','listorder ASC');
            foreach($field as $j => $f){
                $filed_list[$r['id']][] = $f;
                $filed_list[$r['id']][$j]['conf'] = string2array($f['conf']);
                $filed_list[$r['id']][$j]['val'] = str_replace("<br />",'',$filed_list[$r['id']][$j]['val']);
           }
        }

		//获取站点信息
		$sitedb = pc_base::load_model('site_model');
		$sitelist = $sitedb->select("","siteid,name");
		include $this->admin_tpl('manage_list');
	}

	/* 管理保存（超级管理员） */
	public function manage_save() {
		foreach($this->input->post('postdata') as $key => $post){
			$id = intval($post['id']);
			$options = intval($post['options']);
			if($options!=3 && $post['name'] == "") dr_admin_msg(0,L('cm_errmsg_value'), HTTP_REFERER);
			if($options!=3 && $post['description'] == "") dr_admin_msg(0,L('cm_errmsg_description'), HTTP_REFERER);
			unset($post['id']);
			unset($post['options']);
			$post['siteid'] = intval($this->input->post('siteid'));
			$post['pid'] = intval($post['pid']);
			$post['val'] = nl2br($post['val']);
			$post['listorder'] = intval($post['listorder']);
			$post['conf']['status'] = (!$post['conf']['status']) ? 0 : $post['conf']['status'];
			$post['conf']['textarea'] = (!$post['conf']['textarea']) ? 0 : $post['conf']['textarea'];
			$post['conf'] = array2string($post['conf']);
			switch($options){
				case 1: //修改
					$this->db->update($post,array('id'=>$id));
					break;
				case 2: //添加
					$this->db->insert($post);
					break;
				case 3: //删除
					$this->db->delete(array('id'=>$id));
					break;
				default:
					break;
			}
		$caches[$post['name']] = $post['val'];
		}
		//更新缓存
		$this->setFieldCache();
		dr_admin_msg(1,L('operation_success'), array('url' => '?m=customfield&c=customfield&a=manage_list&page='.(int)$this->input->post('page').'&pc_hash='.dr_get_csrf_token()));
	}

	/* 分类管理（超级管理员） */
	public function category_list() {
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

	/* 分类保存（超级管理员） */
	public function category_save() {
		foreach($this->input->post('postdata') as $key => $post){
			$id = intval($post['id']);
			$options = intval($post['options']);
			if($options!=3 && $post['description'] == "") dr_admin_msg(0,L('cm_errmsg_name'), HTTP_REFERER);
			unset($post['id']); 	 //卸载id字段，防止更新覆盖
			unset($post['options']); //卸载options字段，数据库无此字段，防止出错。
			$post['pid'] = 0; 
			$post['listorder'] = intval($post['listorder']);
			$post['siteid'] = intval($this->input->post('siteid'));
			$post['conf']['status'] = (!$post['conf']['status']) ? 0 : $post['conf']['status'];
			$post['conf'] = array2string($post['conf']);
			switch($options){
				case 1: //修改
					$this->db->update($post,array('id'=>$id));
					break;
				case 2: //添加
					$this->db->insert($post);
					break;
				case 3: //删除
					$vo = $this->db->count('pid = '.$id);
					if($vo > 0){dr_admin_msg(0,L('cm_errmsg_delete'), HTTP_REFERER);}
					else{$this->db->delete(array('id'=>$id));}
					break;
				default:
					break;
			}
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}

	/* 重写缓存 */
	public function setFieldCache(){
		$sitedb = pc_base::load_model('site_model');
		$sitelist = $sitedb->select('','siteid');
	    foreach($sitelist as $slist){
			$fieldlist = $this->db->select("pid != 0 and siteid={$slist['siteid']}",'val,name');
			foreach($fieldlist as $key => $flist){
				$caches['data'][$slist['siteid']][$flist['name']] = $flist['val'];
			}
		}
		$this->cache->set_file('fieldlist', $caches);
	}
}