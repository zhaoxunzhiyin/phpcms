<?php
defined('IN_CMS') or exit('No permission resources.');
class slider_tag {
 	private $input,$slider_db,$type_db;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->slider_db = pc_base::load_model('slider_model');
		$this->type_db = pc_base::load_model('type_model');
 	}

	public function lists($data) {
		$typeid = intval($data['postion']);//分类ID
		$siteid = $data['siteid'];
		if (empty($siteid)){ 
			$siteid = get_siteid();
		}
  		if($typeid!=''){
 				$sql = array('typeid'=>$typeid,'siteid'=>$siteid,'isshow'=>'1');
			}else {
				$sql = array('siteid'=>$siteid,'isshow'=>'1');
		}

		if (!in_array($data['listorder'], array('desc', 'asc'))) {
			$data ['listorder'] = 'desc';
		}

					
  		$r = $this->slider_db->select($sql, '*', $data['limit'], 'listorder '.$data['order']);
		
		return new_html_special_chars($r);
	}


	/**
	 * pc 标签调用
	 */
	public function pc_tag() {
		$sites = pc_base::load_app_class('sites','admin');
		$sitelist = $sites->pc_tag_list();
		return array(
			'action'=>array('lists'=>L('lists', '', 'slider')),
			'lists'=>array(
				'siteid'=>array('name'=>L('site_id','','slider'),'htmltype'=>'input_select', 'data'=>$sitelist, 'ajax'=>array('name'=>L('for_type','','special'), 'action'=>'get_typelist', 'id'=>'typeid')),
				//'order'=>array('name'=>L('sort', '', 'comment'), 'htmltype'=>'select','data'=>array('listorder DESC'=>L('listorder_desc', '', 'content'),'listorder ASC'=>L('listorder_asc', '', 'content'))),

				'order'=>array('name'=>L('sort', '', 'comment'), 'htmltype'=>'select','data'=>array('listorder DESC'=>L('listorder_desc', '', 'content'),'listorder ASC'=>L('listorder_asc', '', 'content'))),

				
			),				
		 
		);
	}

}