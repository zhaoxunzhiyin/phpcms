<?php
defined('IN_CMS') or exit('No permission resources.');
class mobile_url{
	private $input,$urlrules,$category_db,$html_root,$mobile_root;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->category_db = pc_base::load_model('category_model');
		$this->html_root = SYS_HTML_ROOT;
		$this->mobile_root = SYS_MOBILE_ROOT;
	}
	/**
	* 手机内容页链接
	*/
	public function show($id, $page = 0, $catid = 0, $time = 0, $prefix = '',$data = '',$action = 'edit',$upgrade = 0) {
		$category = $this->category_db->get_one(array('catid'=>$catid));
		$page = max($page,1);
		$urls = $showurls = $catdir = '';
		$setting = string2array($category['setting']);
		$content_ishtml = $setting['content_ishtml'];
		//当内容为转换或升级时
		if($upgrade || ($this->input->post('upgrade') && defined('IS_ADMIN') && IS_ADMIN && $this->input->post('upgrade'))) {
			if($this->input->post('upgrade')) $upgrade = $this->input->post('upgrade');
			$upgrade = '/'.ltrim($upgrade,WEB_PATH);
			if($page==1) {
				$url_arr[0] = $url_arr[1] = $upgrade;
				$showurl_arr[0] = $showurl_arr[1] = $upgrade;
			} else {
				$lasttext = strrchr($upgrade,'.');
				$len = -strlen($lasttext);
				$path = substr($upgrade,0,$len);
				$url_arr[0] = $url_arr[1] = $path.'_'.$page.$lasttext;
				$showurl_arr[0] = $showurl_arr[1] = $path.'_'.$page.$lasttext;
			}
		} else {
			$isdomain = 0;
			if(!preg_match('/^(http|https):\/\//', $category['url'])) {
			} elseif ($setting['ishtml']) {
				$isdomain = 1;
			}
			if (dr_site_info('mobilehtml', $category['siteid'])==1) {
				$urlrules = $this->urlrules[$setting['show_ruleid']];
			} else {
				$urlrules = 'index.php?m=mobile&c=index&a=show&catid={$catid}&id={$id}|index.php?m=mobile&c=index&a=show&catid={$catid}&id={$id}&page={$page}';
			}
			if(!$time) $time = SYS_TIME;
			$urlrules_arr = explode('|',(string)$urlrules);
			if($page==1) {
				$urlrule = $urlrules_arr[0];
			} else {
				$urlrule = isset($urlrules_arr[1]) ? $urlrules_arr[1] : $urlrules_arr[0];
			}
			$domain_dir = '';
			if (strpos((string)$category['url'], '://')!==false && strpos((string)$category['url'], '?')===false) {
				if (preg_match('/^((http|https):\/\/)?([^\/]+)/i', $category['url'], $matches)) {
					$match_url = $matches[0];
					$url = $match_url.'/';
				}
				$r = $this->category_db->get_one(array('url'=>$url), '`catid`');
				$r2 = $this->category_db->get_one(array('catid'=>$r['catid']));
				if($r && $r2) $domain_dir = $this->get_categorydir($r['catid']).$r2['catdir'].'/';
			}
			$categorydir = $this->get_categorydir($catid);
			$catdir = $category['catdir'];
			$year = date('Y',$time);
			$month = date('m',$time);
			$day = date('d',$time);
			
			$urls = str_replace(array('{$categorydir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}','{$page}'),array($categorydir,$catdir,$year,$month,$day,$catid,$id,$page),$urlrule);
			$showurls = str_replace(array('{$categorydir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}','{$page}'),array($categorydir,$catdir,$year,$month,$day,$catid,$id,'{page}'),$urlrule);
			$create_to_html_root = $category['create_to_html_root'];
			
			if($create_to_html_root || $category['sethtml']) {
				$html_root = '';
			} else {
				$html_root = $this->html_root;
			}
			!dr_site_info('mobilemode', $category['siteid']) && $html_root = $this->mobile_root.$this->html_root;
			if($content_ishtml && $url) {
				if ($domain_dir && $isdomain) {
					$url_arr[1] = $html_root.'/'.$domain_dir.$urls;
					$url_arr[0] = $url.$urls;
					$showurl_arr[1] = $html_root.'/'.$domain_dir.$showurls;
					$showurl_arr[0] = $url.$showurls;
				} else {
					$url_arr[1] = $html_root.'/'.$urls;
					$url_arr[0] = WEB_PATH == '/' ? $match_url.$html_root.'/'.$urls : $match_url.rtrim(WEB_PATH,'/').$html_root.'/'.$urls;
					$showurl_arr[1] = $html_root.'/'.$showurls;
					$showurl_arr[0] = WEB_PATH == '/' ? $match_url.$html_root.'/'.$showurls : $match_url.rtrim(WEB_PATH,'/').$html_root.'/'.$showurls;
				}
			} elseif($content_ishtml) {
				$url_arr[0] = WEB_PATH == '/' ? $html_root.'/'.$urls : rtrim(WEB_PATH,'/').$html_root.'/'.$urls;
				$url_arr[1] = $html_root.'/'.$urls;
				$showurl_arr[0] = WEB_PATH == '/' ? $html_root.'/'.$showurls : rtrim(WEB_PATH,'/').$html_root.'/'.$showurls;
				$showurl_arr[1] = $html_root.'/'.$showurls;
			} else {
				if(dr_cat_value($catid, 'siteid') && dr_cat_value($catid, 'siteid')!=1) {
					$url_arr[0] = $url_arr[1] = (string)dr_site_info('mobile_domain', dr_cat_value($catid, 'siteid')).$urls;
					$showurl_arr[0] = $showurl_arr[1] = (string)dr_site_info('mobile_domain', dr_cat_value($catid, 'siteid')).$showurls;
				} else {
					$url_arr[0] = $url_arr[1] = APP_PATH.$urls;
					$showurl_arr[0] = $showurl_arr[1] = APP_PATH.$showurls;
				}
			}
		}
		//生成静态 ,在添加文章的时候，同时生成静态，不在批量更新URL处调用
		if($content_ishtml && $data) {
			$data['id'] = $id;
			$url_arr['content_ishtml'] = 1;
			$url_arr['data'] = $data;
			$showurl_arr['content_ishtml'] = 1;
			$showurl_arr['data'] = $data;
		}
		$url_arr = str_replace(array(dr_site_info('domain', $category['siteid']), 'm=content'), array(dr_site_info('mobile_domain', $category['siteid']), 'm=mobile'), $url_arr);
		$showurl_arr = str_replace(array(dr_site_info('domain', $category['siteid']), 'm=content'), array(dr_site_info('mobile_domain', $category['siteid']), 'm=mobile'), $showurl_arr);
		return array($url_arr, $showurl_arr);
	}
	/**
	 * 获取父栏目路径
	 * @param $catid
	 * @param $dir
	 */
	private function get_categorydir($catid, $dir = '') {
		$category = $this->category_db->get_one(array('catid'=>$catid));
		$setting = array();
		$setting = string2array($category['setting']);
		if ($setting['create_to_html_root']) return $dir;
		if ($category['parentid']) {
			$r = $this->category_db->get_one(array('catid'=>$category['parentid']));
			$dir = $r['catdir'].'/'.$dir;
			return $this->get_categorydir($category['parentid'], $dir);
		} else {
			return $dir;
		}
	}
}