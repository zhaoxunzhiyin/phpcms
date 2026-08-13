<?php
defined('IN_CMS') or exit('No permission resources.');
class type {
	private $input,$db,$categorys;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
	}
	/**
	 * 按照模型搜索
	 */
	public function init() {
		$catid = intval($this->input->get('catid'));
		$typeid = intval($this->input->get('typeid'));
		if(!$catid) showmessage(L('missing_part_parameters'));
		if(!$typeid) showmessage(L('illegal_operation'));
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->categorys = get_category($siteid);
		if(!isset($this->categorys[$catid])) showmessage(L('missing_part_parameters'));
		$usable_type = dr_cat_value($catid, 'usable_type');
		$usable_array = array();
		if($usable_type) $usable_array = explode(',',$usable_type);
		$info = $this->input->get('info');
		if(isset($info['catid']) && $info['catid']) {
			$catid = intval($info['catid']);
		}
		// 栏目格式化
		$cat = $catid ? dr_cat_value($catid) : [];
		$top = $cat;
		if ($catid && $cat['topid']) {
			$top = dr_cat_value($cat['topid']);
		}

		// 获取同级栏目及父级栏目
		list($parent, $related) = dr_related_cat($cat);
		$TYPE = getcache('type_content_'.$siteid,'commons');
		$typelist = array();
		if ($TYPE) {
			foreach($TYPE as $_key=>$_value) {
				if(in_array($_key,$usable_array)) $typelist[$_key] = $_value;
			}
		}
		$modelid = $this->categorys[$catid]['modelid'];
		$modelid = intval($modelid);
		if(!$modelid) showmessage(L('illegal_parameters'));
		$CATEGORYS = $this->categorys;
		$siteid = $this->categorys[$catid]['siteid'];
		$this->db->set_model($modelid);
		$page = max(intval($this->input->get('page')), 1);
		$pagesize = 10;
		$datas = array();
		$datas = $this->db->listinfo("`typeid` = '$typeid'",'id DESC',$page,$pagesize);
		$total = $this->db->number;
		if($total) {
			$pages = $this->db->pages;
		} else {
			$pages = '';
		}
		$SEO = seo($siteid, $catid, $TYPE[$typeid]['name'],$TYPE[$typeid]['description'],$TYPE[$typeid]['name'].'类别');
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'catid' => $catid,
			'top' => $top,
			'parent' => $parent,
			'related' => $related,
			'typeid' => $typeid,
			'TYPE' => $TYPE,
			'typelist' => $typelist,
			'CATEGORYS' => $CATEGORYS,
			'total' => $total,
			'datas' => $datas,
			'pages' => $pages,
		]);
		pc_base::load_sys_class('service')->display('content','type',$default_style);
	}
}
?>