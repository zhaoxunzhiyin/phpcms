<?php
@set_time_limit(0);
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);

class create_all_html extends admin {
	private $input,$cache,$db,$site_db,$html,$url,$cache_site;
	public $siteid,$categorys;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
		// 生成权限文件
		if (!dr_html_auth(1)) {
			dr_admin_msg(0, L('/cache/html/ 无法写入文件'));
		}
		// 不是超级管理员
		/*if (!cleck_admin(param::get_session('roleid'))) {
			dr_admin_msg(0,L('需要超级管理员账号操作'));
		}*/
	}

	/**
	* 一键生成全站
	*/
	public function all_update(){
		$show_header = $show_dialog = true;
		$this->site_db = pc_base::load_model('site_model');
		$data = $this->site_db->get_one(array('siteid'=>$this->siteid));
		$ishtml = $data['ishtml'];
		$mobilehtml = $data['mobilehtml'];
		include $this->admin_tpl('create_html_all');
	}
	/**
	* 生成内容页
	*/
	public function show() {
		$show_header = $show_dialog = true;
		$modelid = $this->input->get('modelid');
		$catids = $this->input->get('catids');
		if ($catids && is_array($catids)) {
			$catids = implode(',', $catids);
		}
		$nmid = ''; // 下一个模块
		$module = getcache('model', 'commons');
		if ($module) {
			$is_find = 0;
			foreach ($module as $t) {
				if ($t['siteid']==$this->siteid && $t['modelid'] && $t['items']) {
					if ($is_find) {
						$nmid = $t['modelid'];
						break;
					}
					if ($t['modelid'] == $modelid) {
						$is_find = 1;
					}
				}
			}
		}
		$this->db->set_model($nmid ? intval($nmid) : $modelid);
		$total = $this->db->count();
		$go_url = $this->input->get('go_url');
		$go_url = $go_url ? trim('?m=content&c=create_all_html&a=show&modelid='.$nmid.'&go_url=1&pc_hash='.dr_get_csrf_token()) : '';
		if (!$total || !$nmid) $go_url = '';
		$modulename = $module[$modelid]['name'];
		$count_url = '?m=content&c=create_all_html&a=public_show_count&pagesize='.intval($this->input->get('pagesize')).'&modelid='.intval($this->input->get('modelid')).'&catids='.$catids.'&fromdate='.$this->input->get('fromdate').'&todate='.$this->input->get('todate').'&fromid='.intval($this->input->get('fromid')).'&toid='.intval($this->input->get('toid'));
		$todo_url = '?m=content&c=create_all_html&a=public_show_add&pagesize='.intval($this->input->get('pagesize')).'&modelid='.intval($this->input->get('modelid')).'&catids='.$catids.'&fromdate='.$this->input->get('fromdate').'&todate='.$this->input->get('todate').'&fromid='.intval($this->input->get('fromid')).'&toid='.intval($this->input->get('toid'));
		include $this->admin_tpl('show_html');
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
			'siteid' => $this->siteid
		));
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
		$page = max(1, intval($this->input->get('pp')));
		$name = 'show-'.$modelid.'-html-file';
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
			$sql = $cache['sql']. ' order by id asc limit '.($cache['pagesize'] * ($page - 1)).','.$cache['pagesize'];
			$this->db->query($sql);
			$data = $this->db->fetch_array();
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
							$this->html->show($urls[1],$r,0,'edit',$t['upgrade']);
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
	/**
	* 生成栏目页
	*/
	public function category() {
		$show_header = $show_dialog = true;
		$fmid = ''; // 第一个模块
		$module = getcache('model', 'commons');
		if ($module) {
			foreach ($module as $t) {
				if ($t['siteid']==$this->siteid && $t['modelid']) {
					$fmid = $t['modelid'];
					break;
				}
			}
		}
		
		$this->db->set_model(intval($fmid));
		$total = $this->db->count();
		$maxsize = (int)$this->input->get('maxsize');
		$go_url = $this->input->get('go_url');
		$go_url = $go_url ? trim('?m=content&c=create_all_html&a=show&modelid='.$fmid.'&go_url=1&pc_hash='.dr_get_csrf_token()) : '';
		if (!$total || !$fmid) $go_url = '';
		$modulename = '栏目';
		$count_url = '?m=content&c=create_all_html&a=public_category_count&maxsize='.$maxsize;
		$todo_url = '?m=content&c=create_all_html&a=public_category_add&go_url='.urlencode($go_url).'&maxsize='.$maxsize;
		include $this->admin_tpl('show_html');
	}
	// 栏目的数量统计
	public function public_category_count() {
		$maxsize = (int)$this->input->get('maxsize');
		$cat = get_category($this->siteid);
		pc_base::load_sys_class('html')->get_category_data($cat, $maxsize);
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
}
?>