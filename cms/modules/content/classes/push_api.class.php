<?php
/**
 *  position_api.class.php 推荐至栏目接口类
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-10-14
 */

defined('IN_CMS') or exit('No permission resources.');

class push_api {
 	private $input, $db, $url, $category, $sitemodel, $categorys, $catids_string, $field_model_db, $hits_db, $cache_api, $pos_data; //数据调用属性
 	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');  //加载数据模型
		$this->url = pc_base::load_model('urlrule_model');
		$this->category = pc_base::load_model('category_model');
		$this->sitemodel = pc_base::load_model('sitemodel_model');
	}
	
	/**
	 * 接口处理方法
	 * @param array $param 属性 请求时，为模型、栏目数组。提交添加为二维信息数据 。例：array(1=>array('title'=>'多发发送方法', ....))
	 * @param array $arr 参数 表单数据，只在请求添加时传递。 例：array('modelid'=>1, 'catid'=>12); 
	 */
	public function category_list($param = array(), $arr = array()) {
		if ($arr['dosubmit']) {
			$id = $this->input->post('id');
			if(empty($id)) return true;
			$id_arr = explode('|',$id);
			if(count($id_arr)==0) return true;
			$old_catid = intval($this->input->post('catid'));
			if(!$old_catid) return true;
			$ids = $this->input->post('ids');
			if(empty($ids)) return true;
			$ids = explode('|', $ids);
			$siteid = intval($this->input->post('siteid'));
			$siteids = getcache('category_content','commons');
			$oldsiteid = $siteids[$old_catid];

			$modelid = dr_cat_value($old_catid, 'modelid');
			$this->db->set_model($modelid);
			$tablename = $this->db->table_name;
			$this->hits_db = pc_base::load_model('hits_model');
			foreach(array_reverse($id_arr) as $id) {
				$this->db->table_name = $tablename;
				$r = $this->db->get_one(array('id'=>$id));
				$linkurl = preg_match('/^(http|https):\/\//',$r['url']) ? $r['url'] : siteurl($siteid).$r['url'];
				foreach($ids as $catid) {
					$siteid = $siteids[$catid];
					$modelid = dr_cat_value($catid, 'modelid');
					$this->db->set_model($modelid);
					$newid = $this->db->insert(
					array('title'=>$r['title'],
						'style'=>$r['style'],
						'thumb'=>$r['thumb'],
						'keywords'=>$r['keywords'],
						'description'=>$r['description'],
						'status'=>$r['status'],
						'catid'=>$catid,
						'url'=>$linkurl,
						'sysadd'=>1,
						'tableid'=>0,
						'username'=>$r['username'],
						'inputtime'=>$r['inputtime'],
						'updatetime'=>$r['updatetime'],
						'islink'=>1
					),true);
					$tnewid = $this->db->get_table_id($newid);
					$this->db->is_data_table($this->db->table_name.'_data_', $tnewid);
					$this->db->update(array('tableid'=>$tnewid),array('id'=>$newid));
					$this->db->table_name = $this->db->table_name.'_data_'.$tnewid;
					$this->db->insert(array('id'=>$newid));
					$hitsid = 'c-'.$modelid.'-'.$newid;
					$this->hits_db->insert(array('hitsid'=>$hitsid,'catid'=>$catid,'updatetime'=>SYS_TIME));
					$this->db->set_model($modelid);
					$number = $this->db->count(array('catid'=>$catid));
					$this->category->update(array('items'=>$number),array('catid'=>$catid));
					$sitemodel_number = $this->db->count();
					$this->sitemodel->update(array('items'=>$sitemodel_number),array('modelid'=>$modelid));
				}
				$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
				$this->cache_api->cache('category');
			}
			return true;
		} else {
			$siteid = get_siteid();
			$this->categorys = get_category($siteid);
			$tree = pc_base::load_sys_class('tree');
			$categorys = array();
			$this->catids_string = array();
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
				if($r['child']) {
					$r['checkbox'] = '';
					$r['style'] = 'color:#8A8A8A;';
				} else {
					$checked = '';
					$r_usable_type = $this->category->get_one(array('catid'=>$r['catid']),'usable_type');
					if($typeid && $r_usable_type['usable_type']) {
						$usable_type = explode(',', $r_usable_type['usable_type']);
						if(in_array($typeid, $usable_type)) {
							$checked = 'checked';
							$this->catids_string[] = $r['catid'];
						}
					}
					$r['checkbox'] = "<input type='checkbox' name='ids[]' value='{$r['catid']}' {$checked}>";
					$r['style'] = '';
				}
				$categorys[$r['catid']] = $r;
			}
			$str  = "<tr>
						<td align='center'>\$checkbox</td>
						<td style='\$style'>\$spacer\$catname</td>
					</tr>";
			$tree->init($categorys);
			$categorys = $tree->get_tree(0, $str);
			return $categorys;
		}
	}
	
	/**
	 * 接口处理方法
	 * @param array $param 属性 请求时，为模型、栏目数组。提交添加为二维信息数据 。例：array(1=>array('title'=>'多发发送方法', ....))
	 * @param array $arr 参数 表单数据，只在请求添加时传递。 例：array('modelid'=>1, 'catid'=>12); 
	 */
	public function category_list_copy($param = array(), $arr = array()) {
		if ($arr['dosubmit']) {
			$id = $this->input->post('id');
			if(empty($id)) return true;
			$id_arr = explode('|',$id);
			if(count($id_arr)==0) return true;
			$old_catid = intval($this->input->post('catid'));
			if(!$old_catid) return true;
			$ids = $this->input->post('ids');
			if(empty($ids)) return true;
			$ids = explode('|', $ids);
			$siteid = intval($this->input->post('siteid'));
			$siteids = getcache('category_content','commons');
			$oldsiteid = $siteids[$old_catid];

			$modelid = dr_cat_value($old_catid, 'modelid');
			$this->db->set_model($modelid);
			$tablename = $this->db->table_name;
			$this->field_model_db = pc_base::load_model('sitemodel_field_model');
			$this->hits_db = pc_base::load_model('hits_model');
			foreach(array_reverse($id_arr) as $id) {
				$this->db->table_name = $tablename;
				$r = $this->db->get_one(array('id'=>$id));
				$this->db->table_name = $this->db->table_name.'_data_'.$r['tableid'];
				$r2 = $this->db->get_one(array('id'=>$id));
				foreach($ids as $catid) {
					$siteid = $siteids[$catid];
					$modelid = dr_cat_value($catid, 'modelid');
					$field1 = $this->field_model_db->select(array('siteid'=>$siteid,'modelid'=>$modelid,'issystem'=>1),'*','','listorder ASC');
					foreach($field1 as $r1) {
						$systeminfo[$modelid]['catid'] = $catid;
						$systeminfo[$modelid]['sysadd'] = 1;
						$systeminfo[$modelid][$r1['field']] = $r[$r1['field']];
						$systeminfo[$modelid]['username'] = param::get_cookie('admin_username');
					}
					$this->db->set_model($modelid);
					$newid = $this->db->insert($systeminfo[$modelid],true);
					$category = dr_cat_value($catid);
					$catdir = $category['catdir'];
					$categorydir = $category['parentdir'];
					$parentdir = $this->get_parentdir($catid);
					$setting = string2array($category['setting']);
					$url = $this->url->get_one(array('urlruleid'=>$setting['show_ruleid'],'module'=>'content','file'=>'show','ishtml'=>$setting['content_ishtml']));
					if ($r['islink']==0) {
						$linkurl = explode("|",$url['urlrule']);
						$linkurl = $linkurl[0];
						$linkurl = str_replace('{$year}',date('Y',$r['inputtime']),$linkurl);
						$linkurl = str_replace('{$month}',date('m',$r['inputtime']),$linkurl);
						$linkurl = str_replace('{$day}',date('d',$r['inputtime']),$linkurl);
						$linkurl = str_replace('{$categorydir}',$categorydir,$linkurl);
						$linkurl = str_replace('{$parentdir}',$parentdir,$linkurl);
						$linkurl = str_replace('{$catdir}',$catdir,$linkurl);
						$linkurl = str_replace('{$catid}',$catid,$linkurl);
						$linkurl = str_replace('{$id}',$newid,$linkurl);
						if ($setting['content_ishtml']=='1') {
							$linkurl = substr(SYS_HTML_ROOT, 1).'/'.$linkurl;
						}
						$linkurl = siteurl($category['siteid']).'/'.$linkurl;
						// 站长工具
						if (module_exists('bdts')) {
							$systeminfo[$modelid]['url'] = $linkurl;
							$sitemodel_model_db = pc_base::load_model('sitemodel_model');
							$sitemodel = $sitemodel_model_db->get_one(array('modelid'=>$modelid));
							$data['tablename'] = $sitemodel['tablename'];
						}
						$this->db->update(array('url'=>$linkurl),array('id'=>$newid));
						// 挂钩点 模块内容发布完成之后
						pc_base::load_sys_class('hooks')::trigger('module_content_after', $systeminfo[$modelid], []);
					}
					$field0 = $this->field_model_db->select(array('siteid'=>$siteid,'modelid'=>$modelid,'issystem'=>0),'*','','listorder ASC');
					foreach($field0 as $r0) {
						$modelinfo[$modelid]['id'] = $newid;
						$modelinfo[$modelid]['readpoint'] = $r2['readpoint'];
						$modelinfo[$modelid]['relation'] = $r2['relation'];
						if ($r0['formtype']=='pages') {
							$modelinfo[$modelid]['paginationtype'] = $r2['paginationtype'];
							$modelinfo[$modelid]['maxcharperpage'] = $r2['maxcharperpage'];
						}else{
							$modelinfo[$modelid][$r0['field']] = $r2[$r0['field']];
						}
					}
					$tnewid = $this->db->get_table_id($newid);
					$this->db->is_data_table($this->db->table_name.'_data_', $tnewid);
					$this->db->update(array('tableid'=>$tnewid),array('id'=>$newid));
					$this->db->table_name = $this->db->table_name.'_data_'.$tnewid;
					$this->db->insert($modelinfo[$modelid]);
					$hitsid = 'c-'.$modelid.'-'.$newid;
					$this->hits_db->insert(array('hitsid'=>$hitsid,'catid'=>$catid,'updatetime'=>SYS_TIME));
					$this->db->set_model($modelid);
					$number = $this->db->count(array('catid'=>$catid));
					$this->category->update(array('items'=>$number),array('catid'=>$catid));
					$sitemodel_number = $this->db->count();
					$this->sitemodel->update(array('items'=>$sitemodel_number),array('modelid'=>$modelid));
				}
				$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
				$this->cache_api->cache('category');
			}
			return true;
		} else {
			$siteid = get_siteid();
			$this->categorys = get_category($siteid);
			$tree = pc_base::load_sys_class('tree');
			$categorys = array();
			$this->catids_string = array();
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
				if($r['child']) {
					$r['checkbox'] = '';
					$r['style'] = 'color:#8A8A8A;';
				} else {
					$checked = '';
					$r_usable_type = $this->category->get_one(array('catid'=>$r['catid']),'usable_type');
					if($typeid && $r_usable_type['usable_type']) {
						$usable_type = explode(',', $r_usable_type['usable_type']);
						if(in_array($typeid, $usable_type)) {
							$checked = 'checked';
							$this->catids_string[] = $r['catid'];
						}
					}
					$r['checkbox'] = "<input type='checkbox' name='ids[]' value='{$r['catid']}' {$checked}>";
					$r['style'] = '';
				}
				$categorys[$r['catid']] = $r;
			}
			$str  = "<tr>
						<td align='center'>\$checkbox</td>
						<td style='\$style'>\$spacer\$catname</td>
					</tr>";
			$tree->init($categorys);
			$categorys = $tree->get_tree(0, $str);
			return $categorys;
		}
	}
	/**
	 * 获取包含父级子级层次的目录
	 * @param $catid
	 */
	private function get_parentdir($catid) {
		$category = $this->category->get_one(array('catid'=>$catid));
		return $category['parentdir'].= $category['catdir'];
	}
}
 ?>