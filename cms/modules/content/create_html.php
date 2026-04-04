<?php
@set_time_limit(0);
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);

class create_html extends admin {
	private $input,$cache,$db,$site_db,$model_db,$url,$form,$sitemodel,$form_cache,$keyword_db,$keyword_data_db,$hits_db,$queue,$content_check_db,$position_data_db,$search_db,$comment,$html,$cache_site,$cat_config,$category_db,$cache_api,$urlrule,$urlrule_db;
	public $siteid,$categorys;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		pc_base::load_sys_class('upload','',0);
		$this->db = pc_base::load_model('content_model');
		$this->model_db = pc_base::load_model('sitemodel_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = get_category($this->siteid);
		// 不是超级管理员
		/*if (!cleck_admin(param::get_session('roleid'))) {
			dr_admin_msg(0,L('需要超级管理员账号操作'));
		}*/
	}
	
	public function update_urls() {
		$page = max(0, intval($this->input->get('page')));
		$show_header = $show_dialog = true;
		$admin_username = param::get_cookie('admin_username');
		$module = $this->model_db->get_one(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0),'modelid','modelid');
		$modelid = $this->input->get('modelid') ? intval($this->input->get('modelid')) : $module['modelid'];
		
		$tables = array();
		$this->db->set_model(intval($modelid));
		$table_list = $this->db->query('show table status');
		foreach ($table_list as $t) {
			$t['Name'] = str_replace('_data_0', '_data_[tableid]', $t['Name']);
			$tables[$t['Name']] = $t;
		}
		include $this->admin_tpl('update_urls');
	}

	private function urls($id, $catid= 0, $inputtime = 0, $prefix = ''){
		$this->url = pc_base::load_app_class('url');
		list($urls) = $this->url->show($id, 0, $catid, $inputtime, $prefix,'','edit');
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
			dr_admin_msg(0, L('/cache/html/ 无法写入文件'));
		}
		$show_header = $show_dialog = true;
		if($this->input->get('dosubmit')) {
			$modelid = intval($this->input->get('modelid'));
			$ids = $this->input->get('ids');
			$catids = $this->input->get('catids');
			$pagesize = intval($this->input->get('pagesize'));
			$number = intval($this->input->get('number'));
			$fromdate = $this->input->get('fromdate');
			$todate = $this->input->get('todate');
			$fromid = intval($this->input->get('fromid'));
			$toid = intval($this->input->get('toid'));
			if ($ids && is_array($ids)) {
				$ids = implode(',', $ids);
			}
			if ($catids && is_array($catids)) {
				$catids = implode(',', $catids);
			}
			$model = $this->model_db->get_one(array('siteid'=>$this->siteid,'modelid'=>$modelid));
			$modulename = $model['name'];
			$count_url = '?m=content&c=create_html&a=public_show_count&pagesize='.$pagesize.'&number='.$number.'&modelid='.$modelid.'&catids='.$catids.'&ids='.$ids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid;
			$todo_url = '?m=content&c=create_html&a=public_show_add&pagesize='.$pagesize.'&number='.$number.'&modelid='.$modelid.'&catids='.$catids.'&ids='.$ids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid;
			include $this->admin_tpl('show_html');
		} else {
			$admin_username = param::get_cookie('admin_username');
			$module = $this->model_db->get_one(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0),'modelid','modelid');
			$modelid = $this->input->get('modelid') ? intval($this->input->get('modelid')) : $module['modelid'];
			
			$tree = pc_base::load_sys_class('tree');
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
					if($modelid && $modelid != $r['modelid']) continue;
					$categorys[$catid] = $r;
				}
			}
			$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string = $tree->get_tree(0, $str);
			include $this->admin_tpl('create_html_show');
		}

	}
	// 断点内容
	public function public_show_point() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$catids = $this->input->get('catids');
		$pagesize = intval($this->input->get('pagesize'));
		$number = intval($this->input->get('number'));
		$fromdate = $this->input->get('fromdate');
		$todate = $this->input->get('todate');
		$fromid = intval($this->input->get('fromid'));
		$toid = intval($this->input->get('toid'));
		if ($catids && is_array($catids)) {
			$catids = implode(',', $catids);
		}
		$html_file = '';
		if (isset($fromdate) && $fromdate) {
			$html_file .= '-' . strtotime($fromdate.' 00:00:00') . '-' . ($todate ? strtotime($todate.' 23:59:59') : SYS_TIME);
		} elseif (isset($todate) && $todate) {
			$html_file .= '-0-' . strtotime($todate.' 23:59:59');
		}
		$html_file .= $pagesize ? '-'.$pagesize : '';
		$html_file .= $number ? '-'.$number : '';
		$html_file .= $fromid && $toid ? '-'.$fromid.'-'.$toid : '';
		$name = 'show-'.$modelid.'-html-file'.$html_file;
		$page = $this->cache->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		}

		$model = $this->model_db->get_one(array('siteid'=>$this->siteid,'modelid'=>$modelid));
		$modulename = $model['name'];
		$count_url = '?m=content&c=create_html&a=public_show_point_count&pagesize='.$pagesize.'&number='.$number.'&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid;
		$todo_url = '?m=content&c=create_html&a=public_show_add&pagesize='.$pagesize.'&number='.$number.'&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid;
		include $this->admin_tpl('show_html');
	}
	// 断点内容的数量统计
	public function public_show_point_count() {
		$modelid = intval($this->input->get('modelid'));
		$pagesize = intval($this->input->get('pagesize'));
		$number = intval($this->input->get('number'));
		$fromdate = $this->input->get('fromdate');
		$todate = $this->input->get('todate');
		$fromid = intval($this->input->get('fromid'));
		$toid = intval($this->input->get('toid'));
		$html_file = '';
		if (isset($fromdate) && $fromdate) {
			$html_file .= '-' . strtotime($fromdate.' 00:00:00') . '-' . ($todate ? strtotime($todate.' 23:59:59') : SYS_TIME);
		} elseif (isset($todate) && $todate) {
			$html_file .= '-0-' . strtotime($todate.' 23:59:59');
		}
		$html_file .= $pagesize ? '-'.$pagesize : '';
		$html_file .= $number ? '-'.$number : '';
		$html_file .= $fromid && $toid ? '-'.$fromid.'-'.$toid : '';
		$name = 'show-'.$modelid.'-html-file'.$html_file;
		$page = $this->cache->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		} elseif (!$this->cache->get_auth_data($name)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		}

		dr_json(1, 'ok');
	}
	// 内容数量统计
	public function public_show_count() {
		pc_base::load_sys_class('html')->get_show_data($this->input->get('modelid'), array(
			'ids' => $this->input->get('ids'),
			'catids' => $this->input->get('catids'),
			'todate' => $this->input->get('todate'),
			'fromdate' => $this->input->get('fromdate'),
			'toid' => $this->input->get('toid'),
			'fromid' => $this->input->get('fromid'),
			'pagesize' => $this->input->get('pagesize'),
			'number' => $this->input->get('number'),
			'siteid' => $this->siteid
		));
	}
	/**
	* 生成栏目页
	*/
	public function category() {
		// 生成权限文件
		if (!dr_html_auth(1)) {
			dr_admin_msg(0, L('/cache/html/ 无法写入文件'));
		}
		$show_header = $show_dialog = true;
		if($this->input->get('dosubmit')) {
			$catids = $this->input->get('catids');
			if ($catids && is_array($catids)) {
				$catids = implode(',', $catids);
			}
			$maxsize = (int)$this->input->get('maxsize');
			$modulename = '栏目';
			$count_url = '?m=content&c=create_html&a=public_category_count&maxsize='.$maxsize.'&catids='.$catids;
			$todo_url = '?m=content&c=create_html&a=public_category_add&maxsize='.$maxsize.'&catids='.$catids;
			include $this->admin_tpl('show_html');
		} else {
			$admin_username = param::get_cookie('admin_username');
			
			$tree = pc_base::load_sys_class('tree');
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					if($this->siteid != $r['siteid'] || $r['type']==2) continue;
					$categorys[$catid] = $r;
				}
			}
			$str = "<option value='\$catid'>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string = $tree->get_tree(0, $str);
			include $this->admin_tpl('create_html_category');
		}

	}
	// 断点生成栏目
	public function public_category_point() {
		$show_header = $show_dialog = true;
		$maxsize = (int)$this->input->get('maxsize');
		$name = 'category-html-file';
		$page = $this->cache->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		}

		$catids = $this->input->get('catids');
		if ($catids && is_array($catids)) {
			$catids = implode(',', $catids);
		}

		$modulename = '栏目';
		$count_url = '?m=content&c=create_html&a=public_category_point_count&maxsize='.$maxsize.'&catids='.$catids;
		$todo_url = '?m=content&c=create_html&a=public_category_add&maxsize='.$maxsize.'&catids='.$catids;
		include $this->admin_tpl('show_html');
	}
	// 断点栏目的数量统计
	public function public_category_point_count() {
		$name = 'category-html-file';
		$page = $this->cache->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		} elseif (!$this->cache->get_auth_data($name)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		} elseif (!$this->cache->get_auth_data($name.'-'.$page)) {
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
		$maxsize = (int)$this->input->get('maxsize');
		$cat = get_category($this->siteid);
		pc_base::load_sys_class('html')->get_category_data($this->_category_data($catids, $cat), $maxsize);
	}
	//生成首页
	public function public_index() {
		$show_header = $show_dialog = true;
		$this->site_db = pc_base::load_model('site_model');
		$data = $this->site_db->get_one(array('siteid'=>$this->siteid));
		$ishtml = $data['ishtml'];
		$mobilehtml = $data['mobilehtml'];
		include $this->admin_tpl('create_html_index');
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
			$modelid = dr_cat_value($catid, 'modelid');
			$setting = dr_string2array(dr_cat_value($catid, 'setting'));
			$content_ishtml = $setting['content_ishtml'];
			if(!$content_ishtml) dr_json(0, L('它是动态模式'));
			$name = 'show-'.$modelid.'-html-file';
			$this->cache->del_auth_data($name, $this->siteid);
			if($content_ishtml) {
				$ids = $this->input->get_post_ids();
				if(empty($ids)) dr_json(0, L('you_do_not_check'));
				dr_json(1, '?m=content&c=create_html&a=show&modelid='.$modelid.'&ids='.implode(',', $ids).'&dosubmit='.$this->input->post('dosubmit').'&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token());
			}
		}
	}
	// 批量批量更新URL
	public function public_url_index() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$tree->init($categorys);
		$string = $tree->get_tree(0, $str);
		$todo_url = '?m=content&c=create_html&a=public_show_url&modelid='.$modelid;
		include $this->admin_tpl('module_content_url');
	}
	/**
	* 批量批量更新URL
	*/
	public function public_show_url() {
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);
		$where = 'status = 99';
		$catids = dr_string2array($this->input->get('catids'));
		$catid = $this->input->get('catid');
		$catid = dr_string2array($catid);
		$url = '?m=content&c=create_html&a=public_show_url&modelid='.$modelid;
		// 获取栏目
		if ($catids) {
			$cat = array();
			foreach ($catids as $i) {
				if ($i) {
					$cat[] = intval($i);
					$icat = dr_cat_value($i);
					if ($icat['child']) {
						$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
					}
					$url.= '&catid[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		} else {
			if ($catid) {
				$cat = array();
				foreach ($catid as $i) {
					if ($i) {
						$cat[] = intval($i);
						$icat = dr_cat_value($i);
						if ($icat['child']) {
							$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
						}
						$url.= '&catid[]='.intval($i);
					}
				}
				$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
			}
		}
		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				html_msg(0, L('无可用内容更新'));
			}

			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}
		$tpage = ceil($total / $psize); // 总页数
		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('更新完成'));
		}
		$data = $this->db->listinfo($where, 'id DESC', $page, $psize);
		// 更新完成
		if (!$data) {
			html_msg(1, L('更新完成'));
		}
		foreach ($data as $t) {
			if($t['islink'] || $t['upgrade']) continue;
			$urls = $this->urls($t['id'], $t['catid'], $t['inputtime'], dr_value(dr_cat_value($t['catid'], 'modelid'), $t['id'], 'prefix'));
		}
		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	/**
	* 批量生成栏目页
	*/
	public function public_category_add() {
		// 判断权限
		if (!dr_html_auth()) {
			dr_json(0, '权限验证超时，请重新执行生成');
		}
		$show_header = $show_dialog = true;
		$this->html = pc_base::load_app_class('html');
		$maxsize = (int)$this->input->get('maxsize');
		$page = max(1, intval($this->input->get('pp')));
		$name = 'category-html-file';
		$name2 = $name.'-'.$page;
		$pcount = $this->cache->get_auth_data($name, $this->siteid);
		if (!$pcount) {
			dr_json(0, '临时缓存数据不存在：'.$name);
		} elseif ($page > $pcount) {
			// 完成
			$this->cache->del_auth_data($name, $this->siteid);
			for ($i = 0; $i < $page; $i ++) {
				$this->cache->del_auth_data($name.'-'.$i, $this->siteid);
			}
			dr_json(-1, '');
		}

		$cache = $this->cache->get_auth_data($name2, $this->siteid);
		if (!$cache) {
			dr_json(0, '临时缓存数据不存在：'.$name2);
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
					$CAT = dr_cat_value($t['catid']);
					if (isset($CAT['modelid']) && $CAT['modelid']) {
						$model_arr = getcache('model', 'commons');
						$sitemodel = $this->cache->get('sitemodel');
						$form_cache = $sitemodel[$model_arr[$CAT['modelid']]['tablename']];
					}
					if (isset($form_cache['setting']['search']['catsync']) && $form_cache['setting']['search']['catsync'] && $CAT['type'] == 0) {
							$class = 'p_error';
							$ok = '<a class="error" href="'.$t['url'].'" target="_blank">此模型开启了搜索集成栏目页，因此栏目无法生成静态</a>';
					} else {
						if (strpos($t['url'], 'index.php?')!==false) {
							$class = 'p_error';
							$ok = '<a class="error" href="'.$t['url'].'" target="_blank">地址【'.$t['url'].'】是动态，请更新内容URL地址为静态模式</a>';
						} else {
							$this->html->category($t['catid'],$t['page'],$maxsize);
							$class = 'ok';
							$ok = '<a class="ok" href="'.$t['url'].'" target="_blank">生成成功</a>';
						}
					}
				}
				$this->cache->set_auth_data($name.'-error', $page); // 设置断点
				$html.= '<p class="todo_p '.$class.'"><label class="rleft">(#'.$t['catid'].')'.$t['catname'].'</label><label class="rright">'.$ok.'</label></p>';
			}
			// 完成
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
		$show_header = $show_dialog = true;
		$this->html = pc_base::load_app_class('html');
		$this->url = pc_base::load_app_class('url');
		$modelid = intval($this->input->get('modelid'));
		$pagesize = intval($this->input->get('pagesize'));
		$number = intval($this->input->get('number'));
		$page = max(1, intval($this->input->get('pp')));
		$fromdate = $this->input->get('fromdate');
		$todate = $this->input->get('todate');
		$fromid = intval($this->input->get('fromid'));
		$toid = intval($this->input->get('toid'));
		$html_file = '';
		if (isset($fromdate) && $fromdate) {
			$html_file .= '-' . strtotime($fromdate.' 00:00:00') . '-' . ($todate ? strtotime($todate.' 23:59:59') : SYS_TIME);
		} elseif (isset($todate) && $todate) {
			$html_file .= '-0-' . strtotime($todate.' 23:59:59');
		}
		$html_file .= $pagesize ? '-'.$pagesize : '';
		$html_file .= $number ? '-'.$number : '';
		$html_file .= $fromid && $toid ? '-'.$fromid.'-'.$toid : '';
		$name = 'show-'.$modelid.'-html-file'.$html_file;
		$name2 = $name.'-data';
		$pcount = $this->cache->get_auth_data($name, $this->siteid);
		if (!$pcount) {
			dr_json(0, '临时缓存数据不存在：'.$name);
		} elseif ($page > $pcount) {
			// 完成
			$this->cache->del_auth_data($name, $this->siteid);
			$this->cache->del_auth_data($name2, $this->siteid);
			dr_json(-1, '');
		}

		$cache = $this->cache->get_auth_data($name2, $this->siteid);
		if (!$cache) {
			dr_json(0, '临时缓存数据不存在：'.$name2);
		} elseif (!$cache['sql']) {
			dr_json(0, '临时数据SQL未生成成功：'.$name2);
		}
		
		if ($cache) {
			$order = 'asc';
			if (isset($cache['number']) && $cache['number']) {
				$order = 'desc';
			}
			$sql = $cache['sql']. ' order by id '.$order.' limit '.($cache['pagesize'] * ($page - 1)).','.$cache['pagesize'];
			$data = $this->db->query($sql);
			if (!$data) {
				// 完成
				$this->cache->del_auth_data($name, $this->siteid);
				$this->cache->del_auth_data($name2, $this->siteid);
				dr_json(-1, '');
			}
			$html = '';
			foreach ($data as $t) {
				$ok = '完成';
				$class = '';
				//设置模型数据表名
				$this->db->set_model(intval(dr_cat_value($t['catid'], 'modelid')));
				$setting = dr_string2array(dr_cat_value($t['catid'], 'setting'));
				if($setting['content_ishtml']) {
					$r = $this->db->get_one(array('id'=>$t['id']));
					if($t['islink']) {
						$class = 'p_error';
						$ok = '<a class="error" href="'.$t['url'].'" target="_blank">转向链接</a>';
					} else {
						//写入文件
						$this->db->table_name = $this->db->table_name.'_data_'.$r['tableid'];
						$r2 = $this->db->get_one(array('id'=>$t['id']));
						if($r2) $r = array_merge($r, $r2);
						$this->db->set_model(intval(dr_cat_value($t['catid'], 'modelid')));
						//判断是否为升级或转换过来的数据
						if($r['upgrade']) {
							$urls[1] = $t['url'];
						} else {
							list($urls) = $this->url->show($t['id'], 0, $t['catid'], $t['inputtime'], $r['prefix']);
						}
						if (strpos($t['url'], 'index.php?')!==false) {
							$class = 'p_error';
							$ok = '<a class="error" href="'.$t['url'].'" target="_blank">地址【'.$t['url'].'】是动态，请更新内容URL地址为静态模式</a>';
						} else {
							$this->html->show($urls[1],$r,0,'edit',$r['upgrade']);
							$class = 'ok';
							if(strpos($t['url'],'http://')!==false || strpos($t['url'],'https://')!==false) {
							} else {
								$t['url'] = substr((string)dr_site_info('domain', dr_cat_value($t['catid'], 'siteid')),0,-1).$t['url'];
							}
							$ok = '<a class="ok" href="'.$t['url'].'" target="_blank">生成成功</a>';
						}
					}
				} else {
					$class = 'p_error';
					$ok = '<a class="error" href="'.$t['url'].'" target="_blank">它是动态模式</a>';
				}
				$this->cache->set_auth_data($name.'-error', $page); // 设置断点
				$html.= '<p class="todo_p '.$class.'"><label class="rleft">(#'.$t['id'].')'.$t['title'].'</label><label class="rright">'.$ok.'</label></p>';
			}
			// 完成
			dr_json($page + 1, $html, array('pcount' => $pcount + 1));
		}
	}
	
	// 统一设置URL规则
	public function public_batch_category() {
		$show_header = $show_dialog = true;
		if(IS_AJAX_POST) {
			$setting = $this->input->post('setting');
			$this->category_db = pc_base::load_model('category_model');
			$row = $this->category_db->select(array('siteid'=>$this->siteid));
			foreach($row as $r) {
				$r['setting'] = dr_string2array($r['setting']);
				if($r['type']!=2) {
					$r['setting']['ishtml'] = $setting['ishtml'];
					if (!$r['type']) {
						$r['setting']['content_ishtml'] = $setting['content_ishtml'];
					} else {
						unset($r['setting']['content_ishtml']);
					}
					if($setting['ishtml']) {
						$r['setting']['category_ruleid'] = $this->input->post('category_html_ruleid');
					} else {
						$r['setting']['category_ruleid'] = $this->input->post('category_php_ruleid');
					}
					if (!$r['type']) {
						if($setting['content_ishtml']) {
							$r['setting']['show_ruleid'] = $this->input->post('show_html_ruleid');
						} else {
							$r['setting']['show_ruleid'] = $this->input->post('show_php_ruleid');
						}
					} else {
						$data['setting']['show_ruleid'] = '';
					}
				}
				$this->category_db->update(array('setting'=>dr_array2string($r['setting'])), array('catid'=>$r['catid']));
			}
			dr_json(1, L('操作成功，请更新内容URL生效'), array('url' => '?m=content&c=create_html&a=public_batch_category&pc_hash='.dr_get_csrf_token()));
		} else {
			include $this->admin_tpl('module_category_html');
		}
	}
	// 按栏目设置URL规则
	public function public_html_index() {
		$show_header = $show_dialog = true;
		$this->cat_config = $this->cache->get_file('category');
		if (!$this->cat_config) {
			$this->cat_config = [
				'sys_field' => ['listorder', 'ismenu', 'disabled', 'iscatpos', 'isleft', 'id', 'typename', 'modelname', 'html'],
				'list_field' => [],
			];
		}
		if (isset($this->cat_config['popen']) && $this->cat_config['popen']) {
			define('SYS_CAT_POPEN', 1);
		}
		list($list, $pcats) = $this->_get_tree_list($this->cat_data(0));
		include $this->admin_tpl('module_content_html');
	}
	/**
	 * 获取菜单数据
	 */
	public function cat_data($pid) {
		$this->category_db = pc_base::load_model('category_model');
		return $this->category_db->select(array('siteid'=>$this->siteid, 'parentid'=>$pid),'*','','listorder ASC,catid ASC');
	}
	// 获取树形结构列表
	private function _get_tree_list($data) {

		$str = "<tr class='\$class'>";
		$str.= "<td style='text-align:center'>\$catid</td>";
		$str.= "<td>\$spacer<a data-container='body' data-placement='right' data-original-title='\$parentdir\$catdir' class='tooltips' target='_blank' href='\$url'>\$catname</a> </td>";
		$str.= "<td style='text-align:center'>\$is_page_html</td>";
		$str.= "<td style='text-align:center'>\$is_show_html</td>";
		$str.= "<td>\$category</td>";
		$str.= "<td>\$show</td>";
		$str.= "</tr>";

		$tree = '';
		$pcats = [];
		foreach($data as $t) {
			if($t['type']==2) continue;
			$t['setting'] = dr_string2array($t['setting']);
			$t['catname'] = isset($this->cat_config['name_size']) && $this->cat_config['name_size'] ? str_cut($t['catname'], intval($this->cat_config['name_size'])) : $t['catname'];
			// 判断是否生成静态
			$ishtml = intval($t['setting']['ishtml']);
			$t['is_page_html'] = '<a href="javascript:;" onclick="dr_cat_ajax_open_close(this, \'?m=content&c=create_html&a=public_html_edit&share=1&catid='.$t['catid'].'&pc_hash=\'+pc_hash, 0);" class="badge badge-'.(!$ishtml ? 'no' : 'yes').'"><i class="fa fa-'.(!$ishtml ? 'times' : 'check').'"></i></a>';
			if ($t['type']==0) {
				$content_ishtml = intval($t['setting']['content_ishtml']);
				$t['is_show_html'] = '<a href="javascript:;" onclick="dr_cat_ajax_open_close(this, \'?m=content&c=create_html&a=public_html_edit&share=0&catid='.$t['catid'].'&pc_hash=\'+pc_hash, 0);" class="badge badge-'.(!$content_ishtml ? 'no' : 'yes').'"><i class="fa fa-'.(!$content_ishtml ? 'times' : 'check').'"></i></a>';
			} else {
				$t['is_show_html'] = '';
			}
			$t['category'] = form::urlrule('content','category',$ishtml,$t['setting']['category_ruleid'],'class="form-control" onchange="dr_save_urlrule(1, \''.$t['catid'].'\', this.value)"');
			if ($t['type']==0) {
				$t['show'] = form::urlrule('content','show',$content_ishtml,$t['setting']['show_ruleid'],'class="form-control" onchange="dr_save_urlrule(0, \''.$t['catid'].'\', this.value)"');
			} else {
				$t['show'] = '';
			}

			$pid = explode(',', $t['arrparentid']);
			$t['topid'] = isset($pid[1]) ? $pid[1] : $t['catid'];

			if ($t['child']) {
				$pcats[] = $t['catid'];
				$t['spacer'] = $this->_get_spacer($t['arrparentid']).'<a href="javascript:dr_tree_data('.$t['catid'].');" class="blue select-cat-'.$t['catid'].'">[+]</a>&nbsp;';
			} else {
				$t['spacer'] = $this->_get_spacer($t['arrparentid']);
			}

			$t['class'] = 'dr_catid_'.$t['catid']. ' dr_pid_'.$t['pid'];
			$arr = explode(',', $t['arrparentid']);
			if ($arr) {
				foreach ($arr as $a) {
					$t['class'].= ' dr_pid_'.$a;
				}
			}
			extract($t);
			eval("\$nstr = \"$str\";");
			$tree.= $nstr;
		}

		return [$tree, $pcats];
	}
	public function public_list_index() {
		$pid = intval($this->input->get('pid'));
		list($b, $pcats) = $this->_get_tree_list($this->cat_data($pid));
		dr_json(1, $b);
	}
	// 替换空格填充符号
	private function _get_spacer($str) {
		$rt = '';
		$num = substr_count((string)$str, ',') * 2;
		if ($num) {
			for ($i = 0; $i < $num; $i ++) {
				$rt.= '&nbsp;&nbsp;&nbsp;';
			}
		}
		return $rt;
	}
	public function public_html_edit() {
		$show_header = $show_dialog = true;
		$share = intval($this->input->get('share'));
		$catid = intval($this->input->get('catid'));
		$this->category_db = pc_base::load_model('category_model');
		$this->urlrule_db = pc_base::load_model('urlrule_model');
		$row = $this->category_db->get_one(array('catid'=>$catid));
		if (!$row) {
			dr_json(0, L('栏目数据不存在'));
		}
		$row['setting'] = dr_string2array($row['setting']);
		if ($share) {
			$html = (int)$row['setting']['ishtml'];
			$v = $html ? 0 : 1;
			$row['setting']['ishtml'] = $v;
			$categoryrules = $this->urlrule_db->select(array('module'=>'content','file'=>'category','ishtml'=>$v));
			if (!$categoryurlruleid) {$onecategoryrules = reset($categoryrules);$categoryurlruleid = $onecategoryrules['urlruleid'];}
			$row['setting']['category_ruleid'] = $categoryurlruleid;
		} else {
			$html = (int)$row['setting']['content_ishtml'];
			$v = $html ? 0 : 1;
			$row['setting']['content_ishtml'] = $v;
			$showrules = $this->urlrule_db->select(array('module'=>'content','file'=>'show','ishtml'=>$v));
			if (!$showurlruleid) {$oneshowrules = reset($showrules);$showurlruleid = $oneshowrules['urlruleid'];}
			$row['setting']['show_ruleid'] = $showurlruleid;
		}
		$this->category_db->update(array('setting' => dr_array2string($row['setting'])),array('catid'=>$catid));
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->cache_api->cache('category');
		dr_json(1, L($v ? '静态模式' : '动态模式'), array('value' => $v, 'share' => 1));
	}
	public function public_index_edit() {
		$show_header = $show_dialog = true;
		$share = intval($this->input->get('share'));
		$this->site_db = pc_base::load_model('site_model');
		$row = $this->site_db->get_one(array('siteid'=>$this->siteid));
		if (!$row) {
			dr_json(0, L('站点数据不存在'));
		}
		if ($share) {
			$html = (int)$row['ishtml'];
			$v = $html ? 0 : 1;
			$row['ishtml'] = $v;
			$this->site_db->update(array('ishtml' => $row['ishtml']),array('siteid'=>$this->siteid));
		} else {
			if ($row['mobilemode'] == -1) {
				dr_json(0, L('关闭手机端'));
			}
			$html = (int)$row['mobilehtml'];
			$v = $html ? 0 : 1;
			$row['mobilehtml'] = $v;
			$this->site_db->update(array('mobilehtml' => $row['mobilehtml']),array('siteid'=>$this->siteid));
		}
		$this->cache_site = pc_base::load_app_class('sites', 'admin');
		$this->cache_site->set_cache();
		dr_json(1, L($v ? ($share ? '静态模式' : '移动端与PC端URL同步') : '动态模式'), array('value' => $v, 'share' => 1));
	}
	public function public_rule_edit() {
		$show_header = $show_dialog = true;
		$share = intval($this->input->get('share'));
		$catid = intval($this->input->get('catid'));
		$value = $this->input->get('value');
		$this->category_db = pc_base::load_model('category_model');
		$data = $this->category_db->get_one(array('catid'=>$catid));
		if (!$data) {
			dr_json(0, L('栏目#'.$id.'不存在'));
		}
		$data['setting'] = dr_string2array($data['setting']);
		if ($share) {
			$data['setting']['category_ruleid'] = $value;
		} else {
			$data['setting']['show_ruleid'] = $value;
		}
		$this->category_db->update(array('setting' => dr_array2string($data['setting'])),array('catid'=>$catid));
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->cache_api->cache('category');
		dr_json(1, L('操作成功，更新缓存生效'));
	}
	public function public_sync_index() {
		$this->category_db = pc_base::load_model('category_model');
		$this->urlrule_db = pc_base::load_model('urlrule_model');
		$url = '?m=content&c=create_html&a=public_sync_index';
		$page = intval($this->input->get('page'));
		$categoryrules = $this->urlrule_db->select(array('module'=>'content','file'=>'category','ishtml'=>1));
		if (!$categoryurlruleid) {$onecategoryrules = reset($categoryrules);$categoryurlruleid = $onecategoryrules['urlruleid'];}
		if (!$page) {
			// 计算数量
			$total = $this->category_db->count(array('siteid'=>$this->siteid));
			if (!$total) {
				html_msg(0, L('无可用栏目更新'));
			}
			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page=1');
		}

		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$tpage = ceil($total / $psize); // 总页数
		// 更新完成
		if ($page > $tpage) {
			$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
			$this->cache_api->cache('category');
			html_msg(1, L('更新完成'));
		}

		$category = $this->category_db->listinfo(array('siteid'=>$this->siteid), 'catid DESC', $page, $psize);
		if ($category) {
			foreach ($category as $data) {
				if ($data['type'] != 2) {
					$data['setting'] = dr_string2array($data['setting']);
					$data['setting']['ishtml'] = 1;
					$data['setting']['category_ruleid'] = $categoryurlruleid;
					$this->category_db->update(array('setting' => dr_array2string($data['setting'])),array('catid'=>$data['catid']));
				}
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	public function public_sync2_index() {
		$this->category_db = pc_base::load_model('category_model');
		$this->urlrule_db = pc_base::load_model('urlrule_model');
		$url = '?m=content&c=create_html&a=public_sync2_index';
		$page = intval($this->input->get('page'));
		$categoryrules = $this->urlrule_db->select(array('module'=>'content','file'=>'category','ishtml'=>0));
		if (!$categoryurlruleid) {$onecategoryrules = reset($categoryrules);$categoryurlruleid = $onecategoryrules['urlruleid'];}
		if (!$page) {
			// 计算数量
			$total = $this->category_db->count(array('siteid'=>$this->siteid));
			if (!$total) {
				html_msg(0, L('无可用栏目更新'));
			}
			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page=1');
		}

		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$tpage = ceil($total / $psize); // 总页数
		// 更新完成
		if ($page > $tpage) {
			$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
			$this->cache_api->cache('category');
			html_msg(1, L('更新完成'));
		}

		$category = $this->category_db->listinfo(array('siteid'=>$this->siteid), 'catid DESC', $page, $psize);
		if ($category) {
			foreach ($category as $data) {
				if ($data['type'] != 2) {
					$data['setting'] = dr_string2array($data['setting']);
					$data['setting']['ishtml'] = 0;
					$data['setting']['category_ruleid'] = $categoryurlruleid;
					$this->category_db->update(array('setting' => dr_array2string($data['setting'])),array('catid'=>$data['catid']));
				}
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	public function public_csync_index() {
		$this->category_db = pc_base::load_model('category_model');
		$this->urlrule_db = pc_base::load_model('urlrule_model');
		$url = '?m=content&c=create_html&a=public_csync_index';
		$page = intval($this->input->get('page'));
		$showrules = $this->urlrule_db->select(array('module'=>'content','file'=>'show','ishtml'=>1));
		if (!$showurlruleid) {$oneshowrules = reset($showrules);$showurlruleid = $oneshowrules['urlruleid'];}
		if (!$page) {
			// 计算数量
			$total = $this->category_db->count(array('siteid'=>$this->siteid));
			if (!$total) {
				html_msg(0, L('无可用栏目更新'));
			}
			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page=1');
		}

		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$tpage = ceil($total / $psize); // 总页数
		// 更新完成
		if ($page > $tpage) {
			$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
			$this->cache_api->cache('category');
			html_msg(1, L('更新完成'));
		}

		$category = $this->category_db->listinfo(array('siteid'=>$this->siteid), 'catid DESC', $page, $psize);
		if ($category) {
			foreach ($category as $data) {
				if ($data['type'] != 2) {
					$data['setting'] = dr_string2array($data['setting']);
					if (!$data['type']) {
						$data['setting']['content_ishtml'] = 1;
					} else {
						unset($data['setting']['content_ishtml']);
					}
					if (!$data['type']) {
						$data['setting']['show_ruleid'] = $showurlruleid;
					} else {
						$data['setting']['show_ruleid'] = '';
					}
					$this->category_db->update(array('setting' => dr_array2string($data['setting'])),array('catid'=>$data['catid']));
				}
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	public function public_csync2_index() {
		$this->category_db = pc_base::load_model('category_model');
		$this->urlrule_db = pc_base::load_model('urlrule_model');
		$url = '?m=content&c=create_html&a=public_csync2_index';
		$page = intval($this->input->get('page'));
		$showrules = $this->urlrule_db->select(array('module'=>'content','file'=>'show','ishtml'=>0));
		if (!$showurlruleid) {$oneshowrules = reset($showrules);$showurlruleid = $oneshowrules['urlruleid'];}
		if (!$page) {
			// 计算数量
			$total = $this->category_db->count(array('siteid'=>$this->siteid));
			if (!$total) {
				html_msg(0, L('无可用栏目更新'));
			}
			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page=1');
		}

		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$tpage = ceil($total / $psize); // 总页数
		// 更新完成
		if ($page > $tpage) {
			$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
			$this->cache_api->cache('category');
			html_msg(1, L('更新完成'));
		}

		$category = $this->category_db->listinfo(array('siteid'=>$this->siteid), 'catid DESC', $page, $psize);
		if ($category) {
			foreach ($category as $data) {
				if ($data['type'] != 2) {
					$data['setting'] = dr_string2array($data['setting']);
					if (!$data['type']) {
						$data['setting']['content_ishtml'] = 0;
					} else {
						unset($data['setting']['content_ishtml']);
					}
					if (!$data['type']) {
						$data['setting']['show_ruleid'] = $showurlruleid;
					} else {
						$data['setting']['show_ruleid'] = '';
					}
					$this->category_db->update(array('setting' => dr_array2string($data['setting'])),array('catid'=>$data['catid']));
				}
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 提取tag
	public function public_tag_index() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$tree->init($categorys);
		$string = $tree->get_tree(0, $str);
		$todo_url = '?m=content&c=create_html&a=public_tag_edit&modelid='.$modelid;
		include $this->admin_tpl('module_content_tag');
	}
	// 提取tag
	public function public_tag_edit() {
		$show_header = $show_dialog = true;
		$this->keyword_db = pc_base::load_model('keyword_model');
		$this->keyword_data_db = pc_base::load_model('keyword_data_model');
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 10; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$where = 'status = 99';
		$catid = $this->input->get('catid');
		$catid = dr_string2array($catid);

		$url = '?m=content&c=create_html&a=public_tag_edit&modelid='.$modelid;

		// 获取栏目
		if ($catid) {
			$cat = array();
			foreach ($catid as $i) {
				if ($i) {
					$cat[] = intval($i);
					$icat = dr_cat_value($i);
					if ($icat['child']) {
						$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
					}
					$url.= '&catid[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		}

		$keyword = $this->input->get('keyword');
		$keyword && $where.= ' AND keywords=""';
		$start = $keyword ? 1 : $page;
		$url.= '&keyword='.$keyword;

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				html_msg(0, L('无可用内容更新'));
			}

			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1), L('在使用网络分词接口时可能会很慢'));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('更新完成'));
		}

		$keywords = array();
		$data = $this->db->listinfo($where, 'id DESC', $start, $psize);
		// 更新完成
		if (!$data) {
			html_msg(1, L('更新完成'));
		}
		foreach ($data as $t) {
			$tag = dr_get_keywords($t['title'].' '.$t['description']);
			if ($tag) {
				$this->db->update(array('keywords' => $tag), array('id' => $t['id']));
			}
			if ($t['keywords']) {
				$keywords = explode(',', $t['keywords']);
				if (is_array($keywords) && !empty($keywords)) {
					foreach ($keywords as $v) {
						$v = str_replace(array('//','#','.'),' ',$v);
						if ($v) {
							if (!$r = $this->keyword_db->get_one(array('keyword'=>$v, 'siteid'=>$this->siteid))) {
								$tagid = $this->keyword_db->insert(array('keyword'=>$v, 'siteid'=>$this->siteid, 'pinyin'=>pc_base::load_sys_class('pinyin')->result($v), 'videonum'=>1), true);
							} else {
								$tagid = $r['id'];
							}
							$contentid = $t['id'].'-'.$modelid;
							if (!$this->keyword_data_db->get_one(array('tagid'=>$tagid, 'siteid'=>$this->siteid, 'contentid'=>$contentid))) {
								$this->keyword_db->update(array('videonum'=>'+=1'), array('id'=>$r['id']));
								$this->keyword_data_db->insert(array('tagid'=>$tagid, 'siteid'=>$this->siteid, 'contentid'=>$contentid));
							}
						}
						unset($contentid, $tagid);
					}
				}
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1), L('在使用网络分词接口时可能会很慢'));
	}
	// 提取缩略图
	public function public_thumb_index() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$tree->init($categorys);
		$string = $tree->get_tree(0, $str);
		$todo_url = '?m=content&c=create_html&a=public_thumb_edit&modelid='.$modelid;
		include $this->admin_tpl('module_content_thumb');
	}
	// 提取缩略图
	public function public_thumb_edit() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$where = 'status = 99';
		$catid = $this->input->get('catid');
		$catid = dr_string2array($catid);

		$url = '?m=content&c=create_html&a=public_thumb_edit&modelid='.$modelid;

		// 获取栏目
		if ($catid) {
			$cat = array();
			foreach ($catid as $i) {
				if ($i) {
					$cat[] = intval($i);
					$icat = dr_cat_value($i);
					if ($icat['child']) {
						$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
					}
					$url.= '&catid[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		}

		$thumb = $this->input->get('thumb');
		$thumb && $where.= ' AND thumb=""';
		$start = $thumb ? 1 : $page;
		$url.= '&thumb='.$thumb;

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				html_msg(0, L('无可用内容更新'));
			}

			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', $start, $psize);
		// 更新完成
		if (!$data) {
			html_msg(1, L('更新完成'));
		}
		foreach ($data as $row) {
			$content = get_content($modelid, $row['id']);
			$this->db->set_model($modelid);
			if ($row && $content && preg_match("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|png|webp))\\2/i", code2html($content), $m)) {
				$this->db->update(array('thumb' => str_replace(array('"', '\''), '', $m[3])), array('id' => $row['id']));
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 缩略图本地化
	public function public_xthumb_index() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$tree->init($categorys);
		$string = $tree->get_tree(0, $str);
		$todo_url = '?m=content&c=create_html&a=public_xthumb_edit&modelid='.$modelid;
		include $this->admin_tpl('module_content_xthumb');
	}
	// 缩略图本地化
	public function public_xthumb_edit() {
		$show_header = $show_dialog = true;
		$this->form = getcache('model', 'commons');
		$this->sitemodel = $this->cache->get('sitemodel');
		$userid = param::get_session('userid') ? param::get_session('userid') : (param::get_cookie('userid') ? param::get_cookie('userid') : param::get_cookie('_userid'));
		$modelid = intval($this->input->get('modelid'));
		$this->form_cache = $this->sitemodel[$this->form[$modelid]['tablename']];
		if ($this->form_cache['field']['thumb']['formtype'] != 'image') {
			html_msg(0, L('thumb字段是'.$this->form_cache['field']['thumb']['formtype'].'类型，只对image类型有效'));
		}
		$page = (int)$this->input->get('page');
		$psize = 10; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$where = 'status = 99';
		$catid = $this->input->get('catid');
		$catid = dr_string2array($catid);

		$url = '?m=content&c=create_html&a=public_xthumb_edit&modelid='.$modelid;

		// 获取栏目
		if ($catid) {
			$cat = array();
			foreach ($catid as $i) {
				if ($i) {
					$cat[] = intval($i);
					$icat = dr_cat_value($i);
					if ($icat['child']) {
						$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
					}
					$url.= '&catid[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		}

		$where.= ' AND thumb like "http%"';

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				html_msg(0, L('无可用内容更新'));
			}

			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', 1, $psize);
		// 更新完成
		if (!$data) {
			html_msg(1, L('更新完成'));
		}
		foreach ($data as $row) {
			$ext = get_image_ext($row['thumb']);
			if (!$ext) {
				continue;
			}
			$upload = new upload('content',$row['catid'],dr_cat_value($row['catid'], 'siteid'));
			$upload->set_userid($userid);
			// 下载远程文件
			$rt = $upload->down_file([
				'url' => $row['thumb'],
				'timeout' => 5,
				'watermark' => intval($this->form_cache['field']['thumb']['setting']['watermark']),
				'attachment' => $upload->get_attach_info(intval($this->form_cache['field']['thumb']['setting']['attachment']), $this->form_cache['field']['thumb']['setting']['image_reduce']),
				'file_ext' => $ext,
			]);
			$attachments = array();
			if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
				$att_db = pc_base::load_model('attachment_model');
				$att = $att_db->get_one(array('userid'=>intval($userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
				if ($att) {
					$attachments = dr_return_data($att['aid'], 'ok');
					// 删除现有附件
					// 开始删除文件
					$storage = new storage('content',$row['catid'],dr_cat_value($row['catid'], 'siteid'));
					$storage->delete($upload->get_attach_info((int)$this->form_cache['field']['thumb']['setting']['attachment']), $rt['data']['file']);
					$rt['data'] = get_attachment($att['aid']);
					if ($rt['data']) {
						$rt['data']['name'] = $rt['data']['filename'];
						$rt['data']['size'] = $rt['data']['filesize'];
					}
				}
			}
			// 附件归档
			if ($rt['code'] && !$attachments) {
				$rt['data']['isadmin'] = 1;
				$attachments = $upload->save_data($rt['data'], $this->db->table_name.'-'.$row['id']);
			}
			if ($rt['code']) {
				if ($attachments['code']) {
					upload_json($attachments['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
					$this->db->update(array('thumb' => $attachments['code']), array('id' => $row['id']));
					//更新附件状态
					if (SYS_ATTACHMENT_STAT) {
						$attachment_db = pc_base::load_model('attachment_model');
						$attachment_db->api_update('','c-'.$row['catid'].'-'.$row['id'],2);
					}
				}
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 提取描述信息
	public function public_desc_index() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$tree->init($categorys);
		$string = $tree->get_tree(0, $str);
		$todo_url = '?m=content&c=create_html&a=public_desc_edit&modelid='.$modelid;
		include $this->admin_tpl('module_content_desc');
	}
	// 提取描述信息
	public function public_desc_edit() {
		$show_header = $show_dialog = true;
		$this->form = getcache('model', 'commons');
		$this->sitemodel = $this->cache->get('sitemodel');
		$modelid = intval($this->input->get('modelid'));
		$this->form_cache = $this->sitemodel[$this->form[$modelid]['tablename']];
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$where = 'status = 99';
		$catid = $this->input->get('catid');
		$catid = dr_string2array($catid);

		$url = '?m=content&c=create_html&a=public_desc_edit&modelid='.$modelid;

		// 获取栏目
		if ($catid) {
			$cat = array();
			foreach ($catid as $i) {
				if ($i) {
					$cat[] = intval($i);
					$icat = dr_cat_value($i);
					if ($icat['child']) {
						$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
					}
					$url.= '&catid[]='.intval($i);
				}
			}
			$cat && $where.= ' AND catid IN ('.implode(',', $cat).')';
		}

		$nums = max(1, $this->input->get('nums'));
		$keyword = $this->input->get('keyword');
		$keyword && $where.= ' AND description=""';
		$start = $keyword ? 1 : $page;
		$url.= '&nums='.$nums;
		$url.= '&keyword='.$keyword;

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				html_msg(0, L('无可用内容更新'));
			}

			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', $start, $psize);
		// 更新完成
		if (!$data) {
			html_msg(1, L('更新完成'));
		}
		foreach ($data as $row) {
			$content = code2html(get_content($modelid, $row['id']));
			if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
				$content = str_replace(' ', '', $content);
			}
			$this->db->set_model($modelid);
			if ($row && $content && dr_get_description($content, $nums)) {
				$this->db->update(array('description' => dr_get_description($content, $nums)), array('id' => $row['id']));
			} elseif ($row['title']) {
				if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
					$this->db->update(array('description' => dr_get_description(code2html(str_replace(' ', '', $row['title'])), $nums)), array('id' => $row['id']));
				} else {
					$this->db->update(array('description' => dr_get_description(code2html($row['title']), $nums)), array('id' => $row['id']));
				}
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 提取变更栏目
	public function public_cat_index() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$tree->init($categorys);
		$string = $tree->get_tree(0, $str);
		$categorys_post = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$r['disabled'] = $r['child'] ? 'disabled' : '';
				$categorys_post[$catid] = $r;
			}
		}
		$str_post = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
		$tree->init($categorys_post);
		$select_post = $tree->get_tree(0, $str_post);
		$todo_url = '?m=content&c=create_html&a=public_cat_edit&modelid='.$modelid;
		include $this->admin_tpl('module_content_cat');
	}
	// 提取变更栏目
	public function public_cat_edit() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 100; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);

		$toid = (int)$this->input->get('toid');
		if (!$toid) {
			html_msg(0, L('目标栏目必须选择'));
		}

		$url = '?m=content&c=create_html&a=public_cat_edit&modelid='.$modelid;
		$url.= '&toid='.$toid;
		$where = array();

		// 获取栏目
		$catid = $this->input->get('catid');
		$catid = dr_string2array($catid);
		if ($catid) {
			$cat = array();
			foreach ($catid as $i) {
				if ($i) {
					$cat[] = intval($i);
					$icat = dr_cat_value($i);
					if ($icat['child']) {
						$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
					}
					$url.= '&catid[]='.intval($i);
				}
			}
			$cat && $where[] = ' catid IN ('.implode(',', $cat).')';
		}
		$sql = $this->input->get('sql', true);
		if ($sql) {
			// 防范sql注入后期需要加强
			foreach (array('outfile', 'dumpfile', '.php', 'union', ';') as $kw) {
				if (strpos(strtolower($sql), $kw) !== false) {
					html_msg(0, L('存在非法SQL关键词：'.$kw));
				}
			}
			$where[] = addslashes($sql);
			$url.= '&sql='.$sql;
		}

		if ($where) {
			$where = implode(' AND ', $where);
		} else {
			$where = '';
		}
		
		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				html_msg(0, L('无可用内容更新'));
			}

			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('更新完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', 1, $psize);
		// 更新完成
		if (!$data) {
			html_msg(1, L('更新完成'));
		}
		foreach ($data as $row) {
			if ($row) {
				$this->db->update(array('catid' => $toid), array('id' => $row['id']));
			}
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
	}
	// 批量删除
	public function public_del_index() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $catid=>$r) {
				if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$categorys[$catid] = $r;
			}
		}
		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$tree->init($categorys);
		$string = $tree->get_tree(0, $str);
		$todo_url = '?m=content&c=create_html&a=public_del_edit&modelid='.$modelid;
		include $this->admin_tpl('module_content_del');
	}
	// 批量删除
	public function public_del_edit() {
		$show_header = $show_dialog = true;
		$modelid = intval($this->input->get('modelid'));
		$page = (int)$this->input->get('page');
		$psize = 10; // 每页处理的数量
		$total = (int)$this->input->get('total');
		$this->db->set_model($modelid);
		$this->hits_db = pc_base::load_model('hits_model');
		$this->queue = pc_base::load_model('queue_model');
		$html_root = SYS_HTML_ROOT;
		//附件初始化
		$attachment = pc_base::load_model('attachment_model');
		$this->content_check_db = pc_base::load_model('content_check_model');
		$this->position_data_db = pc_base::load_model('position_data_model');
		$this->search_db = pc_base::load_model('search_model');
		$this->comment = pc_base::load_app_class('comment', 'comment');
		$search_model = getcache('search_model_'.$this->siteid,'search');
		$typeid = $search_model[$modelid]['typeid'];
		$this->url = pc_base::load_app_class('url', 'content');

		$where = array();
		$catid = $this->input->get('catid');
		$catid = dr_string2array($catid);

		$url = '?m=content&c=create_html&a=public_del_edit&modelid='.$modelid;

		// 获取栏目
		if ($catid) {
			$cat = array();
			foreach ($catid as $i) {
				if ($i) {
					$cat[] = intval($i);
					$icat = dr_cat_value($i);
					if ($icat['child']) {
						$cat = dr_array2array($cat, explode(',', $icat['arrchildid']));
					}
					$url.= '&catid[]='.intval($i);
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

		$sql = $this->input->get('sql', true);
		if ($sql) {
			// 防范sql注入后期需要加强
			foreach (array('outfile', 'dumpfile', '.php', 'union', ';') as $kw) {
				if (strpos(strtolower($sql), $kw) !== false) {
					html_msg(0, L('存在非法SQL关键词：'.$kw));
				}
			}
			$where[] = $this->db->escape($sql);
			$url.= '&sql='.$sql;
		}

		if (!$where) {
			html_msg(0, L('没有设置条件'));
		}

		$where = implode(' AND ', $where);

		if (!$page) {
			// 计算数量
			$total = $this->db->count($where);
			if (!$total) {
				html_msg(0, L('无可用内容更新'));
			}

			html_msg(1, L('正在删除中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('删除'.$total.'条数据完成'));
		}

		$data = $this->db->listinfo($where, 'id DESC', 1, $psize);
		foreach ($data as $row) {
			if ($row) {
				$this->cache->clear('module_'.$modelid.'_show_id_'.$row['id']);
				$sethtml = dr_cat_value($row['catid'], 'sethtml');
				if($sethtml) $html_root = '';
				$setting = dr_string2array(dr_cat_value($row['catid'], 'setting'));
				if($setting['content_ishtml'] && !$row['islink']) {
					$content_info = $this->db->get_content($row['catid'],$row['id']);
					list($urls) = $this->url->show($row['id'], 0, $row['catid'], $row['inputtime'], $content_info['prefix']);
					$fileurl = $urls[1];
					if($this->siteid != 1) {
						$fileurl = $html_root.'/'.dr_site_info('dirname', $this->siteid).$fileurl;
					}
					$mobilefileurl = SYS_MOBILE_ROOT.$fileurl;
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
					if(dr_site_info('mobilehtml', $this->siteid)==1) {
						foreach ($mobilefilelist as $mobiledelfile) {
							$mobilelasttext = strrchr($mobiledelfile,'.');
							if(!in_array($mobilelasttext, array('.htm','.html','.shtml'))) continue;
							@unlink($mobiledelfile);
						}
					}
				}
				//删除内容
				$this->db->delete_content($row['id'],$row['catid']);
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

		html_msg(1, L('正在删除中【'.$tpage.'/'.$page.'】...'), $url.'&total='.$total.'&page='.($page+1));
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
	// 内容维护替换
	public function public_replace_index() {
		$bm = $this->input->post('bm');
		if (!$bm) {
			dr_json(0, L('表名不能为空'));
		}
		$this->db->table_name = $bm;
		$tables = array();
		if (strpos($bm, '[tableid]')) {
			for ($i = 0;; $i ++) {
				$table = str_replace('[tableid]', $i, $bm);
				$this->db->query("SHOW TABLES LIKE '".$table."'");
				$table_exists = $this->db->fetch_array();
				if (!$table_exists) {
					break;
				}
				$tables[$table] = $this->_get_field($table);
			}
		} else {
			$tables[$bm] = $this->_get_field($bm);
		}

		$t1 = $this->input->post('t1');
		$t2 = $this->input->post('t2');
		$fd = dr_safe_replace($this->input->post('fd'));

		if (!$fd) {
			dr_json(0, L('待替换字段必须填写'));
		} elseif (!$t1) {
			dr_json(0, L('被替换内容必须填写'));
		} elseif (!$tables) {
			dr_json(0, L('表名称必须填写'));
		}

		$count = 0;
		$replace = '`'.$fd.'`=REPLACE(`'.$fd.'`, \''.$this->db->escape($t1).'\', \''.$this->db->escape($t2).'\')';

		foreach ($tables as $table => $fields) {
			$this->db->table_name = $table;
			if (!dr_in_array($fd, $fields)) {
				dr_json(0, L('表['.$table.']字段['.$fd.']不存在'));
			} elseif ($fd == $this->db->get_primary($table)) {
				dr_json(0, $this->db->get_primary($table).L('主键不支持替换'));
			}
			$this->db->query('UPDATE `'.$table.'` SET '.$replace);
			$count = $this->db->affected_rows();
		}

		if ($count < 0) {
			dr_json(0, L('执行错误'));
		}

		dr_json(1, L('本次替换'.$count.'条数据'), 'UPDATE `'.$table.'` SET '.$replace);
	}
	// 内容批量修改
	public function public_all_edit() {
		$bm = $this->input->post('bm');
		if (!$bm) {
			dr_json(0, L('表名不能为空'));
		}
		$tables = array();
		if (strpos($bm, '[tableid]')) {
			for ($i = 0;; $i ++) {
				$table = str_replace('[tableid]', $i, $bm);
				$this->db->query("SHOW TABLES LIKE '".$table."'");
				$table_exists = $this->db->fetch_array();
				if (!$table_exists) {
					break;
				}
				$tables[$table] = $this->_get_field($table);
			}
		} else {
			$tables[$bm] = $this->_get_field($bm);
		}

		$t1 = $this->input->post('t1');
		$t2 = $this->input->post('t2');
		$ms = (int)$this->input->post('ms');
		$fd = dr_safe_replace($this->input->post('fd'));

		if (!$fd) {
			dr_json(0, L('待修改字段必须填写'));
		} elseif (!$tables) {
			dr_json(0, L('表名称必须填写'));
		}

		$count = 0;

		$where = '';
		if ($t1) {
			// 防范sql注入后期需要加强
			foreach (array('outfile', 'dumpfile', '.php', 'union', ';') as $kw) {
				if (strpos(strtolower($t1), $kw) !== false) {
					dr_json(0, L('存在非法SQL关键词：'.$kw));
				}
			}
			$where = ' WHERE '.$this->db->escape($t1);
		}

		if ($ms == 1) {
			// 之前
			$replace = '`'.$fd.'`=CONCAT(\''.$this->db->escape($t2).'\', `'.$fd.'`)';
		} elseif ($ms == 2) {
			// 之后
			$replace = '`'.$fd.'`=CONCAT(`'.$fd.'`, \''.$this->db->escape($t2).'\')';
		} else {
			// 替换
			$replace = '`'.$fd.'`=\''.$this->db->escape($t2).'\'';
		}


		foreach ($tables as $table => $fields) {
			$this->db->table_name = $table;
			if (!dr_in_array($fd, $fields)) {
				dr_json(0, L('表['.$table.']字段['.$fd.']不存在'));
			} elseif ($fd == $this->db->get_primary($table)) {
				dr_json(0, $this->db->get_primary($table).L('主键不支持替换'));
			}
			$this->db->query('UPDATE `'.$table.'` SET '.$replace . $where);
			$count = $this->db->affected_rows();
		}

		if ($count < 0) {
			dr_json(0, L('执行错误'));
		}

		dr_json(1, L('本次替换'.$count.'条数据'), 'UPDATE `'.$table.'` SET '.$replace . $where);
	}
	// 全库
	public function public_dball_edit() {
		$key = (int)$this->input->get('key');
		$page = (int)$this->input->get('page');
		$tpage = (int)$this->input->get('tpage');
		$prefix = $this->db->db_tablepre;
		$name = 'dball_edit'.param::get_session('userid').'_';

		$url = '?m=content&c=create_html&a=public_dball_edit';
		if ($key) {
			$url .= '&key='.$key;
		}
		$sqls = $this->cache->get_auth_data($name.$key);

		if (!$page) {
			// 计算数量
			$t1 = $this->input->get('t1');
			$t2 = $this->input->get('t2');
			if (!$t1) {
				dr_json(0, L('替换内容不能为空'));
			}
			$data = [];
			$module = getcache('model', 'commons');
			if ($module) {
				foreach ($module as $m) {
					if($m['siteid']!=$this->siteid) continue;
					$mod = getcache('model_field_'.$m['modelid'], 'model');
					if ($mod) {
						$table = $prefix.$m['tablename'];
						foreach ($mod as $t) {
							if ($t['issystem']) {
								$this->_is_rp_field($t, $table) && $data[] = [ $table, $t['field'] ];
							} else {
								for ($i = 0;; $i ++) {
									$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$i."'");
									$table_exists = $this->db->fetch_array();
									if (!$table_exists) {
										break;
									}
									$this->_is_rp_field($t, $table.'_data_'.$i) && $data[] = [ $table.'_data_'.$i, $t['field'] ];
								}
							}
						}
					}
				}
			}
			$mod = getcache('model_field_0', 'model');
			if ($mod) {
				$table = $prefix.'site';
				foreach ($mod as $t) {
					if ($t['issystem']) {
						$this->_is_rp_field($t, $table) && $data[] = [ $table, $t['field'] ];
					}
				}
			}
			$mod = getcache('model_field_-1', 'model');
			if ($mod) {
				$table = $prefix.'category';
				foreach ($mod as $t) {
					if ($t['issystem']) {
						$this->_is_rp_field($t, $table) && $data[] = [ $table, $t['field'] ];
					}
				}
			}
			$mod = getcache('model_field_-2', 'model');
			if ($mod) {
				$table = $prefix.'page';
				foreach ($mod as $t) {
					if ($t['issystem']) {
						$this->_is_rp_field($t, $table) && $data[] = [ $table, $t['field'] ];
					}
				}
			}

			$cache = array_chunk($data, 30);
			foreach ($cache as $i => $t) {
				$this->cache->set_auth_data($name.'-'.($i+1), $t);
			}

			$this->cache->set_auth_data($name, [$t1, $t2]);

			html_msg(1, L('正在执行中...'), $url.'&cache='.$name.'&page=1&tpage='.dr_count($cache));
		}

		$value = $this->cache->get_auth_data($name);
		$replace = $this->cache->get_auth_data($name.'-'.$page);
		if (!$value) {
			html_msg(0, L('临时数据读取失败'));
		} elseif (!isset($replace[$page+1])) {
			html_msg(1, L('替换完成').'<br><br><pre><script type="text/html" style="display:block">'.($sqls).'</script></pre>');
		}

		// 更新完成
		if ($page > $tpage) {
			html_msg(1, L('替换完成').'<br><br><pre><script type="text/html" style="display:block">'.($sqls).'</script></pre>');
		}

		foreach ($replace as $t) {
			$sql = 'update `'.$t[0].'` set `'.$t[1].'`=REPLACE(`'.$t[1].'`, \''.$this->db->escape($value[0]).'\', \''.$this->db->escape($value[1]).'\')';
			$this->db->query($sql);
			$sqls = $sqls.PHP_EOL.$sql.';';
			$this->cache->set_auth_data($name.$key, $sqls);
		}

		html_msg(1, L('正在执行中【'.$tpage.'/'.$page.'】...'), $url.'&tpage='.$tpage.'&page='.($page+1));
	}
	// 检测字段是否存在
	private function _is_rp_field($f, $table) {
		$this->db->table_name = $table;
		if (in_array($f['formtype'], array(
			'image',
			'images',
			'file',
			'downfile',
			'downfiles',
			'editor'
			))) {
			if ($this->db->field_exists($f['field'])) {
				return 1;
			}
		}

		return 0;
	}
	// 联动加载字段
	public function public_field_index() {
		$table = dr_safe_replace($this->input->get('table'));
		$table = str_replace('_data_[tableid]', '_data_0', $table);
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
			if ($t['Key'] != 'PRI') {
				$msg.= '<option value="'.$t['Field'].'">'.$t['Field'].($t['Comment'] ? '（'.$t['Comment'].'）' : '').'</option>';
			}
		}
		$msg.= '</select>';

		dr_json(1, $msg);
	}

	public function public_test_index() {
		$kw = 'iphone手机出现“白苹果”原因及解决办法，用苹果手机的可以看下';
		$rt = dr_get_keywords($kw);
		if (!$rt) {
			exit('测试失败：无法提取到关键词');
		}
		exit('原文：'.$kw.'<br>测试成功：'.$rt);
	}
}
?>