<?php
set_time_limit(300);
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
//定义在单独操作内容的时候，同时更新相关栏目页面
define('RELATION_HTML',true);

pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
pc_base::load_app_func('util');
pc_base::load_sys_class('format','',0);

class content extends admin {
	private $input,$cache,$cache_api,$db,$menu_db,$field_db,$linkage_db,$isadmin,$priv_db,$sitemodel,$form_cache,$page_db,$fields,$hits_db,$queue,$content_check_db,$position_data_db,$search_db,$comment,$url,$model,$admin_db,$sitemodel_db,$att_db,$sitedb,$upload;
	public $siteid,$categorys;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->db = pc_base::load_model('content_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->field_db = pc_base::load_model('sitemodel_field_model');
		$this->linkage_db = pc_base::load_model('linkage_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = get_category($this->siteid);
		$this->isadmin = IS_ADMIN && param::get_session('roleid') ? 1 : 0;
		$this->userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
		//权限判断
		if($this->input->get('catid') && !cleck_admin(param::get_session('roleid')) && ROUTE_A !='pass' && strpos(ROUTE_A,'public_')===false) {
			$catid = intval($this->input->get('catid'));
			$this->priv_db = pc_base::load_model('category_priv_model');
			$action = dr_cat_value($catid, 'type')==0 ? ROUTE_A : 'init';
			$priv_datas = $this->priv_db->get_one(array('catid'=>$catid,'is_admin'=>1,'action'=>$action));
			if(!$priv_datas) dr_admin_msg(0,L('permission_to_operate'));
		}
	}
	
	public function init() {
		$show_header = $show_dialog = $show_pc_hash = true;
		//搜索
		$param = $this->input->get();
		if(isset($param['catid']) && $param['catid'] && dr_cat_value($param['catid'], 'siteid')==$this->siteid) {
			$category = dr_cat_value($param['catid']);
			$modelid = $category['modelid'];
			$model_arr = getcache('model', 'commons');
			$MODEL = $model_arr[$modelid];
			unset($model_arr);
			$this->sitemodel = $this->cache->get('sitemodel');
			$this->form_cache = $this->sitemodel[$MODEL['tablename']];
			$field = $this->form_cache['field'];
			$list_field = $this->form_cache['setting']['list_field'];
			if (!$list_field) {
				$list_field = array(
					'title' => array(
						'use' => 1,
						'name' => L('主题'),
						'width' => '',
						'func' => 'title',
					),
					'username' => array(
						'use' => 1,
						'name' => L('用户名'),
						'width' => '100',
						'func' => 'author',
					),
					'updatetime' => array(
						'use' => 1,
						'name' => L('更新时间'),
						'width' => '160',
						'func' => 'datetime',
					),
					'listorder' => array(
						'use' => 1,
						'name' => L('排序'),
						'width' => '100',
						'center' => 1,
						'func' => 'save_text_value',
					),
				);
			}
			$date_field = $this->form_cache['setting']['search_time'] ? $this->form_cache['setting']['search_time'] : 'updatetime';
			$admin_username = param::get_cookie('admin_username');
			//查询当前的工作流
			$setting = string2array($category['setting']);
			$workflowid = $setting['workflowid'];
			$workflows = getcache('workflow_'.$this->siteid,'commons');
			if ($workflowid) {
				$workflows = $workflows[$workflowid];
				$workflows_setting = string2array($workflows['setting']);
			} else{
				$workflows_setting = array();
			}

			//将有权限的级别放到新数组中
			$admin_privs = array();
			foreach($workflows_setting as $_k=>$_v) {
				if(empty($_v)) continue;
				foreach($_v as $_value) {
					if($_value==$admin_username) $admin_privs[$_k] = $_k;
				}
			}
			//工作流审核级别
			if ($workflowid) {
				$workflow_steps = $workflows['steps'];
			} else{
				$workflow_steps = '';
			}
			$workflow_menu = '';
			$steps = $param['steps'] ? intval($param['steps']) : 0;
			//工作流权限判断
			if(!cleck_admin(param::get_session('roleid')) && $steps && !in_array($steps,$admin_privs)) dr_admin_msg(0,L('permission_to_operate'));
			$this->db->set_model($modelid);
			if($this->db->table_name==$this->db->db_tablepre) dr_admin_msg(0,L('model_table_not_exists'));
			$status = $steps ? $steps : 99;
			if($param['reject']) $status = 0;
			$where = array();
			$where[] = 'catid='.$param['catid'].' AND status='.$status;
			// 默认以显示字段为搜索字段
			if (!isset($param['field']) && !$param['field']) {
				$param['field'] = isset($this->form_cache['setting']['search_first_field']) && $this->form_cache['setting']['search_first_field'] ? $this->form_cache['setting']['search_first_field'] : 'title';
			}
			if($param['start_time']) {
				$where[] = $date_field.' BETWEEN ' . max((int)strtotime(strpos($param['start_time'], ' ') ? $param['start_time'] : $param['start_time'].' 00:00:00'), 1) . ' AND ' . ($param['end_time'] ? (int)strtotime(strpos($param['end_time'], ' ') ? $param['end_time'] : $param['end_time'].' 23:59:59') : SYS_TIME);
			}
			if (isset($param['keyword']) && $param['keyword'] != '') {
				if (isset($param['keyword']) && $param['keyword']) {
					$param['keyword'] = htmlspecialchars(urldecode($param['keyword']));
				}
				if ($param['field'] == 'id') {
					// 按id查询
					$id = [];
					$ids = explode(',', $param['keyword']);
					foreach ($ids as $i) {
						$id[] = (int)$i;
					}
					dr_count($id) == 1 ? $where[] = '`id`='.(int)$id[0] : $where[] = '`id` in ('.implode(',', $id).')';
				} elseif (dr_in_array($field[$param['field']]['formtype'], ['number'])) {
					// 数字类型
					$where[] = '`'.$param['field'].'`='.intval($param['keyword']);
				} elseif (isset($field[$param['field']]['formtype']) && $field[$param['field']]['formtype'] == 'linkage') {
					// 联动菜单字段
					$arr = explode('|', $param['keyword']);
					if ($param['keyword']) {
						$key = $this->cache->get_file('key', 'linkage/'.$field[$param['field']]['setting']['linkage'].'/');
						if ($key) {
							$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
							$row = $this->linkage_db->get_one(array('name'=>'%'.$param['keyword'].'%'));
							if ($row) {
								$arr[] = $row['cname'];
							}
						}
					}
					$where_linkage = array();
					foreach ($arr as $val) {
						$data = dr_linkage($field[$param['field']]['setting']['linkage'], $val);
						if ($data) {
							if ($data['child']) {
								$where_linkage[] = '`'.$param['field'].'` IN ('.$data['childids'].')';
							} else {
								$where_linkage[] = '`'.$param['field'].'`='.intval($data['ii']);
							}
						}
					}
					$where[] = $where_linkage ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_linkage) : '`id` = 0';
				} elseif (isset($field[$param['field']]['formtype']) && $field[$param['field']]['formtype'] == 'linkages') {
					// 联动菜单多选字段
					$arr = explode('|', $param['keyword']);
					if ($param['keyword']) {
						$key = $this->cache->get_file('key', 'linkage/'.$field[$param['field']]['setting']['linkage'].'/');
						if ($key) {
							$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
							$row = $this->linkage_db->get_one(array('name'=>'%'.$param['keyword'].'%'));
							if ($row) {
								$arr[] = $row['cname'];
							}
						}
					}
					$where_linkages = array();
					foreach ($arr as $val) {
						$data = dr_linkage($field[$param['field']]['setting']['linkage'], $val);
						if ($data) {
							if ($data['child']) {
								$ids = explode(',', $data['childids']);
								foreach ($ids as $id) {
									if ($id) {
										if (version_compare($this->db->version(), '5.7.0') < 0) {
											// 兼容写法
											$where_linkages[] = '`'.$param['field'].'` LIKE "%\"'.intval($id).'\"%"';
										} else {
											// 高版本写法
											$where_linkages[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".intval($id)."\"', '$') ELSE null END)";
										}
									}
								}
							} else {
								if (version_compare($this->db->version(), '5.7.0') < 0) {
									// 兼容写法
									$where_linkages[] = '`'.$param['field'].'` LIKE "%\"'.intval($data['ii']).'\"%"';
								} else {
									// 高版本写法
									$where_linkages[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".intval($data['ii'])."\"', '$') ELSE null END)";
								}
							}
						}
					}
					$where[] = $where_linkages ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_linkages) : '`id` = 0';
				} elseif (dr_in_array($field[$param['field']]['formtype'], ['box'])) {
					// 选项类型
					$arr = explode('|', $param['keyword']);
					$where_box = array();
					if ($param['keyword']) {
						$option = dr_format_option_array($field[$param['field']]['setting']['options']);
						if ($option) {
							$new = [];
							foreach ($option as $k => $v) {
								if (strpos($v, (string)$param['keyword']) !== false) {
									$new[] = $k;
								}
							}
							if ($new) {
								$arr = $new;
							}
						}
					}
					if (dr_in_array($field[$param['field']]['setting']['boxtype'], ['radio','select'])) {
						foreach ($arr as $val) {
							if (is_numeric($val)) {
								$where_box[] = '`'.$param['field'].'`='.$val;
							} else {
								$where_box[] = '`'.$param['field'].'`="'.dr_safe_replace($val, ['\\', '/']).'"';
							}
						}
					} else {
						foreach ($arr as $val) {
							if ($val) {
								if (version_compare($this->db->version(), '5.7.0') < 0) {
									// 兼容写法
									$where_box[] = '`'.$param['field'].'` LIKE "%\"'.$this->db->escape(dr_safe_replace($val)).'\"%"';
								} else {
									// 高版本写法
									$where_box[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".$this->db->escape(dr_safe_replace($val))."\"', '$') ELSE null END)";
								}
							}
						}
					}
					$where[] = $where_box ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_box) : '`id` = 0';
				} elseif ($param['keyword'] && substr_count($param['keyword'], ',') == 1 && preg_match('/[\+\-0-9\.]+,[\+\-0-9\.]+/', $param['keyword'])) {
					// BETWEEN 条件
					list($s, $e) = explode(',', $param['keyword']);
					$s = floatval($s);
					$e = floatval($e);
					if ($s == $e && $s == 0) {
						$where[] = '`'.$param['field'].'` = 0';
					}
					if (!$e && $s > 0) {
						$where[] = '`'.$param['field'].'` > '.$s;
					} else {
						$where[] = '`'.$param['field'].'` BETWEEN '.$s.' AND '.$e;
					}
				} else {
					$where[] = '`'.$param['field'].'` like \'%'.$this->db->escape($param['keyword']).'%\'';
				}
			}
			if($param['posids'] && !empty($param['posids'])) {
				$posids = $param['posids']==1 ? intval($param['posids']) : 0;
				$where[] = "`posids` = '$posids'";
			}
			$pagesize = $param['limit'] ? $param['limit'] : SYS_ADMIN_PAGESIZE;
			$order = $param['order'] ? $param['order'] : ($this->form_cache['setting']['order'] ? dr_safe_replace($this->form_cache['setting']['order']) : 'id desc');
			$datas = $this->db->listinfo(($where ? implode(' AND ', $where) : ''),$order,max(1, intval($param['page'])),$pagesize);
			$pages = $this->db->pages;
			$pc_hash = dr_get_csrf_token();
			for($i=1;$i<=$workflow_steps;$i++) {
				if(!cleck_admin(param::get_session('roleid')) && !in_array($i,$admin_privs)) continue;
				$workflow_menu .= '<li><a href="?m=content&c=content&a=init&catid='.$param['catid'].'&steps='.$i.'&pc_hash='.$pc_hash.'"><i class="fa fa-sort-numeric-asc"></i>'.L('workflow_'.$i).'</a></li>';
			}
			if($workflow_menu) {
				$workflow_menu .= '<li><a href="?m=content&c=content&a=init&catid='.$param['catid'].'&reject=1&pc_hash='.$pc_hash.'"><i class="fa fa-times"></i>'.L('reject').'</a></li>';
			}
			$template = $MODEL['admin_list_template'] ? $MODEL['admin_list_template'] : 'content_list';
			$clink = module_clink($MODEL['tablename']);
			$foot_tpl = '';
			$foot_tpl .= '<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>'.PHP_EOL;
			$foot_tpl .= '<label><button type="button" id="delAll" class="btn red btn-sm"> <i class="fa fa-trash"></i> '.L('delete').'</button></label>'.PHP_EOL;
			$data = [];
			$data[] = [
				'icon' => 'fa fa-arrows',
				'name' => L('remove'),
				'url' => 'javascript:;" id="remove',
				'displayorder' => 0,
			];
			$data[] = [
				'icon' => 'fa fa-clock-o',
				'name' => L('updatetime'),
				'url' => 'javascript:;" onclick="dr_module_send_ajax(\'?m=content&c=content&a=public_send_edit&catid='.$param['catid'].'\')',
				'displayorder' => 0,
			];
			if ($status!=99) {
				$data[] = [
					'icon' => 'fa fa-check',
					'name' => L('passed_checked'),
					'url' => 'javascript:;" id="passed',
					'displayorder' => 0,
				];
			}
			if (!$param['reject']) {
				$data[] = [
					'icon' => 'fa fa-window-restore',
					'name' => L('push'),
					'url' => 'javascript:;" id="push',
					'displayorder' => 0,
				];
				$data[] = [
					'icon' => 'fa fa-copy',
					'name' => L('copy'),
					'url' => 'javascript:;" id="copy',
					'displayorder' => 0,
				];
				if ($workflow_menu) {
					$data[] = [
						'icon' => 'fa fa-sign-out',
						'name' => L('reject'),
						'url' => 'javascript:;" onclick="dr_module_tuigao(\'?m=content&c=content&a=public_send_edit&catid='.$param['catid'].'&action=reject\', \'?m=content&c=content&a=pass&catid='.$param['catid'].'&steps='.$steps.'&reject=1\')',
						'displayorder' => 0,
					];
				}
			}
			$data[] = [
				'icon' => 'fa fa-trash-o',
				'name' => L('in_recycle'),
				'url' => 'javascript:;" id="recycle',
				'displayorder' => 0,
			];
			if ($setting['content_ishtml']) {
				$data[] = [
					'icon' => 'fa fa-html5',
					'name' => L('createhtml'),
					'url' => 'javascript:;" id="createhtml',
					'displayorder' => 0,
				];
			}
			$cbottom = module_cbottom($MODEL['tablename'], '', $data);
			if ($cbottom) {
				$foot_tpl .= '<label>
                    <div class="btn-group dropup">
                        <a class="btn blue btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false" href="javascript:;"><i class="fa fa-cogs"></i> '.L('批量操作').'
                            <i class="fa fa-angle-up"></i>
                        </a>
                        <ul class="dropdown-menu">';
				foreach ($cbottom as $i => $a) {
					$foot_tpl .= '<li><a href="'.str_replace(['{modelid}', '{catid}', '{siteid}', '{m}'], [$modelid, $param['catid'], $this->siteid, ROUTE_M], urldecode($a['url'])).'"> <i class="'.$a['icon'].'"></i> '.$a['name'].' </a></li>';
					if ($i) {
						$foot_tpl .= '<div class="dropdown-line"></div>';
					}
				}
				$foot_tpl .= '</ul>
                    </div>
                </label>';
			}
			include $this->admin_tpl($template);
		} else {
			include $this->admin_tpl('content_quick');
		}
	}
	
	public function public_init() {
		$show_header = $show_dialog = $show_pc_hash = true;
		include $this->admin_tpl('content_quick_list');
	}
	
	public function recycle_init() {
		$show_header = $show_dialog = $show_pc_hash = true;
		//搜索
		$param = $this->input->get();
		if(isset($param['catid']) && $param['catid'] && dr_cat_value($param['catid'], 'siteid')==$this->siteid) {
			$category = dr_cat_value($param['catid']);
			$modelid = $category['modelid'];
			$model_arr = getcache('model', 'commons');
			$MODEL = $model_arr[$modelid];
			unset($model_arr);
			$this->sitemodel = $this->cache->get('sitemodel');
			$this->form_cache = $this->sitemodel[$MODEL['tablename']];
			$field = $this->form_cache['field'];
			$list_field = $this->form_cache['setting']['list_field'];
			if (!$list_field) {
				$list_field = array(
					'title' => array(
						'use' => 1,
						'name' => L('主题'),
						'width' => '',
						'func' => 'title',
					),
					'username' => array(
						'use' => 1,
						'name' => L('用户名'),
						'width' => '100',
						'func' => 'author',
					),
					'updatetime' => array(
						'use' => 1,
						'name' => L('更新时间'),
						'width' => '160',
						'func' => 'datetime',
					),
					'listorder' => array(
						'use' => 1,
						'name' => L('排序'),
						'width' => '100',
						'center' => 1,
						'func' => 'save_text_value',
					),
				);
			}
			$date_field = $this->form_cache['setting']['search_time'] ? $this->form_cache['setting']['search_time'] : 'updatetime';
			$this->db->set_model($modelid);
			if($this->db->table_name==$this->db->db_tablepre) dr_admin_msg(0,L('model_table_not_exists'));
			$where = array();
			$where[] = 'catid='.$param['catid'].' AND status=100';
			// 默认以显示字段为搜索字段
			if (!isset($param['field']) && !$param['field']) {
				$param['field'] = isset($this->form_cache['setting']['search_first_field']) && $this->form_cache['setting']['search_first_field'] ? $this->form_cache['setting']['search_first_field'] : 'title';
			}
			if($param['start_time']) {
				$where[] = $date_field.' BETWEEN ' . max((int)strtotime(strpos($param['start_time'], ' ') ? $param['start_time'] : $param['start_time'].' 00:00:00'), 1) . ' AND ' . ($param['end_time'] ? (int)strtotime(strpos($param['end_time'], ' ') ? $param['end_time'] : $param['end_time'].' 23:59:59') : SYS_TIME);
			}
			if (isset($param['keyword']) && $param['keyword'] != '') {
				if (isset($param['keyword']) && $param['keyword']) {
					$param['keyword'] = htmlspecialchars(urldecode($param['keyword']));
				}
				if ($param['field'] == 'id') {
					// 按id查询
					$id = [];
					$ids = explode(',', $param['keyword']);
					foreach ($ids as $i) {
						$id[] = (int)$i;
					}
					dr_count($id) == 1 ? $where[] = '`id`='.(int)$id[0] : $where[] = '`id` in ('.implode(',', $id).')';
				} elseif (dr_in_array($field[$param['field']]['formtype'], ['number'])) {
					// 数字类型
					$where[] = '`'.$param['field'].'`='.intval($param['keyword']);
				} elseif (isset($field[$param['field']]['formtype']) && $field[$param['field']]['formtype'] == 'linkage') {
					// 联动菜单字段
					$arr = explode('|', $param['keyword']);
					if ($param['keyword']) {
						$key = $this->cache->get_file('key', 'linkage/'.$field[$param['field']]['setting']['linkage'].'/');
						if ($key) {
							$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
							$row = $this->linkage_db->get_one(array('name'=>'%'.$param['keyword'].'%'));
							if ($row) {
								$arr[] = $row['cname'];
							}
						}
					}
					$where_linkage = array();
					foreach ($arr as $val) {
						$data = dr_linkage($field[$param['field']]['setting']['linkage'], $val);
						if ($data) {
							if ($data['child']) {
								$where_linkage[] = '`'.$param['field'].'` IN ('.$data['childids'].')';
							} else {
								$where_linkage[] = '`'.$param['field'].'`='.intval($data['ii']);
							}
						}
					}
					$where[] = $where_linkage ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_linkage) : '`id` = 0';
				} elseif (isset($field[$param['field']]['formtype']) && $field[$param['field']]['formtype'] == 'linkages') {
					// 联动菜单多选字段
					$arr = explode('|', $param['keyword']);
					if ($param['keyword']) {
						$key = $this->cache->get_file('key', 'linkage/'.$field[$param['field']]['setting']['linkage'].'/');
						if ($key) {
							$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
							$row = $this->linkage_db->get_one(array('name'=>'%'.$param['keyword'].'%'));
							if ($row) {
								$arr[] = $row['cname'];
							}
						}
					}
					$where_linkages = array();
					foreach ($arr as $val) {
						$data = dr_linkage($field[$param['field']]['setting']['linkage'], $val);
						if ($data) {
							if ($data['child']) {
								$ids = explode(',', $data['childids']);
								foreach ($ids as $id) {
									if ($id) {
										if (version_compare($this->db->version(), '5.7.0') < 0) {
											// 兼容写法
											$where_linkages[] = '`'.$param['field'].'` LIKE "%\"'.intval($id).'\"%"';
										} else {
											// 高版本写法
											$where_linkages[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".intval($id)."\"', '$') ELSE null END)";
										}
									}
								}
							} else {
								if (version_compare($this->db->version(), '5.7.0') < 0) {
									// 兼容写法
									$where_linkages[] = '`'.$param['field'].'` LIKE "%\"'.intval($data['ii']).'\"%"';
								} else {
									// 高版本写法
									$where_linkages[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".intval($data['ii'])."\"', '$') ELSE null END)";
								}
							}
						}
					}
					$where[] = $where_linkages ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_linkages) : '`id` = 0';
				} elseif (dr_in_array($field[$param['field']]['formtype'], ['box'])) {
					// 选项类型
					$arr = explode('|', $param['keyword']);
					$where_box = array();
					if ($param['keyword']) {
						$option = dr_format_option_array($field[$param['field']]['setting']['options']);
						if ($option) {
							$new = [];
							foreach ($option as $k => $v) {
								if (strpos($v, (string)$param['keyword']) !== false) {
									$new[] = $k;
								}
							}
							if ($new) {
								$arr = $new;
							}
						}
					}
					if (dr_in_array($field[$param['field']]['setting']['boxtype'], ['radio','select'])) {
						foreach ($arr as $val) {
							if (is_numeric($val)) {
								$where_box[] = '`'.$param['field'].'`='.$val;
							} else {
								$where_box[] = '`'.$param['field'].'`="'.dr_safe_replace($val, ['\\', '/']).'"';
							}
						}
					} else {
						foreach ($arr as $val) {
							if ($val) {
								if (version_compare($this->db->version(), '5.7.0') < 0) {
									// 兼容写法
									$where_box[] = '`'.$param['field'].'` LIKE "%\"'.$this->db->escape(dr_safe_replace($val)).'\"%"';
								} else {
									// 高版本写法
									$where_box[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".$this->db->escape(dr_safe_replace($val))."\"', '$') ELSE null END)";
								}
							}
						}
					}
					$where[] = $where_box ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_box) : '`id` = 0';
				} elseif ($param['keyword'] && substr_count($param['keyword'], ',') == 1 && preg_match('/[\+\-0-9\.]+,[\+\-0-9\.]+/', $param['keyword'])) {
					// BETWEEN 条件
					list($s, $e) = explode(',', $param['keyword']);
					$s = floatval($s);
					$e = floatval($e);
					if ($s == $e && $s == 0) {
						$where[] = '`'.$param['field'].'` = 0';
					}
					if (!$e && $s > 0) {
						$where[] = '`'.$param['field'].'` > '.$s;
					} else {
						$where[] = '`'.$param['field'].'` BETWEEN '.$s.' AND '.$e;
					}
				} else {
					$where[] = '`'.$param['field'].'` like \'%'.$this->db->escape($param['keyword']).'%\'';
				}
			}
			if($param['posids'] && !empty($param['posids'])) {
				$posids = $param['posids']==1 ? intval($param['posids']) : 0;
				$where[] = "`posids` = '$posids'";
			}
			$pagesize = $param['limit'] ? $param['limit'] : SYS_ADMIN_PAGESIZE;
			$order = $param['order'] ? $param['order'] : ($this->form_cache['setting']['order'] ? dr_safe_replace($this->form_cache['setting']['order']) : 'id desc');
			$datas = $this->db->listinfo(($where ? implode(' AND ', $where) : ''),$order,max(1, intval($param['page'])),$pagesize);
			$pages = $this->db->pages;
			$foot_tpl = '';
			$foot_tpl .= '<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>'.PHP_EOL;
			$foot_tpl .= '<label><button type="button" id="recycle" class="btn green btn-sm"> <i class="fa fa-reply"></i> '.L('recover').'</button></label>'.PHP_EOL;
			$foot_tpl .= '<label><button type="button" id="delAll" class="btn red btn-sm"> <i class="fa fa-trash"></i> '.L('thorough').L('delete').'</button></label>'.PHP_EOL;
			$foot_tpl .= '<label><button type="button" onclick="ajax_confirm_url(\'?m=content&c=content&a=public_recycle_del&catid='.$param['catid'].'\', \'你确定要清空回收站吗？\', \'\')" class="btn red btn-sm"> <i class="fa fa-close"></i> '.L('empty_recycle').L('recycle').'</button></label>'.PHP_EOL;
			include $this->admin_tpl('content_recycle');
		} else {
			include $this->admin_tpl('content_quick');
		}
	}
	
	public function initall() {
		$show_header = $show_dialog = $show_pc_hash = true;
		//搜索
		$param = $this->input->get();
		$this->admin_db = pc_base::load_model('admin_model');
		$infos = $this->admin_db->select();
		$this->sitemodel_db = pc_base::load_model('sitemodel_model');
		$datas2 = $this->sitemodel_db->select(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0), "*", '', 'sort,modelid');
		foreach ($datas2 as $r) {
			$this->db->set_model($r['modelid']);
			$number = $this->db->count();
			$this->sitemodel_db->update(array('items'=>$number),array('modelid'=>$r['modelid']));
			$recycle[$r['modelid']] = $this->db->count(array('status'=>100));
		}
		$modelid = intval($param['modelid']);
		if (!$modelid) {$one = reset($datas2);$modelid = $one['modelid'];}
		$model_arr = getcache('model', 'commons');
		$MODEL = $model_arr[$modelid];
		unset($model_arr);
		$this->sitemodel = $this->cache->get('sitemodel');
		$this->form_cache = $this->sitemodel[$MODEL['tablename']];
		$field = $this->form_cache['field'];
		$list_field = $this->form_cache['setting']['list_field'];
		if (!$list_field) {
			$list_field = array(
				'title' => array(
					'use' => 1,
					'name' => L('主题'),
					'width' => '',
					'func' => 'title',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '100',
					'func' => 'author',
				),
				'updatetime' => array(
					'use' => 1,
					'name' => L('更新时间'),
					'width' => '160',
					'func' => 'datetime',
				),
				'listorder' => array(
					'use' => 1,
					'name' => L('排序'),
					'width' => '100',
					'center' => 1,
					'func' => 'save_text_value',
				),
			);
		}
		$date_field = $this->form_cache['setting']['search_time'] ? $this->form_cache['setting']['search_time'] : 'updatetime';
		$this->db->set_model($modelid);
		if($this->db->table_name==$this->db->db_tablepre) dr_admin_msg(0,L('model_table_not_exists'));
		$where = array();
		// 默认以显示字段为搜索字段
		if (!isset($param['field']) && !$param['field']) {
			$param['field'] = isset($this->form_cache['setting']['search_first_field']) && $this->form_cache['setting']['search_first_field'] ? $this->form_cache['setting']['search_first_field'] : 'title';
		}
		if($param['recycle']) {
			$where[] = 'status=100';
		}
		if($param['catid']) {
			$cat = dr_cat_value($param['catid']);
			$where[] = 'catid in ('.($cat['arrchildid'] ? $cat['arrchildid'] : $param['catid']).')';
		}
		if($param['start_time']) {
			$where[] = $date_field.' BETWEEN ' . max((int)strtotime(strpos($param['start_time'], ' ') ? $param['start_time'] : $param['start_time'].' 00:00:00'), 1) . ' AND ' . ($param['end_time'] ? (int)strtotime(strpos($param['end_time'], ' ') ? $param['end_time'] : $param['end_time'].' 23:59:59') : SYS_TIME);
		}
		if (isset($param['keyword']) && $param['keyword'] != '') {
			if (isset($param['keyword']) && $param['keyword']) {
				$param['keyword'] = htmlspecialchars(urldecode($param['keyword']));
			}
			if ($param['field'] == 'id') {
				// 按id查询
				$id = [];
				$ids = explode(',', $param['keyword']);
				foreach ($ids as $i) {
					$id[] = (int)$i;
				}
				dr_count($id) == 1 ? $where[] = '`id`='.(int)$id[0] : $where[] = '`id` in ('.implode(',', $id).')';
			} elseif (dr_in_array($field[$param['field']]['formtype'], ['number'])) {
				// 数字类型
				$where[] = '`'.$param['field'].'`='.intval($param['keyword']);
			} elseif (isset($field[$param['field']]['formtype']) && $field[$param['field']]['formtype'] == 'linkage') {
				// 联动菜单字段
				$arr = explode('|', $param['keyword']);
				if ($param['keyword']) {
					$key = $this->cache->get_file('key', 'linkage/'.$field[$param['field']]['setting']['linkage'].'/');
					if ($key) {
						$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
						$row = $this->linkage_db->get_one(array('name'=>'%'.$param['keyword'].'%'));
						if ($row) {
							$arr[] = $row['cname'];
						}
					}
				}
				$where_linkage = array();
				foreach ($arr as $val) {
					$data = dr_linkage($field[$param['field']]['setting']['linkage'], $val);
					if ($data) {
						if ($data['child']) {
							$where_linkage[] = '`'.$param['field'].'` IN ('.$data['childids'].')';
						} else {
							$where_linkage[] = '`'.$param['field'].'`='.intval($data['ii']);
						}
					}
				}
				$where[] = $where_linkage ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_linkage) : '`id` = 0';
			} elseif (isset($field[$param['field']]['formtype']) && $field[$param['field']]['formtype'] == 'linkages') {
				// 联动菜单多选字段
				$arr = explode('|', $param['keyword']);
				if ($param['keyword']) {
					$key = $this->cache->get_file('key', 'linkage/'.$field[$param['field']]['setting']['linkage'].'/');
					if ($key) {
						$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
						$row = $this->linkage_db->get_one(array('name'=>'%'.$param['keyword'].'%'));
						if ($row) {
							$arr[] = $row['cname'];
						}
					}
				}
				$where_linkages = array();
				foreach ($arr as $val) {
					$data = dr_linkage($field[$param['field']]['setting']['linkage'], $val);
					if ($data) {
						if ($data['child']) {
							$ids = explode(',', $data['childids']);
							foreach ($ids as $id) {
								if ($id) {
									if (version_compare($this->db->version(), '5.7.0') < 0) {
										// 兼容写法
										$where_linkages[] = '`'.$param['field'].'` LIKE "%\"'.intval($id).'\"%"';
									} else {
										// 高版本写法
										$where_linkages[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".intval($id)."\"', '$') ELSE null END)";
									}
								}
							}
						} else {
							if (version_compare($this->db->version(), '5.7.0') < 0) {
								// 兼容写法
								$where_linkages[] = '`'.$param['field'].'` LIKE "%\"'.intval($data['ii']).'\"%"';
							} else {
								// 高版本写法
								$where_linkages[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".intval($data['ii'])."\"', '$') ELSE null END)";
							}
						}
					}
				}
				$where[] = $where_linkages ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_linkages) : '`id` = 0';
			} elseif (dr_in_array($field[$param['field']]['formtype'], ['box'])) {
				// 选项类型
				$arr = explode('|', $param['keyword']);
				$where_box = array();
				if ($param['keyword']) {
					$option = dr_format_option_array($field[$param['field']]['setting']['options']);
					if ($option) {
						$new = [];
						foreach ($option as $k => $v) {
							if (strpos($v, (string)$param['keyword']) !== false) {
								$new[] = $k;
							}
						}
						if ($new) {
							$arr = $new;
						}
					}
				}
				if (dr_in_array($field[$param['field']]['setting']['boxtype'], ['radio','select'])) {
					foreach ($arr as $val) {
						if (is_numeric($val)) {
							$where_box[] = '`'.$param['field'].'`='.$val;
						} else {
							$where_box[] = '`'.$param['field'].'`="'.dr_safe_replace($val, ['\\', '/']).'"';
						}
					}
				} else {
					foreach ($arr as $val) {
						if ($val) {
							if (version_compare($this->db->version(), '5.7.0') < 0) {
								// 兼容写法
								$where_box[] = '`'.$param['field'].'` LIKE "%\"'.$this->db->escape(dr_safe_replace($val)).'\"%"';
							} else {
								// 高版本写法
								$where_box[] = "(CASE WHEN JSON_VALID(`{$param['field']}`) THEN JSON_CONTAINS (`{$param['field']}`->'$[*]', '\"".$this->db->escape(dr_safe_replace($val))."\"', '$') ELSE null END)";
							}
						}
					}
				}
				$where[] = $where_box ? implode(strpos($param['keyword'], '||') !== false ? ' AND ' : ' OR ', $where_box) : '`id` = 0';
			} elseif ($param['keyword'] && substr_count($param['keyword'], ',') == 1 && preg_match('/[\+\-0-9\.]+,[\+\-0-9\.]+/', $param['keyword'])) {
				// BETWEEN 条件
				list($s, $e) = explode(',', $param['keyword']);
				$s = floatval($s);
				$e = floatval($e);
				if ($s == $e && $s == 0) {
					$where[] = '`'.$param['field'].'` = 0';
				}
				if (!$e && $s > 0) {
					$where[] = '`'.$param['field'].'` > '.$s;
				} else {
					$where[] = '`'.$param['field'].'` BETWEEN '.$s.' AND '.$e;
				}
			} else {
				$where[] = '`'.$param['field'].'` like \'%'.$this->db->escape($param['keyword']).'%\'';
			}
		}
		if($param['posids'] && !empty($param['posids'])) {
			$posids = $param['posids']==1 ? intval($param['posids']) : 0;
			$where[] = "`posids` = '$posids'";
		}
		$pagesize = $param['limit'] ? $param['limit'] : SYS_ADMIN_PAGESIZE;
		$order = $param['order'] ? $param['order'] : ($this->form_cache['setting']['order'] ? dr_safe_replace($this->form_cache['setting']['order']) : 'id desc');
		$datas = $this->db->listinfo(($where ? implode(' AND ', $where) : ''),$order,max(1, intval($param['page'])),$pagesize);
		$pages = $this->db->pages;
		$clink = module_clink($MODEL['tablename']);
		$foot_tpl = '';
		$foot_tpl .= '<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>'.PHP_EOL;
		if ($param['recycle']) {
			$foot_tpl .= '<label><button type="button" id="recycle" class="btn green btn-sm"> <i class="fa fa-reply"></i> '.L('recover').'</button></label>'.PHP_EOL;
		}
		$foot_tpl .= '<label><button type="button" id="delAll" class="btn red btn-sm"> <i class="fa fa-trash"></i> '.($param['recycle'] ? L('thorough') : '').L('delete').'</button></label>'.PHP_EOL;
		if ($param['recycle']) {
			$foot_tpl .= '<label><button type="button" onclick="ajax_confirm_url(\'?m=content&c=content&a=public_recycle_del&modelid='.$modelid.'\', \'你确定要清空回收站吗？\', \'\')" class="btn red btn-sm"> <i class="fa fa-close"></i> '.L('empty_recycle').L('recycle').'</button></label>'.PHP_EOL;
		} else {
			$data = [];
			$data[] = [
				'icon' => 'fa fa-clock-o',
				'name' => L('updatetime'),
				'url' => 'javascript:;" onclick="dr_module_send_ajax(\'?m=content&c=content&a=public_send_edit&modelid='.$modelid.'\')',
			];
			$data[] = [
				'icon' => 'fa fa-trash-o',
				'name' => L('in_recycle'),
				'url' => 'javascript:;" id="recycle',
			];
			$cbottom = module_cbottom($MODEL['tablename'], '', $data);
			if ($cbottom) {
				$foot_tpl .= '<label>
                    <div class="btn-group dropup">
                        <a class="btn blue btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false" href="javascript:;"><i class="fa fa-cogs"></i> '.L('批量操作').'
                            <i class="fa fa-angle-up"></i>
                        </a>
                        <ul class="dropdown-menu">';
				foreach ($cbottom as $i => $a) {
					$foot_tpl .= '<li><a href="'.str_replace(['{modelid}', '{catid}', '{siteid}', '{m}'], [$modelid, $param['catid'], $this->siteid, ROUTE_M], urldecode($a['url'])).'"> <i class="'.$a['icon'].'"></i> '.$a['name'].' </a></li>';
					if ($i) {
						$foot_tpl .= '<div class="dropdown-line"></div>';
					}
				}
				$foot_tpl .= '</ul>
                    </div>
                </label>';
			}
		}
		include $this->admin_tpl('content_all');
	}
	public function add() {
		if(IS_POST) {
			define('INDEX_HTML',true);
			$info = $this->input->post('info');
			$catid = $info['catid'] = intval($info['catid']);
			//if(!trim($info['title'])) dr_json(0, L('title_is_empty'), array('field' => 'title'));
			$category = dr_cat_value($catid);
			if($category['type']==0) {
				$modelid = dr_cat_value($catid, 'modelid');
				$this->db->set_model($modelid);
				//如果该栏目设置了工作流，那么必须走工作流设定
				$setting = string2array($category['setting']);
				$workflowid = $setting['workflowid'];
				if($workflowid && $this->input->post('status')!=99) {
					//如果用户是超级管理员，那么则根据自己的设置来发布
					$info['status'] = cleck_admin(param::get_session('roleid')) ? intval($this->input->post('status')) : 1;
				} else {
					$info['status'] = 99;
				}
				$this->db->add_content($info);
				$this->cache_api->cache('sitemodels');
				dr_json(1, L('add_success'));
			} else {
				//单网页
				$this->page_db = pc_base::load_model('page_model');
				$modelid = dr_cat_value($catid, 'modelid');
				require_once CACHE_MODEL_PATH.'content_input.class.php';
				require_once CACHE_MODEL_PATH.'content_update.class.php';
				$content_input = new content_input(-2);
				$inputinfo = $content_input->get($info);
				$systeminfo = $inputinfo['system'];
				if($systeminfo['updatetime'] && !is_numeric($systeminfo['updatetime'])) {
					$systeminfo['updatetime'] = strtotime($systeminfo['updatetime']);
				} elseif(!$systeminfo['updatetime']) {
					$systeminfo['updatetime'] = SYS_TIME;
				} else {
					$systeminfo['updatetime'] = $systeminfo['updatetime'];
				}
				$this->fields = getcache('model_field_-2','model');
				pc_base::load_sys_class('upload','',0);
				foreach($this->fields as $field=>$t) {
					if ($t['formtype']=='editor') {
						// 提取缩略图
						$is_auto_thumb = $this->input->post('is_auto_thumb_'.$field);
						if(isset($systeminfo['thumb']) && isset($is_auto_thumb) && $is_auto_thumb && !$systeminfo['thumb']) {
							$downloadfiles = pc_base::load_sys_class('cache')->get_data('downloadfiles-'.$this->siteid);
							$auto_thumb_length = intval($this->input->post('auto_thumb_'.$field))-1;
							if (isset($downloadfiles) && $downloadfiles) {
								$systeminfo['thumb'] = $downloadfiles[$auto_thumb_length];
							} else {
								$setting = string2array($t['setting']);
								$watermark = $setting['watermark'];
								$attachment = $setting['attachment'];
								$image_reduce = $setting['image_reduce'];
								$watermark = dr_site_value('ueditor', $this->siteid) || $watermark ? 1 : 0;
								if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", str_replace('_"data:image', '"data:image', code2html($systeminfo[$field])), $matches)) {
									$this->upload = new upload('content',$systeminfo['catid'],$this->siteid);
									$images = [];
									foreach ($matches[3] as $img) {
										if (preg_match('/^(data:\s*image\/(\w+);base64,)/i', $img, $result)) {
											// 处理图片
											$ext = strtolower($result[2]);
											if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
												continue;
											}
											$content = base64_decode(str_replace($result[1], '', $img));
											if (strlen($content) > 30000000) {
												continue;
											}
											$rt = $this->upload->base64_image([
												'ext' => $ext,
												'content' => $content,
												'watermark' => $watermark,
												'attachment' => $this->upload->get_attach_info(intval($attachment), intval($image_reduce)),
											]);
											$attachments = array();
											if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
												$attachmentdb = pc_base::load_model('attachment_model');
												$att = $attachmentdb->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
												if ($att) {
													$attachments = dr_return_data($att['aid'], 'ok');
													$images[] = $att['aid'];
													// 删除现有附件
													// 开始删除文件
													$storage = new storage($this->module,$catid,$this->siteid);
													$storage->delete($this->upload->get_attach_info((int)$attachment), $rt['data']['file']);
													$rt['data'] = get_attachment($att['aid']);
												}
											}
											if (!$attachments) {
												$rt['data']['isadmin'] = 1;
												$attachments = $this->upload->save_data($rt['data'], 'ueditor:'.$this->rid);
												if ($attachments['code']) {
													// 附件归档
													$images[] = $attachments['code'];
													// 标记附件
													upload_json($attachments['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
												}
											}
										} else {
											$ext = get_image_ext($img);
											if (!$ext) {
												continue;
											}
											// 下载缩略图
											// 判断域名白名单
											$arr = parse_url($img);
											$domain = $arr['host'];
											if ($domain) {
												$this->sitedb = pc_base::load_model('site_model');
												$site_data = $this->sitedb->select();
												$sites = array();
												foreach ($site_data as $t) {
													$site_domain = parse_url($t['domain']);
													if ($site_domain['port']) {
														$sites[$site_domain['host'].':'.$site_domain['port']] = $t['siteid'];
													} else {
														$sites[$site_domain['host']] = $t['siteid'];
													}
													if ($t['mobile_domain']) {
														$site_mobile_domain = parse_url($t['mobile_domain']);
														if ($site_mobile_domain['port']) {
															$sites[$site_mobile_domain['host'].':'.$site_mobile_domain['port']] = $t['siteid'];
														} else {
															$sites[$site_mobile_domain['host']] = $t['siteid'];
														}
													}
												}
												if (isset($sites[$domain])) {
													// 过滤站点域名
													$images[] = $img;
												} elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
													// 过滤附件白名单
													$images[] = $img;
												} else {
													if(strpos($img, '://') === false) continue;
													$zj = 0;
													$remote = get_cache('attachment');
													if ($remote) {
														foreach ($remote as $t) {
															if (strpos($t['url'], $domain) !== false) {
																$zj = 1;
																break;
															}
														}
													}
													if ($zj == 0) {
														// 可以下载文件
														// 下载远程文件
														$file = dr_catcher_data($img, 8);
														if (!$file) {
															CI_DEBUG && log_message('debug', '服务器无法下载图片：'.$img);
														} else {
															// 尝试找一找附件库
															$attachmentdb = pc_base::load_model('attachment_model');
															$att = $attachmentdb->get_one(array('related'=>'%ueditor%', 'filemd5'=>md5($file)));
															if ($att) {
																$images[] = $att['aid'];
																// 标记附件
																upload_json($att['aid'],dr_get_file($att['aid']),$att['name'],format_file_size($att['size']));
															} else {
																// 下载归档
																$rt = $this->upload->down_file([
																	'url' => $img,
																	'timeout' => 5,
																	'watermark' => $watermark,
																	'attachment' => $this->upload->get_attach_info(intval($attachment), intval($image_reduce)),
																	'file_ext' => $ext,
																	'file_content' => $file,
																]);
																if ($rt['code']) {
																	$rt['data']['isadmin'] = 1;
																	$att = $this->upload->save_data($rt['data'], 'ueditor:'.$this->rid);
																	if ($att['code']) {
																		// 归档成功
																		$images[] = $att['code'];
																		// 标记附件
																		upload_json($att['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
																	}
																}
															}
														}
													} else {
														$images[] = $img;
													}
												}
											}
										}
									}
									if ($images) {
										$systeminfo['thumb'] = $images[$auto_thumb_length];
									}
								}
							}
						}
						// 提取描述信息
						$is_auto_description = $this->input->post('is_auto_description_'.$field);
						if(isset($systeminfo['description']) && isset($is_auto_description) && !$systeminfo['description']) {
							$auto_description_length = intval($this->input->post('auto_description_'.$field));
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', code2html($systeminfo[$field])), $auto_description_length);
						}
					}
				}
				if($this->input->post('edit')) {
					$this->page_db->update($systeminfo,array('catid'=>$catid));
				} else {
					$systeminfo['catid'] = $catid;
					$catid = $this->page_db->insert($systeminfo,true);
				}
				$this->page_db->create_html($catid);
				$this->cache_api->cache('page');
				$this->cache_api->del_file();
				dr_json(1, $this->input->post('edit') ? L('update_success') : L('add_success'));
			}
		} else {
			$show_header = $show_dialog = $show_validator = true;
			//设置cookie 在附件添加处调用
			param::set_cookie('module', 'content');

			if($this->input->get('catid') && $this->input->get('catid')) {
				$catid = intval($this->input->get('catid'));
				
				param::set_cookie('catid', $catid);
				$category = dr_cat_value($catid);
				if($category['type']==0) {
					$modelid = $category['modelid'];
					//取模型ID，依模型ID来生成对应的表单
					require CACHE_MODEL_PATH.'content_form.class.php';
					$content_form = new content_form($modelid,$catid,$this->categorys);
					$forminfos = $content_form->get();
 					$formValidator = $content_form->formValidator;
					$setting = string2array($category['setting']);
					$workflowid = $setting['workflowid'];
					$workflows = getcache('workflow_'.$this->siteid,'commons');
					if ($workflowid) {
						$workflows = $workflows[$workflowid];
						$workflows_setting = string2array($workflows['setting']);
						$nocheck_users = $workflows_setting['nocheck_users'];
					} else{
						$workflows_setting = array();
						$nocheck_users = array();
					}
					$admin_username = param::get_cookie('admin_username');
					if(!empty($nocheck_users) && in_array($admin_username, $nocheck_users)) {
						$priv_status = true;
					} else {
						$priv_status = false;
					}
					include $this->admin_tpl('content_add');
				} else {
					//单网页
					$this->page_db = pc_base::load_model('page_model');
					require CACHE_MODEL_PATH.'content_form.class.php';
					$content_form = new content_form(-2,$catid,$this->categorys);
					$formValidator = $content_form->formValidator;
					$r = $this->page_db->get_one(array('catid'=>$catid));
					if ($r) {
						$forminfos = $content_form->get($r);
						extract($r);
						$style_arr = explode(';',$style);
						$style_color = $style_arr[0];
						$style_font_weight = $style_arr[1] ? $style_arr[1] : '';
					} else {
						$forminfos = $content_form->get();
					}
					include $this->admin_tpl('content_page');
				}
			} else {
				include $this->admin_tpl('content_add');
			}
			header("Cache-control: private");
		}
	}
	
