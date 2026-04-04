<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_func('util','content');
class html {
	private $input,$cache,$siteid,$url,$html_root,$mobile_root,$queue,$categorys,$db,$sitemodel,$form_cache,$page_db,$hits_db;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->queue = pc_base::load_model('queue_model');
		define('HTML',true);
		$this->siteid = get_siteid();
		$this->db = pc_base::load_model('content_model');
		$this->categorys = get_category($this->siteid);
		$this->url = pc_base::load_app_class('url', 'content');
		$this->html_root = SYS_HTML_ROOT;
		$this->mobile_root = SYS_MOBILE_ROOT;
	}

	/**
	 * 生成内容页
	 * @param $file 文件地址
	 * @param $data 数据
	 * @param $array_merge 是否合并
	 * @param $action 方法
	 * @param $upgrade 是否是升级数据
	 */
	public function show($file, $data = '', $array_merge = 1,$action = 'add',$upgrade = 0) {
		// 挂钩点
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('module_show', $data);
		if ($rt2 && isset($rt2['code']) && $rt2['code']) {
			$data = $rt2['data'];
		}
		if (strpos((string)$data['url'], 'index.php?')!==false) return false;
		if($upgrade) $file = '/'.ltrim($file,WEB_PATH);
		$allow_visitor = 1;
		$id = $data['id'];
		if($array_merge) {
			$data = new_stripslashes($data);
			$data = array_merge($data['system'], $data['model']);
		}
		//通过rs获取原始值
		$rs = $data;
		if(isset($data['paginationtype'])) {
			$paginationtype = $data['paginationtype'];
			$maxcharperpage = $data['maxcharperpage'];
		} else {
			$paginationtype = 0;
		}
		$catid = $data['catid'];
		$isdomain = 0;
		$category_url = pc_base::load_model('category_model')->get_one(array('catid'=>$catid));
		if(!preg_match('/^(http|https):\/\//', (string)$category_url['url'])) {
		} elseif ($setting['ishtml']) {
			$isdomain = 1;
		}
		$CATEGORYS = $this->categorys;
		$category = $CAT = dr_cat_value($catid);
		$CAT['setting'] = dr_string2array($CAT['setting']);
		define('STYLE',$CAT['setting']['template_list']);
		define('SITEID', $this->siteid);
		define('ISHTML', $CAT['setting']['content_ishtml']);
		define('IS_HTML', $CAT['setting']['content_ishtml']);
		if(!$CAT['setting']['content_ishtml']) return false;

		//最顶级栏目ID
		$arrparentid = explode(',', $CAT['arrparentid']);
		$top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
		
		//$file = '/'.$file;
		//添加到发布点队列
		//当站点为非系统站点
		
		if($this->siteid!=1) {
			$site_dir = dr_site_info('dirname', $this->siteid);
			$queue_file = $this->html_root.'/'.$site_dir.$file;
			$mobile_file = $site_dir.$this->mobile_root.$file;
			$file = $site_dir.$file;
		} else {
			$queue_file = $file;
			$mobile_file = substr($this->mobile_root,1).$file;
		}
		
		$this->queue->add_queue($action,$queue_file,$this->siteid);
		
		$MODEL = getcache('model','commons');
		$modelid = $CAT['modelid'];
		$this->sitemodel = $this->cache->get('sitemodel');
		$this->form_cache = $this->sitemodel[$MODEL[$modelid]['tablename']];
		$name = 'module_'.$modelid.'_show_html_id_'.$id;
		$output_data = $this->cache->get_data($name);
		if (!$output_data) {
			require_once CACHE_MODEL_PATH.'content_output.class.php';
			$content_output = new content_output($modelid,$catid,$CATEGORYS);
			$output_data = $content_output->get($data);
			
			// 缓存结果 
			if (SYS_CACHE) {
				$this->cache->set_data($name, $output_data, SYS_CACHE_SHOW * 3600);
			}
		}
		// 挂钩点 内容读取之后
		$rt3 = pc_base::load_sys_class('hooks')::trigger_callback('module_show_data', array_merge($data,$output_data));
		if ($rt3 && isset($rt3['code']) && $rt3['code']) {
			$output_data = $rt3['data'];
		}
		// 检测转向字段
		foreach ($this->form_cache['field'] as $t) {
			if ($t['formtype'] == 'redirect' && $output_data[$t['field']]) {
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
				ob_start();
				pc_base::load_sys_class('service')->assign('gotu_url', $output_data[$t['field']]);
				pc_base::load_sys_class('service')->admin_display('go', 'admin');
				$this->createhtml(CMS_PATH.$file);
				if(dr_site_info('mobilehtml', $this->siteid)==1) {
					ob_start();
					pc_base::load_sys_class('service')->assign('gotu_url', $output_data[$t['field']]);
					pc_base::load_sys_class('service')->admin_display('go', 'admin');
					$this->createhtml(CMS_PATH.$mobile_file);
				}
				return $output_data;
			}
		}
		$output_data['cat'] = $CAT;
		if ($output_data['keywords']) {
			foreach ($output_data['keywords'] as $t) {
				$t = trim($t);
				if ($t) {
					$output_data['kws'][$t] = tag_url($t, $siteid, $catid);
				}
			}
		}
		$output_data['_inputtime'] = $rs['inputtime'];
		$output_data['_updatetime'] = $rs['updatetime'];
		extract($output_data);
		if(module_exists('comment')) {
			$allow_comment = isset($allow_comment) ? $allow_comment : 1;
		} else {
			$allow_comment = 0;
		}
		$this->db->set_model($modelid);
		//上一页
		$previous_page = $this->db->get_one(array('catid' => (int)$catid, 'id<' => (int)$id, 'status' => 99), '*', 'id DESC');
		if(isset($this->form_cache['setting']['previous']) && $this->form_cache['setting']['previous'] && !$previous_page) {
			$previous_page = $this->db->get_one(array('catid' => (int)$catid, 'status' => 99), '*', 'id DESC');
		}
		//下一页
		$next_page = $this->db->get_one(array('catid' => (int)$catid, 'id>' => (int)$id, 'status' => 99), '*', 'id ASC');
		if(isset($this->form_cache['setting']['previous']) && $this->form_cache['setting']['previous'] && !$next_page) {
			$next_page = $this->db->get_one(array('catid' => (int)$catid, 'status' => 99), '*', 'id ASC');
		}
		
		// 获取同级栏目及父级栏目
		list($parent, $related) = dr_related_cat($category);
		$top = $category;
		if ($catid && $category['topid']) {
			$top = dr_cat_value($category['topid']);
		}

		$title = clearhtml($title);
		//SEO
		$seo_keywords = '';
		if(!empty($keywords)) $seo_keywords = implode(',',$keywords);
		$siteid = $this->siteid;
		$SEO = seo($siteid, $catid, $title, $description, $seo_keywords);
		
		$ishtml = 1;
		$template = $template ? $template : $CAT['setting']['show_template'];
		
		//分页处理
		$pages = '';
		$titles = array();
		if($paginationtype==1) {
			//自动分页
			if($maxcharperpage < 10) $maxcharperpage = 500;
			$contentpage = pc_base::load_app_class('contentpage');
			$content = $contentpage->get_data($content,$maxcharperpage);
		}

		if($paginationtype!=0) {
			//手动分页
			$CONTENT_POS = strpos((string)$content, '[page]');
			if($CONTENT_POS !== false) {
				$contents = array_filter(explode('[page]', $content));
				$pagenumber = dr_count($contents);
				if (strpos((string)$content, '[/page]')!==false && ($CONTENT_POS<7)) {
					$pagenumber--;
				}
				for($i=1; $i<=$pagenumber; $i++) {
					$upgrade = $upgrade ? '/'.ltrim($file,WEB_PATH) : '';
					list($pageurls[$i], $showurls[$i]) = $this->url->show($id, $i, $catid, $data['inputtime'], $data['prefix'], '', 'edit', $upgrade);
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
				//生成分页
				foreach ($pageurls as $page=>$urls) {
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
						if(strpos((string)$content,'</p>')===0) {
							$content = '<p>'.$content;
						}
						if(stripos((string)$content,'<p>')===0) {
							$content = $content.'</p>';
						}
					}
					$pagefile = $urls[1];
					if ($this->siteid!=1) {
						$queue_pagefile = $this->html_root.'/'.$site_dir.$pagefile;
						$pagefile = $site_dir.$pagefile;
					} else {
						$queue_pagefile = $pagefile;
					}
					$this->queue->add_queue($action,$queue_pagefile,$this->siteid);
					// 传入模板
					pc_base::load_sys_class('service')->assign($output_data);
					pc_base::load_sys_class('service')->assign(array(
						'SEO' => $SEO,
						'siteid' => $siteid,
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
						'allow_visitor' => $allow_visitor,
						'previous_page' => $previous_page,
						'next_page' => $next_page,
						'allow_comment' => $allow_comment,
						'title' => clearhtml($title) ? $title : $data['title'],
						'content' => $content,
						'titles' => $titles,
						'pages' => $pages,
						'fix_html_now_url' => siteurl($this->siteid).$urls[1],
					));
					ob_start();
					pc_base::load_sys_class('service')->init('pc');
					pc_base::load_sys_class('service')->display('content', $template);
					$this->createhtml(CMS_PATH.$pagefile);
				}
				//生成手机分页
				if(dr_site_info('mobilehtml', $this->siteid)==1) {
					for($i=1; $i<=$pagenumber; $i++) {
						$upgrade = $upgrade ? '/'.ltrim($file,WEB_PATH) : '';
						list($pageurls[$i], $showurls[$i]) = $this->url->show($id, $i, $catid, $data['inputtime'], $data['prefix'], '', 'edit', $upgrade, 1);
					}
					$titles = array();
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
					foreach ($pageurls as $page=>$urls) {
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
							$content = trim($content);
							if(strpos((string)$content,'</p>')===0) {
								$content = '<p>'.$content;
							}
							if(stripos((string)$content,'<p>')===0) {
								$content = $content.'</p>';
							}
						}
						$mobile_pagefile = $urls[1];
						if($this->siteid!=1) {
							$mobile_pagefile = '/'.$site_dir.$mobile_pagefile;
						}
						// 传入模板
						pc_base::load_sys_class('service')->assign($output_data);
						pc_base::load_sys_class('service')->assign(array(
							'SEO' => $SEO,
							'siteid' => $siteid,
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
							'allow_visitor' => $allow_visitor,
							'previous_page' => $previous_page,
							'next_page' => $next_page,
							'allow_comment' => $allow_comment,
							'title' => clearhtml($title) ? $title : $data['title'],
							'content' => $content,
							'titles' => $titles,
							'pages' => $pages,
							'fix_html_now_url' => siteurl($this->siteid).$urls[1],
						));
						ob_start();
						pc_base::load_sys_class('service')->init('mobile');
						pc_base::load_sys_class('service')->display('content', $template);
						$this->createhtml(CMS_PATH.$mobile_pagefile);
					}
				}
				return true;
			}
		}
		//分页处理结束
		// 传入模板
		pc_base::load_sys_class('service')->assign($output_data);
		pc_base::load_sys_class('service')->assign(array(
			'SEO' => $SEO,
			'siteid' => $siteid,
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
			'allow_visitor' => $allow_visitor,
			'previous_page' => $previous_page,
			'next_page' => $next_page,
			'allow_comment' => $allow_comment,
			'title' => clearhtml($title) ? $title : $data['title'],
			'content' => $content,
			'titles' => $titles,
			'pages' => $pages,
			'fix_html_now_url' => preg_match('/^((http|https):\/\/)([a-z0-9\-\.]+)\/$/', (string)$url) ? $url : siteurl($this->siteid).$url,
		));
		ob_start();
		pc_base::load_sys_class('service')->init('pc');
		pc_base::load_sys_class('service')->display('content', $template);
		$this->createhtml(CMS_PATH.$file);
		if(dr_site_info('mobilehtml', $this->siteid)==1) {
			// 传入模板
			pc_base::load_sys_class('service')->assign($output_data);
			pc_base::load_sys_class('service')->assign(array(
				'SEO' => $SEO,
				'siteid' => $siteid,
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
				'allow_visitor' => $allow_visitor,
				'previous_page' => $previous_page,
				'next_page' => $next_page,
				'allow_comment' => $allow_comment,
				'title' => clearhtml($title) ? $title : $data['title'],
				'content' => $content,
				'titles' => $titles,
				'pages' => $pages,
				'fix_html_now_url' => preg_match('/^((http|https):\/\/)([a-z0-9\-\.]+)\/$/', (string)$url) ? $url : siteurl($this->siteid, 1).$url,
			));
			ob_start();
			pc_base::load_sys_class('service')->init('mobile');
			pc_base::load_sys_class('service')->display('content', $template);
			$this->createhtml(CMS_PATH.$mobile_file);
		}
		return true;
	}

	/**
	 * 生成栏目列表
	 * @param $catid 栏目id
	 * @param $page 当前页数
	 */
	public function category($catid, $page = 0, $maxsize = 0) {
		pc_base::load_sys_class('cache')->set_auth_data('maxsize'.USER_HTTP_CODE.(param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')), $maxsize);
		// 挂钩点
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('module_category');
		if ($rt2 && isset($rt2['code']) && $rt2['code']) {
			$catid = $rt2['data'];
		}
		if(!$catid){
			return false;
		}
		$category = $CAT = dr_cat_value($catid);
		if (!$category) {
			return false;
		}
		// 挂钩点 格式化栏目数据
		$rt3 = pc_base::load_sys_class('hooks')::trigger_callback('module_category_data', $category);
		if ($rt3 && isset($rt3['code']) && $rt3['code']) {
			$category = $CAT = $rt3['data'];
		}
		if (strpos((string)$CAT['url'], 'index.php?')!==false) return false;
		if (is_array($CAT)) {
			@extract($CAT);
		}
		$setting = dr_string2array($setting);
		if(!$setting['ishtml']){
			return false;
		}
		$CATEGORYS = $this->categorys;
		if(!isset($CATEGORYS[$catid])){
			return false;
		}
		$siteid = $CAT['siteid'];
		$copyjs = '';
		if(!$setting['meta_title']) $setting['meta_title'] = $catname;
		$SEO = seo($siteid, '',$setting['meta_title'],$setting['meta_description'],$setting['meta_keywords']);
		define('STYLE',$setting['template_list']);
		define('SITEID', $siteid);
		define('ISHTML', $setting['ishtml']);
		define('IS_HTML', $setting['ishtml']);

		$page = intval($page);
		$catdir = $CAT['catdir'];
		//检查是否生成到根目录
		$create_to_html_root = $CAT['sethtml'];
		$isdomain = 0;
		$category_url = pc_base::load_model('category_model')->get_one(array('catid'=>$catid));
		if(!preg_match('/^(http|https):\/\//', (string)$category_url['url'])) {
		} elseif ($setting['ishtml']) {
			$isdomain = 1;
		}
		
		//获取父级的配置，看是否生成静态，如果是动态则直接把父级目录调过来为生成静态目录所用
		$parent_setting = dr_string2array(dr_cat_value($CAT['parentid'], 'setting'));
		
		$base_file = $this->url->get_list_url($setting['category_ruleid'], $this->get_categorydir($catid), $this->get_parentdir($catid), $catdir, $catid, $page);
		$base_file = '/'.$base_file;
		
		//非系统站点时，生成到指定目录
		if($this->siteid!=1) {
			$site_dir = dr_site_info('dirname', $this->siteid);
			if($create_to_html_root) {
				$queue_file = $site_dir.$base_file;
			} else {
				$queue_file = $site_dir.$this->html_root.$base_file;
			}
		} else {
			$queue_file = $base_file;
		}
		//判断二级域名是否直接绑定到该栏目
		$root_domain = preg_match('/^((http|https):\/\/)([a-z0-9\-\.]+)\/$/', (string)$CAT['url']) ? 1 : 0;
		$count_number = substr_count((string)$CAT['url'], '/');
		$urlrules = getcache('urlrules','commons');
		$urlrules = explode('|',$urlrules[$setting['category_ruleid']]);
		
		if($create_to_html_root) {
			$file = $base_file;
			$mobile_file = substr($this->mobile_root,1).$base_file;
			//添加到发布点队列
			$this->queue->add_queue('add','/'.$queue_file,$this->siteid);
			//评论跨站调用所需的JS文件
			if(substr($queue_file, -10)=='index.html' && $count_number==3) {
				$copyjs = 1;
				$this->queue->add_queue('add','/'.$queue_file,$this->siteid);
			}
			//URLRULES
			if($isdomain) {
				foreach ($urlrules as $_k=>$_v) {
					$urlrules[$_k] = $_v;
				}
			} else {
				foreach ($urlrules as $_k=>$_v) {
					$urlrules[$_k] = '/'.$_v;
				}
			}
		} else {
			$file = substr($this->html_root,1).$base_file;
			$mobile_file = substr($this->mobile_root,1).$this->html_root.$base_file;
			//添加到发布点队列
			$this->queue->add_queue('add',$this->html_root.'/'.$queue_file,$this->siteid);
			//评论跨站调用所需的JS文件
			if(substr($queue_file, -10)=='index.html' && $count_number==3) {
				$copyjs = 1;
				$this->queue->add_queue('add',$this->html_root.'/'.$queue_file,$this->siteid);
			}
			//URLRULES
			$htm_prefix = $root_domain ? '' : $this->html_root;
			$htm_prefix = rtrim(WEB_PATH,'/').$htm_prefix;
			if($isdomain) {
			} else {
				foreach ($urlrules as $_k=>$_v) {
					$urlrules[$_k] = $htm_prefix.'/'.$_v;
				}
			}
		}
		//非系统站点时，生成到指定目录
		if($this->siteid!=1) {
			$site_dir = dr_site_info('dirname', $this->siteid);
			$file = $site_dir.'/'.$file;
			$mobile_file = $site_dir.'/'.$mobile_file;
		}
		
		// 获取同级栏目及父级栏目
		list($parent, $related) = dr_related_cat($category);
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

		if($type==0) {
			$template = $setting['category_template'] ? $setting['category_template'] : 'category';
			$template_list = $setting['list_template'] ? $setting['list_template'] : 'list';
			$template = $child ? $template : $template_list;
			$arrparentid = explode(',', $arrparentid);
			$top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
			$array_child = array();
			$self_array = explode(',', $arrchildid);
			foreach ($self_array as $arr) {
				if($arr!=$catid) $array_child[] = $arr;
			}
			$arrchildid = implode(',', $array_child);
			$year = date('Y',$time);
			$month = date('m',$time);
			$day = date('d',$time);
			//URL规则
			$urlrule = implode('~', str_replace(array('{$categorydir}','{$parentdir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$page}'),array($this->get_categorydir($catid),$this->get_parentdir($catid),$catdir,$year,$month,$day,$catid,'{page}'),$urlrules));
			
			//绑定域名时，设置$catdir 为空
			if($isdomain) $catdir = '';
			
			$categoryurl = str_replace(array('{$categorydir}','{$parentdir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$page}'),array($this->get_categorydir($catid),$this->get_parentdir($catid),$catdir,$year,$month,$day,$catid,'{page}'),$urlrules);
			
			pc_base::load_sys_class('service')->assign(array(
				'fix_html_now_url' => $page == 1 ? ($isdomain ? substr((string)$CAT['url'], 0, -1).$categoryurl[0] : siteurl($this->siteid).$categoryurl[0]) : str_replace('{page}', $page, ($isdomain ? substr((string)$CAT['url'], 0, -1).$categoryurl[1] : siteurl($this->siteid).$categoryurl[1])),
				'urlrule' => $urlrule,
			));
		} else {
			//单网页
			$this->page_db = pc_base::load_model('page_model');
			$r = $this->page_db->get_one(array('catid'=>$catid));
			require_once CACHE_MODEL_PATH.'content_output.class.php';
			$content_output = new content_output(-2);
			$datas = $content_output->get($r);
			$datas = $r ? array_merge($r, $datas) : $datas;
			if($datas) extract($datas);
			$template = $setting['page_template'] ? $setting['page_template'] : 'page';
			$keywords = $keywords ? $keywords : $setting['meta_keywords'];
			$SEO = seo($siteid, 0, $setting['meta_title'] ? $setting['meta_title'] : $title,$setting['meta_description'],$keywords);
			pc_base::load_sys_class('service')->assign(array(
				'fix_html_now_url' => $url,
			));
			pc_base::load_sys_class('service')->assign($datas);
		}
		// 验证是否存在子栏目，是否将下级第一个栏目作为当前页
		if ($type != 2 && $child && $setting['getchild']) {
			$temp = explode(',', $arrchildid);
			if ($temp) {
				foreach ($temp as $i) {
					$row = dr_cat_value($i);
					if ($i != $catid && $row['type'] != 2 && !$row['setting']['getchild']) {
						$catid = $i;
						ob_start();
						pc_base::load_sys_class('service')->assign('gotu_url', $row['url']);
						pc_base::load_sys_class('service')->admin_display('go', 'admin');
						$this->createhtml(CMS_PATH.$file, $copyjs);
						if(dr_site_info('mobilehtml', $this->siteid)==1) {
							ob_start();
							pc_base::load_sys_class('service')->assign('gotu_url', $row['url']);
							pc_base::load_sys_class('service')->admin_display('go', 'admin');
							$this->createhtml(CMS_PATH.$mobile_file, $copyjs);
						}
						return $row;
						break;
					}
				}
			}
		} else {
			// 传入模板
			pc_base::load_sys_class('service')->assign($category);
			pc_base::load_sys_class('service')->assign(array(
				'SEO' => $SEO,
				'siteid' => $siteid,
				'catid' => $catid,
				'cat' => $CAT,
				'CAT' => $CAT,
				'category' => $category,
				'top' => $top,
				'top_parentid' => $top_parentid,
				'CATEGORYS' => $CATEGORYS,
				'params' => ['catid' => $catid],
				'page' => max(1, $page),
				'parent' => $parent,
				'related' => $related,
				'arrchild_arr' => $arrchild_arr,
			));
			ob_start();
			pc_base::load_sys_class('service')->init('pc');
			pc_base::load_sys_class('service')->display('content',$template);
			$this->createhtml(CMS_PATH.$file, $copyjs);
			if(dr_site_info('mobilehtml', $this->siteid)==1) {
				if($type==0) {
					$mobile_category = !dr_site_info('mobilemode', $this->siteid) ? $this->mobile_root : '';
					$url_arr[0] = $mobile_category.$categoryurl[0];
					$url_arr[1] = $mobile_category.$categoryurl[1];
					$urlrule = $url_arr[0].'~'.$url_arr[1];
					pc_base::load_sys_class('service')->assign(array(
						'fix_html_now_url' => $page == 1 ? ($isdomain ? substr((string)$CAT['url'], 0, -1).$url_arr[0] : siteurl($this->siteid, 1).(dr_site_info('mobilemode', $this->siteid)==0 ? str_replace('mobile/', '', $url_arr[0]) : $url_arr[0])) : str_replace('{page}', $page, ($isdomain ? substr((string)$CAT['url'], 0, -1).$url_arr[1] : siteurl($this->siteid, 1).(dr_site_info('mobilemode', $this->siteid)==0 ? str_replace('mobile/', '', $url_arr[1]) : $url_arr[1]))),
						'urlrule' => $urlrule,
					));
				} else {
					pc_base::load_sys_class('service')->assign(array(
						'fix_html_now_url' => $url,
					));
				}
				pc_base::load_sys_class('service')->assign($datas);
				pc_base::load_sys_class('service')->assign(array(
					'SEO' => $SEO,
					'siteid' => $siteid,
					'catid' => $catid,
					'cat' => $CAT,
					'CAT' => $CAT,
					'category' => $category,
					'top' => $top,
					'top_parentid' => $top_parentid,
					'CATEGORYS' => $CATEGORYS,
					'params' => ['catid' => $catid],
					'page' => max(1, $page),
					'parent' => $parent,
					'related' => $related,
					'arrchild_arr' => $arrchild_arr,
				));
				ob_start();
				pc_base::load_sys_class('service')->init('mobile');
				pc_base::load_sys_class('service')->display('content',$template);
				$this->createhtml(CMS_PATH.$mobile_file, $copyjs);
			}
		}
		return true;
	}
	/**
	 * 更新首页
	 */
	public function index() {
		// 挂钩点 网站首页时
		pc_base::load_sys_class('hooks')::trigger('cms_index');
		if($this->siteid==1) {
			$file = 'index.html';
			$mobile_file = $this->mobile_root.'/index.html';
			$mobile_map_file = $this->mobile_root.'/map.html';
			//添加到发布点队列
			$this->queue->add_queue('edit','/index.html',$this->siteid);
		} else {
			$site_dir = dr_site_info('dirname', $this->siteid);
			$file = $site_dir.'/index.html';
			$mobile_file = $site_dir.$this->mobile_root.'/index.html';
			$mobile_map_file = $site_dir.$this->mobile_root.'/map.html';
			//添加到发布点队列
			$this->queue->add_queue('edit',$this->html_root.'/'.$site_dir.'/index.html',$this->siteid);
		}
		define('SITEID', $this->siteid);
		//SEO
		$SEO = seo($this->siteid);
		$siteid = $this->siteid;
		$CATEGORYS = $this->categorys;
		$style = dr_site_info('default_style', $this->siteid);
		define('STYLE', $style);
		define('IS_HTML', dr_site_info('ishtml', $this->siteid));
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'CATEGORYS' => $CATEGORYS,
			'fix_html_now_url' => siteurl($this->siteid),
		]);
		pc_base::load_sys_class('hooks')::trigger('module_index');
		if(dr_site_info('ishtml', $this->siteid)==1) {
			ob_start();
			pc_base::load_sys_class('service')->init('pc');
			pc_base::load_sys_class('service')->display('content','index',$style);
			$pc = $this->createhtml(CMS_PATH.$file, 1);
			if(dr_site_info('mobilehtml', $this->siteid)==1) {
				pc_base::load_sys_class('service')->assign([
					'SEO' => $SEO,
					'siteid' => $siteid,
					'CATEGORYS' => $CATEGORYS,
					'fix_html_now_url' => sitemobileurl($this->siteid).'/map.html',
				]);
				ob_start();
				pc_base::load_sys_class('service')->init('mobile');
				pc_base::load_sys_class('service')->display('content','maps',$style);
				$this->createhtml(CMS_PATH.$mobile_map_file);
				pc_base::load_sys_class('service')->assign([
					'SEO' => $SEO,
					'siteid' => $siteid,
					'CATEGORYS' => $CATEGORYS,
					'fix_html_now_url' => sitemobileurl($this->siteid),
				]);
				ob_start();
				pc_base::load_sys_class('service')->init('mobile');
				pc_base::load_sys_class('service')->display('content','index',$style);
				$mobile = $this->createhtml(CMS_PATH.$mobile_file, 1);
			}
		}
		return L('电脑端 （'.format_file_size($pc).'），移动端 （'.format_file_size($mobile).'）');
	}
	/**
	* 写入文件
	* @param $file 文件路径
	* @param $copyjs 是否复制js，跨站调用评论时，需要该js
	*/
	private function createhtml($file, $copyjs = '') {
		$data = ob_get_clean();
		$dir = dirname($file);
		if(!is_dir($dir)) {
			mkdir($dir, 0777,1);
		}
		if ($copyjs && !file_exists($dir.'/js.html')) {
			@copy(CMS_PATH.'js.html', $dir.'/js.html');
		}
		$strlen = file_put_contents($file, $data, LOCK_EX);
		@chmod($file,0777);
		if(!is_writable($file)) {
			$file = str_replace(CMS_PATH,'',$file);
			pc_base::load_sys_class('service')->show_error(L('file').'：'.$file.'<br>'.L('not_writable'));
		}
		return $strlen;
	}

	/**
	 * 获取父栏目路径
	 * @param $catid
	 * @param $dir
	 */
	private function get_categorydir($catid, $dir = '') {
		$setting = array();
		$setting = dr_string2array(dr_cat_value($catid, 'setting'));
		if ($setting['create_to_html_root']) return $dir;
		if ($this->categorys[$catid]['parentid']) {
			$dir = $this->categorys[$this->categorys[$catid]['parentid']]['catdir'].'/'.$dir;
			return $this->get_categorydir($this->categorys[$catid]['parentid'], $dir);
		} else {
			return $dir;
		}
	}
	/**
	 * 获取包含父级子级层次的目录
	 * @param $catid
	 */
	private function get_parentdir($catid) {
		return $this->categorys[$catid]['parentdir'].= $this->categorys[$catid]['catdir'];
	}
	/**
	* 生成相关栏目列表、只生成前5页
	* @param $catid
	*/
	public function create_relation_html($catids, $content = array()) {
		if(!empty($content)) {
			foreach ($content as $rs) {
				$this->_create_previous_next($rs[0], $rs[1]);
			}
		}
		if(!is_array($catids)) {
			$catids = array($catids);
		}
		foreach ($catids as $catid) {
			$CAT = dr_cat_value($catid);
			$array_child = array();
			$self_array = explode(',', (string)$CAT['arrchildid']);
			foreach ($self_array as $arr) {
				if($arr!=$catid) $array_child[] = $arr;
			}
			$arrchildid = implode(',', $array_child);
			$this->db->set_model($CAT['modelid']);
			$setting = dr_string2array($CAT['setting']);
			$pagesize = (int)$setting['pagesize'];
			$maxsize = (int)$setting['maxsize'];
			!$pagesize && $pagesize = 10;
			if ($arrchildid) {
				$pagenumber = $this->db->count(array('catid'=>explode(',', $arrchildid)));
			} else {
				$pagenumber = $this->db->count(array('catid'=>$catid));
			}
			!$maxsize && $maxsize = ceil($pagenumber/$pagesize);
			$maxsize > 20 && $maxsize = 20;
			!$setting['maxsize'] && $maxsize > 5 && $maxsize = 5;
			!$maxsize && $maxsize = 2;
			for($page = 1; $page < $maxsize + 1; $page++) {
				$this->category($catid,$page,(int)$setting['maxsize'] ? (int)$maxsize : 0);
			}
			//检查当前栏目的父栏目，如果存在则生成
			$arrparentid = $this->categorys[$catid]['arrparentid'];
			if($arrparentid) {
				$arrparentid = explode(',', $arrparentid);
				foreach ($arrparentid as $catid) {
					if($catid) $this->category($catid,1);
				}
			}
		}
	}

	/**
	 * 生成相关上下页
	 * @param $id
	 * @param $catid
	 * @return void
	 */
	private function _create_previous_next($id, $catid){
		$db = pc_base::load_model('content_model');
		$db->set_model($this->categorys[$catid]['modelid']);
		$previous = $db->get_one("catid = $catid AND id < $id", '*', 'id DESC');
		$this->_update_show($previous);
		$next = $db->get_one("catid = $catid AND id > $id", '*', 'id ASC');
		$this->_update_show($next);
	}

	/**
	 * 更新页面
	 * @param $data
	 * @return void
	 */
	private function _update_show($data){
		if(!empty($data)) {
			$db = pc_base::load_model('content_model');
			$db->set_model($this->categorys[$data['catid']]['modelid']);
			$db->table_name .= '_data_'.$data['tableid'];
			$rs = $db->get_one(array('id' => $data['id']));
			$db->set_model($this->categorys[$data['catid']]['modelid']);
			$rs && $data = array_merge($data, $rs);
			list($urls) = $this->url->show($data['id'], 0, $data['catid'], $data['inputtime'], $data['prefix'], $data);
			$urls['content_ishtml'] && $this->show($urls[1], $data, 0);
		}
	}
}