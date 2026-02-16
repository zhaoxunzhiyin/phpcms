<?php
defined('IN_CMS') or exit('No permission resources.');
class guestbook_tag {
 	private $input,$guestbook_db,$type_db;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->guestbook_db = pc_base::load_model('guestbook_model');
		$this->type_db = pc_base::load_model('type_model');
 	}
	
 	/**
 	 * 取出该分类的详细 信息
  	 * @param $typeid 分类ID 
 	 */
 	  
 	public function get_type($data){
 		$typeid = intval($data['typeid']);
 		if($typeid=='0'){
 			$arr = array();
 			$arr['name'] = '默认分类';
 			return $arr;
 		}else {
		$r = $this->type_db->get_one(array('typeid'=>$typeid));
  		return new_html_special_chars($r);	
 		}
 		
		
 	}
 	
	/**
	 * 留言板
	 * @param  $data 
	 */
	public function lists($data) {
		$typeid = intval($data['typeid']);//分类ID
		$siteid = $data['siteid'];
		if (empty($siteid)){ 
			$siteid = get_siteid();
		}
  		if($typeid!='' || $typeid=='0'){
 				$sql = array('typeid'=>$typeid,'siteid'=>$siteid,'passed'=>'1');
			}else {
				$sql = array('siteid'=>$siteid,'passed'=>'1');
		}
  		$r = $this->guestbook_db->select($sql, '*', $data['limit'], 'listorder '.$data['order']);
		
		return new_html_special_chars($r);
	}
	
	
	
	/**
	 * 返回该分类下的留言板 ...
	 * @param  $data 传入数组参数
	 */
	public function type_list($data) {
 		$siteid = $data['siteid'];
		$typeid = $data['typeid'];
 		if (empty($siteid)){
			$siteid = get_siteid();
		}
 		if($typeid){
				if(is_int($typeid)) return false;
				$sql = array('typeid'=>$typeid,'siteid'=>$siteid,'passed'=>'1');
			}else {
				$sql = array('siteid'=>$siteid,'passed'=>'1');
		}
		$r = $this->guestbook_db->select($sql, '*', $data['limit'], $data['order']);
		return new_html_special_chars($r);
	}
	
	/**
	 * 首页  留言板分类 循环 .
	 * @param  $data
	 */
	public function type_lists($data) {
			if (!in_array($data['listorder'], array('desc', 'asc'))) {
					$data ['listorder'] = 'desc';
				}
 			$sql = array('module'=>ROUTE_M,'siteid'=>$data['siteid']);
 			$r = $this->type_db->select($sql, '*', $data['limit'], 'listorder '.$data['listorder']);
			return new_html_special_chars($r);
	}
	 
	/**
	 * 
	 * 传入的站点ID,读取站点下的留言板分类 ...
	 * @param $siteid 选择的站点ID 
	 */ 
	public function get_typelist($siteid='1', $value = '', $id = '') {
   			$data = $arr = array();
			$data = $this->type_db->select(array('module'=>'guestbook', 'siteid'=>$siteid));
			foreach($data as $r) {
				$arr[$r['typeid']] = $r['name'];
			}
			$html = $id ? ' id="typeid" onchange="$(\'#'.$id.'\').val(this.value);"' : 'name="typeid", id="typeid"';
  			return pc_base::load_sys_class('form')::select($arr, $value, $html, L('please_select')); 
	}
	/**
	 * 实现数据分页
	 */
	public function count($data){
		$siteid = $data['siteid'];
		$typeid = $data['typeid'];
 		if (empty($siteid)){
			$siteid = get_siteid();
		}
 		if($typeid){
				if(is_int($typeid)) return false;
				$sql = array('typeid'=>$typeid,'siteid'=>$siteid,'passed'=>'1');
			}else {
				$sql = array('siteid'=>$siteid,'passed'=>'1');
		}
			return $this->guestbook_db->count($sql);
		 
	}
	
	/**
	 * pc 标签调用
	 */
	public function pc_tag() {
		$sites = pc_base::load_app_class('sites','admin');
		$sitelist = $sites->pc_tag_list();
		return array(
			'action'=>array('type_list'=>L('guestbook_list', '', 'guestbook')),
			'type_list'=>array(
				'siteid'=>array('name'=>L('site_id','','comment'),'htmltype'=>'input_select', 'data'=>$sitelist, 'ajax'=>array('name'=>L('for_type','','special'), 'action'=>'get_typelist', 'id'=>'typeid')),
				'order'=>array('name'=>L('sort', '', 'comment'), 'htmltype'=>'select','data'=>array('listorder DESC'=>L('listorder_desc', '', 'content'),'listorder ASC'=>L('listorder_asc', '', 'content'))),
			),				
		 
		);
	}

}