	public function edit() {
		//设置cookie 在附件添加处调用
		param::set_cookie('module', 'content');
		if(IS_POST) {
			define('INDEX_HTML',true);
			$info = $this->input->post('info');
			$id = $info['id'] = intval($this->input->post('id'));
			$catid = $info['catid'] = intval($info['catid']);
			//if(!trim($info['title'])) dr_json(0, L('title_is_empty'), array('field' => 'title'));
			$modelid = dr_cat_value($catid, 'modelid');
			$this->db->set_model($modelid);
			$this->db->edit_content($info,$id);
			dr_json(1, L('update_success'));
		} else {
			$show_header = $show_dialog = $show_validator = true;
			//从数据库获取内容
			$id = intval($this->input->get('id'));
			if(!$this->input->get('catid') || !$this->input->get('catid')) dr_admin_msg(0,L('missing_part_parameters'));
			$catid = intval($this->input->get('catid'));
			
			$this->model = getcache('model', 'commons');
			
			param::set_cookie('catid', $catid);
			$category = dr_cat_value($catid);
			$modelid = $category['modelid'];
			$this->db->table_name = $this->db->db_tablepre.$this->model[$modelid]['tablename'];
			$r = $this->db->get_one(array('id'=>$id));
			$this->db->table_name = $this->db->table_name.'_data_'.$r['tableid'];
			$r2 = $this->db->get_one(array('id'=>$id));
			if(!$r2) dr_admin_msg(0,L('subsidiary_table_datalost'));
			$data = array_merge($r,$r2);
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form($modelid,$catid,$this->categorys);

			$forminfos = $content_form->get($data);
			$formValidator = $content_form->formValidator;
			include $this->admin_tpl('content_edit');
		}
		header("Cache-control: private");
	}
	/**
	 * 删除
	 */
	public function delete() {
		if($this->input->post('dosubmit')) {
			$modelid = intval($this->input->get('modelid'));
			$catid = intval($this->input->get('catid'));
			if ($modelid && !$catid) {
				if(!$modelid) dr_json(0, L('choose_model'));
				$ids = $this->input->get_post_ids();
				if(empty($ids)) dr_json(0, L('you_do_not_check'));
				$this->hits_db = pc_base::load_model('hits_model');
				$this->queue = pc_base::load_model('queue_model');
				//附件初始化
				$attachment = pc_base::load_model('attachment_model');
				$this->content_check_db = pc_base::load_model('content_check_model');
				$this->position_data_db = pc_base::load_model('position_data_model');
				$this->search_db = pc_base::load_model('search_model');
				$this->comment = pc_base::load_app_class('comment', 'comment');
				$this->url = pc_base::load_app_class('url', 'content');
				$this->db->set_model($modelid);
				$data = $this->db->select(array('id'=>(array)$ids));
				foreach($data as $r) {
					$this->cache->clear('module_'.$modelid.'_show_id_'.$r['id']);
					// 删除钩子
					pc_base::load_sys_class('hooks')::trigger('module_content_delete', $r);
					$sethtml = dr_cat_value($r['catid'], 'sethtml');
					$siteid = dr_cat_value($r['catid'], 'siteid');
					
					$html_root = SYS_HTML_ROOT;
					if($sethtml) $html_root = '';
					
					$setting = dr_string2array(dr_cat_value($r['catid'], 'setting'));
					$content_ishtml = $setting['content_ishtml'];
					$search_model = getcache('search_model_'.$this->siteid,'search');
					$typeid = $search_model[$modelid]['typeid'];
					
					if($content_ishtml && !$r['islink']) {
						list($urls) = $this->url->show($r['id'], 0, $r['catid'], $r['inputtime'], dr_value($modelid, $r['id'], 'prefix'));
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
					$this->db->delete_content($r['id'],$r['catid']);
					//删除统计表数据
					$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));
					//删除附件
					$attachment->api_delete('c-'.$r['catid'].'-'.$r['id']);
					//删除审核表数据
					$this->content_check_db->delete(array('checkid'=>'c-'.$r['id'].'-'.$modelid));
					//删除推荐位数据
					$this->position_data_db->delete(array('id'=>$r['id'],'catid'=>$r['catid'],'module'=>'content'));
					//删除全站搜索中数据
					$this->search_db->delete_search($typeid,$r['id']);
					//删除关键词和关键词数量重新统计
					$keyword_db = pc_base::load_model('keyword_model');
					$keyword_data_db = pc_base::load_model('keyword_data_model');
					$keyword_arr = $keyword_data_db->select(array('siteid'=>$siteid,'contentid'=>$r['id'].'-'.$modelid));
					if($keyword_arr){
						foreach ($keyword_arr as $val){
							$keyword_db->update(array('videonum'=>'-=1'),array('id'=>$val['tagid']));
						}
						$keyword_data_db->delete(array('siteid'=>$siteid,'contentid'=>$r['id'].'-'.$modelid));
						$keyword_db->delete(array('videonum'=>'0'));
					}
					
					//删除相关的评论,删除前应该判断是否还存在此模块
					if(module_exists('comment')){
						$commentid = id_encode('content_'.$r['catid'], $r['id'], $siteid);
						$this->comment->del($commentid, $siteid, $r['id'], $r['catid']);
					}
					
				}
			} else {
				if(!$catid) dr_json(0, L('missing_part_parameters'));
				$modelid = dr_cat_value($catid, 'modelid');
				$sethtml = dr_cat_value($catid, 'sethtml');
				$siteid = dr_cat_value($catid, 'siteid');
				
				$html_root = SYS_HTML_ROOT;
				if($sethtml) $html_root = '';
				
				$setting = dr_string2array(dr_cat_value($catid, 'setting'));
				$content_ishtml = $setting['content_ishtml'];
				$this->db->set_model($modelid);
				$this->hits_db = pc_base::load_model('hits_model');
				$this->queue = pc_base::load_model('queue_model');
				if($this->input->get('ajax_preview')) {
					$ids = array(0=>intval($this->input->get('id')));
				} else {
					$ids = $this->input->get_post_ids();
				}
				if(empty($ids)) dr_json(0, L('you_do_not_check'));
				//附件初始化
				$attachment = pc_base::load_model('attachment_model');
				$this->content_check_db = pc_base::load_model('content_check_model');
				$this->position_data_db = pc_base::load_model('position_data_model');
				$this->search_db = pc_base::load_model('search_model');
				$this->comment = pc_base::load_app_class('comment', 'comment');
				$search_model = getcache('search_model_'.$this->siteid,'search');
				$typeid = $search_model[$modelid]['typeid'];
				$this->url = pc_base::load_app_class('url', 'content');
				
				foreach($ids as $id) {
					$r = $this->db->get_one(array('id'=>$id));
					$this->cache->clear('module_'.$modelid.'_show_id_'.$r['id']);
					// 删除钩子
					pc_base::load_sys_class('hooks')::trigger('module_content_delete', $r);
					if($content_ishtml && !$r['islink']) {
						list($urls) = $this->url->show($id, 0, $r['catid'], $r['inputtime'], dr_value($modelid, $r['id'], 'prefix'));
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
					$this->db->delete_content($id,$catid);
					//删除统计表数据
					$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$id));
					//删除附件
					$attachment->api_delete('c-'.$catid.'-'.$id);
					//删除审核表数据
					$this->content_check_db->delete(array('checkid'=>'c-'.$id.'-'.$modelid));
					//删除推荐位数据
					$this->position_data_db->delete(array('id'=>$id,'catid'=>$catid,'module'=>'content'));
					//删除全站搜索中数据
					$this->search_db->delete_search($typeid,$id);
					//删除关键词和关键词数量重新统计
					$keyword_db = pc_base::load_model('keyword_model');
					$keyword_data_db = pc_base::load_model('keyword_data_model');
					$keyword_arr = $keyword_data_db->select(array('siteid'=>$siteid,'contentid'=>$id.'-'.$modelid));
					if($keyword_arr){
						foreach ($keyword_arr as $val){
							$keyword_db->update(array('videonum'=>'-=1'),array('id'=>$val['tagid']));
						}
						$keyword_data_db->delete(array('siteid'=>$siteid,'contentid'=>$id.'-'.$modelid));
						$keyword_db->delete(array('videonum'=>'0'));
					}
					
					//删除相关的评论,删除前应该判断是否还存在此模块
					if(module_exists('comment')){
						$commentid = id_encode('content_'.$catid, $id, $siteid);
						$this->comment->del($commentid, $siteid, $id, $catid);
					}
				}
			}
			//更新栏目统计
			$this->db->cache_items();
			$this->cache_api->cache('sitemodels');
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 更新回收站
	 */
	public function recycle() {
		$this->content_check_db = pc_base::load_model('content_check_model');
		if($this->input->post('dosubmit')) {
			$modelid = intval($this->input->get('modelid'));
			$catid = intval($this->input->get('catid'));
			$recycle = intval($this->input->get('recycle'));
			$this->url = pc_base::load_app_class('url', 'content');
			$this->queue = pc_base::load_model('queue_model');
			if ($modelid && !$catid) {
				if(!$modelid) dr_json(0, L('choose_model'));
				if($this->input->post('id')) {
					$ids = array(0=>intval($this->input->post('id')));
				} else {
					$ids = $this->input->get_post_ids();
				}
				if(empty($ids)) dr_json(0, L('you_do_not_check'));
				if ($modelid) {
					$this->db->set_model($modelid);
					foreach($ids as $id) {
						if ($recycle) {
							$r = $this->db->get_one(array('id'=>$id));
							$this->cache->clear('module_'.$modelid.'_show_id_'.$r['id']);
							$sethtml = dr_cat_value($r['catid'], 'sethtml');
							$siteid = dr_cat_value($r['catid'], 'siteid');
							
							$html_root = SYS_HTML_ROOT;
							if($sethtml) $html_root = '';
							
							$setting = dr_string2array(dr_cat_value($r['catid'], 'setting'));
							$content_ishtml = $setting['content_ishtml'];
							
							if($content_ishtml && !$r['islink']) {
								list($urls) = $this->url->show($r['id'], 0, $r['catid'], $r['inputtime'], dr_value($modelid, $r['id'], 'prefix'));
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
							$this->db->update(array('status'=>100),array('id'=>$id));
							$this->content_check_db->update(array('status'=>100),array('checkid'=>'c-'.$id.'-'.$modelid));
						} else {
							$this->db->update(array('status'=>99),array('id'=>$id));
							$this->content_check_db->update(array('status'=>99),array('checkid'=>'c-'.$id.'-'.$modelid));
						}
					}
					dr_json(1, L('operation_success'));
				}
			} else {
				if(!$catid) dr_json(0, L('missing_part_parameters'));
				if($this->input->post('id')) {
					$ids = array(0=>intval($this->input->post('id')));
				} else {
					$ids = $this->input->get_post_ids();
				}
				if(empty($ids)) dr_json(0, L('you_do_not_check'));
				if ($catid) {
					$modelid = dr_cat_value($catid, 'modelid');
					$this->db->set_model($modelid);
					foreach($ids as $id) {
						if ($recycle) {
							$r = $this->db->get_one(array('id'=>$id));
							$this->cache->clear('module_'.$modelid.'_show_id_'.$r['id']);
							$sethtml = dr_cat_value($r['catid'], 'sethtml');
							$siteid = dr_cat_value($r['catid'], 'siteid');
							
							$html_root = SYS_HTML_ROOT;
							if($sethtml) $html_root = '';
							
							$setting = dr_string2array(dr_cat_value($r['catid'], 'setting'));
							$content_ishtml = $setting['content_ishtml'];
							
							if($content_ishtml && !$r['islink']) {
								list($urls) = $this->url->show($r['id'], 0, $r['catid'], $r['inputtime'], dr_value($modelid, $r['id'], 'prefix'));
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
							$this->db->update(array('status'=>100),array('id'=>$id));
							$this->content_check_db->update(array('status'=>100),array('checkid'=>'c-'.$id.'-'.$modelid));
						} else {
							$this->db->update(array('status'=>99),array('id'=>$id));
							$this->content_check_db->update(array('status'=>99),array('checkid'=>'c-'.$id.'-'.$modelid));
						}
					}
					dr_json(1, L('operation_success'));
				}
			}
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 更新
	 */
	public function update() {
		if($this->input->post('dosubmit')) {
			$catid = intval($this->input->get('catid'));
			if ($catid) {
				$modelid = dr_cat_value($catid, 'modelid');
				$this->db->set_model($modelid);
				$this->db->update(array($this->input->post('field')=>$this->input->post('value')),array('id'=>$this->input->post('id')));
				dr_json(1, L('operation_success'));
			} else {
				$modelid = intval($this->input->get('modelid'));
				$this->db->set_model($modelid);
				$this->db->update(array($this->input->post('field')=>$this->input->post('value')),array('id'=>$this->input->post('id')));
				dr_json(1, L('operation_success'));
			}
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 回收站清空
	 */
	public function public_recycle_del() {
		$page = (int)$this->input->get('page');
		$catid = intval($this->input->get('catid'));
		$modelid = intval($this->input->get('modelid'));
		$catid = intval($this->input->get('catid'));
		$this->hits_db = pc_base::load_model('hits_model');
		$this->queue = pc_base::load_model('queue_model');

		//附件初始化
		$attachment = pc_base::load_model('attachment_model');
		$this->content_check_db = pc_base::load_model('content_check_model');
		$this->position_data_db = pc_base::load_model('position_data_model');
		$this->search_db = pc_base::load_model('search_model');
		$this->comment = pc_base::load_app_class('comment', 'comment');
		$this->url = pc_base::load_app_class('url', 'content');
		if ($modelid && !$catid) {
			if(!$modelid) dr_json(0, L('choose_model'));
			$this->db->set_model($modelid);

			$psize = 10;
			if (!$page) {
				$nums = $this->db->count(array('status'=>100));
				if (!$nums) {
					dr_json(0, L('数据为空'));
				}
				$tpage = ceil($nums / $psize); // 总页数
				dr_json(1, L('即将执行清空回收站命令'), [
					'jscode' => 'iframe_show(\''.L('清空回收站').'\', \'?m=content&c=content&a=public_recycle_del&modelid='.$modelid.'&page=1&total='.$nums.'&tpage='.$tpage.'\', \'500px\', \'300px\', \'load\')'
				]);
			}

			$tpage = (int)$this->input->get('tpage');
			$total = (int)$this->input->get('total');

			$data = $this->db->listinfo(array('status'=>100),'id DESC',1,$psize);
			if (!$data) {
				html_msg(1, L('共删除'.$total.'条数据'));
			}

			foreach($data as $r) {
				$this->cache->clear('module_'.$modelid.'_show_id_'.$r['id']);
				// 删除钩子
				pc_base::load_sys_class('hooks')::trigger('module_content_delete', $r);
				$sethtml = dr_cat_value($r['catid'], 'sethtml');
				$siteid = dr_cat_value($r['catid'], 'siteid');
				
				$html_root = SYS_HTML_ROOT;
				if($sethtml) $html_root = '';
				
				$setting = dr_string2array(dr_cat_value($r['catid'], 'setting'));
				$content_ishtml = $setting['content_ishtml'];
				$search_model = getcache('search_model_'.$this->siteid,'search');
				$typeid = $search_model[$modelid]['typeid'];
				if($content_ishtml && !$r['islink']) {
					list($urls) = $this->url->show($r['id'], 0, $r['catid'], $r['inputtime'], dr_value($modelid, $r['id'], 'prefix'));
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
				$this->db->delete_content($r['id'],$r['catid']);
				//删除统计表数据
				$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));
				//删除附件
				$attachment->api_delete('c-'.$r['catid'].'-'.$r['id']);
				//删除审核表数据
				$this->content_check_db->delete(array('checkid'=>'c-'.$r['id'].'-'.$modelid));
				//删除推荐位数据
				$this->position_data_db->delete(array('id'=>$r['id'],'catid'=>$r['catid'],'module'=>'content'));
				//删除全站搜索中数据
				$this->search_db->delete_search($typeid,$r['id']);
				//删除关键词和关键词数量重新统计
				$keyword_db = pc_base::load_model('keyword_model');
				$keyword_data_db = pc_base::load_model('keyword_data_model');
				$keyword_arr = $keyword_data_db->select(array('siteid'=>$siteid,'contentid'=>$r['id'].'-'.$modelid));
				if($keyword_arr){
					foreach ($keyword_arr as $val){
						$keyword_db->update(array('videonum'=>'-=1'),array('id'=>$val['tagid']));
					}
					$keyword_data_db->delete(array('siteid'=>$siteid,'contentid'=>$r['id'].'-'.$modelid));
					$keyword_db->delete(array('videonum'=>'0'));
				}

				//删除相关的评论,删除前应该判断是否还存在此模块
				if(module_exists('comment')){
					$commentid = id_encode('content_'.$r['catid'], $r['id'], $siteid);
					$this->comment->del($commentid, $siteid, $r['id'], $r['catid']);
				}

				// 回收站钩子
				pc_base::load_sys_class('hooks')::trigger('module_content_recycle', $r);
			}
			//更新栏目统计
			$this->db->cache_items();
			$this->cache_api->cache('sitemodels');

			html_msg(1, L('正在执行中【'.$tpage.'/'.($page+1).'】...'), '?m=content&c=content&a=public_recycle_del&modelid='.$modelid.'&total='.$total.'&tpage='.$tpage.'&page='.($page+1));
		} else {
			if(!$catid) dr_json(0, L('missing_part_parameters'));
			$modelid = dr_cat_value($catid, 'modelid');
			$sethtml = dr_cat_value($catid, 'sethtml');
			$siteid = dr_cat_value($catid, 'siteid');
			
			$html_root = SYS_HTML_ROOT;
			if($sethtml) $html_root = '';
			
			$setting = dr_string2array(dr_cat_value($catid, 'setting'));
			$content_ishtml = $setting['content_ishtml'];
			$this->db->set_model($modelid);
			$search_model = getcache('search_model_'.$this->siteid,'search');
			$typeid = $search_model[$modelid]['typeid'];

			$psize = 10;
			if (!$page) {
				$nums = $this->db->count(array('catid'=>$catid, 'status'=>100));
				if (!$nums) {
					dr_json(0, L('数据为空'));
				}
				$tpage = ceil($nums / $psize); // 总页数
				dr_json(1, L('即将执行清空回收站命令'), [
					'jscode' => 'iframe_show(\''.L('清空回收站').'\', \'?m=content&c=content&a=public_recycle_del&catid='.$catid.'&page=1&total='.$nums.'&tpage='.$tpage.'\', \'500px\', \'300px\', \'load\')'
				]);
			}

			$tpage = (int)$this->input->get('tpage');
			$total = (int)$this->input->get('total');

			$data = $this->db->listinfo(array('catid'=>$catid, 'status'=>100),'id DESC',1,$psize);
			if (!$data) {
				html_msg(1, L('共删除'.$total.'条数据'));
			}

			foreach($data as $r) {
				$this->cache->clear('module_'.$modelid.'_show_id_'.$r['id']);
				// 删除钩子
				pc_base::load_sys_class('hooks')::trigger('module_content_delete', $r);
				if($content_ishtml && !$r['islink']) {
					list($urls) = $this->url->show($r['id'], 0, $r['catid'], $r['inputtime'], dr_value($modelid, $r['id'], 'prefix'));
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
				$this->db->delete_content($r['id'],$catid);
				//删除统计表数据
				$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));
				//删除附件
				$attachment->api_delete('c-'.$catid.'-'.$r['id']);
				//删除审核表数据
				$this->content_check_db->delete(array('checkid'=>'c-'.$r['id'].'-'.$modelid));
				//删除推荐位数据
				$this->position_data_db->delete(array('id'=>$r['id'],'catid'=>$catid,'module'=>'content'));
				//删除全站搜索中数据
				$this->search_db->delete_search($typeid,$r['id']);
				//删除关键词和关键词数量重新统计
				$keyword_db = pc_base::load_model('keyword_model');
				$keyword_data_db = pc_base::load_model('keyword_data_model');
				$keyword_arr = $keyword_data_db->select(array('siteid'=>$siteid,'contentid'=>$r['id'].'-'.$modelid));
				if($keyword_arr){
					foreach ($keyword_arr as $val){
						$keyword_db->update(array('videonum'=>'-=1'),array('id'=>$val['tagid']));
					}
					$keyword_data_db->delete(array('siteid'=>$siteid,'contentid'=>$r['id'].'-'.$modelid));
					$keyword_db->delete(array('videonum'=>'0'));
				}

				//删除相关的评论,删除前应该判断是否还存在此模块
				if(module_exists('comment')){
					$commentid = id_encode('content_'.$catid, $r['id'], $siteid);
					$this->comment->del($commentid, $siteid, $r['id'], $catid);
				}

				// 回收站钩子
				pc_base::load_sys_class('hooks')::trigger('module_content_recycle', $r);
			}
			//更新栏目统计
			$this->db->cache_items();
			$this->cache_api->cache('sitemodels');

			html_msg(1, L('正在执行中【'.$tpage.'/'.($page+1).'】...'), '?m=content&c=content&a=public_recycle_del&catid='.$catid.'&total='.$total.'&tpage='.$tpage.'&page='.($page+1));
		}
	}
	/**
	 * 过审内容
	 */
	public function pass() {
		$admin_username = param::get_cookie('admin_username');
		$catid = intval($this->input->get('catid'));
		
		if(!$catid) dr_json(0, L('missing_part_parameters'));
		$category = dr_cat_value($catid);
		$setting = string2array($category['setting']);
		$workflowid = $setting['workflowid'];
		//只有存在工作流才需要审核
		if($workflowid) {
			$steps = intval($this->input->get('steps'));
			//检查当前用户有没有当前工作流的操作权限
			$workflows = getcache('workflow_'.$this->siteid,'commons');
			$workflows = $workflows[$workflowid];
			$workflows_setting = string2array($workflows['setting']);
			//将有权限的级别放到新数组中
			$admin_privs = array();
			foreach($workflows_setting as $_k=>$_v) {
				if(empty($_v)) continue;
				foreach($_v as $_value) {
					if($_value==$admin_username) $admin_privs[$_k] = $_k;
				}
			}
			if(!cleck_admin(param::get_session('roleid')) && $steps && !in_array($steps,$admin_privs)) dr_json(0, L('permission_to_operate'));
			//更改内容状态
				if($this->input->get('reject')) {
				//退稿
					$status = 0;
				} else {
					//工作流审核级别
					$workflow_steps = $workflows['steps'];
					
					if($workflow_steps>$steps) {
						$status = $steps+1;
					} else {
						$status = 99;
					}
				}
				
				$modelid = dr_cat_value($catid, 'modelid');
				$this->db->set_model($modelid);
				$this->db->search_db = pc_base::load_model('search_model');
				//审核通过，检查投稿奖励或扣除积分
				if ($status==99) {
					$html = pc_base::load_app_class('html', 'content');
					$this->url = pc_base::load_app_class('url', 'content');
					$member_db = pc_base::load_model('member_model');
					if ($this->input->post('ids') && !empty($this->input->post('ids'))) {
						foreach ($this->input->post('ids') as $id) {
							$content_info = $this->db->get_content($catid,$id);
							$memberinfo = $member_db->get_one(array('username'=>$content_info['username']), 'userid, username');
							$flag = $catid.'_'.$id;
							if($setting['presentpoint']>0) {
								pc_base::load_app_class('receipts','pay',0);
								receipts::point($setting['presentpoint'],$memberinfo['userid'], $memberinfo['username'], $flag,'selfincome',L('contribute_add_point'),$memberinfo['username']);
							} else {
								pc_base::load_app_class('spend','pay',0);
								spend::point($setting['presentpoint'], L('contribute_del_point'), $memberinfo['userid'], $memberinfo['username'], '', '', $flag);
							}
							if($setting['content_ishtml'] == '1'){//栏目有静态配置
								list($urls) = $this->url->show($id, 0, $content_info['catid'], $content_info['inputtime'], $content_info['prefix'], $content_info, 'add');
								$html->show($urls[1],$urls['data'],0);
 							}
							//更新到全站搜索
							$inputinfo = array();
							$inputinfo['system'] = $content_info;
							$this->db->search_api($id,$inputinfo);
						}
					} else if ($this->input->get('id')) {
						$id = intval($this->input->get('id'));
						$content_info = $this->db->get_content($catid,$id);
						$memberinfo = $member_db->get_one(array('username'=>$content_info['username']), 'userid, username');
						$flag = $catid.'_'.$id;
						if($setting['presentpoint']>0) {
							pc_base::load_app_class('receipts','pay',0);
							receipts::point($setting['presentpoint'],$memberinfo['userid'], $memberinfo['username'], $flag,'selfincome',L('contribute_add_point'),$memberinfo['username']);
						} else {
							pc_base::load_app_class('spend','pay',0);
							spend::point($setting['presentpoint'], L('contribute_del_point'), $memberinfo['userid'], $memberinfo['username'], '', '', $flag);
						}
						//单篇审核，生成静态
						if($setting['content_ishtml'] == '1'){//栏目有静态配置
						list($urls) = $this->url->show($id, 0, $content_info['catid'], $content_info['inputtime'], $content_info['prefix'], $content_info, 'add');
						$html->show($urls[1],$urls['data'],0);
						}
						//更新到全站搜索
						$inputinfo = array();
						$inputinfo['system'] = $content_info;
						$this->db->search_api($id,$inputinfo);
					}
				}
				if($this->input->get('ajax_preview')) {
					$ids = $this->input->get('id');
				}
				$this->db->status((isset($ids) && $ids ? $ids : $this->input->post('ids')),$status);
		}
		dr_json(1, L('operation_success'));
	}
	/**
	 * 用于控制器的存储
	 */
	public function public_save_value_edit() {
		$cache_uid = $this->cache->get_auth_data('function_list_save_text_value', 1);
		if (!$cache_uid) {
			dr_json(0, L('权限认证过期，请重试'));
		} elseif (param::get_session('userid') != $cache_uid) {
			dr_json(0, L('权限认证失败，请重试'));
		}
		$id = intval($this->input->get('id'));
		$catid = intval($this->input->get('catid'));
		$name = dr_safe_filename($this->input->get('name'));
		$value = $this->input->get('value');
		$after = dr_safe_filename($this->input->get('after'));
		$before = dr_safe_filename($this->input->get('before'));
		if(!$catid) dr_json(0, L('missing_part_parameters'));
		if($id && $catid) {
			$modelid = dr_cat_value($catid, 'modelid');
			$this->db->set_model($modelid);
			// 查询数据
			$row = $this->db->get_one(array('catid'=>$catid,'id'=>$id));
			if (!$row) {
				dr_json(0, L('数据'.$id.'不存在'));
			} elseif ($row[$name] == $value) {
				dr_json(1, L('没有变化'));
			}
			$this->db->update(array($name=>$value),array('id'=>$id));
			// 提交之后的操作
			if ($after) {
				call_user_func_array($after, [$row]);
			}
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	// 批量推送
	public function public_send_edit() {
		$action = $this->input->get('action');
		$modelid = intval($this->input->get('modelid'));
		$id = $this->input->get('ids');
		if (!$id) {
			dr_json(0, L('所选数据不存在'));
		}
		if($modelid) {
			$this->db->set_model($modelid);
			$this->db->update(array('updatetime'=>SYS_TIME),array('id'=>(array)$id));
			dr_json(1, L('操作成功'));
		} else {
			$catid = intval($this->input->get('catid'));
			if(!$catid) dr_json(0, L('missing_part_parameters'));
			if($catid) {
				if ($action == 'reject') {
					include $this->admin_tpl('content_send');
				} else {
					$modelid = dr_cat_value($catid, 'modelid');
					$this->db->set_model($modelid);
					$this->db->update(array('updatetime'=>SYS_TIME),array('id'=>(array)$id));
					dr_json(1, L('操作成功'));
				}
			}
		}
	}
	/**
	 * 显示栏目菜单列表
	 */
	public function public_categorys() {
		$show_header = true;
		$cfg = getcache('common','commons');
		$ajax_show = intval($cfg['category_ajax']);
		$from = $this->input->get('from') && in_array($this->input->get('from'),array('block')) ? $this->input->get('from') : 'content';
		$tree = pc_base::load_sys_class('tree');
		if($from=='content' && !cleck_admin(param::get_session('roleid'))) {	
			$this->priv_db = pc_base::load_model('category_priv_model');
			$priv_result = $this->priv_db->select(array('action'=>'init','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$this->siteid,'is_admin'=>1));
			$priv_catids = array();
			foreach($priv_result as $_v) {
				$priv_catids[] = $_v['catid'];
			}
			if(empty($priv_catids)) return '';
		}
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $r) {
				if($r['siteid']!=$this->siteid || $r['type']==2) continue;
				if($from=='content' && !cleck_admin(param::get_session('roleid')) && !in_array($r['catid'],$priv_catids)) {
					$arrchildid = explode(',',$r['arrchildid']);
					$array_intersect = array_intersect($priv_catids,$arrchildid);
					if(empty($array_intersect)) continue;
				}
				if($r['type']==1 || $from=='block') {
					if($r['type']==0) {
						$r['vs_show'] = "<a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."&type=show' target='".$this->input->get('from')."_right'>[".L('content_page')."]</a>";
					} else {
						$r['vs_show'] ='';
					}
					$r['icon_type'] = 'file';
					$r['category_edit'] = "<span class='folder'></span><a href='?m=content&c=content&a=add&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."' target='right'>".$r['catname']."</a>";
					$r['add_icon'] = '';
					$r['type'] = 'add';
				} else {
					$r['icon_type'] = $r['vs_show'] = '';
					$r['category_edit'] = "<span class='folder'>".$r['catname']."</span>";
					$r['type'] = 'init';
					$r['add_icon'] = "<a target='right' href='?m=content&c=content&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."' onclick=\"javascript:dr_content_submit('?m=content&c=content&a=add&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."','add')\"><img src='".IMG_PATH."add_content.png' alt='".L('add')."'></a> ";
				}
				$categorys[$r['catid']] = $r;
			}
		}
		if(!empty($categorys)) {
			$tree->init($categorys);
				switch($from) {
					case 'block':
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&type=list' target='".$this->input->get('from')."_right'>\$catname</a> \$vs_show</span>";
						$strs2 = "<img src='".IMG_PATH."folder.png'> <a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&type=category' target='".$this->input->get('from')."_right'>\$catname</a>";
					break;

					default:
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=content&c=content&a=\$type&menuid=".intval($this->input->get('menuid'))."&catid=\$catid' target='right'>\$catname</a></span>";
						$strs2 = "\$category_edit";
						break;
				}
			$categorys = $tree->get_treeview(0,'category_tree',$strs,$strs2,$ajax_show);
		} else {
			$categorys = L('please_add_category');
		}
		include $this->admin_tpl('category_tree');
		exit;
	}
	/**
	 * 检查标题是否存在
	 */
	public function public_check_title() {
		if($this->input->get('data')=='' || (!$this->input->get('catid'))) return '';
		$is_ajax = intval($this->input->get('is_ajax'));
		$catid = intval($this->input->get('catid'));
		$id = intval($this->input->get('id'));
		$modelid = dr_cat_value($catid, 'modelid');
		$this->db->set_model($modelid);
		$title = $this->input->get('data');
		if(CHARSET=='gbk') $title = iconv('utf-8','gbk',$title);
		$r = $this->db->get_one(array('title'=>$title, 'id<>'=>$id));
		if ($is_ajax) {
			if($r) {
				exit(L('已经有相同的存在'));
			}
		} else {
			if($r) {
				exit('1');
			} else {
				exit('0');
			}
		}
	}

	/**
	 * 修改某一字段数据
	 */
	public function update_param() {
		$id = intval($this->input->get('id'));
		$field = $this->input->get('field');
		$modelid = intval($this->input->get('modelid'));
		$value = $this->input->get('value');
		if (CHARSET!='utf-8') {
			$value = iconv('utf-8', 'gbk', $value);
		}
		//检查字段是否存在
		$this->db->set_model($modelid);
		if ($this->db->field_exists($field)) {
			$this->db->update(array($field=>$value), array('id'=>$id));
			exit('200');
		} else {
			$r = $this->db->get_one(array('id' => $id), 'tableid');
			$this->db->table_name = $this->db->table_name.'_data_'.$r['tableid'];
			if ($this->db->field_exists($field)) {
				$this->db->update(array($field=>$value), array('id'=>$id));
				exit('200');
			} else {
				exit('300');
			}
		}
	}
	
	/**
	 * 图片裁切
	 */
	public function public_crop() {
		$this->att_db = pc_base::load_model('attachment_model');
		if (IS_POST) {
			$post = $this->input->post('data');
			$aid = (int)$post['aid'];
			if (!$aid) {
				dr_json(0, L('附件id不能为空'));
			}
			$catid = intval($this->input->get('catid'));
			if ($this->input->get('module') && !empty($this->input->get('module'))) {
				$module = $this->input->get('module');
			}
			$data = $this->att_db->get_one(array('aid' => $aid));
			if (!$data) {
				dr_json(0, L('附件'.$aid.'不存在'));
			}

			if (!dr_is_image($data['fileext'])) {
				dr_json(0, L('此文件不属于图片'));
			}

			$data['file'] = SYS_UPLOAD_PATH.$data['filepath'];
			$data['url'] = dr_get_file_url($data);

			// 文件真实地址
			if ($data['remote']) {
				$remote = get_cache('attachment', $data['remote']);
				if (!$remote) {
					// 远程地址无效
					dr_json(0, L('自定义附件（'.$data['remote'].'）的配置已经不存在'));
				} else {
					$data['file'] = $remote['value']['path'].$data['filepath'];
					if (!is_file($data['file'])) {
						dr_json(0, L('远程附件无法编辑'));
					}
				}
			}

			if (!$post['w']) {
				dr_json(0, L('图形宽度不规范'));
			}
			$image = pc_base::load_sys_class('image');

			!$name && $name = substr(md5(SYS_TIME.$data['file'].uniqid()), rand(0, 20), 15);

			if (defined('SYS_ATTACHMENT_SAVE_TYPE') && SYS_ATTACHMENT_SAVE_TYPE) {
				// 按后台设置目录
				if (SYS_ATTACHMENT_SAVE_DIR) {
					$path = str_replace(
							array('{y}', '{m}', '{d}', '{yy}', '.'),
							array(date('Y', SYS_TIME), date('m', SYS_TIME), date('d', SYS_TIME), date('y', SYS_TIME), ''),
							trim(SYS_ATTACHMENT_SAVE_DIR, '/')).'/';
				} else {
					$path = '';
				}
			} else {
				// 默认目录格式
				$path = date('Y/md/', SYS_TIME);
			}
			$filetype = fileext($data['file']);
			$file_path = (SYS_ATTACHMENT_FILE ? $this->siteid.'/' : '').$path.$name.'.'.$filetype;
			dr_mkdirs(SYS_UPLOAD_PATH.(SYS_ATTACHMENT_FILE ? $this->siteid.'/' : '').$path);
			$new_file = SYS_UPLOAD_PATH.$file_path;

			if ($post['sx']==-1 || $post['sy']==-1) {
				$config = array();
				$config['source_image'] = $data['file'];
				$config['new_image'] = $new_file;
				if ($post['sx']==-1) {
					$config['rotation_angle'] = 'hor';
				}
				if ($post['sy']==-1) {
					$config['rotation_angle'] = 'vrt';
				}
				$image->initialize($config);

				if (!$image->rotate()) {
					$err = $image->display_errors();
					dr_json(0, $err ? $err : L('旋转失败'));
				}
			} else if ($post['r']==-90 || $post['r']==-270) {
				$config = array();
				$config['source_image'] = $data['file'];
				$config['new_image'] = $new_file;
				if ($post['r']==-90) {
					$config['rotation_angle'] = '90';
				}
				if ($post['r']==-270) {
					$config['rotation_angle'] = '270';
				}
				$image->initialize($config);

				if (!$image->rotate()) {
					$err = $image->display_errors();
					dr_json(0, $err ? $err : L('旋转失败'));
				}
			} else {
				$config = array();
				$config['source_image'] = $data['file'];
				$config['new_image'] = $new_file;
				$config['maintain_ratio'] = false;
				$config['width'] = $post['w'];
				$config['height'] = $post['h'];
				$config['x_axis'] = $post['x'];
				$config['y_axis'] = $post['y'];
				$image->initialize($config);

				if (!$image->crop()) {
					$err = $image->display_errors();
					dr_json(0, $err ? $err : L('剪切失败'));
				}
			}

			$info = array();
			if (dr_is_image($config['new_image'])) {
				$info = array(
					'width' => $config['width'],
					'height' => $config['height'],
				);
			}

			$uploadedfile['module'] = $this->input->get('module');
			$uploadedfile['catid'] = $this->input->get('catid');
			$uploadedfile['siteid'] = $this->siteid;
			$uploadedfile['userid'] = $this->userid;
			$uploadedfile['uploadtime'] = SYS_TIME;
			$uploadedfile['uploadip'] = ip();
			$uploadedfile['status'] = SYS_ATTACHMENT_STAT ? 0 : 1;
			$uploadedfile['authcode'] = md5($file_path);
			$uploadedfile['filemd5'] = md5_file($config['new_image']);
			$uploadedfile['remote'] = 0;
			$uploadedfile['attachinfo'] = dr_array2string($info);
			$uploadedfile['isimage'] = in_array($filetype, array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp')) ? 1 : 0;
			$uploadedfile['filepath'] = $file_path;
			$uploadedfile['related'] = 'rand';
			$uploadedfile['filename'] = file_name($data['file']);
			$uploadedfile['filesize'] = filesize($config['new_image']);
			$uploadedfile['fileext'] = $filetype;
			$uploadedfile['downloads'] = 0;
			$aid = $this->att_db->api_add($uploadedfile);
			upload_json($aid,SYS_UPLOAD_URL.$file_path,file_name($data['file']),format_file_size(filesize($config['new_image'])));

			dr_json(1, L('operation_success'), array('aid' => $aid, 'filepath' => SYS_UPLOAD_URL.$file_path));
		}
		$show_header = true;
		$aid = (int)$this->input->get('aid');
		if (!$aid) {
			dr_admin_msg(0, L('附件id不能为空'), 'close', 3, 1);
		}
		$catid = intval($this->input->get('catid'));
		if ($this->input->get('module') && !empty($this->input->get('module'))) {
			$module = $this->input->get('module');
		}
		$data = $this->att_db->get_one(array('aid' => $aid));
		if (!$data) {
			dr_admin_msg(0, L('附件'.$aid.'不存在'), 'close', 3, 1);
		}

		if (!dr_is_image($data['fileext'])) {
			dr_admin_msg(0, L('此文件不属于图片'), 'close', 3, 1);
		}

		$data['file'] = SYS_UPLOAD_PATH.$data['filepath'];
		$data['url'] = dr_get_file_url($data);

		$spec = $this->input->get('spec') ? intval($this->input->get('spec')) : 1; 
		$catid = intval($this->input->get('catid'));
		$input = $this->input->get('input') ? $this->input->get('input') : 'thumb';
		$preview = $this->input->get('preview') ? $this->input->get('preview') : 'thumb';
		$files_row = $this->input->get('files_row') ? $this->input->get('files_row') : '';
		switch ($spec){
			case 1:
				$spec = '4 / 3';
				break;
			case 2:
				$spec = '3 / 2';
				break;
			case 3:
				$spec = '1 / 1';
				break;
			case 4:
				$spec = '2 / 3';
				break;
			default:
				$spec = '3 / 2';
		}
		include $this->admin_tpl('crop');
	}
	/**
	 * 相关文章选择
	 */
	public function public_relationlist() {
		pc_base::load_sys_class('format','',0);
		$show_header = true;
		$model_cache = getcache('model','commons');
		if(!$this->input->get('modelid')) {
			dr_admin_msg(0,L('please_select_modelid'));
		} else {
			$page = intval($this->input->get('page'));
			$modelid = intval($this->input->get('modelid'));
			$model_arr = getcache('model', 'commons');
			$MODEL = $model_arr[$modelid];
			unset($model_arr);
			$this->sitemodel = $this->cache->get('sitemodel');
			$this->form_cache = $this->sitemodel[$MODEL['tablename']];
			$this->db->set_model($modelid);
			$where = array();
			if($this->input->get('catid')) {
				$catid = intval($this->input->get('catid'));
				$where[] = "catid='$catid'";
			}
			$where[] = 'status=99';
			
			if($this->input->get('keywords')) {
				$keywords = trim($this->input->get('keywords'));
				$field = $this->input->get('field');
				if(in_array($field, array('id','title','keywords','description'))) {
					if($field=='id') {
						$where[] = "`id` ='$keywords'";
					} else {
						$where[] = "`$field` like '%$keywords%'";
					}
				}
			}
			$order = $param['order'] ? $param['order'] : ($this->form_cache['setting']['order'] ? dr_safe_replace($this->form_cache['setting']['order']) : 'id desc');
			$infos = $this->db->listinfo(($where ? implode(' AND ', $where) : ''),$order,$page,SYS_ADMIN_PAGESIZE);
			$pages = $this->db->pages;
			include $this->admin_tpl('relationlist');
		}
	}
	public function public_getjson_ids() {
		$modelid = intval($this->input->get('modelid'));
		$id = intval($this->input->get('id'));
		$this->db->set_model($modelid);
		$tablename = $this->db->table_name;
		$rt = $this->db->get_one(array('id' => $id), 'tableid');
		$this->db->table_name = $tablename.'_data_'.$rt['tableid'];
		$r = $this->db->get_one(array('id'=>$id),'relation');

		if($r['relation']) {
			$relation = str_replace('|', ',', $r['relation']);
			$relation = trim($relation,',');
			$where = "id IN($relation)";
			$infos = array();
			$this->db->table_name = $tablename;
			$datas = $this->db->select($where,'id,title');
			foreach($datas as $_v) {
				$_v['sid'] = 'v'.$_v['id'];
				if(strtolower(CHARSET)=='gbk') $_v['title'] = iconv('gbk', 'utf-8', $_v['title']);
				$infos[] = $_v;
			}
			exit(dr_array2string($infos));
		}
	}

	//文章预览
	public function public_preview() {
		$catid = intval($this->input->get('catid'));
		$id = intval($this->input->get('id'));
		
		if(!$catid || !$id) dr_admin_msg(0,L('missing_part_parameters'));
		$page = max(intval($this->input->get('page')), 1);
		$CATEGORYS = get_category($this->siteid);
		
		if(!isset($CATEGORYS[$catid]) || $CATEGORYS[$catid]['type']!=0) dr_admin_msg(0,L('missing_part_parameters'));
		define('HTML', true);
		define('IS_HTML', true);
		$category = $CAT = dr_cat_value($catid);
		
		$siteid = $CAT['siteid'];
		$MODEL = getcache('model','commons');
		$modelid = $CAT['modelid'];

		$this->db->table_name = $this->db->db_tablepre.$MODEL[$modelid]['tablename'];
		$r = $this->db->get_one(array('id'=>$id));
		if(!$r) dr_admin_msg(0,L('information_does_not_exist'));
		$this->db->table_name = $this->db->table_name.'_data_'.$r['tableid'];
		$r2 = $this->db->get_one(array('id'=>$id));
		$rs = $r2 ? array_merge($r,$r2) : $r;

		//再次重新赋值，以数据库为准
		$catid = $CATEGORYS[$r['catid']]['catid'];
		$modelid = $CATEGORYS[$catid]['modelid'];
		
		require_once CACHE_MODEL_PATH.'content_output.class.php';
		$content_output = new content_output($modelid,$catid,$CATEGORYS);
		$data = $content_output->get($rs);
		extract($data);
		$CAT['setting'] = string2array($CAT['setting']);
		//最顶级栏目ID
		$arrparentid = explode(',', $CAT['arrparentid']);
		$top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
		$template = $template ? $template : $CAT['setting']['show_template'];
		$allow_visitor = 1;
		//SEO
		$SEO = seo($siteid, $catid, $title, $description);
		
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
				$contents = array_filter(explode('[page]', $content));
				$pagenumber = dr_count($contents);
				if (strpos($content, '[/page]')!==false && ($CONTENT_POS<7)) {
					$pagenumber--;
				}
				for($i=1; $i<=$pagenumber; $i++) {
					list($pageurls[$i], $showurls[$i]) = $this->preview_url($id, $i, $catid, $rs['inputtime']);
				}
				$END_POS = strpos($content, '[/page]');
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
					$content = trim($content);
					if(strpos($content,'</p>')===0) {
						$content = '<p>'.$content;
					}
					if(stripos($content,'<p>')===0) {
						$content = $content.'</p>';
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
		// 传入模板
		pc_base::load_sys_class('service')->assign($data);
		pc_base::load_sys_class('service')->assign(array(
			'SEO' => $SEO,
			'siteid' => $siteid,
			'modelid' => $modelid,
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
			'title' => $title,
			'content' => $content,
			'pages' => $pages,
		));
		pc_base::load_sys_class('service')->display('content',$template);
		$pc_hash = dr_get_csrf_token();
		$steps = intval($this->input->get('steps'));
		if ($steps) {
			echo "
			<script language=\"javascript\" type=\"text/javascript\" src=\"".JS_PATH."Dialog/main.js\"></script>
			<script type=\"text/javascript\">var diag = new Dialog({id:'content_m',title:'".L('operations_manage')."',html:'<span id=cloading ><a href=\'javascript:ajax_manage(1)\'>".L('passed_checked')."</a> | <a href=\'javascript:ajax_manage(2)\'>".L('reject')."</a> |　<a href=\'javascript:ajax_manage(3)\'>".L('delete')."</a></span>',left:'100%',top:'100%',modal:false});diag.show();
			function ajax_manage(type) {
				if(type==1) {
					$.get('?m=content&c=content&a=pass&ajax_preview=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."');
				} else if(type==2) {
					$.get('?m=content&c=content&a=pass&ajax_preview=1&reject=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."');
				} else if(type==3) {
					$.post('?m=content&c=content&a=delete&ajax_preview=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."',{dosubmit:1,".SYS_TOKEN_NAME.":'".csrf_hash()."'});
				}
				$('#cloading').html('<font color=red>".L('operation_success')."<span id=\"secondid\">2</span>".L('after_a_few_seconds_left')."</font>');
				setInterval('set_time()', 1000);
				setInterval('window.opener.location.reload(true);window.close();', 2000);
			}
			function set_time() {
				$('#secondid').html(1);
			}
			</script>";
		} else {
			echo "
			<script language=\"javascript\" type=\"text/javascript\" src=\"".JS_PATH."Dialog/main.js\"></script>
			<script type=\"text/javascript\">var diag = new Dialog({id:'content_m',title:'".L('operations_manage')."',html:'<span id=cloading ><a href=\'javascript:ajax_manage(1)\'>".L('passed_checked')."</a> | <a href=\'javascript:ajax_manage(2)\'>".L('delete')."</a></span>',left:'100%',top:'100%',modal:false});diag.show();
			function ajax_manage(type) {
				if(type==1) {
					$.get('?m=content&c=content&a=ajax_pass&ajax_preview=1&catid=".$catid."&id=".$id."&pc_hash=".$pc_hash."');
				} else if(type==2) {
					$.post('?m=content&c=content&a=delete&ajax_preview=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."',{dosubmit:1,".SYS_TOKEN_NAME.":'".csrf_hash()."'});
				}
				$('#cloading').html('<font color=red>".L('operation_success')."<span id=\"secondid\">2</span>".L('after_a_few_seconds_left')."</font>');
				setInterval('set_time()', 1000);
				setInterval('window.opener.location.reload(true);window.close();', 2000);
			}
			function set_time() {
				$('#secondid').html(1);
			}
			</script>";
		}
	}
	private function preview_url($id, $page = 0, $catid = 0) {
		$urlrule = SELF.'?m=content&c=content&a=public_preview&steps='.intval($this->input->get('steps')).'&catid={$catid}&id={$id}';
		if($page!=1) {
			$urlrule = $urlrule.'&page={$page}';
		}
		$urls = str_replace(array('{$catid}','{$id}','{$page}'),array($catid,$id,$page),$urlrule);
		$showurls = str_replace(array('{$catid}','{$id}','{$page}'),array($catid,$id,'{page}'),$urlrule);
		$url_arr[1] = $urls;
		$url_arr[0] = $urls;
		$showurl_arr[1] = $showurls;
		$showurl_arr[0] = $showurls;
		return array($url_arr, $showurl_arr);
	}
	/**
	 * 过审内容
	 */
	public function ajax_pass() {
		$catid = intval($this->input->get('catid'));
		if(!$catid) dr_json(0, L('missing_part_parameters'));
		$category = dr_cat_value($catid);
		$modelid = dr_cat_value($catid, 'modelid');
		$this->db->set_model($modelid);
		$this->db->update(array('status'=>99),array('id'=>$this->input->get('id')));
		dr_json(1, L('operation_success'));
	}

	/**
	 * 审核所有内容
	 */
	public function public_checkall() {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		
		$show_header = true;
		$workflows = getcache('workflow_'.$this->siteid,'commons');
		$datas = array();
		$pagesize = SYS_ADMIN_PAGESIZE;
		$sql = '';
		if (cleck_admin(param::get_session('roleid'))) {
			$super_admin = 1;
			$status = $this->input->get('status') ? $this->input->get('status') : -1;
		} else {
			$super_admin = 0;
			$status = $this->input->get('status') ? $this->input->get('status') : 1;
			if($status==-1) $status = 1;
		}
		if($status>4) $status = 4;
		$this->priv_db = pc_base::load_model('category_priv_model');
		$admin_username = param::get_cookie('admin_username');
		if($status==-1) {
			$sql = "`status` NOT IN (99,0,-2) AND `siteid`=$this->siteid";
		} else {
			$sql = "`status` = '$status' AND `siteid`=$this->siteid";
		}
		if($status!=0 && !$super_admin) {
			//以栏目进行循环
			foreach ($this->categorys as $catid => $cat) {
				if($cat['type']!=0) continue;
				//查看管理员是否有这个栏目的查看权限。
				if (!$this->priv_db->get_one(array('catid'=>$catid, 'siteid'=>$this->siteid, 'roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'), 'is_admin'=>'1'))) {
					continue;
				}
				//如果栏目有设置工作流，进行权限检查。
				$workflow = array();
				$cat['setting'] = dr_string2array(dr_cat_value($catid, 'setting'));
				if (isset($cat['setting']['workflowid']) && !empty($cat['setting']['workflowid'])) {
					$workflow = $workflows[$cat['setting']['workflowid']];
					$workflow['setting'] = string2array($workflow['setting']);
					$usernames = $workflow['setting'][$status];
					if (empty($usernames) || !in_array($admin_username, $usernames)) {//判断当前管理，在工作流中可以审核几审
						continue;
					}
				}
				$priv_catid[] = $catid;
			}
			if(empty($priv_catid)) {
				$sql .= " AND catid = -1";
			} else {
				$priv_catid = implode(',', $priv_catid);
				$sql .= " AND catid IN ($priv_catid)";
			}
		}
		$this->content_check_db = pc_base::load_model('content_check_model');
		$datas = $this->content_check_db->listinfo($sql,'inputtime DESC',$page,SYS_ADMIN_PAGESIZE);		
		$pages = $this->content_check_db->pages;
		$status = $this->input->get('status');
		include $this->admin_tpl('content_checkall');
	}
	
	/**
	 * 批量移动文章
	 */
	public function remove() {
		if(IS_POST) {
			$this->content_check_db = pc_base::load_model('content_check_model');
			$this->hits_db = pc_base::load_model('hits_model');
			if($this->input->post('fromtype')) {
				if(!$this->input->post('fromid')) dr_admin_msg(0,L('please_input_move_source'));
				if(!$this->input->post('tocatid')) dr_admin_msg(0,L('please_select_target_category'));
				$tocatid = intval($this->input->post('tocatid'));
				$modelid = dr_cat_value($tocatid, 'modelid');
				if(!$modelid) dr_admin_msg(0,L('illegal_operation'));
				$fromid = array_filter($this->input->post('fromid'),"is_numeric");
				$fromid = implode(',', $fromid);
				$this->db->set_model($modelid);
				$this->db->update(array('catid'=>$tocatid),"catid IN($fromid)");
				$this->hits_db->update(array('catid'=>$tocatid),"catid IN($fromid)");
			} else {
				if(!$this->input->post('ids')) dr_admin_msg(0,L('please_input_move_source'));
				if(!$this->input->post('tocatid')) dr_admin_msg(0,L('please_select_target_category'));
				$tocatid = intval($this->input->post('tocatid'));
				$modelid = dr_cat_value($tocatid, 'modelid');
				if(!$modelid) dr_admin_msg(0,L('illegal_operation'));
				$ids = array_filter(explode(',', $this->input->post('ids')),"is_numeric");
				foreach ($ids as $id) {
					$checkid = 'c-'.$id.'-'.$modelid;
					$this->content_check_db->update(array('catid'=>$tocatid), array('checkid'=>$checkid));
					$hitsid = 'c-'.$modelid.'-'.$id;
					$this->hits_db->update(array('catid'=>$tocatid),array('hitsid'=>$hitsid));
				}
				$ids = implode(',', $ids);
				$this->db->set_model($modelid);
				$this->db->update(array('catid'=>$tocatid),"id IN($ids)");
			}
			dr_admin_msg(1,L('operation_success'), '', '', 'remove');
			//ids
		} else {
			$show_header = true;
			$catid = intval($this->input->get('catid'));
			$modelid = dr_cat_value($catid, 'modelid');
			$tree = pc_base::load_sys_class('tree');
			$categorys = array();
			foreach($this->categorys as $cid=>$r) {
				if($this->siteid != $r['siteid'] || $r['type']) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$r['disabled'] = $r['child'] ? 'disabled' : '';
				$r['selected'] = $cid == $catid ? 'selected' : '';
				$categorys[$cid] = $r;
			}
			$str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
 			$str = "<option value='\$catid'>\$spacer \$catname</option>";
			$source_string = '';
			$tree->init($categorys);
			$source_string .= $tree->get_tree(0, $str);
			$ids = empty($this->input->get('ids')) ? '' : $this->input->get('ids');
			include $this->admin_tpl('content_remove');
		}
	}
	
	/**
	 * 同时发布到其他栏目
	 */
	public function add_othors() {
		$show_header = true;
		$sitelist = getcache('sitelist','commons');
		$siteid = $this->input->get('siteid');
		include $this->admin_tpl('add_othors');
		
	}
	/**
	 * 同时发布到其他栏目 异步加载栏目
	 */
	public function public_getsite_categorys() {
		$siteid = intval($this->input->get('siteid'));
		$this->categorys = get_category($siteid);
		$models = getcache('model','commons');
		$tree = pc_base::load_sys_class('tree');
		$categorys = array();
		if(!cleck_admin(param::get_session('roleid'))) {
			$this->priv_db = pc_base::load_model('category_priv_model');
			$priv_result = $this->priv_db->select(array('action'=>'add','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid,'is_admin'=>1));
			$priv_catids = array();
			foreach($priv_result as $_v) {
				$priv_catids[] = $_v['catid'];
			}
			if(empty($priv_catids)) return '';
		}
		
		foreach($this->categorys as $r) {
			if($r['siteid']!=$siteid || $r['type']!=0) continue;
			if(!cleck_admin(param::get_session('roleid')) && !in_array($r['catid'],$priv_catids)) {
				$arrchildid = explode(',',$r['arrchildid']);
				$array_intersect = array_intersect($priv_catids,$arrchildid);
				if(empty($array_intersect)) continue;
			}
			$r['modelname'] = $models[$r['modelid']]['name'];
			$r['style'] = $r['child'] ? 'color:#8A8A8A;' : '';
			$r['click'] = $r['child'] ? '' : "onclick=\"select_list(this,'".safe_replace($r['catname'])."',".$r['catid'].")\" class='cu' title='".L('click_to_select')."'";
			$categorys[$r['catid']] = $r;
		}
		$str = "<tr \$click >
					<td align='center'>\$id</td>
					<td style='\$style'>\$spacer\$catname</td>
					<td align='center'>\$modelname</td>
				</tr>";
		$tree->init($categorys);
		$categorys = $tree->get_tree(0, $str);
		echo $categorys;
	}
	
	public function public_sub_categorys() {
		$cfg = getcache('common','commons');
		$ajax_show = intval(abs($cfg['category_ajax']));	
		$catid = intval($this->input->post('root'));
		$modelid = intval($this->input->post('modelid'));
		$this->categorys = get_category($this->siteid);
		$tree = pc_base::load_sys_class('tree');
		if(!empty($this->categorys)) {
			foreach($this->categorys as $r) {
				if($r['siteid']!=$this->siteid || ($r['type']==2 && $r['child']==0)) continue;
				if($from=='content' && !cleck_admin(param::get_session('roleid')) && !in_array($r['catid'],$priv_catids)) {
					$arrchildid = explode(',',$r['arrchildid']);
					$array_intersect = array_intersect($priv_catids,$arrchildid);
					if(empty($array_intersect)) continue;
				}
				if($r['type']==1 || $from=='block') {
					if($r['type']==0) {
						$r['vs_show'] = "<a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."&type=show' target='right'>[".L('content_page')."]</a>";
					} else {
						$r['vs_show'] ='';
					}
					$r['icon_type'] = 'file';
					$r['add_icon'] = '';
					$r['type'] = 'add';
				} else {
					$r['icon_type'] = $r['vs_show'] = '';
					$r['type'] = 'init';
					$r['add_icon'] = "<a target='right' href='?m=content&c=content&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."' onclick=javascript:dr_content_submit('?m=content&c=content&a=add&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."','add')><img src='".IMG_PATH."add_content.png' alt='".L('add')."'></a> ";
				}
				$categorys[$r['catid']] = $r;
			}
		}
		if(!empty($categorys)) {
			$tree->init($categorys);
				switch($from) {
					case 'block':
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&type=list&pc_hash=".dr_get_csrf_token()."' target='right'>\$catname</a> \$vs_show</span>";
					break;

					default:
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=content&c=content&a=\$type&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&pc_hash=".dr_get_csrf_token()."' target='right' onclick='open_list(this)'>\$catname</a></span>";
						break;
				}
			$data = $tree->creat_sub_json($catid,$strs);
		}		
		echo $data;
	}

	/**
	 * 一键清理数据
	 */
	public function clear_data() {
		if(!cleck_admin(param::get_session('roleid')) && !dr_in_array(param::get_session('userid'), ADMIN_FOUNDERS)) {
			showmessage(L('only_fonder_admin_operation'), 'close');
		}
		//清理数据涉及到的数据表
		if ($this->input->post('dosubmit')) {
			set_time_limit(0);
			$models = array('category', 'content', 'hits', 'search', 'position_data', 'comment', 'keyword');
			$tables = $this->input->post('tables');
			if (is_array($tables)) {
				foreach ($tables as $t) {
					if (in_array($t, $models)) {
						if ($t=='content') {
							$model = $this->input->post('model');
							$db = pc_base::load_model('content_model');
							//读取网站的所有模型
							$model_arr = getcache('model', 'commons');
							foreach ($model as $modelid) {
								$db->set_model($modelid);
								if ($r = $db->count()) { //判断模型下是否有数据
									$result = $data = $db->select();
									if (is_array($result) && $result) {
										$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$model_arr[$modelid]['tablename'].'.sql';
										$this->create_sql_file($result, $db->db_tablepre.$model_arr[$modelid]['tablename'], $sql_file);
									}
									$db->query('TRUNCATE TABLE `cms_'.$model_arr[$modelid]['tablename'].'`');
									//开始清理模型data表数据
									for ($i = 0;; $i ++) {
										$db->table_name = $db->table_name.'_data_'.$i;
										if ($db->table_exists($db->table_name)) {
											$result = $db->select();
											if (is_array($result) && $result) {
												$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$model_arr[$modelid]['tablename'].'_data_'.$i.'.sql';
												$this->create_sql_file($result, $db->db_tablepre.$model_arr[$modelid]['tablename'].'_data_'.$i.'', $sql_file);
											}
											$db->query('TRUNCATE TABLE `cms_'.$model_arr[$modelid]['tablename'].'_data_'.$i.'`');
											$db->set_model($modelid);
										} else {
											break;
										}
									}
									//删除该模型中在hits表的数据
									$hits_db = pc_base::load_model('hits_model');
									$hitsid = 'c-'.$modelid.'-';
									$result = $hits_db->select("`hitsid` LIKE '%$hitsid%'");
									if (is_array($result) && $result) {
										$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'hits-'.$modelid.'.sql';
										$this->create_sql_file($result, $hits_db->db_tablepre.'hits', $sql_file);
									}
									$hits_db->delete("`hitsid` LIKE '%$hitsid%'");
									//删除该模型在search中的数据
									$search_db = pc_base::load_model('search_model');
									$type_model = getcache('type_model_'.$model_arr[$modelid]['siteid'], 'search');
									$typeid = $type_model[$modelid];
									$result = $search_db->select("`typeid`=".$typeid);
									if (is_array($result) && $result) {
										$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'search-'.$modelid.'.sql';
										$this->create_sql_file($result, $search_db->db_tablepre.'search', $sql_file);
									}
									$search_db->delete("`typeid`=".$typeid);
									//Delete the model data in the position table
									$position_db = pc_base::load_model('position_data_model');
									$result = $position_db->select('`modelid`='.$modelid.' AND `module`=\'content\'');
									if (is_array($result) && $result) {
										$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'position_data-'.$modelid.'.sql';
										$this->create_sql_file($result, $position_db->db_tablepre.'position_data', $sql_file);
									}
									$position_db->delete('`modelid`='.$modelid.' AND `module`=\'content\'');
									//清理评论表及附件表，附件的清理为不可逆操作。
									//附件初始化
									//$attachment = pc_base::load_model('attachment_model');
									//$comment = pc_base::load_app_class('comment', 'comment');
									//if(module_exists('comment')){
										//$comment_exists = 1;
									//}
									//foreach ($data as $d) {
										//$attachment->api_delete('c-'.$d['catid'].'-'.$d['id']);
										//if ($comment_exists) {
											//$commentid = id_encode('content_'.$d['catid'], $d['id'], $model_arr[$modelid]['siteid']);
											//$comment->del($commentid, $model_arr[$modelid]['siteid'], $d['id'], $d['catid']);
										//}
									//}
								}
							}
						} elseif ($t=='comment') {
							$comment_db = pc_base::load_model('comment_data_model');
							for($i=1;;$i++) {
								$comment_db->table_name($i);
								if ($comment_db->table_exists(str_replace($comment_db->db_tablepre, '', $comment_db->table_name))) {
									if ($r = $comment_db->count()) {
										$result = $comment_db->select();
										if (is_array($result) && $result) {
											$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'comment_data_'.$i.'.sql';
											$this->create_sql_file($result, $comment_db->db_tablepre.'comment_data_'.$i, $sql_file);
										}
										$comment_db->query('TRUNCATE TABLE `cms_comment_data_'.$i.'`');
									}
								} else {
									break;
								}
							}
						} elseif ($t=='keyword') {
							$keyword_db = pc_base::load_model($t.'_model');
							$keyword_data_db = pc_base::load_model($t.'_data_model');
							if ($r = $keyword_db->count()) {
								$result = $keyword_db->select();
								if (is_array($result) && $result) {
									$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$t.'.sql';
									$this->create_sql_file($result, $keyword_db->db_tablepre.$t, $sql_file);
								}
								$keyword_db->query('TRUNCATE TABLE `cms_'.$t.'`');
							}
							if ($r = $keyword_data_db->count()) {
								$result = $keyword_data_db->select();
								if (is_array($result) && $result) {
									$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$t.'_data.sql';
									$this->create_sql_file($result, $keyword_data_db->db_tablepre.$t.'_data', $sql_file);
								}
								$keyword_data_db->query('TRUNCATE TABLE `cms_'.$t.'_data`');
							}
						} else {
							$db = pc_base::load_model($t.'_model');
							if ($r = $db->count()) {
								$result = $db->select();
								$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$t.'.sql';
								$this->create_sql_file($result, $db->db_tablepre.$t, $sql_file);
								$db->query('TRUNCATE TABLE `cms_'.$t.'`');
							}
						}
					}
				}
			}
			dr_admin_msg(1, L('clear_data_message'), array('url' => '?m=content&c=content&a=clear_data&&page='.(int)($this->input->post('page')).'&pc_hash='.dr_get_csrf_token()));
		} else {
			//读取网站的所有模型
			$show_header = true;
			$model_arr = getcache('model', 'commons');
			include $this->admin_tpl('clear_data');
		}
	}

	/**
	 * 备份数据到文件
	 * @param $data array 备份的数据数组
	 * @param $tablename 数据所属数据表
	 * @param $file 备份到的文件
	 */
	private function create_sql_file($data, $db, $file) {
		if (is_array($data)) {
			$sql = '';
			foreach ($data as $d) {
				$tag = '';
				$sql .= "REPLACE INTO `".$db.'` VALUES(';
				foreach ($d as $_f => $_v) {
					$sql .= $tag.'\''.$_v.'\'';
					$tag = ',';
				}
				$sql .= ');'."\r\n";
			}
			file_put_contents($file, $sql);
		}
		return true;
	}
}
?>