<?php
defined('IN_CMS') or exit('No permission resources.');
if (!module_exists('comment')) showmessage(L('module_not_exists', array('module' => 'comment')));
class comment_api {
	private $input,$db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
	}
	
	/**
	 * 获取评论信息
	 * @param $module      模型
	 * @param $contentid   文章ID
	 * @param $siteid      站点ID
	 */
	function get_info($module, $contentid, $siteid) {
		list($module, $catid) = explode('_', $module);
		if (empty($contentid) || empty($catid)) {
			return false;
		}
		//判断栏目是否存在 s
		$CATEGORYS = get_category($siteid);
 		if(!$CATEGORYS[$catid]){
 			return false;
		}
		
		//判断模型是否存在
		$this_modelid = $CATEGORYS[$catid]['modelid'];
		$MODEL = getcache('model','commons'); 
		if(!$MODEL[$this_modelid]){
			return false;
		}
		
		$this->db->set_catid($catid);
		$r = $this->db->get_one(array('catid'=>$catid, 'id'=>$contentid), '`title`,`tableid`');
		$category = get_category($siteid);
		$model = getcache('model', 'commons');
		
		$cat = $category[$catid];
		$data_info = array();
		if ($cat['type']==0) {
			if ($model[$cat['modelid']]['tablename']) {
				$this->db->table_name = $this->db->db_tablepre.$model[$cat['modelid']]['tablename'].'_data_'.$r['tableid'];
				$data_info = $this->db->get_one(array('id'=>$contentid));
			}
		}
		
		if ($r) {
			return array('title'=>$r['title'], 'url'=>dr_go($catid, $contentid, 1), 'allow_comment'=>(isset($data_info['allow_comment']) ? $data_info['allow_comment'] : 1));
		} else {
			return false;
		}
	}
}