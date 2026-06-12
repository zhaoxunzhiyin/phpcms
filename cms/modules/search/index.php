<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_sys_class('form');
pc_base::load_sys_class('format');
class index {
	private $input,$cache,$db,$content_db,$special_db,$get;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('search_model');
		$this->content_db = pc_base::load_model('content_model');
	}

	/**
	 * 关键词搜索
	 */
	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$get = $this->input->get();
		// 挂钩点 搜索之前对参数处理
		pc_base::load_sys_class('hooks')::trigger('search_param', $get);
		//获取siteid
		$siteid = intval($this->input->request('siteid')) ? intval($this->input->request('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		$SEO = seo($siteid);

		//搜索配置
		$search_setting = getcache('search');
		$setting = $search_setting[$siteid];
		$get = $this->get_param($get, $setting);
		if (isset($setting['use']) && $setting['use']) {
			dr_msg(0, L('此网站已经关闭了搜索功能'));
		} elseif ($get['keyword'] && $setting['length']
			&& dr_strlen($get['keyword']) < (int)$setting['length']) {
			dr_msg(0, L('关键字不得少于系统规定的长度'));
		} elseif ($get['keyword'] && $setting['maxlength']
			&& dr_strlen($get['keyword']) > (int)$setting['maxlength']) {
			dr_msg(0, L('关键字不得大于系统规定的长度'));
		}
		// 搜索数据
		$search = $this->get_data();
		unset($search['params']['page']);
		dr_search_url($search['params'], 'keyword', '', $siteid, (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE ? 1 : 0));

		$default_style = dr_site_info('default_style', $siteid);

		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'params' => dr_htmlspecialchars($search['params']),
			'search_model' => getcache('search_model_'.$siteid),
			'type_module' => getcache('type_module_'.$siteid),
			'setting' => $setting,
		]);
		$datas = array();
		if ($get['keyword']) {
			//搜索间隔
			if (isset($setting['search_time'])
			&& $setting['search_time']
			&& $get['page'] <= 1) {
				$cname = 'search_time_'.ROUTE_M.param::get_cookie('_userid').USER_HTTP_CODE.md5(dr_array2string($get));
				$ctime = $this->cache->get_auth_data($cname);
				if (!$ctime) {
					$this->cache->set_auth_data($cname, SYS_TIME);
				} elseif (SYS_TIME - $ctime < $setting['search_time']) {
					dr_msg(0, L('search_minrefreshtime',array('min'=>$setting['search_time']),'content'), dr_search_url($search['params'], 'keyword', '', $siteid, (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE ? 1 : 0)));
				} else {
					$this->cache->set_auth_data($cname, SYS_TIME);
				}
			}
			//搜索间隔
			$typeid = empty($get['typeid']) ? 0 : intval($get['typeid']);
			$time = empty($get['time']) || !in_array($get['time'], array('all','day','month','year','week')) ? 'all' : trim($get['time']);
			$page = max(intval($get['page']), 1);
			//分页数量
			if (intval($setting['pagesize'])) {
				$pagesize = $setting['pagesize'];
			} else {
				$pagesize = 10;
			}
			$keyword = str_replace('%', '', $get['keyword']); //过滤'%'，用户全文搜索
			$sql_tid = $sql_time = '';
			if ($typeid) {
				$sql_tid = ' AND typeid = '.$typeid;
			}
			//按时间搜索
			if ($time == 'day') {
				$search_time = SYS_TIME - 86400;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif ($time == 'week') {
				$search_time = SYS_TIME - 604800;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif ($time == 'month') {
				$search_time = SYS_TIME - 2592000;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif ($time == 'year') {
				$search_time = SYS_TIME - 31536000;
				$sql_time = ' AND adddate > '.$search_time;
			} else {
				$search_time = 0;
				$sql_time = '';
			}
			//如果开启sphinx
			if ($setting['sphinxenable']) {
				$sphinx = pc_base::load_app_class('search_interface', '', 0);
				$sphinx = new search_interface();

				$offset = $pagesize*($page-1);
				$res = $sphinx->search($keyword, array($siteid), ($typeid ? array($typeid) : array()), array($search_time, SYS_TIME), $offset, $pagesize, '@weight desc');
				$totalnums = $res['total'];
				//如果结果不为空
				if (!empty($res['matches'])) {
					$datas = $res['matches'];
				}
			} else {
				$datas = $this->db->listinfo("`siteid`= '$siteid' $sql_tid $sql_time AND `data` like '%".$this->db->escape($keyword)."%'", 'searchid DESC', $page, $pagesize);
			}
			$model_type_cache = getcache('type_model_'.$siteid, 'search');
			$model_type_cache && $model_type_cache = array_flip($model_type_cache);
			foreach($datas as $_k => $_v) {
				$modelid = $model_type_cache[$_v['typeid']];
				if ($modelid) {
					$this->content_db->set_model($modelid);
					if ($this->content_db->model_tablename) {
						$datas_count = $this->content_db->count(array('status<>'=>99));
						$content_data = $this->content_db->get_one(array('id'=>$_v['id'], 'status'=>99));
						if ($content_data) {
							$datas[$_k] = array_merge($datas[$_k], $content_data);
						} else {
							unset($datas[$_k]);
						}
					} else {
						$this->content_db = pc_base::load_model('yp_content_model');
						$this->content_db->set_model($modelid);
						$content_data = $this->content_db->get_one(array('id'=>$_v['id']));
						if ($content_data) {
							$datas[$_k] = array_merge($datas[$_k], $content_data);
						} else {
							unset($datas[$_k]);
						}
					}
				} else {
					$this->special_db = pc_base::load_model('special_content_model');
					$content_data = $this->special_db->get_one(array('id'=>$_v['id']));
					if ($content_data) {
						$datas[$_k] = array_merge($datas[$_k], $content_data);
					} else {
						unset($datas[$_k]);
					}
				}
			}
			$datas && $totalnums = $this->db->number - intval($datas_count);
			if ($setting['max'] && $page * $pagesize > $setting['max']) {
				log_message('debug', '全站搜索设置最大显示'.$setting['max'].'条，当前（'.($page * $pagesize).'）已超出');
				$datas = array();
			}
			$execute_time = execute_time();
			$totalnums = intval($totalnums);
			$datas && $totalnums ? $pages = pages($totalnums, $page, $pagesize, dr_search_url($search['params'], 'page', '{$page}', $siteid, (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE ? 1 : 0))) : '';
			
			//开启后遇到搜索内容为空时直接跳转404页面
			if (!$null && !$totalnums && isset($setting['search_404']) && $setting['search_404']) {
				param::goto_404_page('内容匹配结果为空');
			}

			// 挂钩点 搜索完成之后
			$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('search_data', $datas);
			if ($rt2 && isset($rt2['code']) && $rt2['code']) {
				$datas = $rt2['data'];
			}

			pc_base::load_sys_class('service')->assign([
				'typeid' => $typeid,
				'keyword' => dr_htmlspecialchars($keyword),
				'execute_time' => $execute_time,
				'run_time' => sprintf('%01.2f', $execute_time),
				'datas' => $datas,
				'totalnums' => $totalnums,
				'pages' => $pages,
			]);
			$tpl = '';
			if (isset($setting['tpl_field'])
				&& $setting['tpl_field']
				&& isset($get[$setting['tpl_field']])
				&& $get[$setting['tpl_field']]
			) {
				$tpl = dr_safe_filename('list_'.$get[$setting['tpl_field']]);
				if (!is_file(TPLPATH.$default_style.'/'.pc_base::load_sys_class('service')->get_dir().'/'.$tpl.'.html')) {
					$msg = '全站搜索模板字段'.$setting['tpl_field'].'参数值对应的模板（'.TPLPATH.$default_style.'/'.pc_base::load_sys_class('service')->get_dir().'/'.$tpl.'.html）不存在，将加载默认的搜索模板';
					log_message('debug', $msg);
					$tpl = ''; // 自定义模板不存在
				}
			}
			if (!$tpl) {
				$tpl = 'list';
			}
			pc_base::load_sys_class('service')->display('search', $tpl, $default_style);
		} else {
			pc_base::load_sys_class('service')->display('search', 'index', $default_style);
		}
	}

	// 获取搜索参数
	public function get_param($get, $setting) {

		$get = isset($get['rewrite']) ? dr_search_rewrite_decode($get['rewrite'], $setting) : $get;

		$get['m'] = $get['c'] = $get['a'] = $get['id'] = null;
		unset($get['m'], $get['c'], $get['a'], $get['id']);

		// 固定模式下的填充
		if ($get && $setting['param_rule']) {
			foreach ($get as $i => $t) {
				if ((string)$setting['param_join_default_value'] === $t) {
					unset($get[$i]);
				}
			}
		}

		// 解密关键词
		if (isset($get['keyword']) && $get['keyword'] && strpos($get['keyword'], 'CODE') === 0) {
			$kw = dr_authcode(substr($get['keyword'], 4));
			if ($kw) {
				$get['keyword'] = $kw;
			}
		}

		$this->get = $get;

		return $get;
	}

	/**
	 * 查询数据并设置缓存
	 */
	public function get_data() {

		// 挂钩点 自定义返回数据
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('search_get_data');
		if ($rt2 && isset($rt2['code']) && $rt2['code']) {
			return $rt2['data'];
		}

		// 排序查询参数
		ksort($this->get);
		$param = $this->get;
		$this->get['order'] = $this->get['page'] = null;
		unset($this->get['order'], $this->get['page']);

		$this->get['keyword'] = null;
		unset($this->get['keyword']);

		// 格式化值
		$data['params'] = $param;
		// order 参数
		if (isset($param['order']) && $param['order']) {
			$data['params']['order'] = dr_rp(dr_safe_filename($param['order']), '`', '');
		}

		return $data;
	}
}
?>