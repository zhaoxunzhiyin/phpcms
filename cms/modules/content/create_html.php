<?php
defined('IN_CMS') or exit('No permission resources.');

pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);

class create_html extends admin {
	private $db;
	public $siteid,$categorys;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
	}
	
	public function update_urls() {
		$show_header = $show_dialog  = '';
		$admin_username = param::get_cookie('admin_username');
		$this->model_db = pc_base::load_model('sitemodel_model');
		$module = $this->model_db->get_one(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0),'modelid','modelid');
		$modelid = $this->input->get('modelid') ? intval($this->input->get('modelid')) : $module['modelid'];
		
		$tables = array();
		$this->db->set_model(intval($modelid));
		$table_list = $this->db->query('show table status');
		foreach ($table_list as $t) {
			if (strpos($t['Name'], $this->db->table_name) === 0) {
				$tables[$t['Name']] = $t;
			}
		}
		
		$tree = pc_base::load_sys_class('tree');
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				$setting = string2array($r['setting']);
				if ($setting['disabled']) continue;
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";

		$tree->init($categorys);
		$string .= $tree->get_tree(0, $str);
		
		$tree = pc_base::load_sys_class('tree');
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys_post = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				$setting = string2array($r['setting']);
				if ($setting['disabled']) continue;
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$r['disabled'] = $r['child'] ? 'disabled' : '';
				$categorys_post[$catid] = $r;
			}
		}
		$str_post  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

		$tree->init($categorys_post);
		$select_post .= $tree->get_tree(0, $str_post);
		include $this->admin_tpl('update_urls');
	}

	private function urls($id, $catid= 0, $inputtime = 0, $prefix = ''){
		$this->url = pc_base::load_app_class('url');
		$urls = $this->url->show($id, 0, $catid, $inputtime, $prefix,'','edit');
		//更新到数据库
		$url = $urls[0];
		$this->db->update(array('url'=>$url),array('id'=>$id));
		//echo $id; echo "|";
		return $urls;
	}
	/**
	* 生成内容页
	*/
	public function show() {
		// 生成权限文件
		if (!dr_html_auth(1)) {
			showmessage(L('/cache/html/ 无法写入文件'));
		}
		if($this->input->get('dosubmit')) {
			$modelid = intval($this->input->get('modelid'));
			$catids = $this->input->get('catids');
			$pagesize = intval($this->input->get('pagesize'));
			$fromdate = $this->input->get('fromdate');
			$todate = $this->input->get('todate');
			$fromid = intval($this->input->get('fromid'));
			$toid = intval($this->input->get('toid'));
			$number = intval($this->input->get('number'));
			if ($catids && is_array($catids)) {
				$catids = implode(',', $catids);
			}
			$count_url = '?m=content&c=create_html&a=public_show_count&pagesize='.$pagesize.'&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
			$todo_url = '?m=content&c=create_html&a=public_show_add&set_catid=1&pagesize='.$pagesize.'&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
			include $this->admin_tpl('show_html');
		} else {
			$show_header = $show_dialog  = '';
			$admin_username = param::get_cookie('admin_username');
			$this->model_db = pc_base::load_model('sitemodel_model');
			$module = $this->model_db->get_one(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0),'modelid','modelid');
			$modelid = $this->input->get('modelid') ? intval($this->input->get('modelid')) : $module['modelid'];
			
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					$setting = string2array($r['setting']);
					if ($setting['disabled']) continue;
					if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
					if($modelid && $modelid != $r['modelid']) continue;
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			include $this->admin_tpl('create_html_show');
		}

	}
	// 断点内容
	public function public_show_point() {
		$cache_class = pc_base::load_sys_class('cache');
		$modelid = intval($this->input->get('modelid'));
		$catids = $this->input->get('catids');
		$pagesize = intval($this->input->get('pagesize'));
		$fromdate = intval($this->input->get('fromdate'));
		$todate = $this->input->get('todate');
		$fromid = $this->input->get('fromid');
		$toid = intval($this->input->get('toid'));
		$number = intval($this->input->get('number'));
		if ($catids && is_array($catids)) {
			$catids = implode(',', $catids);
		}
		$name = 'show-'.$modelid.'-html-file';
		$page = $cache_class->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		}

		$count_url = '?m=content&c=create_html&a=public_show_point_count&pagesize='.$pagesize.'&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
		$todo_url = '?m=content&c=create_html&a=public_show_add&pagesize='.$pagesize.'&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
		include $this->admin_tpl('show_html');
	}
	// 断点内容的数量统计
	public function public_show_point_count() {
		$cache_class = pc_base::load_sys_class('cache');
		$modelid = intval($this->input->get('modelid'));
		$name = 'show-'.$modelid.'-html-file';
		$page = $cache_class->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		} elseif (!$cache_class->get_auth_data($name)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		} elseif (!$cache_class->get_auth_data($name.'-'.$page)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		}

		dr_json(1, 'ok');
	}
	// 内容数量统计
	public function public_show_count() {
		$html = pc_base::load_sys_class('html');
		$html->get_show_data($this->input->get('modelid'), array(
			'catids' => $this->input->get('catids'),
			'todate' => $this->input->get('todate'),
			'fromdate' => $this->input->get('fromdate'),
			'toid' => $this->input->get('toid'),
			'fromid' => $this->input->get('fromid'),
			'pagesize' => $this->input->get('pagesize'),
			'siteid' => $this->siteid,
			'number' => $this->input->get('number')
		));
	}
	/**
	* 生成栏目页
	*/
	public function category() {
		// 生成权限文件
		if (!dr_html_auth(1)) {
			showmessage(L('/cache/html/ 无法写入文件'));
		}
		if($this->input->get('dosubmit')) {
			$catids = $this->input->get('catids');
			if ($catids && is_array($catids)) {
				$catids = implode(',', $catids);
			}
			$pagesize = $this->input->get('pagesize');
			$maxsize = $this->input->get('maxsize');
			$count_url = '?m=content&c=create_html&a=public_category_count&maxsize='.$maxsize.'&pagesize='.$pagesize.'&catids='.$catids;
			$todo_url = '?m=content&c=create_html&a=public_category_add&maxsize='.$maxsize.'&pagesize='.$pagesize.'&&catids='.$catids;
			include $this->admin_tpl('show_html');
		} else {
			$show_header = $show_dialog  = '';
			$admin_username = param::get_cookie('admin_username');
			$modelid = $this->input->get('modelid') ? intval($this->input->get('modelid')) : 0;
			
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					$setting = string2array($r['setting']);
					if ($setting['disabled']) continue;
					if($this->siteid != $r['siteid'] || ($r['type']==2 && $r['child']==0)) continue;
					if($modelid && $modelid != $r['modelid']) continue;
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid'>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			include $this->admin_tpl('create_html_category');
		}

	}
	// 断点生成栏目
	public function public_category_point() {
		$cache_class = pc_base::load_sys_class('cache');
		$name = 'category-html-file';
		$page = $cache_class->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		}

		$catids = $this->input->get('catids');
		if ($catids && is_array($catids)) {
			$catids = implode(',', $catids);
		}

		$count_url = '?m=content&c=create_html&a=public_category_point_count&maxsize='.$maxsize.'&pagesize='.$pagesize.'&catids='.$catids;
		$todo_url = '?m=content&c=create_html&a=public_category_add&maxsize='.$maxsize.'&pagesize='.$pagesize.'&catids='.$catids;
		include $this->admin_tpl('show_html');
	}
	// 断点栏目的数量统计
	public function public_category_point_count() {
		$cache_class = pc_base::load_sys_class('cache');
		$name = 'category-html-file';
		$page = $cache_class->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		} elseif (!$cache_class->get_auth_data($name)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		} elseif (!$cache_class->get_auth_data($name.'-'.$page)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		}
		dr_json(1, 'ok');
	}
	// 获取生成的栏目
	private function _category_data($catids, $cats) {

		if (!$catids) {
			return $cats;
		}

		$rt = array();
		$arr = explode(',', $catids);
		foreach ($arr as $id) {
			if ($id && $cats[$id]) {
				$rt[$id] = $cats[$id];
			}
		}

		return $rt;
	}
	// 栏目的数量统计
	public function public_category_count() {
		$catids = $this->input->get('catids');
		$pagesize = (int)$this->input->get('pagesize');
		$maxsize = (int)$this->input->get('maxsize');

		$cat = getcache('category_content_'.$this->siteid,'commons');
		$html = pc_base::load_sys_class('html');
		$html->get_category_data($this->_category_data($catids, $cat), $pagesize, $maxsize);
	}
	//生成首页
	public function public_index() {
		$this->html = pc_base::load_app_class('html');
		$this->db = pc_base::load_model('site_model');
		$data = $this->db->get_one(array('siteid'=>$this->siteid));
		if($data['ishtml']==1) {
			$html = $this->html->index();
			showmessage(L('首页更新成功！').$html);
		} else {
			showmessage(L('index_create_close'));
		}
	}
	//生成首页
	public function public_index_ajax() {
		$this->html = pc_base::load_app_class('html');
		$this->db = pc_base::load_model('site_model');
		$data = $this->db->get_one(array('siteid'=>$this->siteid));
		if($data['ishtml']==1) {
			$html = $this->html->index();
			dr_json(1, $html);
		} else {
			dr_json(0, L('index_create_close'));
		}
	}
	/**
	* 批量生成内容页
	*/
	public function batch_show() {
		if($this->input->post('dosubmit')) {
			$catid = intval($this->input->get('catid'));
			if(!$catid) dr_json(0, L('missing_part_parameters'));
			$modelid = $this->categorys[$catid]['modelid'];
			$setting = string2array($this->categorys[$catid]['setting']);
			$content_ishtml = $setting['content_ishtml'];
			if(!$content_ishtml) dr_json(0, L('它是动态模式'));
			if($content_ishtml) {
				$ids = $this->input->get_post_ids();
				if(empty($ids)) dr_json(0, L('you_do_not_check'));
				$count = 0;
				foreach($ids as $id) {
					$insert['catid']=$catid;
					$insert['id']=$id;
					$count ++;
					$cache_data[] = $insert;
				}
				$cache = array();
				if ($count > 100) {
					$pagesize = ceil($count/100);
					for ($i = 1; $i <= 100; $i ++) {
						$cache[$i] = array_slice($cache_data, ($i - 1) * $pagesize, $pagesize);
					}
				} else {
					for ($i = 1; $i <= $count; $i ++) {
						$cache[$i] = array_slice($cache_data, ($i - 1), 1);
					}
				}
				setcache('update_html_show-'.$this->siteid.'-'.$_SESSION['userid'], $cache,'content');
				dr_json(1, 'ok', array('url' => '?m=content&c=create_html&a=public_batch_show_add&menuid='.$this->input->get('menuid').'&pc_hash='.$this->input->get('pc_hash')));
			}
		}
	}
	/**
	* 批量生成内容页
	*/
	public function public_batch_show_add() {
		$show_header = $show_dialog = $show_pc_hash = '';
		$todo_url = '?m=content&c=create_html&a=public_batch_show&menuid='.$this->input->get('menuid').'&pc_hash='.$this->input->get('pc_hash');
		include $this->admin_tpl('show_url');
	}
	/**
	* 批量生成内容页
	*/
	public function public_batch_show() {
		$this->html = pc_base::load_app_class('html');
		$this->url = pc_base::load_app_class('url');
		$page = max(1, intval($this->input->get('page')));
		$update_html_show = getcache('update_html_show-'.$this->siteid.'-'.$_SESSION['userid'], 'content');
		if (!$update_html_show) {
			dr_json(0, '临时缓存数据不存在');
		}

		$cache_data = $update_html_show[$page];
		if ($cache_data) {
			$html = '';
			foreach ($cache_data as $insert) {
				$ok = '完成';
				$class = '';
				$modelid = $this->categorys[$insert['catid']]['modelid'];
				$setting = string2array($this->categorys[$insert['catid']]['setting']);
				$content_ishtml = $setting['content_ishtml'];
				$this->db->set_model($modelid);
				$rs = $this->db->get_one(array('id'=>$insert['id']));
				if($content_ishtml) {
					if($rs['islink']) {
						$class = 'p_error';
						$ok = '<a class="error" href="'.$rs['url'].'" target="_blank">转向链接</a>';
					} else {
						//写入文件
						$this->db->table_name = $this->db->table_name.'_data';
						$r2 = $this->db->get_one(array('id'=>$rs['id']));
						if($r2) $rs = array_merge($rs,$r2);
						//判断是否为升级或转换过来的数据
						if(!$rs['upgrade']) {
							$urls = $this->url->show($rs['id'], '', $rs['catid'], $rs['inputtime']);
						} else {
							$urls[1] = $rs['url'];
						}
						$this->html->show($urls[1],$rs,0,'edit',$rs['upgrade']);
						$class = 'ok';
						$ok = '<a class="ok" href="'.$rs['url'].'" target="_blank">生成成功</a>';
					}
				} else {
					$class = 'p_error';
					$ok = '<a class="error" href="'.$rs['url'].'" target="_blank">它是动态模式</a>';
				}
				$html.= '<p class="'.$class.'"><label class="rleft">(#'.$rs['id'].')'.$rs['title'].'</label><label class="rright">'.$ok.'</label></p>';
			}
			dr_json($page + 1, $html);
		}
		// 完成
		delcache('update_html_show-'.$this->siteid.'-'.$_SESSION['userid'], 'content');
		dr_json(100, '');
	}
	/**
	* 批量批量更新URL
	*/
	public function public_show_url() {
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 500; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);
		if (!$page) {
			// 计算数量
			$total = $this->db->count(array('status' => 99));
			if (!$total) {
				$this->html_msg(0, L('无可用内容更新'));
			}

			$url = '?m=content&c=create_html&a=public_show_url&modelid='.$modelid;
			$this->html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}
		$tpage = ceil($total / $psize); // 总页数
		// 更新完成
		if ($page > $tpage) {
			$this->html_msg(1, L('更新完成'));
		}
		$data = $this->db->listinfo(array('status' => 99), 'id DESC', $page, $psize);
		foreach ($data as $t) {
			if(!$t['islink'] || !$t['upgrade']) {
				$urls = $this->urls($t['id'], $t['catid'], $t['inputtime'], $t['prefix']);
			}
		}
		$this->html_msg( 1, L('正在执行中'.$tpage.'/'.$page.'...'), '?m=content&c=create_html&a=public_show_url&modelid='.$modelid.'&total='.$total.'&page='.($page+1));
	}
	/**
	* 批量生成栏目页
	*/
	public function public_category_add() {
		// 判断权限
		if (!dr_html_auth()) {
			dr_json(0, '权限验证超时，请重新执行生成');
		}
		$cache_class = pc_base::load_sys_class('cache');
		$this->html = pc_base::load_app_class('html');
		$page = max(1, intval($this->input->get('pp')));
		$name2 = 'category-html-file';
		$pcount = $cache_class->get_auth_data($name2);
		if (!$pcount) {
			dr_json(0, '临时缓存数据不存在：'.$name2);
		} elseif ($page > $pcount) {
			// 完成
			$cache_class->del_auth_data($name2);
			dr_json(-1, '');
		}

		$name = 'category-html-file-'.$page;
		$cache = $cache_class->get_auth_data($name);
		if (!$cache) {
			dr_json(0, '临时缓存数据不存在：'.$name);
		}

		if ($cache) {
			$html = '';
			foreach ($cache as $t) {
				$ok = '完成';
				$class = '';
				if (!$t['ishtml']) {
					$class = 'p_error';
					$ok = '<a class="error" href="'.$t['url'].'" target="_blank">它是动态模式</a>';
				} else {
					$this->html->category($t['catid'],$t['page']);
					$cache_class->set_auth_data($name2.'-error', $page); // 设置断点
					$class = 'ok';
					$ok = '<a class="ok" href="'.$t['url'].'" target="_blank">生成成功</a>';
				}
				$html.= '<p class="'.$class.'"><label class="rleft">(#'.$t['catid'].')'.$t['catname'].'</label><label class="rright">'.$ok.'</label></p>';
			}
			// 完成
			//$cache_class->del_auth_data($name);
			dr_json($page + 1, $html, array('pcount' => $pcount + 1));
		}
	}
	/**
	* 批量生成内容页
	*/
	public function public_show_add() {
		// 判断权限
		if (!dr_html_auth()) {
			dr_json(0, '权限验证超时，请重新执行生成');
		}
		$cache_class = pc_base::load_sys_class('cache');
		$this->html = pc_base::load_app_class('html');
		$this->url = pc_base::load_app_class('url');
		$modelid = intval($this->input->get('modelid'));
		$page = max(1, intval($this->input->get('pp')));
		$name2 = 'show-'.$modelid.'-html-file';
		$pcount = $cache_class->get_auth_data($name2);
		if (!$pcount) {
			dr_json(0, '临时缓存数据不存在：'.$name2);
		} elseif ($page > $pcount) {
			// 完成
			$cache_class->del_auth_data($name2);
			dr_json(-1, '');
		}

		$name = 'show-'.$modelid.'-html-file-'.$page;
		$cache = $cache_class->get_auth_data($name);
		if (!$cache) {
			dr_json(0, '临时缓存数据不存在：'.$name);
		}
		
		if ($cache) {
			$html = '';
			foreach ($cache as $t) {
				$ok = '完成';
				$class = '';
				//设置模型数据表名
				$this->db->set_model(intval($this->categorys[$t['catid']]['modelid']));
				$setting = string2array($this->categorys[$t['catid']]['setting']);
				$content_ishtml = $setting['content_ishtml'];
				if($content_ishtml) {
					$r = $this->db->get_one(array('id'=>$t['id']));
					if($t['islink']) {
						$class = 'p_error';
						$ok = '<a class="error" href="'.$t['url'].'" target="_blank">转向链接</a>';
					} else {
						//写入文件
						$this->db->table_name = $this->db->table_name.'_data';
						$r2 = $this->db->get_one(array('id'=>$t['id']));
						if($r2) $r = array_merge($r, $r2);
						//判断是否为升级或转换过来的数据
						if($r['upgrade']) {
							$urls[1] = $t['url'];
						} else {
							$urls = $this->url->show($t['id'], '', $t['catid'], $t['inputtime']);
						}
						$this->html->show($urls[1],$r,0,'edit',$t['upgrade']);
						$cache_class->set_auth_data($name2.'-error', $page); // 设置断点
						$class = 'ok';
						$ok = '<a class="ok" href="'.$t['url'].'" target="_blank">生成成功</a>';
					}
				} else {
					$class = 'p_error';
					$ok = '<a class="error" href="'.$t['url'].'" target="_blank">它是动态模式</a>';
				}
				$html.= '<p class="'.$class.'"><label class="rleft">(#'.$t['id'].')'.$t['title'].'</label><label class="rright">'.$ok.'</label></p>';
			}
			// 完成
			//$cache_class->del_auth_data($name);
			dr_json($page + 1, $html, array('pcount' => $pcount + 1));
		}
	}
	// 提取tag
	public function public_tag_index() {
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 10; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$where = 'status = 99';
		$catids = $this->input->get('catids');

		$url = '?m=content&c=create_html&a=public_tag_index&modelid='.$modelid;

		// 获取生成栏目
		if ($catids) {
			$cat = array();
			foreach ($catids as $i) {
				if ($i) {
					$cat[] = intval($i);
					if ($this->categorys[$i]['child']) {
						$cat = dr_array2array($cat, explode(',', $this->categorys[$i]['arrchildid']));
					}
					$url.= '&catids[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		}

		$keyword = $this->input->get('keyword');
		$keyword && $where.= ' AND keywords=""';
		$url.= '&keyword='.$keyword;

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				$this->html_msg(0, L('无可用内容更新'));
			}

			$this->html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1), L('在使用网络分词接口时可能会很慢'));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			$this->html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', $page, $psize);
		foreach ($data as $t) {
			$tag = dr_get_keywords($t['title'].' '.$t['description']);
			if ($tag) {
				$this->db->update(array('keywords' => $tag), array('id' => $t['id']));
			}
		}

		$this->html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1), L('在使用网络分词接口时可能会很慢'));
	}
	// 提取缩略图
	public function public_thumb_index() {
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$where = 'status = 99';
		$catids = $this->input->get('catids');

		$url = '?m=content&c=create_html&a=public_thumb_index&modelid='.$modelid;

		// 获取生成栏目
		if ($catids) {
			$cat = array();
			foreach ($catids as $i) {
				if ($i) {
					$cat[] = intval($i);
					if ($this->categorys[$i]['child']) {
						$cat = dr_array2array($cat, explode(',', $this->categorys[$i]['arrchildid']));
					}
					$url.= '&catids[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		}

		$thumb = $this->input->get('thumb');
		$thumb && $where.= ' AND thumb=""';
		$url.= '&thumb='.$thumb;

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				$this->html_msg(0, L('无可用内容更新'));
			}

			$this->html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			$this->html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', $page, $psize);
		foreach ($data as $row) {
			$content = get_content($modelid, $row['id']);
			$this->db->set_model($modelid);
			if ($row && $content && preg_match("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|png))\\2/i", htmlspecialchars_decode($content), $m)) {
				$this->db->update(array('thumb' => str_replace(array('"', '\''), '', $m[3])), array('id' => $row['id']));
			}
		}

		$this->html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 提取描述信息
	public function public_desc_index() {
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$where = 'status = 99';
		$catids = $this->input->get('catids');

		$url = '?m=content&c=create_html&a=public_desc_index&modelid='.$modelid;

		// 获取生成栏目
		if ($catids) {
			$cat = array();
			foreach ($catids as $i) {
				if ($i) {
					$cat[] = intval($i);
					if ($this->categorys[$i]['child']) {
						$cat = dr_array2array($cat, explode(',', $this->categorys[$i]['arrchildid']));
					}
					$url.= '&catids[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		}

		$nums = max(1, $this->input->get('nums'));
		$keyword = $this->input->get('keyword');
		$keyword && $where.= ' AND description=""';
		$url.= '&nums='.$nums;
		$url.= '&keyword='.$keyword;

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				$this->html_msg(0, L('无可用内容更新'));
			}

			$this->html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			$this->html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', $page, $psize);
		foreach ($data as $row) {
			$content = get_content($modelid, $row['id']);
			$this->db->set_model($modelid);
			if ($row && $content && dr_get_description(code2html($content), $nums)) {
				$this->db->update(array('description' => dr_get_description(code2html($content), $nums)), array('id' => $row['id']));
			} elseif ($row['title']) {
				$this->db->update(array('description' => dr_get_description(code2html($row['title']), $nums)), array('id' => $row['id']));
			}
		}

		$this->html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 提取变更栏目
	public function public_cat_index() {
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$toid = (int)$this->input->get('toid');
		if (!$toid) {
			$this->html_msg(0, L('目标栏目必须选择'));
		}

		$url = '?m=content&c=create_html&a=public_cat_index&modelid='.$modelid;
		$url.= '&toid='.$toid;
		$where = '';

		// 获取生成栏目
		$catids = $this->input->get('catids');
		if ($catids) {
			$cat = array();
			foreach ($catids as $i) {
				if ($i) {
					$cat[] = intval($i);
					if ($this->categorys[$i]['child']) {
						$cat = dr_array2array($cat, explode(',', $this->categorys[$i]['arrchildid']));
					}
					$url.= '&catids[]='.intval($i);
				}
			}
			$cat && $where.= ' catid IN ('.implode(',', $cat).')';
		}
		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				$this->html_msg(0, L('无可用内容更新'));
			}

			$this->html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			$this->html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', 1, $psize);
		foreach ($data as $row) {
			if ($row) {
				$this->db->update(array('catid' => $toid), array('id' => $row['id']));
			}
		}

		$this->html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 批量删除
	public function public_del_index() {
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);
		$this->hits_db = pc_base::load_model('hits_model');
		$this->queue = pc_base::load_model('queue_model');
		$html_root = pc_base::load_config('system','html_root');
		//附件初始化
		$attachment = pc_base::load_model('attachment_model');
		$this->content_check_db = pc_base::load_model('content_check_model');
		$this->position_data_db = pc_base::load_model('position_data_model');
		$this->search_db = pc_base::load_model('search_model');
		$this->comment = pc_base::load_app_class('comment', 'comment');
		$search_model = getcache('search_model_'.$this->siteid,'search');
		$typeid = $search_model[$modelid]['typeid'];
		$this->url = pc_base::load_app_class('url', 'content');
		$sitelist = getcache('sitelist','commons');

		$where = array();
		$catids = $this->input->get('catids');

		$url = '?m=content&c=create_html&a=public_del_index&modelid='.$modelid;

		// 获取生成栏目
		if ($catids) {
			$cat = array();
			foreach ($catids as $i) {
				if ($i) {
					$cat[] = intval($i);
					if ($this->categorys[$i]['child']) {
						$cat = dr_array2array($cat, explode(',', $this->categorys[$i]['arrchildid']));
					}
					$url.= '&catids[]='.intval($i);
				}
			}
			$cat && $where[] = ' catid IN ('.implode(',', $cat).')';
		}

		$author = $this->input->get('author');
		if (is_numeric($author)) {
			$author = (int)$author;
			$author_db = pc_base::load_model('admin_model');
			$author_data = $author_db->get_one(array('userid'=>$author));
			$author = $author_data['username'];
		}
		if ($author) {
			$where[] = 'username="'.dr_safe_replace($author).'"';
			$url.= '&author='.$author;
		}

		$id1 = (int)$this->input->get('id1');
		$id2 = (int)$this->input->get('id2');
		if ($id1 || $id2) {
			if (!$id2) {
				$where[] = 'id>'.$id1;
			} else {
				$where[] = '`id` BETWEEN '.$id1.' AND '.$id2;
			}
			$url.= '&id1='.$id1.'&id2='.$id2;
		}

		if (!$where) {
			$this->html_msg(0, L('没有设置条件'));
		}

		$where = implode(' AND ', $where);

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				$this->html_msg(0, L('无可用内容更新'));
			}

			$this->html_msg(1, L('正在删除中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			$this->html_msg(1, L('删除完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', 1, $psize);
		foreach ($data as $row) {
			if ($row) {
				$sethtml = $this->categorys[$row['catid']]['sethtml'];
				if($sethtml) $html_root = '';
				$setting = string2array($this->categorys[$row['catid']]['setting']);
				if($setting['content_ishtml'] && !$row['islink']) {
					$urls = $this->url->show($row['id'], 0, $row['catid'], $row['inputtime']);
					$fileurl = $urls[1];
					if($this->siteid != 1) {
						$fileurl = $html_root.'/'.$sitelist[$this->siteid]['dirname'].$fileurl;
					}
					$mobilefileurl = pc_base::load_config('system','mobile_root').$fileurl;
					//删除静态文件，排除htm/html/shtml外的文件
					$lasttext = strrchr($fileurl,'.');
					$len = -strlen($lasttext);
					$path = substr($fileurl,0,$len);
					$path = ltrim($path,'/');
					$filelist = glob(CMS_PATH.$path.'{_,-,.}*',GLOB_BRACE);
					$mobilelasttext = strrchr($mobilefileurl,'.');
					$mobilelen = -strlen($mobilelasttext);
					$mobilepath = substr($mobilefileurl,0,$mobilelen);
					$mobilepath = ltrim($mobilepath,'/');
					$mobilefilelist = glob(CMS_PATH.$mobilepath.'{_,-,.}*',GLOB_BRACE);
					foreach ($filelist as $delfile) {
						$lasttext = strrchr($delfile,'.');
						if(!in_array($lasttext, array('.htm','.html','.shtml'))) continue;
						@unlink($delfile);
						//删除发布点队列数据
						$delfile = str_replace(CMS_PATH, '/', $delfile);
						$this->queue->add_queue('del',$delfile,$this->siteid);
					}
					if($sitelist[$this->siteid]['mobilehtml']==1) {
						foreach ($mobilefilelist as $mobiledelfile) {
							$mobilelasttext = strrchr($mobiledelfile,'.');
							if(!in_array($mobilelasttext, array('.htm','.html','.shtml'))) continue;
							@unlink($mobiledelfile);
						}
					}
				} else {
					$fileurl = 0;
				}
				//删除内容
				$this->db->delete_content($row['id'],$fileurl,$row['catid']);
				//删除统计表数据
				$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$row['id']));
				//删除附件
				$attachment->api_delete('c-'.$row['catid'].'-'.$row['id']);
				//删除审核表数据
				$this->content_check_db->delete(array('checkid'=>'c-'.$row['id'].'-'.$modelid));
				//删除推荐位数据
				$this->position_data_db->delete(array('id'=>$row['id'],'catid'=>$row['catid'],'module'=>'content'));
				//删除全站搜索中数据
				$this->search_db->delete_search($typeid,$row['id']);
				//删除关键词和关键词数量重新统计
				$keyword_db = pc_base::load_model('keyword_model');
				$keyword_data_db = pc_base::load_model('keyword_data_model');
				$keyword_arr = $keyword_data_db->select(array('siteid'=>$this->siteid,'contentid'=>$row['id'].'-'.$modelid));
				if($keyword_arr){
					foreach ($keyword_arr as $val){
						$keyword_db->update(array('videonum'=>'-=1'),array('id'=>$val['tagid']));
					}
					$keyword_data_db->delete(array('siteid'=>$this->siteid,'contentid'=>$row['id'].'-'.$modelid));
					$keyword_db->delete(array('videonum'=>'0'));
				}
				
				//删除相关的评论,删除前应该判断是否还存在此模块
				if(module_exists('comment')){
					$commentid = id_encode('content_'.$row['catid'], $row['id'], $this->siteid);
					$this->comment->del($commentid, $this->siteid, $row['id'], $row['catid']);
				}
			}
		}
		//更新栏目统计
		$this->db->cache_items();

		$this->html_msg(1, L('正在删除中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 获取可用字段
	private function _get_field($bm) {

		$fields = $this->db->query('SHOW FULL COLUMNS FROM `'.$bm.'`');
		if (!$fields) {
			dr_json(0, L('表['.$bm.']没有可用字段'));
		}

		$rt = array();
		foreach ($fields as $t) {
			$rt[] = $t['Field'];
		}

		return $rt;
	}
	public function public_replace_index() {
		$bm = $this->input->post('bm');
		if (!$bm) {
			dr_json(0, L('表名不能为空'));
		}
		$tables = array();
		$tables[$bm] = $this->_get_field($bm);

		$t1 = $this->input->post('t1');
		$t2 = $this->input->post('t2');
		$fd = dr_safe_replace($this->input->post('fd'));

		if (!$fd) {
			dr_json(0, L('待替换字段必须填写'));
		} elseif (!$t1) {
			dr_json(0, L('被替换内容必须填写'));
		} elseif (!$tables) {
			dr_json(0, L('表名称必须填写'));
		} elseif ($fd == 'id') {
			dr_json(0, L('ID主键不支持替换'));
		}

		$count = 0;
		$replace = '`'.$fd.'`=REPLACE(`'.$fd.'`, \''.addslashes($t1).'\', \''.addslashes($t2).'\')';

		foreach ($tables as $table => $fields) {
			if (!dr_in_array($fd, $fields)) {
				dr_json(0, L('表['.$table.']字段['.$fd.']不存在'));
			}
			$this->db->query('UPDATE `'.$table.'` SET '.$replace);
			$count = $this->db->affected_rows();
		}

		if ($count < 0) {
			dr_json(0, L('执行错误'));
		}

		dr_json(1, L('本次替换'.$count.'条数据'));
	}
	public function public_field_index() {
		$table = dr_safe_replace($this->input->get('table'));
		$table = str_replace($this->db->db_tablepre, '', $table);
		if (!$table) {
			dr_json(0, L('表参数不能为空'));
		} elseif (!$this->db->table_exists($table)) {
			dr_json(0, L('表['.$table.']不存在'));
		}

		$fields = $this->db->query('SHOW FULL COLUMNS FROM `'.$this->db->db_tablepre.$table.'`');
		if (!$fields) {
			dr_json(0, L('表['.$table.']没有可用字段'));
		}

		$msg = '<select name="fd" class="form-control">';
		foreach ($fields as $t) {
			if ($t['Field'] != 'id') {
				$msg.= '<option value="'.$t['Field'].'">'.$t['Field'].($t['Comment'] ? '（'.$t['Comment'].'）' : '').'</option>';
			}
		}
		$msg.= '</select>';

		dr_json(1, $msg);
	}
	/**
	 * 生成静态时的跳转提示
	 */
	public function html_msg($code, $msg, $url = '', $note = '') {
		include $this->admin_tpl('html_msg');exit;
	}
}
?>