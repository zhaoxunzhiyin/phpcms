<?php
defined('IN_CMS') or exit('No permission resources.');
class search {
	private $input,$cache,$get,$db,$linkage_db,$categorys;
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
		$get = $this->input->get();
		if ($get) {
			$get = $this->input->xss_clean($get);
		}
		// 挂钩点 搜索之前对参数处理
		pc_base::load_sys_class('hooks')::trigger('search_param', $get);
		$grouplist = getcache('grouplist','member');
		$_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		if(!$grouplist[$_groupid]['allowsearch']) {
			showmessage(($_groupid==8 ? L('guest_not_allowsearch') : L('group_not_allowsearch')));
		}

		$catid = intval($get['catid']);
		if(!$catid){
			$catid = get_catid($get['catdir'] ? $get['catdir'] : $get['categorydir']);
		}
		if(!$catid) showmessage(L('missing_part_parameters'));
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->categorys = get_category($siteid);
		if(!$this->categorys[$catid]) showmessage(L('missing_part_parameters'));
		$info = $get['info'];
		!$info && $info = array();
		if (isset($get['rewrite']) && $get['rewrite']) {
			list($field, $keyword) = explode('-', $get['rewrite']);
			if(isset($field) && $field) {
				$info[$field] = safe_replace(urldecode((string)$keyword));
			}
		}
		if(isset($info['catid']) && $info['catid']) {
			$catid = intval($info['catid']);
		}
		// 栏目格式化
		$cat = $catid ? dr_cat_value($catid) : [];
		$top = $cat;
		if ($catid && $cat && $cat['topid']) {
			$top = dr_cat_value($cat['topid']);
		}

