<?php
defined('IN_CMS') or exit('No permission resources.');
class mobile_url{
	private $urlrules,$categorys,$html_root,$mobile_root;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->urlrules = getcache('urlrules','commons');
		self::set_siteid();
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
		$this->html_root = pc_base::load_config('system','html_root');
		$this->mobile_root = pc_base::load_config('system','mobile_root');
	}
	/**
	* WAP内容页链接
	*/
	public function show($id, $page = 0, $catid = 0, $time = 0, $prefix = '',$data = '',$action = 'edit',$upgrade = 0) {
		$page = max($page,1);
		$urls = $catdir = '';
		$category = $this->categorys[$catid];
		$setting = string2array($category['setting']);
		$content_ishtml = $setting['content_ishtml'];
		if($this->siteid) {
			$siteid = $this->siteid;
		} else {
			$siteid = param::get_cookie('siteid');
		}
		if (!$siteid) $siteid = 1;
		$sitelist = getcache('sitelist','commons');
		if ($sitelist[$siteid]['mobilehtml']==1 && $content_ishtml) {
			$mobile_root = $this->mobile_root;
		}
		//当内容为转换或升级时
		if($upgrade || ($this->input->post('upgrade') && defined('IN_ADMIN') && $this->input->post('upgrade'))) {
			if($this->input->post('upgrade')) $upgrade = $this->input->post('upgrade');
			$upgrade = '/'.ltrim($upgrade,WEB_PATH);
			if($page==1) {
				$url_arr[0] = $url_arr[1] = $upgrade;
			} else {
				$lasttext = strrchr($upgrade,'.');
				$len = -strlen($lasttext);
				$path = substr($upgrade,0,$len);
				$url_arr[0] = $url_arr[1] = $path.'_'.$page.$lasttext;
			}
		} else {
			$show_ruleid = $setting['show_ruleid'];
			if ($sitelist[$siteid]['mobilehtml']==1 && $content_ishtml) {
				$urlrules = $this->urlrules[$show_ruleid];
			} else {
				$urlrules = 'index.php?m=mobile&c=index&a=show&catid={$catid}&id={$id}|index.php?m=mobile&c=index&a=show&catid={$catid}&id={$id}&page={$page}';
			}
			if(!$time) $time = SYS_TIME;
			$urlrules_arr = explode('|',$urlrules);
			if($page==1) {
				$urlrule = $urlrules_arr[0];
			} else {
				$urlrule = isset($urlrules_arr[1]) ? $urlrules_arr[1] : $urlrules_arr[0];
			}
			$domain_dir = '';
			if (strpos($category['url'], '://')!==false && strpos($category['url'], '?')===false) {
				if (preg_match('/^((http|https):\/\/)?([^\/]+)/i', $category['url'], $matches)) {
					$match_url = $matches[0];
					$url = $match_url.'/';
				}
				$db = pc_base::load_model('category_model');
				$r = $db->get_one(array('url'=>$url), '`catid`');
				
				if($r) $domain_dir = $this->get_categorydir($r['catid']).$this->categorys[$r['catid']]['catdir'].'/';
			}
			$categorydir = $this->get_categorydir($catid);
			$catdir = $category['catdir'];
			$year = date('Y',$time);
			$month = date('m',$time);
			$day = date('d',$time);
			
			$urls = str_replace(array('{$categorydir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}','{$page}'),array($categorydir,$catdir,$year,$month,$day,$catid,$id,$page),$urlrule);
			$create_to_html_root = $category['create_to_html_root'];
			
			if ($sitelist[$siteid]['mobilehtml']==1 && $content_ishtml) {
				if($create_to_html_root || $category['sethtml']) {
					$html_root = '';
				} else {
					$html_root = $this->html_root;
				}
			}
			if($content_ishtml && $url) {
				if ($domain_dir && $category['isdomain']) {
					$url_arr[1] = $mobile_root.$html_root.'/'.$domain_dir.$urls;
					$url_arr[0] = $mobile_root.$url.$urls;
				} else {
					$url_arr[1] = $mobile_root.$html_root.'/'.$urls;
					$url_arr[0] = WEB_PATH == '/' ? $match_url.$mobile_root.$html_root.'/'.$urls : $match_url.rtrim(WEB_PATH,'/').$mobile_root.$html_root.'/'.$urls;
				}
			} elseif($content_ishtml) {
				$url_arr[0] = WEB_PATH == '/' ?  $mobile_root.$html_root.'/'.$urls : rtrim(WEB_PATH,'/').$mobile_root.$html_root.'/'.$urls;
				$url_arr[1] = $mobile_root.$html_root.'/'.$urls;
			} else {
				$url_arr[0] = $url_arr[1] = $mobile_root.$urls;
			}
		}
		//生成静态 ,在添加文章的时候，同时生成静态，不在批量更新URL处调用
		if($content_ishtml && $data) {
			$data['id'] = $id;
			$url_arr['content_ishtml'] = 1;
			$url_arr['data'] = $data;
		}
		return $url_arr;
	}
	/**
	 * 获取父栏目路径
	 * @param $catid
	 * @param $dir
	 */
	private function get_categorydir($catid, $dir = '') {
		$setting = array();
		$setting = string2array($this->categorys[$catid]['setting']);
		if ($setting['create_to_html_root']) return $dir;
		if ($this->categorys[$catid]['parentid']) {
			$dir = $this->categorys[$this->categorys[$catid]['parentid']]['catdir'].'/'.$dir;
			return $this->get_categorydir($this->categorys[$catid]['parentid'], $dir);
		} else {
			return $dir;
		}
	}
	/**
	 * 设置站点id
	 */
	private function set_siteid() {
		if(defined('IN_ADMIN')) {
			$this->siteid = get_siteid();
		} else {
			param::get_cookie('siteid');
			$this->siteid = param::get_cookie('siteid');
		}
	}
}