<?php 
defined('IN_CMS') or exit('No permission resources.');
class index {
	private $input,$db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('announce_model');
	}
	
	public function init() {
		
	}
	
	/**
	 * 展示公告
	 */
	public function show() {
		if(!$this->input->get('aid')) {
			showmessage(L('illegal_operation'));
		}
		$aid = intval($this->input->get('aid'));
		$where = '';
		$where .= "`aid`='".$aid."' AND `passed`='1' AND (`endtime` >= '".date('Y-m-d')."' or `endtime`='0000-00-00')";
		$r = $this->db->get_one($where);
		if($r['aid']) {
			$this->db->update(array('hits'=>'+=1'), array('aid'=>$r['aid']));
			$template = $r['show_template'] ? $r['show_template'] : 'show';
			$SEO = seo(get_siteid(), '', $r['title']);
			pc_base::load_sys_class('service')->assign([
				'siteid' => get_siteid(),
				'SEO' => $SEO,
				'r' => $r,
				'title' => $r['title'],
				'content' => $r['content'],
			]);
			pc_base::load_sys_class('service')->display('announce', $template, $r['style']);
		} else {
			showmessage(L('no_exists'));	
		}
	}
}
?>