		// 获取同级栏目及父级栏目
		list($parent, $related) = dr_related_cat($cat);
		$modelid = intval($this->categorys[$catid]['modelid']);
		if(!$modelid) showmessage(L('illegal_parameters'));
		$model_arr = getcache('model', 'commons');
		$sitemodel = $this->cache->get('sitemodel');
		$form_cache = $sitemodel[$model_arr[$modelid]['tablename']];
		$get = $this->get_param($get, $form_cache);
		if (isset($form_cache['setting']['search']['use']) && $form_cache['setting']['search']['use']) {
			dr_msg(0, L('此模块已经关闭了搜索功能'));
		} elseif ($get['keyword'] && $form_cache['setting']['search']['length']
			&& dr_strlen($get['keyword']) < (int)$form_cache['setting']['search']['length']) {
			dr_msg(0, L('关键字不得少于系统规定的长度'));
		} elseif ($get['keyword'] && $form_cache['setting']['search']['maxlength']
			&& dr_strlen($get['keyword']) > (int)$form_cache['setting']['search']['maxlength']) {
			dr_msg(0, L('关键字不得大于系统规定的长度'));
		} elseif ($info['title'] && $form_cache['setting']['search']['length']
			&& dr_strlen($info['title']) < (int)$form_cache['setting']['search']['length']) {
			dr_msg(0, L('关键字不得少于系统规定的长度'));
		} elseif ($info['title'] && $form_cache['setting']['search']['maxlength']
			&& dr_strlen($info['title']) > (int)$form_cache['setting']['search']['maxlength']) {
			dr_msg(0, L('关键字不得大于系统规定的长度'));
		} elseif ($info['keywords'] && $form_cache['setting']['search']['length']
			&& dr_strlen($info['keywords']) < (int)$form_cache['setting']['search']['length']) {
			dr_msg(0, L('关键字不得少于系统规定的长度'));
		} elseif ($info['keywords'] && $form_cache['setting']['search']['maxlength']
			&& dr_strlen($info['keywords']) > (int)$form_cache['setting']['search']['maxlength']) {
			dr_msg(0, L('关键字不得大于系统规定的长度'));
		}
		$CATEGORYS = $this->categorys;
		//搜索参数为空时不显示结果
		$null = 0;
		if (isset($form_cache['setting']['search']['search_param']) && $form_cache['setting']['search']['search_param']) {
			if (!$get) {
				$null = 1;
			} else {
				$null = 1;
				foreach ($get as $t) {
					if ($t) {
						$null = 0;
						break;
					}
				}
			}
		} else {
			$info['catid'] = $catid;
		}
		if ($null) {
			$search = [
				'id' => 0,
				'catid' => $catid,
				'params' => $get,
				'keyword' => $get['keyword']
			];
		} else {
			// 搜索数据
			$search = $this->get_data();
			unset($search['params']['page']);
		}
		dr_module_search_url($catid, $search['params'], 'keyword', '', (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE ? 1 : 0));
		//产生表单
		pc_base::load_sys_class('form');
		$fields = getcache('model_field_'.$modelid,'model');
		$forminfos = array();
		foreach ($fields as $field=>$r) {
			if($r['issystem'] && $r['issearch']) {
				if($r['formtype']=='catid') {
					$r['form'] = form::select_category('',(isset($info['catid']) ? $info['catid'] : $catid),'name="info[catid]" class="form-control form-control-sm"',L('please_select_category'),$modelid,0,1);
				} elseif($r['formtype']=='number') {
					$info[$field] = $get[$field.'_end'];
					$r['form'] = "<input type='text' name='{$field}_start' id='{$field}_start' value='".$get[$field.'_start']."' class='input-text form-control form-control-sm'/> - <input type='text' name='{$field}_end' id='{$field}_end' value='".$get[$field.'_end']."' class='input-text form-control form-control-sm'/>";
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
		$get['order'] && list($order_a, $order_b) = explode('_', (string)$get['order']);
		if ($order_a && !dr_in_array($order_a, array('id', 'inputtime', 'updatetime', 'listorder'))) {
			$get['order'] = '';
		}
		if($info || $get['keyword']) {
			//搜索间隔
			if (isset($form_cache['setting']['search']['search_time'])
			&& $form_cache['setting']['search']['search_time']
			&& $get['page'] <= 1) {
				$cname = 'search_time_'.ROUTE_M.param::get_cookie('_userid').USER_HTTP_CODE.md5(dr_array2string($get));
				$ctime = $this->cache->get_auth_data($cname);
				if (!$ctime) {
					$this->cache->set_auth_data($cname, SYS_TIME);
				} elseif (SYS_TIME - $ctime < $form_cache['setting']['search']['search_time']) {
					dr_msg(0, L('search_minrefreshtime',array('min'=>$form_cache['setting']['search']['search_time'])), dr_module_search_url($catid, $search['params'], 'keyword', '', (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE ? 1 : 0)));
				} else {
					$this->cache->set_auth_data($cname, SYS_TIME);
				}
			}
			//搜索间隔
			$siteid = $this->categorys[$catid]['siteid'];
			$this->db->set_model($modelid);
			
			$page = max(intval($get['page']), 1);
			//分页数量
			if(intval($form_cache['setting']['search']['pagesize'])){
				$pagesize = $form_cache['setting']['search']['pagesize'];
			}else{
				$pagesize = 10;
			}
			//构造搜索SQL
			$where = 'status=99';
			$where_temp = [];
			foreach ($fields as $field=>$r) {
				if($r['issystem'] && $r['issearch']) {
					if($r['formtype']=='catid') {
						if($catid) $where .= ' AND `catid`'.($cat['child'] ? ' IN ('.$cat['arrchildid'].')' : '='.(int)$catid);
					} elseif($r['formtype']=='number') {
						$start = "{$field}_start";
						$end = "{$field}_end";
						if($get[$start]) {
							$start = intval($get[$start]);
							$where_temp[] = "{$field}>'$start'";
						}
						if($get[$end]) {
							$end = intval($get[$end]);
							$where_temp[] = "{$field}<'$end'";
						}
					} elseif($r['formtype']=='datetime') {
						// 匹配时间字段
						/*list($s, $e) = explode(',', $info[$field]);
						$s = (int)strtotime((string)$s);
						$e = (int)strtotime((string)$e);
						if ($s == $e && $s == 0) {
						} else {
							if (!$e) {
								$where_temp[] = '`'.$field.'` > '.$s;
							} else {
								$where_temp[] = '`'.$field.'` BETWEEN '.$s.' AND '.$e;
							}
						}*/
						if($info[$field]) {
							$start[$field] = strtotime($info[$field]);
							if($start[$field]) $where_temp[] = "{$field}>'".$start[$field]."'";
						}
					} elseif($r['formtype']=='box') {
						if($info[$field]) {
							/*$arr = explode('|', $info[$field]);
							if (intval($form_cache['setting']['search']['is_like']) && $info[$field]) {
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
										$where_temp[] = '`'.$field.'`='.$val;
									} else {
										$where_temp[] = '`'.$field.'`="'.dr_safe_replace($val, ['\\', '/']).'"';
									}
								}
							} else {
								foreach ($arr as $val) {
									if ($val) {
										if (version_compare($this->db->version(), '5.7.0') < 0) {
											// 兼容写法
											$where_temp[] = '`'.$field.'` LIKE \'%"'.$this->db->escape(dr_safe_replace($val)).'"%\'';
										} else {
											// 高版本写法
											$where_temp[] = "(CASE WHEN JSON_VALID(`{$field}`) THEN JSON_CONTAINS (`{$field}`->'$[*]', '\"".$this->db->escape(dr_safe_replace($val))."\"', '$') ELSE null END)";
										}
									}
								}
							}*/
							$field_value[$field] = safe_replace($info[$field]);
							switch($r['boxtype']) {
								case 'radio':
									$where_temp[] = "`$field`='".$this->db->escape($field_value[$field])."'";
								break;
					
								case 'checkbox':
									$where_temp[] = "`$field` LIKE '%".$this->db->escape($field_value[$field])."%'";
								break;
					
								case 'select':
									$where_temp[] = "`$field`='".$this->db->escape($field_value[$field])."'";
								break;
					
								case 'multiple':
									$where_temp[] = "`$field` LIKE '%".$this->db->escape($field_value[$field])."%'";
								break;
							}
						}
					} elseif($r['formtype']=='typeid') {
						if($info[$field]) {
							$typeid[$field] = intval($info[$field]);
							$where_temp[] = (intval($form_cache['setting']['search']['is_like']) ? " `$field` LIKE '%".$typeid[$field]."%'" : " `$field`='".$typeid[$field]."'");
						}
					} elseif($r['formtype']=='linkage') {
						if($info[$field]) {
							$linkage[$field] = $info[$field];
							$arr = explode('|', $linkage[$field]);
							if (intval($form_cache['setting']['search']['is_like']) && $linkage[$field]) {
								$key = $this->cache->get_file('key', 'linkage/'.dr_string2array($r['setting'])['linkage'].'/');
								if ($key) {
									$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
									$row = $this->linkage_db->get_one(array('name'=>'%'.$linkage[$field].'%'));
									if ($row) {
										$arr[] = $row['cname'];
									}
								}
							}
							foreach ($arr as $val) {
								$data = dr_linkage(dr_string2array($r['setting'])['linkage'], $val);
								if ($data) {
									if ($data['child']) {
										$where_temp[] = '`'.$field.'` IN ('.$data['childids'].')';
									} else {
										$where_temp[] = '`'.$field.'`='.intval($data['ii']);
									}
								}
							}
						}
					} elseif($r['formtype']=='linkages') {
						if($info[$field]) {
							$linkages[$field] = $info[$field];
							$arr = explode('|', $linkages[$field]);
							if (intval($form_cache['setting']['search']['is_like']) && $linkages[$field]) {
								$key = $this->cache->get_file('key', 'linkage/'.dr_string2array($r['setting'])['linkage'].'/');
								if ($key) {
									$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
									$row = $this->linkage_db->get_one(array('name'=>'%'.$linkages[$field].'%'));
									if ($row) {
										$arr[] = $row['cname'];
									}
								}
							}
							foreach ($arr as $val) {
								$data = dr_linkage(dr_string2array($r['setting'])['linkage'], $val);
								if ($data) {
									if ($data['child']) {
										$ids = explode(',', $data['childids']);
										foreach ($ids as $id) {
											if ($id) {
												if (version_compare($this->db->version(), '5.7.0') < 0) {
													// 兼容写法
													$where_temp[] = '`'.$field.'` LIKE \'%"'.intval($id).'"%\'';
												} else {
													// 高版本写法
													$where_temp[] = "(CASE WHEN JSON_VALID(`{$field}`) THEN JSON_CONTAINS (`{$field}`->'$[*]', '\"".intval($id)."\"', '$') ELSE null END)";
												}
											}
										}
									} else {
										if (version_compare($this->db->version(), '5.7.0') < 0) {
											// 兼容写法
											$where_temp[] = '`'.$field.'` LIKE \'%"'.intval($data['ii']).'"%\'';
										} else {
											// 高版本写法
											$where_temp[] = "(CASE WHEN JSON_VALID(`{$field}`) THEN JSON_CONTAINS (`{$field}`->'$[*]', '\"".intval($data['ii'])."\"', '$') ELSE null END)";
										}
									}
								}
							}
						}
					} elseif(in_array($r['formtype'], array('text','keyword','textarea','editor','title','author','omnipotent','linkfield'))) {
						if($info[$field]) {
							$keywords[$field] = safe_replace($info[$field]);
							$where_temp[] = (intval($form_cache['setting']['search']['complete']) ? " `$field` = '".$this->db->escape($keywords[$field])."'" : " `$field` LIKE '%".$this->db->escape($keywords[$field])."%'");
						}
					} else {
						continue;
					}
				}
			}
			$where .= $where_temp ? ' AND ('.implode((intval($form_cache['setting']['search']['is_double_like']) ? ' AND ' : ' OR '), $where_temp).')' : '';
			// 关键字匹配条件
			if ($get['keyword'] != '') {
				$temp = [];
				$search_field = explode(',', $form_cache['setting']['search']['field'] ? $form_cache['setting']['search']['field'] : 'title,keywords');
				$search_keyword = explode('|', $this->db->escape((string)$get['keyword']));
				foreach ($search_keyword as $kw) {
					$is = 0;
					if ($search_field) {
						foreach ($search_field as $t) {
							if ($t && dr_in_array($t, $fields[$t])) {
								$is = 1;
								if($fields[$t]['formtype']=='number') {
									$temp[] = $form_cache['setting']['search']['is_like'] ? '`'.$t.'` = \''.$kw.'\'' : '`'.$t.'` LIKE \'%'.$kw.'%\'';
								} elseif($fields[$t]['formtype']=='datetime') {
									list($s, $e) = explode(',', $kw);
									$s = (int)strtotime((string)$s);
									$e = (int)strtotime((string)$e);
									if ($s == $e && $s == 0) {
									} else {
										if (!$e) {
											$temp[] = '`'.$t.'` > '.$s;
										} else {
											$temp[] = '`'.$t.'` BETWEEN '.$s.' AND '.$e;
										}
									}
								} elseif($fields[$t]['formtype']=='box') {
									$arr = explode('|', $kw);
									if (intval($form_cache['setting']['search']['is_like']) && $kw) {
										$option = dr_format_option_array(dr_string2array($fields[$t]['setting'])['options']);
										if ($option) {
											$new = [];
											foreach ($option as $k => $v) {
												if (strpos($v, (string)$kw) !== false) {
													$new[] = $k;
												}
											}
											if ($new) {
												$arr = $new;
											}
										}
									}
									if (dr_in_array($fields[$t]['boxtype'], ['radio','select'])) {
										foreach ($arr as $val) {
											if (is_numeric($val)) {
												$temp[] = '`'.$t.'`='.$val;
											} else {
												$temp[] = '`'.$t.'`="'.dr_safe_replace($val, ['\\', '/']).'"';
											}
										}
									} else {
										foreach ($arr as $val) {
											if ($val) {
												if (version_compare($this->db->version(), '5.7.0') < 0) {
													// 兼容写法
													$temp[] = '`'.$t.'` LIKE \'%"'.$this->db->escape(dr_safe_replace($val)).'"%\'';
												} else {
													// 高版本写法
													$temp[] = "(CASE WHEN JSON_VALID(`{$t}`) THEN JSON_CONTAINS (`{$t}`->'$[*]', '\"".$this->db->escape(dr_safe_replace($val))."\"', '$') ELSE null END)";
												}
											}
										}
									}
								} elseif($fields[$t]['formtype']=='typeid') {
									$temp[] = ''.(intval($form_cache['setting']['search']['is_like']) ? '`'.$t.'` LIKE \'%'.$kw.'%\'' : '`'.$t.'` = \''.$kw.'\'');
								} elseif($fields[$t]['formtype']=='linkage') {
									$arr = explode('|', $kw);
									if (intval($form_cache['setting']['search']['is_like']) && $kw) {
										$key = $this->cache->get_file('key', 'linkage/'.dr_string2array($fields[$t]['setting'])['linkage'].'/');
										if ($key) {
											$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
											$row = $this->linkage_db->get_one(array('name'=>'%'.$kw.'%'));
											if ($row) {
												$arr[] = $row['cname'];
											}
										}
									}
									foreach ($arr as $val) {
										$data = dr_linkage(dr_string2array($fields[$t]['setting'])['linkage'], $val);
										if ($data) {
											if ($data['child']) {
												$temp[] = '`'.$t.'` IN ('.$data['childids'].')';
											} else {
												$temp[] = '`'.$t.'`='.intval($data['ii']);
											}
										}
									}
								} elseif($fields[$t]['formtype']=='linkages') {
									$arr = dr_string2array($kw);
									if (intval($form_cache['setting']['search']['is_like']) && $kw) {
										$key = $this->cache->get_file('key', 'linkage/'.dr_string2array($fields[$t]['setting'])['linkage'].'/');
										if ($key) {
											$this->linkage_db->table_name = $this->linkage_db->db_tablepre.'linkage_data_'.$key;
											$row = $this->linkage_db->get_one(array('name'=>'%'.$kw.'%'));
											if ($row) {
												$arr[] = $row['cname'];
											}
										}
									}
									foreach ($arr as $val) {
										$data = dr_linkage(dr_string2array($fields[$t]['setting'])['linkage'], $val);
										if ($data) {
											if ($data['child']) {
												$ids = explode(',', $data['childids']);
												foreach ($ids as $id) {
													if ($id) {
														if (version_compare($this->db->version(), '5.7.0') < 0) {
															// 兼容写法
															$temp[] = '`'.$t.'` LIKE \'%"'.intval($id).'"%\'';
														} else {
															// 高版本写法
															$temp[] = "(CASE WHEN JSON_VALID(`{$t}`) THEN JSON_CONTAINS (`{$t}`->'$[*]', '\"".intval($id)."\"', '$') ELSE null END)";
														}
													}
												}
											} else {
												if (version_compare($this->db->version(), '5.7.0') < 0) {
													// 兼容写法
													$temp[] = '`'.$t.'` LIKE \'%"'.intval($data['ii']).'"%\'';
												} else {
													// 高版本写法
													$temp[] = "(CASE WHEN JSON_VALID(`{$t}`) THEN JSON_CONTAINS (`{$t}`->'$[*]', '\"".intval($data['ii'])."\"', '$') ELSE null END)";
												}
											}
										}
									}
								} else {
									$temp[] = $form_cache['setting']['search']['complete'] ? '`'.$t.'` = \''.$kw.'\'' : '`'.$t.'` LIKE \'%'.$kw.'%\'';
								}
							}
						}
					}
					if (!$is) {
						$temp[] = $form_cache['setting']['search']['complete'] ? '`title` = \''.$kw.'\'' : '`title` LIKE \'%'.$kw.'%\'';
					}
				}
				$where .= $temp ? ' AND ('.implode((intval($form_cache['setting']['search']['is_double_like']) ? ' AND ' : ' OR '), $temp).')' : '';
			}
			if($where=='') showmessage(L('please_enter_content_to_search'));
			// 获取搜索总量
			$total = $this->db->count($where);
			if($total) {
				$datas = $this->db->listinfo($where, ($get['order'] && $order_a ? $order_a.' '.$order_b : 'updatetime DESC'), $page, $pagesize, 'id');
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
				if ($form_cache['setting']['search']['max'] && $page * $pagesize > $form_cache['setting']['search']['max']) {
					log_message('debug', '搜索设置最大显示'.$form_cache['setting']['search']['max'].'条，当前（'.($page * $pagesize).'）已超出');
					$datas = array();
				}
				$pages = pages($total, $page, $pagesize, dr_module_search_url($catid, $search['params'], 'page', '{$page}', (is_mobile($siteid) && dr_site_info('mobileauto', $siteid) || defined('IS_MOBILE') && IS_MOBILE ? 1 : 0)));
			} else {
				$datas = array();
				$pages = '';
			}
			//开启后遇到搜索内容为空时直接跳转404页面
			if (!$null && !$total && isset($form_cache['setting']['search']['search_404']) && $form_cache['setting']['search']['search_404']) {
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
			'params' => dr_htmlspecialchars($search['params']),
			'keyword' => dr_htmlspecialchars($get['keyword']),
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
		$tpl = '';
		if (isset($form_cache['setting']['search']['tpl_field'])
			&& $form_cache['setting']['search']['tpl_field']
			&& isset($get[$form_cache['setting']['search']['tpl_field']])
			&& $get[$form_cache['setting']['search']['tpl_field']]
		) {
			$tpl = dr_safe_filename('search_'.$get[$form_cache['setting']['search']['tpl_field']]);
			if (!is_file(TPLPATH.$default_style.'/'.pc_base::load_sys_class('service')->get_dir().'/'.$tpl.'.html')) {
				$msg = '搜索模板字段'.$form_cache['setting']['search']['tpl_field'].'参数值对应的模板（'.TPLPATH.$default_style.'/'.pc_base::load_sys_class('service')->get_dir().'/'.$tpl.'.html）不存在，将加载默认的搜索模板';
				log_message('debug', $msg);
				$tpl = ''; // 自定义模板不存在
			}
		}
		if (!$tpl) {
			$tpl = 'search';
		}
		pc_base::load_sys_class('service')->display('content', $tpl, $default_style);
	}

	// 获取搜索参数
	public function get_param($get, $module) {

		$get = isset($get['rewrite']) ? dr_search_rewrite_decode($get['rewrite'], $module['setting']['search']) : $get;

		$get['m'] = $get['c'] = $get['a'] = $get['id'] = null;
		unset($get['m'], $get['c'], $get['a'], $get['id']);

		// 固定模式下的填充
		if ($get && $module['setting']['search']['param_rule']) {
			foreach ($get as $i => $t) {
				if ((string)$module['setting']['search']['param_join_default_value'] === $t) {
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
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('module_search_get_data');
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
		unset($data['params']['catid']);
		// order 参数
		if (isset($param['order']) && $param['order']) {
			$data['params']['order'] = dr_rp(dr_safe_filename($param['order']), '`', '');
		}

		return $data;
	}
}
?>