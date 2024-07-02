<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util');
pc_base::load_app_func('global');
class index {
	private $input,$cache,$db,$_userid,$_username,$_groupid,$category,$category_priv_db,$category_setting,$sitemodel,$form_cache,$page_db,$url,$category_db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('content_model');
		$this->_userid = param::get_cookie('_userid');
		$this->_username = param::get_cookie('_username');
		$this->_groupid = param::get_cookie('_groupid');
	}
	//首页
	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		// 挂钩点 网站首页时
		pc_base::load_sys_class('hooks')::trigger('cms_index');
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
		$SEO = seo($siteid);
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';
		$CATEGORYS = getcache('cache', 'module/category-'.$siteid.'-data');
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'_userid' => $_userid,
			'_username' => $_username,
			'_groupid' => $_groupid,
			'CATEGORYS' => $CATEGORYS,
		]);
		pc_base::load_sys_class('hooks')::trigger('module_index');
		if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(TPLPATH.$default_style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'index.html')) {
				define('ISMOBILE', 0);
				define('IS_HTML', dr_site_info('ishtml', $siteid));
				pc_base::load_sys_class('service')->display('content','index',$default_style);
			} else {
				pc_base::load_app_func('global','mobile');
				define('ISMOBILE', 1);
				if(dr_site_info('ishtml', $siteid) && dr_site_info('mobilehtml', $siteid)) {
					define('IS_HTML', 1);
				} else {
					define('IS_HTML', 0);
				}
				pc_base::load_sys_class('service')->display('mobile','index',$default_style);
			}
		}else{
			define('ISMOBILE', 0);
			define('IS_HTML', dr_site_info('ishtml', $siteid));
			pc_base::load_sys_class('service')->display('content','index',$default_style);
		}
	}
	//列表页
	public function lists() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$catid = intval($this->input->get('catid'));
		// 挂钩点
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('module_category');
		if ($rt2 && isset($rt2['code']) && $rt2['code']) {
			$catid = $rt2['data'];
		}
		if(!$catid){
			$catid = $this->_getCategoryId($this->input->get('catdir') ? $this->input->get('catdir') : $this->input->get('categorydir'));
		}
		$_priv_data = $this->_category_priv($catid);
		if($_priv_data=='-1') {
			$forward = urlencode(dr_now_url());
			showmessage(L('login_website'),APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
		} elseif($_priv_data=='-2') {
			showmessage(L('no_priv'));
		}
		$_userid = $this->_userid;
		$_username = $this->_username;
		$_groupid = $this->_groupid;

		if(!$catid) showmessage(L('category_not_exists'),'blank');
		$category = $CAT = dr_cat_value($catid);
		if (!$category) {
			param::goto_404_page(L('category_not_exists'));
		}
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		define('SITEID', $siteid);
		$CATEGORYS = getcache('cache', 'module/category-'.$siteid.'-data');
		if(!isset($CATEGORYS[$catid])) showmessage(L('category_not_exists'),'blank');
		// 挂钩点 格式化栏目数据
		$rt3 = pc_base::load_sys_class('hooks')::trigger_callback('module_category_data', $category);
		if ($rt3 && isset($rt3['code']) && $rt3['code']) {
			$category = $CAT = $rt3['data'];
		}
		$siteid = $GLOBALS['siteid'] = $CAT['siteid'];
		extract($CAT);
		$setting = dr_string2array($setting);
		//SEO
		if(!$setting['meta_title']) $setting['meta_title'] = $catname;
		$SEO = seo($siteid, '',$setting['meta_title'],$setting['meta_description'],$setting['meta_keywords']);
		define('STYLE',$setting['template_list']);
		$page = intval($this->input->get('page'));

		$template = $setting['category_template'] ? $setting['category_template'] : 'category';
		$template_list = $setting['list_template'] ? $setting['list_template'] : 'list';
		
		$style = $setting['template_list'];
		if(!$style) $style = 'default';
		
		// 判断是否外链
		if ($type == 2) {
			dr_redirect($url, 'refresh');exit;
		}
		
		// 验证是否存在子栏目，是否将下级第一个栏目作为当前页
		if ($type != 2 && $child && $setting['getchild']) {
			$temp = explode(',', $arrchildid);
			if ($temp) {
				foreach ($temp as $i) {
					$row = dr_cat_value($i);
					if ($i != $catid && $row['type'] != 2 && !$row['setting']['getchild']) {
						$catid = $i;
						$category = $row;
						$url = $category['url'];
						param::redirect($url);
						exit;
						break;
					}
				}
			}
		}
		
		// 获取同级栏目及父级栏目
		list($parent, $related) = dr_related_cat($category);
		$top = $category;
		if ($catid && $category['topid']) {
			$top = dr_cat_value($category['topid']);
		}
		$arrchild_arrs = $CATEGORYS[$parentid]['arrchildid'];
		if($arrchild_arrs=='') $arrchild_arrs = $CATEGORYS[$catid]['arrchildid'];
		$arrchild_arrs = explode(',',$arrchild_arrs);
		array_shift($arrchild_arrs);
		$arrchild_arr = [];
		foreach ($arrchild_arrs as $cache) {
			$arrchild_setting = dr_string2array(dr_cat_value($cache, 'setting'));
			if (!$arrchild_setting['isleft']) {
				continue;
			}
			$arrchild_arr[] = $cache;
		}
		unset($arrchild_arrs);
		
		// 传入模板
		pc_base::load_sys_class('service')->assign($category);
		pc_base::load_sys_class('service')->assign(array(
			'siteid' => $siteid,
			'_userid' => $_userid,
			'_username' => $_username,
			'_groupid' => $_groupid,
			'catid' => $catid,
			'CAT' => $CAT,
			'category' => $category,
			'top' => $top,
			'CATEGORYS' => $CATEGORYS,
			'params' => ['catid' => $catid],
			'page' => max(1, $page),
			'parent' => $parent,
			'related' => $related,
			'arrchild_arr' => $arrchild_arr,
		));
		
		if($type==0) {
			$template = $child ? $template : $template_list;
			$arrparentid = explode(',', $arrparentid);
			$top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
			$array_child = array();
			$self_array = explode(',', $arrchildid);
			//获取一级栏目ids
			foreach ($self_array as $arr) {
				if($arr!=$catid && $CATEGORYS[$arr]['parentid']==$catid) {
					$array_child[] = $arr;
				}
			}
			$arrchildid = implode(',', $array_child);
			//URL规则
			$urlrules = getcache('urlrules','commons');
			$urlrules = str_replace('|', '~',$urlrules[$setting['category_ruleid']]);
			$tmp_urls = explode('~',$urlrules);
			$tmp_urls = isset($tmp_urls[1]) ? $tmp_urls[1] : $tmp_urls[0];
			preg_match_all('/{\$([a-z0-9_]+)}/i',$tmp_urls,$_urls);
			if(!empty($_urls[1])) {
				foreach($_urls[1] as $_v) {
					$GLOBALS['URL_ARRAY'][$_v] = $this->input->get($_v);
				}
			}
			define('URLRULE', $urlrules);
			$GLOBALS['URL_ARRAY']['categorydir'] = $this->get_categorydir($catid);
			$GLOBALS['URL_ARRAY']['catdir'] = $catdir;
			$GLOBALS['URL_ARRAY']['catid'] = $catid;
			pc_base::load_sys_class('service')->assign(array(
				'SEO' => $SEO,
				'top_parentid' => $top_parentid,
			));
			if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
				if (!file_exists(TPLPATH.$style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.$template.'.html')) {
					define('ISMOBILE', 0);
					define('IS_HTML', $setting['ishtml']);
					pc_base::load_sys_class('service')->display('content',$template);
				} else {
					pc_base::load_app_func('global','mobile');
					define('ISMOBILE', 1);
					if($setting['ishtml'] && dr_site_info('mobilehtml', $siteid)) {
						define('IS_HTML', 1);
					} else {
						define('IS_HTML', 0);
					}
					pc_base::load_sys_class('service')->display('mobile',$template);
				}
			}else{
				define('ISMOBILE', 0);
				define('IS_HTML', $setting['ishtml']);
				pc_base::load_sys_class('service')->display('content',$template);
			}
		} else {
		//单网页
			$this->page_db = pc_base::load_model('page_model');
			$r = $this->page_db->get_one(array('catid'=>$catid));
			require_once CACHE_MODEL_PATH.'content_output.class.php';
			$content_output = new content_output(-2);
			$data = $content_output->get($r);
			$data = $r ? array_merge($r,$data) : $data;
			if($data) extract($data);
			$template = $setting['page_template'] ? $setting['page_template'] : 'page';
			$keywords = $keywords ? $keywords : $setting['meta_keywords'];
			$SEO = seo($siteid, 0, $setting['meta_title'] ? $setting['meta_title'] : $title,$setting['meta_description'],$keywords);
			pc_base::load_sys_class('service')->assign(array(
				'SEO' => $SEO,
			));
			pc_base::load_sys_class('service')->assign($data);
			if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
				if (!file_exists(TPLPATH.$style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.$template.'.html')) {
					define('ISMOBILE', 0);
					define('IS_HTML', $setting['ishtml']);
					pc_base::load_sys_class('service')->display('content',$template);
				} else {
					pc_base::load_app_func('global','mobile');
					define('ISMOBILE', 1);
					if($setting['ishtml'] && dr_site_info('mobilehtml', $siteid)) {
						define('IS_HTML', 1);
					} else {
						define('IS_HTML', 0);
					}
					pc_base::load_sys_class('service')->display('mobile',$template);
				}
			}else{
				define('ISMOBILE', 0);
				define('IS_HTML', $setting['ishtml']);
				pc_base::load_sys_class('service')->display('content',$template);
			}
		}
	}
	//内容页
	public function show() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$catid = intval($this->input->get('catid'));
		if(!$catid){
			$catid = $this->_getCategoryId($this->input->get('catdir') ? $this->input->get('catdir') : $this->input->get('categorydir'));
		}
		$id = intval($this->input->get('id'));
		$remains = true;

		if(!$catid || !$id) showmessage(L('information_does_not_exist'),'blank');
		$_userid = $this->_userid;
		$_username = $this->_username;
		$_groupid = $this->_groupid;

		$page = intval($this->input->get('page'));
		$page = max($page,1);
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		define('SITEID', $siteid);
		$CATEGORYS = getcache('cache', 'module/category-'.$siteid.'-data');
		
		if(!isset($CATEGORYS[$catid]) || dr_cat_value($catid, 'type')!=0) showmessage(L('information_does_not_exist'),'blank');
		$this->category = $category = $CAT = dr_cat_value($catid);
		$this->category_setting = $CAT['setting'] = string2array($this->category['setting']);
		$siteid = $GLOBALS['siteid'] = $CAT['siteid'];
		
		$MODEL = getcache('model','commons');
		$modelid = $CAT['modelid'];
		$this->sitemodel = $this->cache->get('sitemodel');
		$this->form_cache = $this->sitemodel[$MODEL[$modelid]['tablename']];
		$tablename = $this->db->table_name = $this->db->db_tablepre.$MODEL[$modelid]['tablename'];
		
		$name = 'module_'.$modelid.'_show_id_'.$id.($page > 1 ? '_p'.$page : '');
		$rs_name = 'rs_module_'.$modelid.'_show_id_'.$id.($page > 1 ? '_p'.$page : '');
		$data = $this->cache->get_data($name);
		$rs = $this->cache->get_data($rs_name);
		if (!$data || !$rs) {
			$r = $this->db->get_one(array('id'=>$id));
			if(!$r || $r['status'] != 99) param::goto_404_page(L('info_does_not_exists'));
			
			$this->db->table_name = $tablename.'_data_'.$r['tableid'];
			$r2 = $this->db->get_one(array('id'=>$id));
			$rs = $r2 ? array_merge($r,$r2) : $r;
			// 挂钩点
			$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('module_show', $rs);
			if ($rt2 && isset($rt2['code']) && $rt2['code']) {
				$rs = $rt2['data'];
			}

			//再次重新赋值，以数据库为准
			$catid = $CATEGORYS[$r['catid']]['catid'];
			$modelid = $CATEGORYS[$catid]['modelid'];
			
			// 格式化字段
			require_once CACHE_MODEL_PATH.'content_output.class.php';
			$content_output = new content_output($modelid,$catid,$CATEGORYS);
			$data = $content_output->get($rs);
			
			// 缓存结果 
			if (SYS_CACHE) {
				$this->cache->set_data($name, $data, SYS_CACHE_SHOW * 3600);
				$this->cache->set_data($rs_name, $rs, SYS_CACHE_SHOW * 3600);
			}
		}
		// 挂钩点 内容读取之后
		$rt3 = pc_base::load_sys_class('hooks')::trigger_callback('module_show_data', $data);
		if ($rt3 && isset($rt3['code']) && $rt3['code']) {
			$data = $rt3['data'];
		}
		// 检测转向字段
		foreach ($this->form_cache['field'] as $t) {
			if ($t['formtype'] == 'redirect' && $data[$t['field']]) {
				// 存在转向字段时的情况
				$this->hits_db = pc_base::load_model('hits_model');
				$hitsid = 'c-'.$modelid.'-'.$id;
				$hits_data = $this->hits_db->get_one(array('hitsid'=>$hitsid));
				if ($hits_data) {
					$views = $hits_data['views'] + 1;
					$yesterdayviews = (date('Ymd', $hits_data['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $hits_data['dayviews'] : $hits_data['yesterdayviews'];
					$dayviews = (date('Ymd', $hits_data['updatetime']) == date('Ymd', SYS_TIME)) ? ($hits_data['dayviews'] + 1) : 1;
					$weekviews = (date('YW', $hits_data['updatetime']) == date('YW', SYS_TIME)) ? ($hits_data['weekviews'] + 1) : 1;
					$monthviews = (date('Ym', $hits_data['updatetime']) == date('Ym', SYS_TIME)) ? ($hits_data['monthviews'] + 1) : 1;
					$this->hits_db->update(array('views'=>$views,'yesterdayviews'=>$yesterdayviews,'dayviews'=>$dayviews,'weekviews'=>$weekviews,'monthviews'=>$monthviews,'updatetime'=>SYS_TIME),array('hitsid'=>$hitsid));
				}
				pc_base::load_sys_class('service')->assign('gotu_url', $data[$t['field']]);
				pc_base::load_sys_class('service')->admin_display('go', 'admin');
				return $data;
			}
		}
		extract($data);
		
		//检查文章会员组权限
		if($groupids_view && is_array($groupids_view)) {
			$_groupid = param::get_cookie('_groupid');
			$_groupid = intval($_groupid);
			if(!$_groupid) {
				$forward = urlencode(dr_now_url());
				showmessage(L('login_website'),APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
			}
			if(!in_array($_groupid,$groupids_view)) showmessage(L('no_priv'));
		} else {
			//根据栏目访问权限判断权限
			$_priv_data = $this->_category_priv($catid);
			if($_priv_data=='-1') {
				$forward = urlencode(dr_now_url());
				showmessage(L('login_website'),APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
			} elseif($_priv_data=='-2') {
				showmessage(L('no_priv'));
			}
		}
		if(module_exists('comment')) {
			$allow_comment = isset($allow_comment) ? $allow_comment : 1;
		} else {
			$allow_comment = 0;
		}
		//阅读收费 类型
		$paytype = $rs['paytype'];
		$readpoint = $rs['readpoint'];
		$allow_visitor = 1;
		if($readpoint || $this->category_setting['defaultchargepoint']) {
			if(!$readpoint) {
				$readpoint = $this->category_setting['defaultchargepoint'];
				$paytype = $this->category_setting['paytype'];
			}
			
			//检查是否支付过
			$allow_visitor = self::_check_payment($catid.'_'.$id,$paytype);
			if(!$allow_visitor) {
				$http_referer = urlencode(dr_now_url());
				$allow_visitor = sys_auth($catid.'_'.$id.'|'.$readpoint.'|'.$paytype).'&http_referer='.$http_referer;
			} else {
				$allow_visitor = 1;
			}
		}
		//最顶级栏目ID
		$arrparentid = explode(',', $CAT['arrparentid']);
		$top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
		
		$template = $template ? $template : $CAT['setting']['show_template'];
		if(!$template) $template = 'show';
		//SEO
		$seo_keywords = '';
		if(!empty($keywords)) $seo_keywords = implode(',',$keywords);
		$SEO = seo($siteid, $catid, $title, $description, $seo_keywords);
		
		define('STYLE',$CAT['setting']['template_list']);
		if(isset($rs['paginationtype'])) {
			$paginationtype = $rs['paginationtype'];
			$maxcharperpage = $rs['maxcharperpage'];
		}
		$pages = '';
		$titles = array();
		if($rs['paginationtype']==1) {
			//自动分页
			if($maxcharperpage < 10) $maxcharperpage = 500;
			$contentpage = pc_base::load_app_class('contentpage');
			$content = $contentpage->get_data($content,$maxcharperpage);
		}
		if($rs['paginationtype']!=0) {
			//手动分页
			$CONTENT_POS = strpos($content, '[page]');
			if($CONTENT_POS !== false) {
				$this->url = pc_base::load_app_class('url', 'content');
				$contents = array_filter(explode('[page]', $content));
				$pagenumber = dr_count($contents);
				if (strpos($content, '[/page]')!==false && ($CONTENT_POS<7)) {
					$pagenumber--;
				}
				for($i=1; $i<=$pagenumber; $i++) {
					list($pageurls[$i], $showurls[$i]) = $this->url->show($id, $i, $catid, $rs['inputtime'], $rs['prefix']);
				}
				$END_POS = strpos((string)$content, '[/page]');
				if($END_POS !== false) {
					if($CONTENT_POS>7) {
						$content = '[page]'.$title.'[/page]'.$content;
					}
					if(preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
						foreach($m[1] as $k=>$v) {
							$p = $k+1;
							$titles[$p]['title'] = clearhtml($v);
							$titles[$p]['url'] = $pageurls[$p][0];
						}
					}
				}
				//当不存在 [/page]时，则使用下面分页
				$pages = content_pages($pagenumber,$page,$showurls);
				//判断[page]出现的位置是否在第一位 
				if($CONTENT_POS<7) {
					$content = $contents[$page];
				} else {
					if ($page==1 && !empty($titles)) {
						$content = $title.'[/page]'.$contents[$page-1];
					} else {
						$content = $contents[$page-1];
					}
				}
				if($titles) {
					list($title, $content) = explode('[/page]', $content);
					$content = trim((string)$content);
					if(strpos($content,'</p>')===0) {
						$content = '<p>'.$content;
					}
					if(stripos($content,'<p>')===0) {
						$content = $content.'</p>';
					}
				}
			}
		}
		$this->db->table_name = $tablename;
		//上一页
		$previous_page = $this->db->get_one("`catid` = '$catid' AND `id`<'$id' AND `status`=99",'*','id DESC');
		//下一页
		$next_page = $this->db->get_one("`catid`= '$catid' AND `id`>'$id' AND `status`=99",'*','id ASC');

		$style = $CAT['setting']['template_list'];
		if(!$style) $style = 'default';
		// 获取同级栏目及父级栏目
		list($parent, $related) = dr_related_cat($category);
		$top = $category;
		if ($catid && $category['topid']) {
			$top = dr_cat_value($category['topid']);
		}
		// 传入模板
		pc_base::load_sys_class('service')->assign($data);
		pc_base::load_sys_class('service')->assign(array(
			'SEO' => $SEO,
			'siteid' => $siteid,
			'_userid' => $_userid,
			'_username' => $_username,
			'_groupid' => $_groupid,
			'modelid' => $modelid,
			'rs' => $rs,
			'id' => $id,
			'catid' => $catid,
			'CAT' => $CAT,
			'category' => $category,
			'top' => $top,
			'top_parentid' => $top_parentid,
			'CATEGORYS' => $CATEGORYS,
			'params' => ['catid' => $catid],
			'page' => max(1, $page),
			'parent' => $parent,
			'related' => $related,
			'title' => clearhtml($title) ? $title : $rs['title'],
			'content' => $content,
			'paytype' => $paytype,
			'readpoint' => $readpoint,
			'allow_visitor' => $allow_visitor,
			'titles' => $titles,
			'pages' => $pages,
			'previous_page' => $previous_page,
			'next_page' => $next_page,
			'allow_comment' => $allow_comment,
		));
		if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(TPLPATH.$style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.$template.'.html')) {
				define('ISMOBILE', 0);
				define('IS_HTML', $CAT['setting']['content_ishtml']);
				pc_base::load_sys_class('service')->display('content',$template);
			} else {
				pc_base::load_app_func('global','mobile');
				define('ISMOBILE', 1);
				if($CAT['setting']['content_ishtml'] && dr_site_info('mobilehtml', $siteid)) {
					define('IS_HTML', 1);
				} else {
					define('IS_HTML', 0);
				}
				pc_base::load_sys_class('service')->display('mobile',$template);
			}
		}else{
			define('ISMOBILE', 0);
			define('IS_HTML', $CAT['setting']['content_ishtml']);
			pc_base::load_sys_class('service')->display('content',$template);
		}
	}
	
	//导航页
	function maps() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
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
		$SEO = seo($siteid);
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'_userid' => $_userid,
			'_username' => $_username,
			'_groupid' => $_groupid,
		]);
		if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(TPLPATH.$default_style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'index.html')) {
				define('ISMOBILE', 0);
				define('IS_HTML', 0);
				pc_base::load_sys_class('service')->display('content','maps');
			} else {
				pc_base::load_app_func('global','mobile');
				define('ISMOBILE', 1);
				define('IS_HTML', 0);
				pc_base::load_sys_class('service')->display('mobile','maps');
			}
		}else{
			define('ISMOBILE', 0);
			define('IS_HTML', 0);
			pc_base::load_sys_class('service')->display('content','maps');
		}
	}
	
	//test
	function test() {
		dr_jsonp(1, '服务器支持伪静态功能，可以自定义URL规则和解析规则了');
	}
	
	//JSON 输出
	public function json_list() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		if($this->input->get('type')=='keyword' && $this->input->get('modelid') && $this->input->get('keywords')) {
		//根据关键字搜索
			$modelid = intval($this->input->get('modelid'));
			$id = intval($this->input->get('id'));

			$MODEL = getcache('model','commons');
			if(isset($MODEL[$modelid])) {
				$keywords = safe_replace(new_html_special_chars($this->input->get('keywords')));
				$keywords = $this->db->escape($keywords);
				$this->db->set_model($modelid);
				$result = $this->db->select("keywords LIKE '%$keywords%'",'id,title,url',10);
				if(!empty($result)) {
					$data = array();
					foreach($result as $rs) {
						if($rs['id']==$id) continue;
						$data[] = $rs;
					}
					if(dr_count($data)==0) exit('0');
					echo json_encode($data);
				} else {
					//没有数据
					exit('0');
				}
			}
		}

	}
	
	/**
	 * 检查支付状态
	 */
	protected function _check_payment($flag,$paytype) {
		$_userid = $this->_userid;
		$_username = $this->_username;
		if(!$_userid) return false;
		pc_base::load_app_class('spend','pay',0);
		$setting = $this->category_setting;
		$repeatchargedays = intval($setting['repeatchargedays']);
		if($repeatchargedays) {
			$fromtime = SYS_TIME - 86400 * $repeatchargedays;
			$r = spend::spend_time($_userid,$fromtime,$flag);
			if($r['id']) return true;
		}
		return false;
	}
	
	/**
	 * 检查阅读权限
	 *
	 */
	protected function _category_priv($catid) {
		$catid = intval($catid);
		if(!$catid) return '-2';
		$_groupid = $this->_groupid;
		$_groupid = intval($_groupid);
		if($_groupid==0) $_groupid = 8;
		$this->category_priv_db = pc_base::load_model('category_priv_model');
		$result = $this->category_priv_db->select(array('catid'=>$catid,'is_admin'=>0,'action'=>'visit'));
		if($result) {
			if(!$_groupid) return '-1';
			foreach($result as $r) {
				if($r['roleid'] == $_groupid) return '1';
			}
			return '-2';
		} else {
			return '1';
		}
	}
	
	private function _getCategoryId($catdir){
		if(!strpos($catdir,'/')){
			$dirname = $catdir;
		}else{
			$dirname = end(explode('/',$catdir));
		}
		$this->category_db = pc_base::load_model('category_model');
		$result = $this->category_db->select(array('catdir'=>$dirname, 'siteid'=>get_siteid()));
		foreach($result as $r){
			if ($r['parentid']) {
				$cat_dir[$r['parentdir'].$r['catdir']] = $r['catid'];
			}
			$cat_dir[$r['catdir']] = $r['catid'];
		}
		$catid = isset($cat_dir[$dirname]) ? $cat_dir[$dirname] : 0;
		return $catid;
	}
	
	/**
	 * 获取父栏目路径
	 * @param $catid
	 * @param $dir
	 */
	private function get_categorydir($catid, $dir = '') {
		$setting = array();
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$categorys = getcache('cache', 'module/category-'.$siteid.'-data');
		$setting = dr_string2array(dr_cat_value($catid, 'setting'));
		if ($setting['create_to_html_root']) return $dir;
		if ($categorys[$catid]['parentid']) {
			$dir = $categorys[$categorys[$catid]['parentid']]['catdir'].'/'.$dir;
			return $this->get_categorydir($categorys[$catid]['parentid'], $dir);
		} else {
			return $dir;
		}
	}
}
?>