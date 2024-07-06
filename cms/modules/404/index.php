<?php
defined('IN_CMS') or exit('No permission resources.');
class index {
	private $input,$cache,$_userid,$_username,$_groupid;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->_userid = param::get_cookie('_userid');
		$this->_username = param::get_cookie('_username');
		$this->_groupid = param::get_cookie('_groupid');
	}
	/**
	 * 404 页面
	 */
	public function init() {
		if (IS_DEV) {
			$uri = $this->input->get('uri', true);
			$msg = '没有找到这个页面: '.$uri;
		} else {
			$msg = L('没有找到这个页面');
		}
		param::goto_404_page($msg);
	}
	/**
	 * 跳转地址安全检测
	 */
	public function jump() {
		if(intval($this->input->get('siteid'))) {
			$siteid = intval($this->input->get('siteid'));
		} else if(defined('SITE_ID') && SITE_ID!=1) {
			$siteid = SITE_ID;
		} else {
			$siteid = get_siteid();
		}
		$siteid = $GLOBALS['siteid'] = max($siteid,1);
		define('SITEID', $siteid);
		$_userid = $this->_userid;
		$_username = $this->_username;
		$_groupid = $this->_groupid;
		//SEO
		$SEO = seo($siteid, 0, L('安全中心'));
		$go = $this->input->get('go');
		$link = $this->cache->get_auth_data($go, 1);
		if (!$link) {
			dr_redirect(WEB_PATH.'index.php?m=404', 'refresh');exit;;
		}
		$arr = parse_url($link);
		$host = $arr['host'];
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';

		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'_userid' => $_userid,
			'_username' => $_username,
			'_groupid' => $_groupid,
			'link' => $link,
			'host' => $host,
		]);
		pc_base::load_sys_class('service')->display('404','jump',$default_style);
	}
}
?>