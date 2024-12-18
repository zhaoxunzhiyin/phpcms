<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util','content');
pc_base::load_app_func('global');
class search {
	private $input,$cache,$db,$linkage_db,$module,$categorys;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('content_model');
		$this->linkage_db = pc_base::load_model('linkage_model');
	}
	/**
	 * 按照模型搜索
	 */
	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$params = $this->input->get();
		// 挂钩点 搜索之前对参数处理
		pc_base::load_sys_class('hooks')::trigger('search_param', $params);
		$grouplist = getcache('grouplist','member');
		$_groupid = param::get_cookie('_groupid');
		if(!$_groupid) $_groupid = 8;
		if(!$grouplist[$_groupid]['allowsearch']) {
			if ($_groupid==8) showmessage(L('guest_not_allowsearch')); 
			else showmessage(L('group_not_allowsearch'));
		}

		if(!$this->input->get('catid')) showmessage(L('missing_part_parameters'));
		$catid = intval($this->input->get('catid'));
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->categorys = get_category($siteid);
		if(!$this->categorys[$catid]) showmessage(L('missing_part_parameters'));
		$info = $this->input->get('info');
		!$info && $info = array();
		if(isset($info['catid']) && $info['catid']) {
			$catid = intval($info['catid']);
		}
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
		if (isset($this->module['setting']['search']['use']) && $this->module['setting']['search']['use']) {
			dr_msg(0, L('此模块已经关闭了搜索功能'));
		} elseif ($info['title'] && $this->module['setting']['search']['length']
			&& dr_strlen($info['title']) < (int)$this->module['setting']['search']['length']) {
			dr_msg(0, L('关键字不得少于系统规定的长度'));
		} elseif ($info['title'] && $this->module['setting']['search']['maxlength']
			&& dr_strlen($info['title']) > (int)$this->module['setting']['search']['maxlength']) {
			dr_msg(0, L('关键字不得大于系统规定的长度'));
		} elseif ($info['keywords'] && $this->module['setting']['search']['length']
			&& dr_strlen($info['keywords']) < (int)$this->module['setting']['search']['length']) {
			dr_msg(0, L('关键字不得少于系统规定的长度'));
		} elseif ($info['keywords'] && $this->module['setting']['search']['maxlength']
			&& dr_strlen($info['keywords']) > (int)$this->module['setting']['search']['maxlength']) {
			dr_msg(0, L('关键字不得大于系统规定的长度'));
		}
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
		$search_field = ($this->module['setting']['search']['field'] ? explode(',', $this->module['setting']['search']['field']) : array());
		if (isset($this->module['setting']['search']['search_param']) && $this->module['setting']['search']['search_param'] && $search_field) {
			$module_search = 0;
			foreach ($search_field as $t) {
				if ($info[$t]) {
					$module_search = 1;
				}
			}
		} else {
			$module_search = 1;
		}
		if($info && $module_search) {
			//搜索间隔
			$minrefreshtime = getcache('common','commons');
			$minrefreshtime = intval($minrefreshtime['minrefreshtime']);
			$minrefreshtime = $minrefreshtime ? $minrefreshtime : 5;
			if(param::get_cookie('search_cookie') && param::get_cookie('search_cookie')>SYS_TIME-$minrefreshtime) {
				showmessage(L('search_minrefreshtime',array('min'=>$minrefreshtime)),'index.php?m=content&c=search&catid='.$catid,$minrefreshtime*1280);
			} else {
				param::set_cookie('search_cookie', SYS_TIME+$minrefreshtime);
			}
			//搜索间隔
			$siteid = $this->categorys[$catid]['siteid'];
			$this->db->set_model($modelid);
			
			$page = max(intval($this->input->get('page')), 1);
			//构造搜索SQL
			$where = 'status=99';
			foreach ($fields as $field=>$r) {
				if($r['issystem'] && $r['issearch']) {
					if($r['formtype']=='catid') {
						if($info['catid']) $where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " ".(isset($this->module['setting']['search']['search_catid']) && $this->module['setting']['search']['search_catid'] ? "catid in (".($cat['arrchildid'] ? $cat['arrchildid'] : $catid).")" : "catid='$catid'");
					} elseif($r['formtype']=='number') {
						$start = "{$field}_start";
						$end = "{$field}_end";
						if($this->input->get($start)) {
							$start = intval($this->input->get($start));
							$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " {$field}>'$start'";
						}
						if($this->input->get($end)) {
							$end = intval($this->input->get($end));
							$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " {$field}<'$end'";
						}
					} elseif($r['formtype']=='datetime') {
						// 匹配时间字段
						/*list($s, $e) = explode(',', $info[$field]);
						$s = (int)strtotime((string)$s);
						$e = (int)strtotime((string)$e);
						if ($s == $e && $s == 0) {
						} else {
							if (!$e) {
								$where .= ' ' . (intval($this->module['setting']['search']['is_double_like']) ? 'OR' : 'AND') . ' `'.$field.'` > '.$s;
							} else {
								$where .= ' ' . (intval($this->module['setting']['search']['is_double_like']) ? 'OR' : 'AND') . ' `'.$field.'` BETWEEN '.$s.' AND '.$e;
							}
						}*/
						if($info[$field]) {
							$start[$field] = strtotime($info[$field]);
							if($start[$field]) $where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " {$field}>'".$start[$field]."'";
						}
					} elseif($r['formtype']=='box') {
						if($info[$field]) {
							/*$arr = explode('|', $info[$field]);
							$where_box = array();
							if (intval($this->module['setting']['search']['is_like']) && $info[$field]) {
								$option = dr_format_option_array(dr_string2array($r['setting'])['options']);
								if ($option) {
									$new = [];
									foreach ($option as $k => $v) {
										if (strpos($v, (string)$info[$field]) !== false) {
											$new[] = $k;
										}
									}
									if ($new) {
										$arr = $new;
									}
								}
							}
							if (dr_in_array($r['boxtype'], ['radio','select'])) {
								foreach ($arr as $val) {
									if (is_numeric($val)) {
										$where_box[] = '`'.$field.'`='.$val;
									} else {
										$where_box[] = '`'.$field.'`="'.dr_safe_replace($val, ['\\', '/']).'"';
									}
								}
							} else {
								foreach ($arr as $val) {
									if ($val) {
										if (version_compare($this->db->version(), '5.7.0') < 0) {
											// 兼容写法
											$where_box[] = '`'.$field.'` LIKE "%\"'.$this->db->escape(dr_safe_replace($val)).'\"%"';
										} else {
											// 高版本写法
											$where_box[] = "(CASE WHEN JSON_VALID(`{$field}`) THEN JSON_CONTAINS (`{$field}`->'$[*]', '\"".$this->db->escape(dr_safe_replace($val))."\"', '$') ELSE null END)";
										}
									}
								}
							}
							$where .= ' ' . (intval($this->module['setting']['search']['is_double_like']) ? 'OR' : 'AND') . ' ' . ($where_box ? implode(' OR ', $where_box) : '`id` = 0');*/
							$field_value[$field] = safe_replace($info[$field]);
							switch($r['boxtype']) {
								case 'radio':
									$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " `$field`='".$this->db->escape($field_value[$field])."'";
								break;
					
								case 'checkbox':
									$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " `$field` LIKE '%".$this->db->escape($field_value[$field])."%'";
								break;
					
								case 'select':
									$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " `$field`='".$this->db->escape($field_value[$field])."'";
								break;
					
								case 'multiple':
									$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . " `$field` LIKE '%".$this->db->escape($field_value[$field])."%'";
								break;
							}
						}
					} elseif($r['formtype']=='typeid') {
						if($info[$field]) {
							$typeid[$field] = intval($info[$field]);
							$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . (intval($this->module['setting']['search']['is_like']) ? " `$field` LIKE '%".$typeid[$field]."%'" : " `$field`='".$typeid[$field]."'");
						}
					} elseif($r['formtype']=='linkage') {
						if($info[$field]) {
							$linkage[$field] = $info[$field];
							$arr = explode('|', $linkage[$field]);
							if (intval($this->module['setting']['search']['is_like']) && $linkage[$field]) {
								$key = $this->cache->get_file('key', 'linkage/'.dr_string2array($r['setting'])['linkage'].'/');
								if ($key) {
									$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
									$row = $this->linkage_db->get_one(array('name'=>'%'.$linkage[$field].'%'));
									if ($row) {
										$arr[] = $row['cname'];
									}
								}
							}
							$where_linkage = array();
							foreach ($arr as $val) {
								$data = dr_linkage(dr_string2array($r['setting'])['linkage'], $val);
								if ($data) {
									if ($data['child']) {
										$where_linkage[] = '`'.$field.'` IN ('.$data['childids'].')';
									} else {
										$where_linkage[] = '`'.$field.'`='.intval($data['ii']);
									}
								}
							}
							$where .= ' ' . (intval($this->module['setting']['search']['is_double_like']) ? 'OR' : 'AND') . ' ' . ($where_linkage ? implode(' OR ', $where_linkage) : '`id` = 0');
						}
					} elseif($r['formtype']=='linkages') {
						if($info[$field]) {
							$linkages[$field] = $info[$field];
							$arr = dr_string2array($linkages[$field]);
							if (intval($this->module['setting']['search']['is_like']) && $linkages[$field]) {
								$key = $this->cache->get_file('key', 'linkage/'.dr_string2array($r['setting'])['linkage'].'/');
								if ($key) {
									$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
									$row = $this->linkage_db->get_one(array('name'=>'%'.$linkages[$field].'%'));
									if ($row) {
										$arr[] = $row['cname'];
									}
								}
							}
							$where_linkages = array();
							foreach ($arr as $val) {
								$data = dr_linkage(dr_string2array($r['setting'])['linkage'], $val);
								if ($data) {
									if ($data['child']) {
										$ids = explode(',', $data['childids']);
										foreach ($ids as $id) {
											if ($id) {
												if (version_compare($this->db->version(), '5.7.0') < 0) {
													// 兼容写法
													$where_linkages[] = '`'.$field.'` LIKE "%\"'.intval($id).'\"%"';
												} else {
													// 高版本写法
													$where_linkages[] = "(CASE WHEN JSON_VALID(`{$field}`) THEN JSON_CONTAINS (`{$field}`->'$[*]', '\"".intval($id)."\"', '$') ELSE null END)";
												}
											}
										}
									} else {
										if (version_compare($this->db->version(), '5.7.0') < 0) {
											// 兼容写法
											$where_linkages[] = '`'.$field.'` LIKE "%\"'.intval($data['ii']).'\"%"';
										} else {
											// 高版本写法
											$where_linkages[] = "(CASE WHEN JSON_VALID(`{$field}`) THEN JSON_CONTAINS (`{$field}`->'$[*]', '\"".intval($data['ii'])."\"', '$') ELSE null END)";
										}
									}
								}
							}
							$where .= ' ' . (intval($this->module['setting']['search']['is_double_like']) ? 'OR' : 'AND') . ' ' . ($where_linkages ? implode(' OR ', $where_linkages) : '`id` = 0');
						}
					} elseif(in_array($r['formtype'], array('text','keyword','textarea','editor','title','author','omnipotent','linkfield'))) {
						if($info[$field]) {
							$keywords[$field] = safe_replace($info[$field]);
							$where .= " " . (intval($this->module['setting']['search']['is_double_like']) ? "OR" : "AND") . (intval($this->module['setting']['search']['complete']) ? " `$field` = '".$this->db->escape($keywords[$field])."'" : " `$field` LIKE '%".$this->db->escape($keywords[$field])."%'");
						}
					} else {
						continue;
					}
				}
			}
			if($where=='') showmessage(L('please_enter_content_to_search'));
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
		if (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(TPLPATH.$default_style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'search.html')) {
				define('ISMOBILE', 0);
				define('IS_HTML', 0);
				pc_base::load_sys_class('service')->display('content','search',$default_style);
			} else {
				pc_base::load_app_func('global','mobile');
				define('ISMOBILE', 1);
				define('IS_HTML', 0);
				pc_base::load_sys_class('service')->display('mobile','search',$default_style);
			}
		}else{
			define('ISMOBILE', 0);
			define('IS_HTML', 0);
			pc_base::load_sys_class('service')->display('content','search',$default_style);
		}
	}
}
?>