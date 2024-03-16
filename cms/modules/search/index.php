<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_sys_class('form');
pc_base::load_sys_class('format');
class index {
	private $input,$db,$content_db,$special_db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
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
		$params = $this->input->get();
		// 挂钩点 搜索之前对参数处理
		pc_base::load_sys_class('hooks')::trigger('search_param', $params);
		//获取siteid
		$siteid = $this->input->request('siteid') && trim($this->input->request('siteid')) ? intval($this->input->request('siteid')) : 1;
		$SEO = seo($siteid);

		//搜索配置
		$search_setting = getcache('search');
		$setting = $search_setting[$siteid];

		$default_style = dr_site_info('default_style', $siteid);

		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'search_model' => getcache('search_model_'.$siteid),
			'type_module' => getcache('type_module_'.$siteid),
			'setting' => $setting,
		]);
		if($params['q']) {
			if(!trim($params['q'])) {
				header('Location: '.APP_PATH.'index.php?m=search');exit;
			}
			$typeid = empty($params['typeid']) ? 0 : intval($params['typeid']);
			$time = empty($params['time']) || !in_array($params['time'], array('all','day','month','year','week')) ? 'all' : trim($params['time']);
			$page = max(intval($params['page']), 1);
			$pagesize = 10;
			$q = safe_replace(trim($params['q']));
			$q = new_html_special_chars(clearhtml($q));
			$q = str_replace('%', '', $q); //过滤'%'，用户全文搜索
			$sql_time = $sql_tid = '';
			if($typeid) $sql_tid = ' AND typeid = '.$typeid;
			//按时间搜索
			if($time == 'day') {
				$search_time = SYS_TIME - 86400;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif($time == 'week') {
				$search_time = SYS_TIME - 604800;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif($time == 'month') {
				$search_time = SYS_TIME - 2592000;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif($time == 'year') {
				$search_time = SYS_TIME - 31536000;
				$sql_time = ' AND adddate > '.$search_time;
			} else {
				$search_time = 0;
				$sql_time = '';
			}
			if($page==1 && !$setting['sphinxenable']) {
				//精确搜索
				$commend = $this->db->get_one("`siteid`= '$siteid' $sql_tid $sql_time AND `data` like '%".$this->db->escape($q)."%'");
			} else {
				$commend = '';
			}
			//如果开启sphinx
			if($setting['sphinxenable']) {
				$sphinx = pc_base::load_app_class('search_interface', '', 0);
				$sphinx = new search_interface();

				$offset = $pagesize*($page-1);
				$res = $sphinx->search($q, array($siteid), array($typeid), array($search_time, SYS_TIME), $offset, $pagesize, '@weight desc');
				$totalnums = $res['total'];
				//如果结果不为空
				if(!empty($res['matches'])) {
					$result = $res['matches'];
				}
			} else {
				pc_base::load_sys_class('segment', '', 0);
				$segment = new segment();
				//分词结果
				$segment_q = $segment->get_keyword($segment->split_result($q));
				//如果分词结果为空
				if(!empty($segment_q)) {
					$sql = "`siteid`= '$siteid' AND `typeid` = '$typeid' $sql_time AND MATCH (`data`) AGAINST ('$segment_q' IN BOOLEAN MODE)";
				} else {
					$sql = "`siteid`= '$siteid' $sql_tid $sql_time AND `data` like '%".$this->db->escape($q)."%'";
				}
				$result = $this->db->listinfo($sql, 'searchid DESC', $page, $pagesize);
			}
			//var_dump($result);
			//如果结果不为空
			if(!empty($result) || !empty($commend['id'])) {
				foreach($result as $_v) {
					if($_v['typeid']) $sids[$_v['typeid']][] = $_v['id'];
				}

				if(!empty($commend['id'])) {
					if($commend['typeid']) $sids[$commend['typeid']][] = $commend['id'];
				}
				$model_type_cache = getcache('type_model_'.$siteid,'search');
				$model_type_cache = array_flip($model_type_cache);
				foreach($sids as $_k=>$_val) {
					$tid = $_k;
					$ids = array_unique($_val);

					$where = to_sqls($ids, '', 'id');
					//获取模型id
					$modelid = $model_type_cache[$tid];

					//是否读取其他模块接口
					if($modelid) {
						$this->content_db->set_model($modelid);

						/**
						 * 如果表名为空，则为黄页模型
						 */
						if(empty($this->content_db->model_tablename)) {
							$this->content_db = pc_base::load_model('yp_content_model');
							$this->content_db->set_model($modelid);
						} else {
							if ($where) {
								$where .= ' and status=99';
							} else {
								$where = 'status=99';
							}
						}
						$datas = $this->content_db->select($where, '*', '', 'id DESC');
					} else {
						//读取专题搜索接口
						$this->special_db = pc_base::load_model('special_content_model');
						$datas = $this->special_db->select($where, '*', '', 'id DESC');
					}
				}

				$datas && $pages = $this->db->pages;
				$datas && $totalnums = $this->db->number;
			}
			$execute_time = execute_time();
			$pages = isset($pages) ? $pages : '';
			$totalnums = intval($totalnums);
			$datas = isset($datas) ? $datas : array();

			// 挂钩点 搜索完成之后
			$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('search_data', $datas);
			if ($rt2 && isset($rt2['code']) && $rt2['code']) {
				$datas = $rt2['data'];
			}

			pc_base::load_sys_class('service')->assign([
				'typeid' => $typeid,
				'q' => $q,
				'search_q' => $q,
				'params' => $params,
				'execute_time' => $execute_time,
				'datas' => $datas,
				'totalnums' => $totalnums,
				'pages' => $pages,
			]);
			if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
				if (!file_exists(TPLPATH.$default_style.DIRECTORY_SEPARATOR.'mobile_search'.DIRECTORY_SEPARATOR.'list.html')) {
					pc_base::load_sys_class('service')->display('search','list',$default_style);
				} else {
					pc_base::load_app_func('global','mobile');
					pc_base::load_sys_class('service')->display('mobile_search','list',$default_style);
				}
			}else{
				pc_base::load_sys_class('service')->display('search','list',$default_style);
			}
		} else {
			if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
				if (!file_exists(TPLPATH.$default_style.DIRECTORY_SEPARATOR.'mobile_search'.DIRECTORY_SEPARATOR.'index.html')) {
					pc_base::load_sys_class('service')->display('search','index',$default_style);
				} else {
					pc_base::load_app_func('global','mobile');
					pc_base::load_sys_class('service')->display('mobile_search','index',$default_style);
				}
			}else{
				pc_base::load_sys_class('service')->display('search','index',$default_style);
			}
		}
	}
}
?>