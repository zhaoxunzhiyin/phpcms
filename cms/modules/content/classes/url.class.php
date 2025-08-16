<?php
defined('IN_CMS') or exit('No permission resources.');
class url{
	private $input,$category_db,$urlrules,$html_root,$mobile_root;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->category_db = pc_base::load_model('category_model');
		$this->urlrules = getcache('urlrules','commons');
		$this->html_root = SYS_HTML_ROOT;
		$this->mobile_root = SYS_MOBILE_ROOT;
	}

	/**
	 * 内容页链接
	 * @param $id 内容id
	 * @param $page 当前页
	 * @param $catid 栏目id
	 * @param $time 添加时间
	 * @param $prefix 前缀
	 * @param $data 数据
	 * @param $action 操作方法
	 * @param $upgrade 是否是升级数据
	 * @return array 0=>url , 1=>生成路径
	 */
	public function show($id, $page = 0, $catid = 0, $time = 0, $prefix = '',$data = '',$action = 'edit',$upgrade = 0,$mobile = 0) {
		!$prefix && $prefix = $id;
		$prefix = str_replace(' ', '-', $prefix);
		$prefix = strtolower($prefix);
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
			$urlrules = $this->urlrules[$setting['show_ruleid']];
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
			$parentdir = $this->get_parentdir($catid);
			$catdir = $category['catdir'];
			$year = date('Y',$time);
			$month = date('m',$time);
			$day = date('d',$time);
			
			$urls = str_replace(array('{$categorydir}','{$parentdir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$prefix}','{$id}','{$page}'),array($categorydir,$parentdir,$catdir,$year,$month,$day,$catid,$prefix,$id,$page),$urlrule);
			$showurls = str_replace(array('{$categorydir}','{$parentdir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$prefix}','{$id}','{$page}'),array($categorydir,$parentdir,$catdir,$year,$month,$day,$catid,$prefix,$id,'{page}'),$urlrule);
			$create_to_html_root = $setting['create_to_html_root'];
			
			if($create_to_html_root || $category['sethtml']) {
				$html_root = '';
			} else {
				$html_root = $this->html_root;
			}
			if($content_ishtml && $url) {
				if ($domain_dir && $isdomain) {
					$url_arr[1] = ($mobile ? $this->mobile_root : '').$html_root.'/'.$domain_dir.$urls;
					$url_arr[0] = $url.$urls;
					$showurl_arr[1] = ($mobile ? $this->mobile_root : '').$html_root.'/'.$domain_dir.$showurls;
					$showurl_arr[0] = $url.$showurls;
				} else {
					$url_arr[1] = ($mobile ? $this->mobile_root : '').$html_root.'/'.$urls;
					$url_arr[0] = WEB_PATH == '/' ? $match_url.($mobile ? $this->mobile_root : '').$html_root.'/'.$urls : $match_url.rtrim(WEB_PATH,'/').($mobile ? $this->mobile_root : '').$html_root.'/'.$urls;
					$showurl_arr[1] = ($mobile ? $this->mobile_root : '').$html_root.'/'.$showurls;
					$showurl_arr[0] = WEB_PATH == '/' ? $match_url.($mobile ? $this->mobile_root : '').$html_root.'/'.$showurls : $match_url.rtrim(WEB_PATH,'/').($mobile ? $this->mobile_root : '').$html_root.'/'.$showurls;
				}
			} elseif($content_ishtml) {
				$url_arr[0] = WEB_PATH == '/' ? ($mobile ? $this->mobile_root : '').$html_root.'/'.$urls : rtrim(WEB_PATH,'/').($mobile ? $this->mobile_root : '').$html_root.'/'.$urls;
				$url_arr[1] = ($mobile ? $this->mobile_root : '').$html_root.'/'.$urls;
				$showurl_arr[0] = WEB_PATH == '/' ? ($mobile ? $this->mobile_root : '').$html_root.'/'.$showurls : rtrim(WEB_PATH,'/').($mobile ? $this->mobile_root : '').$html_root.'/'.$showurls;
				$showurl_arr[1] = ($mobile ? $this->mobile_root : '').$html_root.'/'.$showurls;
			} else {
				if(dr_cat_value($catid, 'siteid') && dr_cat_value($catid, 'siteid')!=1) {
					$url_arr[0] = $url_arr[1] = (string)dr_site_info('domain', dr_cat_value($catid, 'siteid')).$urls;
					$showurl_arr[0] = $showurl_arr[1] = (string)dr_site_info('domain', dr_cat_value($catid, 'siteid')).$showurls;
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
		return array($url_arr, $showurl_arr);
	}
	
	/**
	 * 获取栏目的访问路径
	 * 在修复栏目路径处重建目录结构用
	 * @param intval $catid 栏目ID
	 * @param intval $page 页数
	 */
	public function category_url($catid, $page = 1) {
		$category = $this->category_db->get_one(array('catid'=>$catid));
		if($category['type']==2) return $category['url'];
		$page = max(intval($page), 1);
		$setting = string2array($category['setting']);
		$category_dir = $this->get_categorydir($catid);
		$parentdir = $this->get_parentdir($catid);
		$urlrules = $this->urlrules[$setting['category_ruleid']];
		$urlrules_arr = explode('|',(string)$urlrules);
		if ($page==1) {
			$urlrule = $urlrules_arr[0];
		} else {
			$urlrule = $urlrules_arr[1];
		}
		if (!$setting['ishtml']) { //如果不生成静态
			$url = str_replace(array('{$categorydir}','{$parentdir}','{$catdir}','{$catid}','{$page}'),array($category_dir,$parentdir,$category['catdir'],$catid,$page),$urlrule);
			if (strpos($url, '\\')!==false) {
				if(dr_cat_value($catid, 'siteid') && dr_cat_value($catid, 'siteid')!=1) {
					$url = (string)dr_site_info('domain', dr_cat_value($catid, 'siteid')).str_replace('\\', '/', $url);
				} else {
					$url = APP_PATH.str_replace('\\', '/', $url);
				}
			}
		} else { //生成静态
			if ($category['arrparentid']) {
				$parentids = explode(',', (string)$category['arrparentid']);
			}
			$parentids[] = $catid;
			$domain_dir = '';
			foreach ($parentids as $pid) { //循环查询父栏目是否设置了二级域名
				$r = $this->category_db->get_one(array('catid'=>$pid));
				if (strpos(strtolower((string)$r['url']), '://')!==false && strpos((string)$r['url'], '?')===false) {
					$r['url'] = preg_replace('/([(http|https):\/\/]{0,})([^\/]*)([\/]{1,})/i', '$1$2/', (string)$r['url'], -1); //取消掉双'/'情况
					if (substr_count((string)$r['url'], '/')==3 && substr((string)$r['url'],-1,1)=='/') { //如果url中包含‘http://’并且‘/’在3个则为二级域名设置栏目
						$url = $r['url'];
						$domain_dir = $this->get_categorydir($pid).$r['catdir'].'/'; //得到二级域名的目录
					}
				}
			}
			
			$urls = str_replace(array('{$categorydir}','{$parentdir}','{$catdir}','{$catid}','{$page}'),array($category_dir,$parentdir,$category['catdir'],$catid,$page),$urlrule);
			if ($url && $domain_dir) { //如果存在设置二级域名的情况
				if (strpos($urls, $domain_dir)===0) {
					$url = str_replace(array($domain_dir, '\\'), array($url, '/'), $urls);
				} else {
					$urls = $domain_dir.$urls;
					$url = str_replace(array($domain_dir, '\\'), array($url, '/'), $urls);
				}
			} else { //不存在二级域名的情况
				$url = $urls;
			}
		}
		if (in_array(basename($url), array('index.html', 'index.htm', 'index.shtml'))) {
			$url = dirname($url).'/';
		}
		if (strpos($url, '://')===false) $url = str_replace('//', '/', $url);
		if(strpos($url, '/')===0) $url = substr($url,1);
		return $url;
	}
	/**
	 * 生成列表页分页地址
	 * @param $ruleid 角色id
	 * @param $categorydir 父栏目路径
	 * @param $parentdir 包含父级子级层次的目录
	 * @param $catdir 栏目路径
	 * @param $catid 栏目id
	 * @param $page 当前页
	 */
	public function get_list_url($ruleid, $categorydir, $parentdir, $catdir, $catid, $page = 1) {
		$urlrules = $this->urlrules[$ruleid];
		$urlrules_arr = explode('|',(string)$urlrules);
		if ($page==1) {
			$urlrule = $urlrules_arr[0];
		} else {
			$urlrule = $urlrules_arr[1];
		}
		$urls = str_replace(array('{$categorydir}','{$parentdir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$page}'),array($categorydir,$parentdir,$catdir,$year,$month,$day,$catid,$page),$urlrule);
		return $urls;
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
	/**
	 * 获取包含父级子级层次的目录
	 * @param $catid
	 */
	private function get_parentdir($catid) {
		$category = $this->category_db->get_one(array('catid'=>$catid));
		return $category['parentdir'].= $category['catdir'];
	}
}