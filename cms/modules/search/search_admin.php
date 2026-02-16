<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
class search_admin extends admin {
	private $input,$siteid,$db,$module_db,$type_db,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->siteid = $this->get_siteid();
		$this->db = pc_base::load_model('search_model');
		$this->module_db = pc_base::load_model('module_model');
		$this->type_db = pc_base::load_model('type_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}

	public function setting() {
		if(IS_POST) {
			//合并数据库缓存与新提交缓存
			$r = $this->module_db->get_one(array('module'=>'search'));
			$search_setting = string2array($r['setting']);
			
			$search_setting[$this->siteid] = $this->input->post('setting');
			$setting = array2string($search_setting);
			$this->module_db->update(array('setting'=>$setting),array('module'=>'search'));
			$this->cache_api->cache('search_setting');
			dr_json(1, L('operation_success'));
		} else {
			$r = $this->module_db->get_one(array('module'=>'search'));
			$setting = string2array($r['setting']);
			if($setting[$this->siteid]){
				extract($setting[$this->siteid]);
			}
			$page = (int)$this->input->get('page');
			include $this->admin_tpl('setting');
		}
	}
	/**
	 * 创建索引
	 */
	public function createindex() {
		if($this->input->get('dosubmit')) {
			//重建索引首先清空表所有数据，然后根据搜索类型接口重新全部重建索引
			if(!$this->input->get('have_truncate')) {
				$db_tablepre = $this->db->db_tablepre;
				//删除该站点全文索引
				$this->db->delete(array('siteid'=>$this->siteid));
				
				$types = $this->type_db->select(array('siteid'=> $this->siteid,'module'=>'search'));
				setcache('search_types', $types, 'search');
			} else{
				$types = getcache('search_types', 'search');
			}
			//$key typeid 的索引
			$key = intval($this->input->get('key'));
			foreach ($types as $_k=>$_v) {
				if($key==$_k) {
					$typeid = $_v['typeid'];
					if($_v['modelid']) {
						if ($_v['typedir']!=='yp') {
							$search_api = pc_base::load_app_class('search_api','content');
						} else {
							$search_api = pc_base::load_app_class('search_api',$_v['typedir']);
						}
						if(!$this->input->get('total')) {
							$total = $search_api->total($_v['modelid']);
						} else {
							$total = intval($this->input->get('total'));
							$search_api->set_model($_v['modelid']);
						}
					} else {
						$module = trim($_v['typedir']);
						$search_api = pc_base::load_app_class('search_api',$module);
						if(!$this->input->get('total')) {
							$total = $search_api->total();
						} else {
							$total = intval($this->input->get('total'));
						}
					}

					$pagesize = intval($this->input->get('pagesize')) ? intval($this->input->get('pagesize')) : 50;
					$page = max(intval($this->input->get('page')), 1);
					$pages = ceil($total/$pagesize);
				
					$datas = $search_api->fulltext_api($pagesize,$page);
					foreach ($datas as $id=>$r) {
						$this->db->update_search($typeid ,$id, $r['fulltextcontent'],$r['title'],$r['adddate'], 1);
					}
					$page++;
					if($pages>=$page) dr_admin_msg(1,"正在更新 <span style='color:#ff0000;'>{$_v['name']}</span> - 总数：{$total} - 当前第 <font color='red'>{$page}</font> 页","?m=search&c=search_admin&a=createindex&menuid={$this->input->get('menuid')}&page={$page}&total={$total}&key={$key}&pagesize={$pagesize}&have_truncate=1&dosubmit=1");
					$key++;
					dr_admin_msg(1,"开始更新：<span style='color:#ff0000;'>{$_v['name']}</span> - 总数：{$total}条","?m=search&c=search_admin&a=createindex&menuid={$this->input->get('menuid')}&page=1&key={$key}&pagesize={$pagesize}&have_truncate=1&dosubmit=1");
				
				}
			}
			dr_admin_msg(1,'全站索引更新完成','?m=search&c=search_admin&a=createindex&menuid='.$this->input->get('menuid'));
		} else {
			include $this->admin_tpl('createindex');
		}
	}
	
	public function public_test_sphinx() {
		$sphinxhost = !empty($this->input->post('sphinxhost')) ? $this->input->post('sphinxhost') : exit('-1');
		$sphinxport = !empty($this->input->post('sphinxport')) ? intval($this->input->post('sphinxport')) : exit('-2');
		$fp = @fsockopen($sphinxhost, $sphinxport, $errno, $errstr , 2);
		if (!$fp) {
			exit($errno.':'.$errstr);
		} else {
			exit('1');
		}
	}
	
}
?>