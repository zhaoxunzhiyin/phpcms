<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util');
class index {
	private $input,$cache,$db,$_userid,$_username,$_groupid,$module,$category,$categorys,$category_priv_db,$category_setting,$sitemodel,$form_cache,$page_db,$url,$category_db,$hits_db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('content_model');
		$this->_userid = param::get_cookie('_userid');
		$this->_username = param::get_cookie('_username');
		$this->_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
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
		//SEO
		$SEO = seo($siteid);
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';
		$CATEGORYS = get_category($siteid);
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'_userid' => $this->_userid,
			'_username' => $this->_username,
			'_groupid' => $this->_groupid,
			'CATEGORYS' => $CATEGORYS,
		]);
		pc_base::load_sys_class('hooks')::trigger('module_index');
		if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(TPLPATH.$default_style.'mobile'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'index.html')) {
				define('IS_HTML', dr_site_info('ishtml', $siteid));
			} else {
				if(dr_site_info('ishtml', $siteid) && dr_site_info('mobilehtml', $siteid)) {
					define('IS_HTML', 1);
				} else {
					define('IS_HTML', 0);
				}
			}
		}else{
			define('IS_HTML', dr_site_info('ishtml', $siteid));
		}
		pc_base::load_sys_class('service')->display('content','index',$default_style);
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
			$catid = get_catid($this->input->get('catdir') ? $this->input->get('catdir') : $this->input->get('categorydir'));
		}
		if(!$catid) showmessage(L('category_not_exists'),'blank');
		$_priv_data = $this->_category_priv($catid);
		if($_priv_data=='-1') {
			$forward = urlencode(dr_now_url());
			showmessage(L('login_website'),APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
		} elseif($_priv_data=='-2') {
			showmessage(L('no_priv'));
		}

		$category = $CAT = dr_cat_value($catid);
		if (!$category) {
			param::goto_404_page(L('栏目（'.$catid.'）不存在'));
		}
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		define('SITEID', $siteid);
		$CATEGORYS = get_category($siteid);
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
		
		if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(TPLPATH.$style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.$template.'.html')) {
				define('IS_HTML', $setting['ishtml']);
			} else {
				if($setting['ishtml'] && dr_site_info('mobilehtml', $siteid)) {
					define('IS_HTML', 1);
				} else {
					define('IS_HTML', 0);
				}
			}
		}else{
			define('IS_HTML', $setting['ishtml']);
		}
		
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

		$model_arr = getcache('model', 'commons');
		$sitemodel = $this->cache->get('sitemodel');
		$form_cache = $sitemodel[$model_arr[$CAT['modelid']]['tablename']];
		$this->module['setting'] = $form_cache['setting'];

		// 跳转到搜索页面
		if (isset($this->module['setting']['search']['catsync'])
			&& $this->module['setting']['search']['catsync']
			&& $CAT['type'] == 0) {
			return $this->search($catid);
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
			'_userid' => $this->_userid,
			'_username' => $this->_username,
			'_groupid' => $this->_groupid,
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
			if ($setting['ishtml'] && !$CAT['sethtml']) {
				$urlrules = SYS_HTML_ROOT.'/'.str_replace('~', '~'.SYS_HTML_ROOT.'/',$urlrules);
			}
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
			$GLOBALS['URL_ARRAY']['parentdir'] = $this->get_parentdir($catid);
			$GLOBALS['URL_ARRAY']['catdir'] = $catdir;
			$GLOBALS['URL_ARRAY']['catid'] = $catid;
			pc_base::load_sys_class('service')->assign(array(
				'SEO' => $SEO,
				'top_parentid' => $top_parentid,
			));
			pc_base::load_sys_class('service')->display('content',$template);
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
			pc_base::load_sys_class('service')->display('content',$template);
		}
	}
	//内容页
	public function show() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$catid = intval($this->input->get('catid'));
		if(!$catid){
			$catid = get_catid($this->input->get('catdir') ? $this->input->get('catdir') : $this->input->get('categorydir'));
		}
		$id = intval($this->input->get('id'));
		if(!$id){
			$id = $this->_getprefixid($catid, $this->input->get('prefix'));
		}

		if(!$catid || !$id) showmessage(L('information_does_not_exist'),'blank');

		$page = intval($this->input->get('page'));
		$page = max($page,1);
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		define('SITEID', $siteid);
		$CATEGORYS = get_category($siteid);
		
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
			if (!$r) param::goto_404_page(L($MODEL[$modelid]['name'].'内容(#'.$id.')不存在'));
			if ($r['status'] != 99) param::goto_404_page(L('info_does_not_exists'));
			
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
		$rt3 = pc_base::load_sys_class('hooks')::trigger_callback('module_show_data', array_merge($rs,$data));
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
			if(!$this->_groupid) {
				$forward = urlencode(dr_now_url());
				showmessage(L('login_website'),APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
			}
			if(!in_array($this->_groupid,$groupids_view)) showmessage(L('no_priv'));
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
			'_userid' => $this->_userid,
			'_username' => $this->_username,
			'_groupid' => $this->_groupid,
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
			if (!file_exists(TPLPATH.$style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.$template.'.html')) {
				define('IS_HTML', $CAT['setting']['content_ishtml']);
			} else {
				if($CAT['setting']['content_ishtml'] && dr_site_info('mobilehtml', $siteid)) {
					define('IS_HTML', 1);
				} else {
					define('IS_HTML', 0);
				}
			}
		}else{
			define('IS_HTML', $CAT['setting']['content_ishtml']);
		}
		pc_base::load_sys_class('service')->display('content',$template);
	}
	/**
	 * 模型搜索
	 */
	public function search($catid) {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$params = $this->input->get();
		// 挂钩点 搜索之前对参数处理
		pc_base::load_sys_class('hooks')::trigger('search_param', $params);
		$grouplist = getcache('grouplist','member');
		if(!$grouplist[$this->_groupid]['allowsearch']) {
			showmessage(($_groupid==8 ? L('guest_not_allowsearch') : L('group_not_allowsearch')));
		}

		if(!$catid) showmessage(L('missing_part_parameters'));
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->categorys = get_category($siteid);
		if(!$this->categorys[$catid]) showmessage(L('missing_part_parameters'));
		$info = $this->input->get('info');
		!$info && $info = array();
		// 栏目格式化
		$cat = $catid ? dr_cat_value($catid) : [];
		$top = $cat;
		if ($catid && $cat['topid']) {
			$top = dr_cat_value($cat['topid']);
		}

		// 获取同级栏目及父级栏目
		list($parent, $related) = dr_related_cat($cat);
		$modelid = $this->categorys[$catid]['modelid'];
		$modelid = intval($modelid);
		if(!$modelid) showmessage(L('illegal_parameters'));
		$model_arr = getcache('model', 'commons');
		$sitemodel = $this->cache->get('sitemodel');
		$form_cache = $sitemodel[$model_arr[$modelid]['tablename']];
		$this->module['setting'] = $form_cache['setting'];
		if(isset($this->module['setting']['search']['search_catid']) && $this->module['setting']['search']['search_catid'] && !isset($info['catid'])) {
			$info['catid'] = $catid;
		}
		$CATEGORYS = $this->categorys;
		//产生表单
		pc_base::load_sys_class('form');
		$fields = getcache('model_field_'.$modelid,'model');
		$forminfos = array();
		foreach ($fields as $field=>$r) {
			if($r['issystem'] && $r['issearch']) {
				if($r['formtype']=='catid') {
					if(isset($this->module['setting']['search']['search_catid']) && $this->module['setting']['search']['search_catid']) {
						$r['form'] = form::select_category('',(isset($info['catid']) ? $info['catid'] : $catid),'name="info[catid]" class="form-control form-control-sm"',L('please_select_category'),$modelid,0);
					} else {
						$r['form'] = form::select_category('',(isset($info['catid']) ? $info['catid'] : $catid),'name="info[catid]" class="form-control form-control-sm"',L('please_select_category'),$modelid,0,1);
					}
				} elseif($r['formtype']=='number') {
					$r['form'] = "<input type='text' name='{$field}_start' id='{$field}_start' value='".$this->input->get($field.'_start')."' class='input-text form-control form-control-sm'/> - <input type='text' name='{$field}_end' id='{$field}_end' value='".$this->input->get($field.'_end')."' class='input-text form-control form-control-sm'/>";
				} elseif($r['formtype']=='datetime') {
					$r['form'] = form::date("info[$field]",$info[$field]);
				} elseif($r['formtype']=='box') {
					if(isset($info[$field]) && $info[$field]) {
						$value[$field] = $info[$field];
					}
					$options = explode("\n",$r['options']);
					$option = array();
					foreach($options as $_k) {
						$v = explode("|",trim($_k));
						$option[$v[1]] = $v[0];
					}
					switch($r['boxtype']) {
						case 'radio':
							$string = form::radio($option,$value[$field],"name='info[$field]' id='$field'");
						break;

						case 'checkbox':
							$string = form::radio($option,$value[$field],"name='info[$field]' id='$field'");
						break;

						case 'select':
							$string = form::select($option,$value[$field],"name='info[$field]' id='$field' class='form-control form-control-sm'");
						break;

						case 'multiple':
							$string = form::select($option,$value[$field],"name='info[$field]' id='$field' class='form-control form-control-sm'");
						break;
					}
					$r['form'] = $string;
				} elseif($r['formtype']=='typeid') {
					$types = getcache('type_content','commons');
					$types_array = array(L('no_limit'));
					foreach ($types as $_k=>$_v) {
						if($modelid == $_v['modelid']) $types_array[$_k] = $_v['name'];
					}
					$r['form'] = form::select($types_array,0,"name='info[$field]' id='$field' class='form-control form-control-sm'");
				} elseif($r['formtype']=='linkage') {
					$setting = string2array($r['setting']);
					if(isset($info[$field]) && $info[$field]) {
						$value[$field] = $info[$field];
					}
					$r['form'] = menu_linkage($setting['linkage'],$field,$value[$field]);
				} elseif($r['formtype']=='linkages') {
					$setting = string2array($r['setting']);
					if(isset($info[$field]) && $info[$field]) {
						$value[$field] = $info[$field];
					}
					$r['form'] = menu_linkage($setting['linkage'],$field,$value[$field],0,1);
				} elseif(in_array($r['formtype'], array('text','keyword','textarea','editor','title','author','omnipotent','linkfield'))) {
					if(isset($info[$field]) && $info[$field]) {
						$value[$field] = safe_replace($info[$field]);
					}
					$r['form'] = "<input type='text' name='info[$field]' id='$field' value='".$value[$field]."' class='input-text search-text form-control form-control-sm w-250px ps-10'/>";
				} else {
					continue;
				}
				$forminfos[$field] = $r;
			}
		}
		list($a) = explode(' ', (string)$params['order']);
		if (!$params['order'] || ($a && !dr_in_array($a, array('id', 'inputtime', 'updatetime', 'listorder')))) {
			$params['order'] = 'id DESC';
		}
		$siteid = $this->categorys[$catid]['siteid'];
		$this->db->set_model($modelid);
			
		$page = max(intval($this->input->get('page')), 1);
		//构造搜索SQL
		$where = 'status=99 AND catid in ('.($cat['arrchildid'] ? $cat['arrchildid'] : $catid).')';
		$pagesize = 10;
		// 获取搜索总量
		$total = $this->db->count($where);
		if($total) {
			$datas = $this->db->listinfo($where, $params['order'], $page, $pagesize, 'id');
			foreach ($datas as $k=>$v) {
				if (isset($v['id']) && !empty($v['id'])) {
					$this->db->table_name = $this->db->table_name.'_data_'.$v['tableid'];
					$data_rs = $this->db->get_one(array('id'=>$v['id']));
					if (isset($data_rs)) $datas[$k] = array_merge($datas[$k], $data_rs);
					$this->db->set_model($modelid);
				} else {
					continue;
				}
			}
			$pages = pages($total, $page, $pagesize);
		} else {
			$datas = array();
			$pages = '';
		}
		//开启后遇到搜索内容为空时直接跳转404页面
		if (!$total && isset($this->module['setting']['search']['search_404']) && $this->module['setting']['search']['search_404']) {
			param::goto_404_page('内容匹配结果为空');
		}

		// 挂钩点 搜索完成之后
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('module_search_data', $datas);
		if ($rt2 && isset($rt2['code']) && $rt2['code']) {
			$datas = $rt2['data'];
		}
		define('SITEID', $siteid);
		$SEO = seo($siteid, $catid, $keywords);
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'catid' => $catid,
			'params' => $params,
			'forminfos' => $forminfos,
			'CATEGORYS' => $CATEGORYS,
			'cat' => $cat,
			'top' => $top,
			'parent' => $parent,
			'related' => $related,
			'total' => $total,
			'datas' => $datas,
			'pages' => $pages,
		]);
		pc_base::load_sys_class('service')->display('content','search',$default_style);
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
		$SEO = seo($siteid);
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'_userid' => $this->_userid,
			'_username' => $this->_username,
			'_groupid' => $this->_groupid,
		]);
		pc_base::load_sys_class('service')->display('content','maps');
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
				$result = $this->db->select(array('keywords'=>'%'.$keywords.'%', 'id<>'=>$id),'id,title,url',10);
				if(!empty($result)) {
					$data = array();
					foreach($result as $rs) {
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
		if(!$this->_userid) return false;
		pc_base::load_app_class('spend','pay',0);
		$setting = $this->category_setting;
		$repeatchargedays = intval($setting['repeatchargedays']);
		if($repeatchargedays) {
			$fromtime = SYS_TIME - 86400 * $repeatchargedays;
			$r = spend::spend_time($this->_userid,$fromtime,$flag);
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
		$this->category_priv_db = pc_base::load_model('category_priv_model');
		$result = $this->category_priv_db->select(array('catid'=>$catid,'is_admin'=>0,'action'=>'visit'));
		if($result) {
			if(!$this->_groupid) return '-1';
			foreach($result as $r) {
				if($r['roleid'] == $this->_groupid) return '1';
			}
			return '-2';
		} else {
			return '1';
		}
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
		$categorys = get_category($siteid);
		$setting = dr_string2array(dr_cat_value($catid, 'setting'));
		if ($setting['create_to_html_root']) return $dir;
		if ($categorys[$catid]['parentid']) {
			$dir = $categorys[$categorys[$catid]['parentid']]['catdir'].'/'.$dir;
			return $this->get_categorydir($categorys[$catid]['parentid'], $dir);
		} else {
			return $dir;
		}
	}
	
	/**
	 * 获取包含父级子级层次的目录
	 * @param $catid
	 */
	private function get_parentdir($catid) {
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$categorys = get_category($siteid);
		return $categorys[$catid]['parentdir'].= $categorys[$catid]['catdir'];
	}

	private function _getprefixid($catid, $prefix){
		if(!dr_cat_value($catid) || dr_cat_value($catid, 'type')!=0) showmessage(L('information_does_not_exist'),'blank');
		$this->db->set_model(dr_cat_value($catid, 'modelid'));
		if($this->db->field_exists('prefix')){
			$result = $this->db->get_one(array('prefix'=>$prefix));
		}
		if(empty($result)){
			$result = $this->db->get_one(array('id'=>$prefix));
		}
		return $result['id'];
	}
}
?>