<?php
/**
 *  position_api.class.php 推荐至栏目接口类
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-10-14
 */

defined('IN_CMS') or exit('No permission resources.');

class push_api {
 	private $db, $pos_data; //数据调用属性
 	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');  //加载数据模型
		$this->url = pc_base::load_model('urlrule_model');
		$this->category = pc_base::load_model('category_model');
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
			$this->categorys = getcache('category_content_'.$oldsiteid,'commons');

			$modelid = $this->categorys[$old_catid]['modelid'];
			$this->db->set_model($modelid);
			$tablename = $this->db->table_name;
			$this->hits_db = pc_base::load_model('hits_model');
			foreach(array_reverse($id_arr) as $id) {
				$this->db->table_name = $tablename;
				$r = $this->db->get_one(array('id'=>$id));
				$linkurl = preg_match('/^(http|https):\/\//',$r['url']) ? $r['url'] : siteurl($siteid).$r['url'];
				foreach($ids as $catid) {
					$siteid = $siteids[$catid];
					$this->categorys = getcache('category_content_'.$siteid,'commons');
					$modelid = $this->categorys[$catid]['modelid'];
					$this->db->set_model($modelid);
						$newid = $this->db->insert(
						array('title'=>addslashes($r['title']),
							'style'=>addslashes($r['style']),
							'thumb'=>addslashes($r['thumb']),
							'keywords'=>addslashes($r['keywords']),
							'description'=>addslashes($r['description']),
							'status'=>addslashes($r['status']),
							'catid'=>addslashes($catid),
							'url'=>addslashes($linkurl),
							'sysadd'=>1,
							'username'=>addslashes($r['username']),
							'inputtime'=>addslashes($r['inputtime']),
							'updatetime'=>addslashes($r['updatetime']),
							'islink'=>1
						),true);
						$this->db->table_name = $this->db->table_name.'_data';
						$this->db->insert(array('id'=>$newid));
						$hitsid = 'c-'.$modelid.'-'.$newid;
						$this->hits_db->insert(array('hitsid'=>$hitsid,'catid'=>$catid,'updatetime'=>SYS_TIME));
				}
				$this->db->set_model($modelid);
				$number = $this->db->count(array('catid'=>$catid));
				$this->category->update(array('items'=>$number),array('catid'=>$catid));
				$categorys = array();
				$models = getcache('model','commons');
				foreach ($models as $modelid=>$model) {
					$datas = $this->category->select(array('modelid'=>$modelid),'catid,type,items',10000);
					$array = array();
					foreach ($datas as $r) {
						if($r['type']==0) $array[$r['catid']] = $r['items'];
					}
					setcache('category_items_'.$modelid, $array,'commons');
				}
				$array = array();
				$categorys = $this->category->select('`module`=\'content\'','catid,siteid',20000,'listorder ASC');
				foreach ($categorys as $r) {
					$array[$r['catid']] = $r['siteid'];
				}
				setcache('category_content',$array,'commons');
				$categorys = $this->categorys = array();
				$this->categorys = $this->category->select(array('siteid'=>$this->siteid, 'module'=>'content'),'*',10000,'listorder ASC');
				foreach($this->categorys as $r) {
					unset($r['module']);
					$setting = string2array($r['setting']);
					$r['create_to_html_root'] = $setting['create_to_html_root'];
					$r['ishtml'] = $setting['ishtml'];
					$r['content_ishtml'] = $setting['content_ishtml'];
					$r['category_ruleid'] = $setting['category_ruleid'];
					$r['show_ruleid'] = $setting['show_ruleid'];
					$r['workflowid'] = $setting['workflowid'];
					$r['isdomain'] = '0';
					if(!preg_match('/^(http|https):\/\//', $r['url'])) {
						$r['url'] = siteurl($r['siteid']).$r['url'];
					} elseif ($r['ishtml']) {
						$r['isdomain'] = '1';
					}
					$categorys[$r['catid']] = $r;
				}
				setcache('category_content_'.$this->siteid,$categorys,'commons');
			}
			return true;
		} else {
			$siteid = get_siteid();
			$this->categorys = getcache('category_content_'.$siteid,'commons');
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			$this->catids_string = array();
			if($_SESSION['roleid'] != 1) {
				$this->priv_db = pc_base::load_model('category_priv_model');
				$priv_result = $this->priv_db->select(array('action'=>'add','roleid'=>$_SESSION['roleid'],'siteid'=>$siteid,'is_admin'=>1));
				$priv_catids = array();
				foreach($priv_result as $_v) {
					$priv_catids[] = $_v['catid'];
				}
				if(empty($priv_catids)) return '';
			}

			foreach($this->categorys as $r) {
				if($r['siteid']!=$siteid || $r['type']!=0) continue;
				if($_SESSION['roleid'] != 1 && !in_array($r['catid'],$priv_catids)) {
					$arrchildid = explode(',',$r['arrchildid']);
					$array_intersect = array_intersect($priv_catids,$arrchildid);
					if(empty($array_intersect)) continue;
				}
				if($r['child']) {
					$r['checkbox'] = '';
					$r['style'] = 'color:#8A8A8A;';
				} else {
					$checked = '';
					if($typeid && $r['usable_type']) {
						$usable_type = explode(',', $r['usable_type']);
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
			$this->categorys = getcache('category_content_'.$oldsiteid,'commons');

			$modelid = $this->categorys[$old_catid]['modelid'];
			$this->db->set_model($modelid);
			$tablename = $this->db->table_name;
			$this->field_model_db = pc_base::load_model('sitemodel_field_model');
			$this->hits_db = pc_base::load_model('hits_model');
			foreach(array_reverse($id_arr) as $id) {
				$this->db->table_name = $tablename;
				$r = $this->db->get_one(array('id'=>$id));
				$this->db->table_name = $this->db->table_name.'_data';
				$r2 = $this->db->get_one(array('id'=>$id));
				/*foreach($ids as $catid) {
					$siteid = $siteids[$catid];
					$this->categorys = getcache('category_content_'.$siteid,'commons');
					$modelid = $this->categorys[$catid]['modelid'];
					$this->db->set_model($modelid);
					$newid = $this->db->insert(
					array('title'=>addslashes($r['title']),
						'style'=>addslashes($r['style']),
						'thumb'=>addslashes($r['thumb']),
						'keywords'=>addslashes($r['keywords']),
						'description'=>addslashes($r['description']),
						'status'=>addslashes($r['status']),
						'catid'=>addslashes($catid),
						'sysadd'=>1,
						'username'=>addslashes($r['username']),
						'inputtime'=>addslashes($r['inputtime']),
						'updatetime'=>addslashes($r['updatetime']),
						'islink'=>0
					),true);
					$category = $this->category->get_one(array('catid'=>$catid));
					$catdir = $category['catdir'];
					$parentdir = $category['parentdir'];
					$setting = string2array($category['setting']);
					$url = $this->url->get_one(array('urlruleid'=>$setting['show_ruleid'],'module'=>'content','file'=>'show','ishtml'=>$setting['content_ishtml']));
					$linkurl = explode("|",$url['urlrule']);
					$linkurl = $linkurl[0];
					$linkurl = str_replace('{$year}',date('Y',$r['inputtime']),$linkurl);
					$linkurl = str_replace('{$month}',date('m',$r['inputtime']),$linkurl);
					$linkurl = str_replace('{$day}',date('d',$r['inputtime']),$linkurl);
					$linkurl = str_replace('{$categorydir}',$parentdir,$linkurl);
					$linkurl = str_replace('{$catdir}',$catdir,$linkurl);
					$linkurl = str_replace('{$catid}',$catid,$linkurl);
					$linkurl = str_replace('{$id}',$newid,$linkurl);
					if ($setting['content_ishtml']=='1') {
						$linkurl = 'html/'.$linkurl;
					}
					$linkurl = siteurl($category['siteid']).'/'.$linkurl;
					$this->db->update(array('url'=>addslashes($linkurl)),array('id'=>$newid));
					$this->db->table_name = $this->db->table_name.'_data';
					$this->db->insert(array('id'=>$newid,'content'=>addslashes($r2['content']),'paginationtype'=>addslashes($r2['paginationtype']),'maxcharperpage'=>addslashes($r2['maxcharperpage']),'template'=>addslashes($r2['template'])));
					$hitsid = 'c-'.$modelid.'-'.$newid;
					$this->hits_db->insert(array('hitsid'=>$hitsid,'catid'=>$catid,'updatetime'=>SYS_TIME));
					$this->db->set_model($modelid);
					$number = $this->db->count(array('catid'=>$catid));
					$this->category->update(array('items'=>$number),array('catid'=>$catid));
					$categorys = array();
					$models = getcache('model','commons');
				}*/
				foreach($ids as $catid) {
					$siteid = $siteids[$catid];
					$this->categorys = getcache('category_content_'.$siteid,'commons');
					$modelid = $this->categorys[$catid]['modelid'];
					$field1 = $this->field_model_db->listinfo(array('siteid'=>$siteid,'modelid'=>$modelid,'issystem'=>1),'listorder ASC');
					foreach($field1 as $r1) {
						$systeminfo[$modelid]['catid'] = $catid;
						$systeminfo[$modelid]['sysadd'] = 1;
						$systeminfo[$modelid]['username'] = addslashes($r['username']);
						//$systeminfo[$modelid]['islink'] = 0;
						$systeminfo[$modelid][$r1['field']] = addslashes($r[$r1['field']]);
					}
					$this->db->set_model($modelid);
					$newid = $this->db->insert($systeminfo[$modelid],true);
					$category = $this->category->get_one(array('catid'=>$catid));
					$catdir = $category['catdir'];
					$parentdir = $category['parentdir'];
					$setting = string2array($category['setting']);
					$url = $this->url->get_one(array('urlruleid'=>$setting['show_ruleid'],'module'=>'content','file'=>'show','ishtml'=>$setting['content_ishtml']));
					if ($r['islink']==0) {
						$linkurl = explode("|",$url['urlrule']);
						$linkurl = $linkurl[0];
						$linkurl = str_replace('{$year}',date('Y',$r['inputtime']),$linkurl);
						$linkurl = str_replace('{$month}',date('m',$r['inputtime']),$linkurl);
						$linkurl = str_replace('{$day}',date('d',$r['inputtime']),$linkurl);
						$linkurl = str_replace('{$categorydir}',$parentdir,$linkurl);
						$linkurl = str_replace('{$catdir}',$catdir,$linkurl);
						$linkurl = str_replace('{$catid}',$catid,$linkurl);
						$linkurl = str_replace('{$id}',$newid,$linkurl);
						if ($setting['content_ishtml']=='1') {
							$linkurl = 'html/'.$linkurl;
						}
						$linkurl = siteurl($category['siteid']).'/'.$linkurl;
						// 站长工具
						if (module_exists('bdts')) {
							//showmessage(L('系统内已存在该模块，请先卸载后再执行该安装程序！'));
							$this->bdts = pc_base::load_app_class('admin_bdts','bdts');
							$sitemodel_model_db = pc_base::load_model('sitemodel_model');
							$sitemodel = $sitemodel_model_db->get_one(array('modelid'=>$modelid));
							$this->bdts->module_bdts($sitemodel['tablename'], addslashes($linkurl), 'add');
						}
						$this->db->update(array('url'=>addslashes($linkurl)),array('id'=>$newid));
					}
					$field0 = $this->field_model_db->listinfo(array('siteid'=>$siteid,'modelid'=>$modelid,'issystem'=>0),'listorder ASC');
					foreach($field0 as $r0) {
						$modelinfo[$modelid]['id'] = $newid;
						$modelinfo[$modelid]['readpoint'] = addslashes($r2['readpoint']);
						$modelinfo[$modelid]['relation'] = addslashes($r2['relation']);
						if ($r0['formtype']=='pages') {
							$modelinfo[$modelid]['paginationtype'] = addslashes($r2['paginationtype']);
							$modelinfo[$modelid]['maxcharperpage'] = addslashes($r2['maxcharperpage']);
						}else{
							$modelinfo[$modelid][$r0['field']] = addslashes($r2[$r0['field']]);
						}
					}
					$this->db->table_name = $this->db->table_name.'_data';
					$this->db->insert($modelinfo[$modelid]);
					$hitsid = 'c-'.$modelid.'-'.$newid;
					$this->hits_db->insert(array('hitsid'=>$hitsid,'catid'=>$catid,'updatetime'=>SYS_TIME));
					$this->db->set_model($modelid);
					$number = $this->db->count(array('catid'=>$catid));
					$this->category->update(array('items'=>$number),array('catid'=>$catid));
					$categorys = array();
					$models = getcache('model','commons');
				}
				foreach ($models as $modelid=>$model) {
					$datas = $this->category->select(array('modelid'=>$modelid),'catid,type,items',10000);
					$array = array();
					foreach ($datas as $r) {
						if($r['type']==0) $array[$r['catid']] = $r['items'];
					}
					setcache('category_items_'.$modelid, $array,'commons');
				}
				$array = array();
				$categorys = $this->category->select('`module`=\'content\'','catid,siteid',20000,'listorder ASC');
				foreach ($categorys as $r) {
					$array[$r['catid']] = $r['siteid'];
				}
				setcache('category_content',$array,'commons');
				$categorys = $this->categorys = array();
				$this->categorys = $this->category->select(array('siteid'=>$this->siteid, 'module'=>'content'),'*',10000,'listorder ASC');
				foreach($this->categorys as $r) {
					unset($r['module']);
					$setting = string2array($r['setting']);
					$r['create_to_html_root'] = $setting['create_to_html_root'];
					$r['ishtml'] = $setting['ishtml'];
					$r['content_ishtml'] = $setting['content_ishtml'];
					$r['category_ruleid'] = $setting['category_ruleid'];
					$r['show_ruleid'] = $setting['show_ruleid'];
					$r['workflowid'] = $setting['workflowid'];
					$r['isdomain'] = '0';
					if(!preg_match('/^(http|https):\/\//', $r['url'])) {
						$r['url'] = siteurl($r['siteid']).$r['url'];
					} elseif ($r['ishtml']) {
						$r['isdomain'] = '1';
					}
					$categorys[$r['catid']] = $r;
				}
				setcache('category_content_'.$this->siteid,$categorys,'commons');
			}
			return true;
		} else {
			$siteid = get_siteid();
			$this->categorys = getcache('category_content_'.$siteid,'commons');
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			$this->catids_string = array();
			if($_SESSION['roleid'] != 1) {
				$this->priv_db = pc_base::load_model('category_priv_model');
				$priv_result = $this->priv_db->select(array('action'=>'add','roleid'=>$_SESSION['roleid'],'siteid'=>$siteid,'is_admin'=>1));
				$priv_catids = array();
				foreach($priv_result as $_v) {
					$priv_catids[] = $_v['catid'];
				}
				if(empty($priv_catids)) return '';
			}

			foreach($this->categorys as $r) {
				if($r['siteid']!=$siteid || $r['type']!=0) continue;
				if($_SESSION['roleid'] != 1 && !in_array($r['catid'],$priv_catids)) {
					$arrchildid = explode(',',$r['arrchildid']);
					$array_intersect = array_intersect($priv_catids,$arrchildid);
					if(empty($array_intersect)) continue;
				}
				if($r['child']) {
					$r['checkbox'] = '';
					$r['style'] = 'color:#8A8A8A;';
				} else {
					$checked = '';
					if($typeid && $r['usable_type']) {
						$usable_type = explode(',', $r['usable_type']);
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
}
 ?>