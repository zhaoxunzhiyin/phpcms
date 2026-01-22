<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class badword extends admin {
	private $input,$db,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$admin_username = param::get_cookie('admin_username');
		$userid = param::get_session('userid');
		$this->db = pc_base::load_model('badword_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}
	
	function init () {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $pages = '';
		$infos = $this->db->listinfo('',$order = 'badid DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$level = array(1=>L('general'),2=>L('danger'));
		include $this->admin_tpl('badword_list');
	}
	
	
	/**
	 * 敏感词添加
	 */
	function add() {
		if(IS_POST){
			$info = $this->input->post('info');
			$info['lastusetime'] = SYS_TIME;
			$info['replaceword'] = str_replace("　","",trim($this->input->post('replaceword')));
			$info['badword'] = str_replace("　","",trim($this->input->post('badword')));
			dr_is_empty($info['badword']) && dr_json(0, L('enter_word'), array('field' => 'badword'));
			if ($this->db->count(array('badword'=>$info['badword']))) {
				dr_admin_msg(0,L('badword_name').L('exists'), array('field' => 'badword'));
			}
			$this->db->insert($info);
			$this->public_cache_file();//更新缓存
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=badword&a=add','', 'add');
		}else{
			$show_validator = $show_scroll = $show_header = true; 
			include $this->admin_tpl('badword_add');
		}
	}
		
	/**
	 * 敏感词排序
	 */
	function listorder() {
		if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
			foreach($this->input->post('listorders') as $badid => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('badid'=>$badid));
			}
		}
		dr_admin_msg(1,L('operation_success'),'?m=admin&c=badword');
	}
	
	/**
	 * 敏感词修改
	 */
	function edit() {
		if(IS_POST){
			$badid = intval($this->input->get('badid'));
			$info = $this->input->post('info');
			$info['replaceword'] = str_replace("　","",trim($this->input->post('replaceword')));
			$info['badword'] = str_replace("　","",trim($this->input->post('badword')));
			dr_is_empty($info['badword']) && dr_json(0, L('enter_word'), array('field' => 'badword'));
			if ($this->db->count(array('badid<>'=>$badid, 'badword'=>$info['badword']))) {
				dr_admin_msg(0,L('badword_name').L('exists'), array('field' => 'badword'));
			}
			$this->db->update($info,array('badid'=>$badid));
			$this->public_cache_file();//更新缓存
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=badword&a=edit','', 'edit');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			$info = array();
			$info = $this->db->get_one(array('badid'=>$this->input->get('badid')));
			if(!$info) dr_admin_msg(0,L('keywords_no_exist'));
			extract($info);
			include $this->admin_tpl('badword_edit');
		}	 
	}
	/**
	 * 关键词删除 包含批量删除 单个删除
	 */
	function delete() {
 		if(is_array($this->input->post('badid'))){
				foreach($this->input->post('badid') as $badid_arr) {
					$this->db->delete(array('badid'=>$badid_arr));
				}
				$this->public_cache_file();//更新缓存
				dr_admin_msg(1,L('operation_success'),'?m=admin&c=badword');	
			}else{
				$badid = intval($this->input->get('badid'));
				if($badid < 1) return false;
				$result = $this->db->delete(array('badid'=>$badid));
				if($result){
					$this->public_cache_file();//更新缓存
					dr_admin_msg(1,L('operation_success'),'?m=admin&c=badword');
					}else {
					dr_admin_msg(0,L("operation_failure"),'?m=admin&c=badword');
				}
		}
	}
	
	/**
	 * 导出敏感词为文本 一行一条记录
	 */
	function export() {
		$result = $s = '';
		$result = $this->db->select('', '*', '', 'badid DESC', '');
		if(!is_array($result) || empty($result)){
			dr_admin_msg(0,'暂无敏感词设置，正在返回！','?m=admin&c=badword');
		}
  		foreach($result as $s){
 			extract($s);
			$str .= $badword.','.$replaceword.','.$level."\n";		  
		}
 		$filename = L('export');
		header('Content-Type: text/x-sql');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		$is_ie = 'IE';
		    if ($is_ie == 'IE') {
		        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		        header('Pragma: public');
		    	} else {
		        header('Pragma: no-cache');
		        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		    }
		exit($str);
 	}
	
	/**
	 * 从文本中导入敏感词, 一行一条记录
	 */
	function import(){
		if(IS_POST){
				$arr = $s = $str = $level_arr = '';
				$s = trim($this->input->post('info'));
			    if(empty($s)) dr_admin_msg(0,L('not_information'));
	 			$arr = explode("\n",$s); 
	 			if(!is_array($arr) || empty($arr)) return false; 
	 			foreach($arr as $s){
			    	$level_arr = array("1","2");
	 				$str = explode(",",$s);
	   				$sql_str = array();
	 				$sql_str['badword'] = $str[0];
	 				$sql_str['replaceword'] = $str[1];
	 				$sql_str['level'] = $str[2];
					$sql_str['lastusetime'] = SYS_TIME;
					if(!in_array($sql_str['level'],$level_arr)) $sql_str['level'] = '1';
	 				if(empty($sql_str['badword'])){
							continue;
						}else{
							$check_badword = $this->db->get_one(array('badword'=>$sql_str['badword']), $data = '*', $order = '', $group = '');
							if($check_badword){
								continue;
							}
							$this->db->insert($sql_str);
					}
					
					unset($sql_str,$check_badword);
	 			}
				dr_admin_msg(1,L('operation_success'),array('url'=>'?m=admin&c=badword&pc_hash='.dr_get_csrf_token()));
 		}else{
			include $this->admin_tpl('badword_import');
		}
	} 
 	
	/**
	 * 生成缓存
	 */
	function public_cache_file() { 
		$this->cache_api->cache('badword');
 	}
	
}
?>