<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util','content');
pc_base::load_app_func('global');
class search {
	private $input,$db,$categorys;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
	}
	/**
	 * 按照模型搜索
	 */
	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		// 挂钩点 搜索之前对参数处理
		pc_base::load_sys_class('hooks')::trigger('search_param', $this->input->get());
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
		$this->categorys = getcache('cache', 'module/category-'.$siteid.'-data');
		if(!$this->categorys[$catid]) showmessage(L('missing_part_parameters'));
		$info = $this->input->get('info');
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
		$CATEGORYS = $this->categorys;
		//产生表单
		pc_base::load_sys_class('form','',0);
		$fields = getcache('model_field_'.$modelid,'model');
		$forminfos = array();
		foreach ($fields as $field=>$r) {
			if($r['issystem'] && $r['issearch']) {
				if($r['formtype']=='catid') {
					$r['form'] = form::select_category('',$catid,'name="info[catid]" class="form-control form-control-sm"',L('please_select_category'),$modelid,0,1);
				} elseif($r['formtype']=='number') {
					$r['form'] = "<input type='text' name='{$field}_start' id='{$field}_start' value='' size=5 class='input-text'/> - <input type='text' name='{$field}_end' id='{$field}_end' value='' size=5 class='input-text'/>";
				} elseif($r['formtype']=='datetime') {
					$r['form'] = form::date("info[$field]");
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
				} elseif(in_array($r['formtype'], array('text','keyword','textarea','editor','title','author','omnipotent'))) {
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
		if($info) {
			//搜索间隔
			$minrefreshtime = getcache('common','commons');
			$minrefreshtime = intval($minrefreshtime['minrefreshtime']);
			$minrefreshtime = $minrefreshtime ? $minrefreshtime : 5;
			if(param::get_cookie('search_cookie') && param::get_cookie('search_cookie')>SYS_TIME-$minrefreshtime) {
				showmessage(L('search_minrefreshtime',array('min'=>$minrefreshtime)),'index.php?m=content&c=search&catid='.$catid,$minrefreshtime*1280);
			} else {
				param::set_cookie('search_cookie', $minrefreshtime);
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
						if($info['catid']) $where .= " AND catid='$catid'";
					} elseif($r['formtype']=='number') {
						$start = "{$field}_start";
						$end = "{$field}_end";
						if($this->input->get($start)) {
							$start = intval($this->input->get($start));
							$where .= " AND {$field}>'$start'";
						}
						if($this->input->get($end)) {
							$end = intval($this->input->get($end));
							$where .= " AND {$field}<'$end'";
						}
					} elseif($r['formtype']=='datetime') {
						if($info[$field]) {
							$start[$field] = strtotime($info[$field]);
							if($start[$field]) $where .= " AND {$field}>'".$start[$field]."'";
						}
					} elseif($r['formtype']=='box') {
						if($info[$field]) {
							$field_value[$field] = safe_replace($info[$field]);
							switch($r['boxtype']) {
								case 'radio':
									$where .= " AND `$field`='".$this->db->escape($field_value[$field])."'";
								break;
					
								case 'checkbox':
									$where .= " AND `$field` LIKE '%,".$this->db->escape($field_value[$field]).",%'";
								break;
					
								case 'select':
									$where .= " AND `$field`='".$this->db->escape($field_value[$field])."'";
								break;
					
								case 'multiple':
									$where .= " AND `$field` LIKE '%,".$this->db->escape($field_value[$field]).",%'";
								break;
							}
						}
					} elseif($r['formtype']=='typeid') {
						if($info[$field]) {
							$typeid[$field] = intval($info[$field]);
							$where .= " AND `$field`='".$typeid[$field]."'";
						}
					} elseif($r['formtype']=='linkage') {
						if($info[$field]) {
							$linkage[$field] = intval($info[$field]);
							$where .= " AND `$field`='".$linkage[$field]."'";
						}
					} elseif(in_array($r['formtype'], array('text','keyword','textarea','editor','title','author','omnipotent'))) {
						if($info[$field]) {
							$keywords[$field] = safe_replace($info[$field]);
							$where .= " AND `$field` LIKE '%".$this->db->escape($keywords[$field])."%'";
						}
					} else {
						continue;
					}
				}
			}
			if($where=='') showmessage(L('please_enter_content_to_search'));
			$pagesize = 10;
			$total = $this->db->count($where);
			if($total) {
				$order = '';
				$order = $this->input->get('orderby')=='id DESC' ? 'id DESC' : 'id ASC';
				$datas = $this->db->listinfo($where, $order, $page, $pagesize, 'id');
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