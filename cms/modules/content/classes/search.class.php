<?php
defined('IN_CMS') or exit('No permission resources.');
/**
 * 模型搜索
 */
class search {
	private $input,$cache,$db,$_groupid,$categorys;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('content_model');
		$this->_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
	}
	/**
	 * 模型搜索
	 */
	public function search($catid = 0) {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$params = $this->input->get();
		// 挂钩点 搜索之前对参数处理
		pc_base::load_sys_class('hooks')::trigger('search_param', $params);
		$grouplist = getcache('grouplist','member');
		if(!$grouplist[$this->_groupid]['allowsearch']) {
			showmessage(($this->_groupid==8 ? L('guest_not_allowsearch') : L('group_not_allowsearch')));
		}

		if(!$catid) showmessage(L('missing_part_parameters'));
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->categorys = get_category($siteid);
		if(!$this->categorys[$catid]) showmessage(L('missing_part_parameters'));
		$info = $this->input->get('info');
		!$info && $info = array();
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
		if(isset($form_cache['setting']['search']['search_catid']) && $form_cache['setting']['search']['search_catid'] && !isset($info['catid'])) {
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
					if(isset($form_cache['setting']['search']['search_catid']) && $form_cache['setting']['search']['search_catid']) {
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
		$siteid = $this->categorys[$catid]['siteid'];
		$this->db->set_model($modelid);
			
		$page = max(intval($this->input->get('page')), 1);
		//构造搜索SQL
		$where = 'status=99 AND catid in ('.($cat['arrchildid'] ? $cat['arrchildid'] : $catid).')';
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
		if (!$total && isset($form_cache['setting']['search']['search_404']) && $form_cache['setting']['search']['search_404']) {
			param::goto_404_page('内容匹配结果为空');
		}

		// 挂钩点 搜索完成之后
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('module_search_data', $datas);
		if ($rt2 && isset($rt2['code']) && $rt2['code']) {
			$datas = $rt2['data'];
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
		pc_base::load_sys_class('service')->display('content','search',$default_style);
	}
}