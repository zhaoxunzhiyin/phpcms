<?php
defined('IN_CMS') or exit('No permission resources.');
class index {
	private $input,$type;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		define("SITEID",$siteid);
	}

	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = SITEID;
		$setting = getcache('link', 'commons');
		$SEO = seo(SITEID, '', L('link'), '', '');
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'setting' => $setting,
			'page' => max(1, intval($this->input->get('page'))),
		]);
		pc_base::load_sys_class('service')->display('link', 'index');
	}

	 /**
	 *	友情链接列表页
	 */
	public function list_type() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = SITEID;
		$type_id = trim(urldecode($this->input->get('type_id')));
		$type_id = intval($type_id);
		if($type_id==""){
			$type_id ='0';
		}
		$setting = getcache('link', 'commons');
		$SEO = seo(SITEID, '', L('link'), '', '');
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'setting' => $setting,
			'type_id' => $type_id,
		]);
		pc_base::load_sys_class('service')->display('link', 'list_type');
	} 

	 /**
	 *	申请友情链接 
	 */
	public function register() { 
		$siteid = SITEID;
		if(IS_POST){
			if(dr_is_empty($this->input->post('name'))){
				showmessage(L('sitename_noempty'),"?m=link&c=index&a=register&siteid=$siteid");
			}
			if(dr_is_empty($this->input->post('url')) || !preg_match('/^http[s]?:\/\/(.*)/i', $this->input->post('url'))){
				showmessage(L('siteurl_not_empty'),"?m=link&c=index&a=register&siteid=$siteid");
			}
			$linktype = $this->input->post('linktype');
			if(!in_array($linktype,array('0','1'))){
				$linktype = '0';
			}
			$link_db = pc_base::load_model('link_model');
			$logo = new_html_special_chars(safe_replace(clearhtml($this->input->post('logo'))));
			if(!preg_match('/^http[s]?:\/\/(.*)/i', $logo)){
				$logo = '';
			}
			$name = safe_replace(clearhtml($this->input->post('name')));
			$url = safe_replace(clearhtml($this->input->post('url')));
			$url = trim_script($url);
			if($linktype=='0'){
				$sql = array('siteid'=>$siteid,'typeid'=>intval($this->input->post('typeid')),'linktype'=>intval($linktype),'name'=>$name,'url'=>$url);
			}else{
				$sql = array('siteid'=>$siteid,'typeid'=>intval($this->input->post('typeid')),'linktype'=>intval($linktype),'name'=>$name,'url'=>$url,'logo'=>$logo);
			}
			$link_db->insert($sql);
			showmessage(L('add_success'), "?m=link&c=index&siteid=$siteid");
		} else {
			$setting = getcache('link', 'commons');
			$setting = $setting[$siteid];
			if($setting['is_post']=='0'){
				showmessage(L('suspend_application'), HTTP_REFERER);
			}
			$this->type = pc_base::load_model('type_model');
			$types = $this->type->get_types($siteid);//获取站点下所有友情链接分类
			pc_base::load_sys_class('form');
			$SEO = seo(SITEID, '', L('application_links'), '', '');
			pc_base::load_sys_class('service')->assign([
				'SEO' => $SEO,
				'siteid' => $siteid,
				'setting' => $setting,
				'types' => $types,
			]);
			pc_base::load_sys_class('service')->display('link', 'register');
		}
	} 

}
?>