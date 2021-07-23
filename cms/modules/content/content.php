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
pc_base::load_app_func('global');

class content extends admin {
	private $db,$priv_db;
	public $siteid,$categorys;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
		//权限判断
		if($this->input->get('catid') && $_SESSION['roleid'] != 1 && ROUTE_A !='pass' && strpos(ROUTE_A,'public_')===false) {
			$catid = intval($this->input->get('catid'));
			$this->priv_db = pc_base::load_model('category_priv_model');
			$action = $this->categorys[$catid]['type']==0 ? ROUTE_A : 'init';
			$priv_datas = $this->priv_db->get_one(array('catid'=>$catid,'is_admin'=>1,'action'=>$action));
			if(!$priv_datas) showmessage(L('permission_to_operate'),'blank');
		}
	}
	
	public function init() {
		$show_header = $show_dialog  = $show_pc_hash = '';
		if($this->input->get('catid') && $this->input->get('catid') && $this->categorys[$this->input->get('catid')]['siteid']==$this->siteid) {
			$catid = intval($this->input->get('catid'));
			$category = $this->categorys[$catid];
			$modelid = $category['modelid'];
			$model_arr = getcache('model', 'commons');
			$MODEL = $model_arr[$modelid];
			unset($model_arr);
			$admin_username = param::get_cookie('admin_username');
			//查询当前的工作流
			$setting = string2array($category['setting']);
			$workflowid = $setting['workflowid'];
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
			//工作流审核级别
			$workflow_steps = $workflows['steps'];
			$workflow_menu = '';
			$steps = $this->input->get('steps') ? intval($this->input->get('steps')) : 0;
			//工作流权限判断
			if($_SESSION['roleid']!=1 && $steps && !in_array($steps,$admin_privs)) showmessage(L('permission_to_operate'));
			$this->db->set_model($modelid);
			if($this->db->table_name==$this->db->db_tablepre) showmessage(L('model_table_not_exists'));;
			$status = $steps ? $steps : 99;
			if($this->input->get('reject')) $status = 0;
			$where = 'catid='.$catid.' AND status='.$status;
			if (IS_POST) {
				//搜索
				if($this->input->post('start_time')) {
					$start_time = strtotime($this->input->post('start_time'));
					$where .= " AND `inputtime` > '$start_time'";
				}
				if($this->input->post('end_time')) {
					$end_time = strtotime($this->input->post('end_time'));
					$where .= " AND `inputtime` < '$end_time'";
				}
				if($start_time>$end_time) dr_json(0, L('starttime_than_endtime'));
				if($this->input->post('keyword')) {
					$type_array = array('title','description','username');
					$searchtype = intval($this->input->post('searchtype'));
					if($searchtype < 3) {
						$searchtype = $type_array[$searchtype];
						$keyword = strip_tags(trim($this->input->post('keyword')));
						$where .= " AND `$searchtype` like '%$keyword%'";
					} elseif($searchtype==3) {
						$keyword = intval($this->input->post('keyword'));
						$where .= " AND `id`='$keyword'";
					}
				}
				if($this->input->post('posids') && !empty($this->input->post('posids'))) {
					$posids = $this->input->post('posids')==1 ? intval($this->input->post('posids')) : 0;
					$where .= " AND `posids` = '$posids'";
				}
				$pagesize = $this->input->post('limit') ? $this->input->post('limit') : 10;
				$datas = $this->db->listinfo($where,'id desc',$this->input->post('page'),$pagesize);
				$total = $this->db->count($where);
				$pages = $this->db->pages;
				if(!empty($datas)) {
					$sitelist = getcache('sitelist','commons');
					$release_siteurl = $sitelist[$category['siteid']]['url'];
					$path_len = -strlen(WEB_PATH);
					$release_siteurl = substr($release_siteurl,0,$path_len);
					$this->hits_db = pc_base::load_model('hits_model');
					foreach($datas as $r) {
						$hits_r = $this->hits_db->get_one(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));
						$rs['id'] = $r['id'];
						$rs['catid'] = $r['catid'];
						if ($r['style'] && strstr($r['style'], ';')) {
							$style_arr = explode(';',$r['style']);
							$rs['title'] = '<span style="'.($style_arr[0] ? 'color:'.$style_arr[0].';' : '').($style_arr[1] ? 'font-weight:'.$style_arr[1].';' : '').'">'.$r['title'].'</span>';
						} else if ($r['style']) {
							$rs['title'] = '<span style="color:'.$r['style'].';">'.$r['title'].'</span>';
						} else {
							$rs['title'] = $r['title'];
						}
						$rs['thumb'] = $r['thumb'];
						$rs['posids'] = $r['posids'];
						$rs['islink'] = $r['islink'];
						$rs['status'] = $r['status'];
						$rs['sysadd'] = $r['sysadd'];
						$rs['username'] = $r['username'];
						$rs['deusername'] = urlencode($r['username']);
						$rs['updatetime'] = dr_date($r['updatetime'],null,'red');
						$rs['listorder'] = $r['listorder'];
						if($r['status']==99) {
							if($r['islink']) {
								$rs['url'] = $r['url'];
							} elseif(strpos($r['url'],'http://')!==false || strpos($r['url'],'https://')!==false) {
								$rs['url'] = $r['url'];
							} else {
								$rs['url'] = $release_siteurl.$r['url'];
							}
						} else {
							$rs['url'] = '?m=content&c=content&a=public_preview&steps='.$steps.'&catid='.$r['catid'].'&id='.$r['id'].'';
						}
						$rs['dayviews'] = $hits_r['dayviews'];
						$rs['yesterdayviews'] = $hits_r['yesterdayviews'];
						$rs['weekviews'] = $hits_r['weekviews'];
						$rs['monthviews'] = $hits_r['monthviews'];
						$rs['views'] = $hits_r['views'];
						$rs['idencode'] = id_encode('content_'.$r['catid'],$r['id'],$this->siteid);
						$rs['safetitle'] = safe_replace($r['title']);
						$array[] = $rs;
					}
				}
				exit(json_encode(array('code'=>0,'msg'=>L('to_success'),'count'=>$total,'data'=>$array,'rel'=>1)));
			}
			$pc_hash = $_SESSION['pc_hash'];
			for($i=1;$i<=$workflow_steps;$i++) {
				if($_SESSION['roleid']!=1 && !in_array($i,$admin_privs)) continue;
				$current = $steps==$i ? ' layui-btn-danger' : '';
				$r = $this->db->get_one(array('catid'=>$catid,'status'=>$i));
				$newimg = $r ? '<img src="'.IMG_PATH.'icon/new.png" style="padding-bottom:2px" onclick="window.location.href=\'?m=content&c=content&a=&menuid='.intval($this->input->get('menuid')).'&catid='.$catid.'&steps='.$i.'&pc_hash='.$pc_hash.'\'">' : '';
				$workflow_menu .= '<a href="?m=content&c=content&a=&menuid='.intval($this->input->get('menuid')).'&catid='.$catid.'&steps='.$i.'&pc_hash='.$pc_hash.'" class="layui-btn layui-btn-sm'.$current.'"><i class="fa fa-check"></i>'.L('workflow_'.$i).$newimg.'</a>';
			}
			if($workflow_menu) {
				$current = $this->input->get('reject') ? ' layui-btn-danger' : '';
				$workflow_menu .= '<a href="?m=content&c=content&a=&menuid='.intval($this->input->get('menuid')).'&catid='.$catid.'&pc_hash='.$pc_hash.'&reject=1" class="layui-btn layui-btn-sm'.$current.'"><i class="fa fa-check"></i>'.L('reject').'</a>';
			}
			//$ = 153fc6d28dda8ca94eaa3686c8eed857;获取模型的thumb字段配置信息
			$model_fields = getcache('model_field_'.$modelid, 'model');
			$setting = string2array($model_fields['thumb']['setting']);
			$args = '1,'.$setting['upload_allowext'].','.$setting['isselectimage'].','.$setting['images_width'].','.$setting['images_height'].','.$setting['watermark'];
			$authkey = upload_key($args);
			$template = $MODEL['admin_list_template'] ? $MODEL['admin_list_template'] : 'content_list';
			include $this->admin_tpl($template);
		} else {
			include $this->admin_tpl('content_quick');
		}
	}
	
	public function public_init() {
		$show_header = $show_dialog  = $show_pc_hash = '';
		include $this->admin_tpl('content_quick_list');
	}
	
	public function recycle_init() {
		$show_header = $show_dialog  = $show_pc_hash = '';
		if($this->input->get('catid') && $this->input->get('catid') && $this->categorys[$this->input->get('catid')]['siteid']==$this->siteid) {
			$catid = intval($this->input->get('catid'));
			$category = $this->categorys[$catid];
			$modelid = $category['modelid'];
			$model_arr = getcache('model', 'commons');
			$MODEL = $model_arr[$modelid];
			unset($model_arr);
			$admin_username = param::get_cookie('admin_username');
			//查询当前的工作流
			$setting = string2array($category['setting']);
			$workflowid = $setting['workflowid'];
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
			$this->db->set_model($modelid);
			if($this->db->table_name==$this->db->db_tablepre) showmessage(L('model_table_not_exists'));;
			$status = 100;
			$where = 'catid='.$catid.' AND status='.$status;
			if (IS_POST) {
				//搜索
				if($this->input->post('start_time')) {
					$start_time = strtotime($this->input->post('start_time'));
					$where .= " AND `inputtime` > '$start_time'";
				}
				if($this->input->post('end_time')) {
					$end_time = strtotime($this->input->post('end_time'));
					$where .= " AND `inputtime` < '$end_time'";
				}
				if($start_time>$end_time) dr_json(0, L('starttime_than_endtime'));
				if($this->input->post('keyword')) {
					$type_array = array('title','description','username');
					$searchtype = intval($this->input->post('searchtype'));
					if($searchtype < 3) {
						$searchtype = $type_array[$searchtype];
						$keyword = strip_tags(trim($this->input->post('keyword')));
						$where .= " AND `$searchtype` like '%$keyword%'";
					} elseif($searchtype==3) {
						$keyword = intval($this->input->post('keyword'));
						$where .= " AND `id`='$keyword'";
					}
				}
				if($this->input->post('posids') && !empty($this->input->post('posids'))) {
					$posids = $this->input->post('posids')==1 ? intval($this->input->post('posids')) : 0;
					$where .= " AND `posids` = '$posids'";
				}
				$pagesize = $this->input->post('limit') ? $this->input->post('limit') : 10;
				$datas = $this->db->listinfo($where,'id desc',$this->input->post('page'),$pagesize);
				$total = $this->db->count($where);
				$pages = $this->db->pages;
				if(!empty($datas)) {
					$sitelist = getcache('sitelist','commons');
					$release_siteurl = $sitelist[$category['siteid']]['url'];
					$path_len = -strlen(WEB_PATH);
					$release_siteurl = substr($release_siteurl,0,$path_len);
					$this->hits_db = pc_base::load_model('hits_model');
					foreach($datas as $r) {
						$hits_r = $this->hits_db->get_one(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));
						$rs['id'] = $r['id'];
						$rs['catid'] = $r['catid'];
						/*if ($r['style'] && strstr($r['style'], ';')) {
							$style_arr = explode(';',$r['style']);
							$rs['title'] = '<span style="'.($style_arr[0] ? 'color:'.$style_arr[0].';' : '').($style_arr[1] ? 'font-weight:'.$style_arr[1].';' : '').'">'.$r['title'].'</span>';
						} else if ($r['style']) {
							$rs['title'] = '<span style="color:'.$r['style'].';">'.$r['title'].'</span>';
						} else {
							$rs['title'] = $r['title'];
						}*/
						$rs['title'] = '<span style="color:#FF0000;">'.$r['title'].'</span>';
						$rs['thumb'] = $r['thumb'];
						$rs['posids'] = $r['posids'];
						$rs['islink'] = $r['islink'];
						$rs['status'] = $r['status'];
						$rs['sysadd'] = $r['sysadd'];
						$rs['username'] = $r['username'];
						$rs['deusername'] = urlencode($r['username']);
						$rs['updatetime'] = dr_date($r['updatetime'],null,'red');
						$rs['listorder'] = $r['listorder'];
						if($r['status']==99) {
							if($r['islink']) {
								$rs['url'] = $r['url'];
							} elseif(strpos($r['url'],'http://')!==false || strpos($r['url'],'https://')!==false) {
								$rs['url'] = $r['url'];
							} else {
								$rs['url'] = $release_siteurl.$r['url'];
							}
						} else {
							$rs['url'] = '?m=content&c=content&a=public_preview&steps='.$steps.'&catid='.$r['catid'].'&id='.$r['id'].'';
						}
						$rs['dayviews'] = $hits_r['dayviews'];
						$rs['yesterdayviews'] = $hits_r['yesterdayviews'];
						$rs['weekviews'] = $hits_r['weekviews'];
						$rs['monthviews'] = $hits_r['monthviews'];
						$rs['views'] = $hits_r['views'];
						$rs['idencode'] = id_encode('content_'.$r['catid'],$r['id'],$this->siteid);
						$rs['safetitle'] = safe_replace($r['title']);
						$array[] = $rs;
					}
				}
				exit(json_encode(array('code'=>0,'msg'=>L('to_success'),'count'=>$total,'data'=>$array,'rel'=>1)));
			}
			$pc_hash = $_SESSION['pc_hash'];
			//$ = 153fc6d28dda8ca94eaa3686c8eed857;获取模型的thumb字段配置信息
			$model_fields = getcache('model_field_'.$modelid, 'model');
			$setting = string2array($model_fields['thumb']['setting']);
			$args = '1,'.$setting['upload_allowext'].','.$setting['isselectimage'].','.$setting['images_width'].','.$setting['images_height'].','.$setting['watermark'];
			$authkey = upload_key($args);
			include $this->admin_tpl('content_recycle');
		} else {
			include $this->admin_tpl('content_quick');
		}
	}
	
	public function initall() {
		$show_header = $show_dialog  = $show_pc_hash = '';
		$this->db = pc_base::load_model('admin_model');
		$infos = $this->db->listinfo('', '', '');
		$this->db = pc_base::load_model('sitemodel_model');
		$this->siteid = $this->get_siteid();
		if(!$this->siteid) $this->siteid = 1;
		$categorys = getcache('category_content_'.$this->siteid,'commons');
		$datas2 = $this->db->listinfo(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0),'','');
		//模型文章数array('模型id'=>数量);
		$items = array();
		foreach ($datas2 as $k=>$r) {
			foreach ($categorys as $catid=>$cat) {
				if(intval($cat['modelid']) == intval($r['modelid'])) {
					$items[$r['modelid']] += intval($cat['items']);
				} else {
					$items[$r['modelid']] += 0;
				}
			}
			$datas2[$k]['items'] = $items[$r['modelid']];
		}
		$this->db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
		//权限判断
		if($this->input->get('catid') && $_SESSION['roleid'] != 1 && ROUTE_A !='pass' && strpos(ROUTE_A,'public_')===false) {
			$catid = intval($this->input->get('catid'));
			$this->priv_db = pc_base::load_model('category_priv_model');
			$action = $this->categorys[$catid]['type']==0 ? ROUTE_A : 'initall';
			$priv_datas = $this->priv_db->get_one(array('catid'=>$catid,'is_admin'=>1,'action'=>$action));
			if(!$priv_datas) showmessage(L('permission_to_operate'),'blank');
		}
		$modelid = intval($this->input->get('modelid'));
		if (!$modelid) {include $this->admin_tpl('sitemodel_manage_all');exit();}
		$admin_username = param::get_cookie('admin_username');
		//查询当前的工作流
		$setting = string2array($category['setting']);
		$workflowid = $setting['workflowid'];
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
		//工作流审核级别
		$workflow_steps = $workflows['steps'];
		$steps = $this->input->get('steps') ? intval($this->input->get('steps')) : 0;
		//工作流权限判断
		if($_SESSION['roleid']!=1 && $steps && !in_array($steps,$admin_privs)) showmessage(L('permission_to_operate'));
		$this->db->set_model($modelid);
		if($this->db->table_name==$this->db->db_tablepre) showmessage(L('model_table_not_exists'));;
		$status = $steps ? $steps : 99;
		if($this->input->get('reject')) $status = 0;
		$where = 'status='.$status;
		if (IS_POST) {
			//搜索
			if($this->input->post('start_time')) {
				$start_time = strtotime($this->input->post('start_time'));
				$where .= " AND `inputtime` > '$start_time'";
			}
			if($this->input->post('end_time')) {
				$end_time = strtotime($this->input->post('end_time'));
				$where .= " AND `inputtime` < '$end_time'";
			}
			if($start_time>$end_time) dr_json(0, L('starttime_than_endtime'));
			if($this->input->post('keyword')) {
				$type_array = array('title','description','username');
				$searchtype = intval($this->input->post('searchtype'));
				if($searchtype < 3) {
					$searchtype = $type_array[$searchtype];
					$keyword = strip_tags(trim($this->input->post('keyword')));
					$where .= " AND `$searchtype` like '%$keyword%'";
				} elseif($searchtype==3) {
					$keyword = intval($this->input->post('keyword'));
					$where .= " AND `id`='$keyword'";
				}
			}
			if($this->input->post('posids') && !empty($this->input->post('posids'))) {
				$posids = $this->input->post('posids')==1 ? intval($this->input->post('posids')) : 0;
				$where .= " AND `posids` = '$posids'";
			}
			$pagesize = $this->input->post('limit') ? $this->input->post('limit') : 10;
			$datas = $this->db->listinfo($where,'id desc',$this->input->post('page'),$pagesize);
			$total = $this->db->count($where);
			$pages = $this->db->pages;
			if(!empty($datas)) {
				$sitelist = getcache('sitelist','commons');
				$release_siteurl = $sitelist[$category['siteid']]['url'];
				$path_len = -strlen(WEB_PATH);
				$release_siteurl = substr($release_siteurl,0,$path_len);
				$this->hits_db = pc_base::load_model('hits_model');
				foreach($datas as $r) {
					$hits_r = $this->hits_db->get_one(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));
					$rs['id'] = $r['id'];
					$rs['catid'] = $r['catid'];
					if ($r['style'] && strstr($r['style'], ';')) {
						$style_arr = explode(';',$r['style']);
						$rs['title'] = '<span style="'.($style_arr[0] ? 'color:'.$style_arr[0].';' : '').($style_arr[1] ? 'font-weight:'.$style_arr[1].';' : '').'">'.$r['title'].'</span>';
					} else if ($r['style']) {
						$rs['title'] = '<span style="color:'.$r['style'].';">'.$r['title'].'</span>';
					} else {
						$rs['title'] = $r['title'];
					}
					$rs['thumb'] = $r['thumb'];
					$rs['posids'] = $r['posids'];
					$rs['islink'] = $r['islink'];
					$rs['status'] = $r['status'];
					$rs['sysadd'] = $r['sysadd'];
					$rs['username'] = $r['username'];
					$rs['deusername'] = urlencode($r['username']);
					$rs['updatetime'] = dr_date($r['updatetime'],null,'red');
					$rs['listorder'] = $r['listorder'];
					if($r['status']==99) {
						if($r['islink']) {
							$rs['url'] = $r['url'];
						} elseif(strpos($r['url'],'http://')!==false || strpos($r['url'],'https://')!==false) {
							$rs['url'] = $r['url'];
						} else {
							$rs['url'] = $release_siteurl.$r['url'];
						}
					} else {
						$rs['url'] = '?m=content&c=content&a=public_preview&steps='.$steps.'&catid='.$r['catid'].'&id='.$r['id'].'';
					}
					$rs['dayviews'] = $hits_r['dayviews'];
					$rs['yesterdayviews'] = $hits_r['yesterdayviews'];
					$rs['weekviews'] = $hits_r['weekviews'];
					$rs['monthviews'] = $hits_r['monthviews'];
					$rs['views'] = $hits_r['views'];
					$rs['idencode'] = id_encode('content_'.$r['catid'],$r['id'],$this->siteid);
					$rs['safetitle'] = safe_replace($r['title']);
					$array[] = $rs;
				}
			}
			exit(json_encode(array('code'=>0,'msg'=>L('to_success'),'count'=>$total,'data'=>$array,'rel'=>1)));
		}
		$pc_hash = $_SESSION['pc_hash'];
		//$ = 153fc6d28dda8ca94eaa3686c8eed857;获取模型的thumb字段配置信息
		$model_fields = getcache('model_field_'.$modelid, 'model');
		$setting = string2array($model_fields['thumb']['setting']);
		$args = '1,'.$setting['upload_allowext'].','.$setting['isselectimage'].','.$setting['images_width'].','.$setting['images_height'].','.$setting['watermark'];
		$authkey = upload_key($args);
		$template = $MODEL['admin_list_template'] ? $MODEL['admin_list_template'] : 'content_list_all';
		include $this->admin_tpl('content_list_all');
	}
	public function add() {
		if($this->input->post('dosubmit') || $this->input->post('dosubmit_continue')) {
			define('INDEX_HTML',true);
			$info = $this->input->post('info');
			$catid = $info['catid'] = intval($info['catid']);
			if(trim($info['title'])=='') showmessage(L('title_is_empty'));
			$category = $this->categorys[$catid];
			if($category['type']==0) {
				$modelid = $this->categorys[$catid]['modelid'];
				$this->db->set_model($modelid);
				//如果该栏目设置了工作流，那么必须走工作流设定
				$setting = string2array($category['setting']);
				$workflowid = $setting['workflowid'];
				if($workflowid && $this->input->post('status')!=99) {
					//如果用户是超级管理员，那么则根据自己的设置来发布
					$info['status'] = $_SESSION['roleid']==1 ? intval($this->input->post('status')) : 1;
				} else {
					$info['status'] = 99;
				}
				// 去除站外链接
				$value = new_stripslashes($info['content']);
				if ($this->input->post('is_remove_a') && preg_match_all("/<a(.*)href=(.+)>(.*)<\/a>/Ui", $value, $arrs)) {
					//$sites = require CACHE_PATH.'caches_commons/caches_data/domain_site.cache.php';
					$this->sitedb = pc_base::load_model('site_model');
					$data = $this->sitedb->select();
					$sites = array();
					foreach ($data as $t) {
						$domain = parse_url($t['domain']);
						if ($domain['port']) {
							$sites[$domain['host'].':'.$domain['port']] = $t['siteid'];
						} else {
							$sites[$domain['host']] = $t['siteid'];
						}
					}
					foreach ($arrs[2] as $i => $a) {
						if (strpos($a, ' ') !== false) {
							list($a) = explode(' ', $a);
						}
						$a = trim($a, '"');
						$a = trim($a, '\'');
						$arr = parse_url($a);
						if ($arr && $arr['host'] && !isset($sites[$arr['host']])) {
							// 去除a标签
							$value = str_replace($arrs[0][$i], $arrs[3][$i], $value);
						}
					}
				}
				$info['content'] = $value;
				$this->db->add_content($info);
				if($this->input->post('dosubmit')) {
					showmessage(L('add_success').L('2s_close'),'blank','','','window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right.location.reload(true);function set_time() {$("#secondid").html(1);}setTimeout("set_time()", 500);setTimeout("ownerDialog.close()", 1200);');
				} else {
					showmessage(L('add_success'),HTTP_REFERER);
				}
			} else {
				//单网页
				$this->page_db = pc_base::load_model('page_model');
				$style_font_weight = $this->input->post('style_font_weight') ? 'font-weight:'.strip_tags($this->input->post('style_font_weight')) : '';
				$info['style'] = strip_tags($this->input->post('style_color')).';'.$style_font_weight;
				
				if($this->input->post('edit')) {
					$this->page_db->update($info,array('catid'=>$catid));
				} else {
					$catid = $this->page_db->insert($info,1);
				}
				$this->page_db->create_html($catid,$info);
				$forward = HTTP_REFERER;
			}
			showmessage(L('add_success'),$forward);
		} else {
			$show_header = $show_dialog = $show_validator = '';
			//设置cookie 在附件添加处调用
			param::set_cookie('module', 'content');

			if($this->input->get('catid') && $this->input->get('catid')) {
				$catid = intval($this->input->get('catid'));
				
				param::set_cookie('catid', $catid);
				$category = $this->categorys[$catid];
				if($category['type']==0) {
					$modelid = $category['modelid'];
					//取模型ID，依模型ID来生成对应的表单
					require CACHE_MODEL_PATH.'content_form.class.php';
					$content_form = new content_form($modelid,$catid,$this->categorys);
					$forminfos = $content_form->get();
 					$formValidator = $content_form->formValidator;
 					$checkall = $content_form->checkall;
					$setting = string2array($category['setting']);
					$workflowid = $setting['workflowid'];
					$workflows = getcache('workflow_'.$this->siteid,'commons');
					$workflows = $workflows[$workflowid];
					$workflows_setting = string2array($workflows['setting']);
					$nocheck_users = $workflows_setting['nocheck_users'];
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
					$content_form = new content_form(-2,$catid);
					$forminfos = $content_form->get();
					$formValidator = $content_form->formValidator;
					$checkall = $content_form->checkall;
					
					$r = $this->page_db->get_one(array('catid'=>$catid));
					
					if($r) {
						$forminfos = $content_form->get($r);
						extract($r);
						$style_arr = explode(';',$style);
						$style_color = $style_arr[0];
						$style_font_weight = $style_arr[1] ? substr($style_arr[1],12) : '';
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
		if($this->input->post('dosubmit') || $this->input->post('dosubmit_continue')) {
			define('INDEX_HTML',true);
			$info = $this->input->post('info');
			$id = $info['id'] = intval($this->input->post('id'));
			$catid = $info['catid'] = intval($info['catid']);
			if(trim($info['title'])=='') showmessage(L('title_is_empty'));
			$modelid = $this->categorys[$catid]['modelid'];
			$this->db->set_model($modelid);
			// 去除站外链接
			$value = new_stripslashes($info['content']);
			if ($this->input->post('is_remove_a') && preg_match_all("/<a(.*)href=(.+)>(.*)<\/a>/Ui", $value, $arrs)) {
				//$sites = require CACHE_PATH.'caches_commons/caches_data/domain_site.cache.php';
				$this->sitedb = pc_base::load_model('site_model');
				$data = $this->sitedb->select();
				$sites = array();
				foreach ($data as $t) {
					$domain = parse_url($t['domain']);
					if ($domain['port']) {
						$sites[$domain['host'].':'.$domain['port']] = $t['siteid'];
					} else {
						$sites[$domain['host']] = $t['siteid'];
					}
				}
				foreach ($arrs[2] as $i => $a) {
					if (strpos($a, ' ') !== false) {
						list($a) = explode(' ', $a);
					}
					$a = trim($a, '"');
					$a = trim($a, '\'');
					$arr = parse_url($a);
					if ($arr && $arr['host'] && !isset($sites[$arr['host']])) {
						// 去除a标签
						$value = str_replace($arrs[0][$i], $arrs[3][$i], $value);
					}
				}
			}
			$info['content'] = $value;
			$this->db->edit_content($info,$id);
			if($this->input->post('dosubmit')) {
				showmessage(L('update_success').L('2s_close'),'blank','','','window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right.location.reload(true);function set_time() {$("#secondid").html(1);}setTimeout("set_time()", 500);setTimeout("ownerDialog.close()", 1200);');
			} else {
				showmessage(L('update_success'),HTTP_REFERER);
			}
		} else {
			$show_header = $show_dialog = $show_validator = '';
			//从数据库获取内容
			$id = intval($this->input->get('id'));
			if(!$this->input->get('catid') || !$this->input->get('catid')) showmessage(L('missing_part_parameters'));
			$catid = intval($this->input->get('catid'));
			
			$this->model = getcache('model', 'commons');
			
			param::set_cookie('catid', $catid);
			$category = $this->categorys[$catid];
			$modelid = $category['modelid'];
			$this->db->table_name = $this->db->db_tablepre.$this->model[$modelid]['tablename'];
			$r = $this->db->get_one(array('id'=>$id));
			$this->db->table_name = $this->db->table_name.'_data';
			$r2 = $this->db->get_one(array('id'=>$id));
			if(!$r2) showmessage(L('subsidiary_table_datalost'),'blank');
			$data = array_merge($r,$r2);
			$data = array_map('htmlspecialchars_decode',$data);
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form($modelid,$catid,$this->categorys);

			$forminfos = $content_form->get($data);
			$formValidator = $content_form->formValidator;
			$checkall = $content_form->checkall;
			include $this->admin_tpl('content_edit');
		}
		header("Cache-control: private");
	}
	/**
	 * 删除
	 */
	public function delete() {
		if($this->input->get('dosubmit')) {
			$catid = intval($this->input->get('catid'));
			if(!$catid) dr_json(0, L('missing_part_parameters'));
			$modelid = $this->categorys[$catid]['modelid'];
			$sethtml = $this->categorys[$catid]['sethtml'];
			$siteid = $this->categorys[$catid]['siteid'];
			
			$html_root = pc_base::load_config('system','html_root');
			if($sethtml) $html_root = '';
			
			$setting = string2array($this->categorys[$catid]['setting']);
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
			$sitelist = getcache('sitelist','commons');
			
			foreach($ids as $id) {
				$r = $this->db->get_one(array('id'=>$id));
				if($content_ishtml && !$r['islink']) {
					$urls = $this->url->show($id, 0, $r['catid'], $r['inputtime']);
					$fileurl = $urls[1];
					if($this->siteid != 1) {
						$fileurl = $html_root.'/'.$sitelist[$this->siteid]['dirname'].$fileurl;
					}
					$mobilefileurl = pc_base::load_config('system','mobile_root').$fileurl;
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
					if($sitelist[$this->siteid]['mobilehtml']==1) {
						foreach ($mobilefilelist as $mobiledelfile) {
							$mobilelasttext = strrchr($mobiledelfile,'.');
							if(!in_array($mobilelasttext, array('.htm','.html','.shtml'))) continue;
							@unlink($mobiledelfile);
						}
					}
				} else {
					$fileurl = 0;
				}
				//删除内容
				$this->db->delete_content($id,$fileurl,$catid);
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
			//更新栏目统计
			$this->db->cache_items();
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 更新
	 */
	public function recycle() {
		if($this->input->get('dosubmit')) {
			$catid = intval($this->input->get('catid'));
			$recycle = intval($this->input->get('recycle'));
			if($this->input->post('id')) {
				$ids = array(0=>intval($this->input->post('id')));
			} else {
				$ids = $this->input->get_post_ids();
			}
			if(empty($ids)) dr_json(0, L('you_do_not_check'));
			if ($catid) {
				$modelid = $this->categorys[$catid]['modelid'];
				$this->db->set_model($modelid);
				foreach($ids as $id) {
					if ($recycle) {
						$this->db->update(array('status'=>100),array('id'=>$id));
					} else {
						$this->db->update(array('status'=>99),array('id'=>$id));
					}
				}
				dr_json(1, L('operation_success'));
			}
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 更新
	 */
	public function update() {
		if($this->input->get('dosubmit')) {
			$catid = intval($this->input->get('catid'));
			if ($catid) {
				$modelid = $this->categorys[$catid]['modelid'];
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
	 * 过审内容
	 */
	public function pass() {
		$admin_username = param::get_cookie('admin_username');
		$catid = intval($this->input->get('catid'));
		
		if(!$catid) dr_json(0, L('missing_part_parameters'));
		$category = $this->categorys[$catid];
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
			if($_SESSION['roleid']!=1 && $steps && !in_array($steps,$admin_privs)) dr_json(0, L('permission_to_operate'));
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
				
				$modelid = $this->categorys[$catid]['modelid'];
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
  								$urls = $this->url->show($id, 0, $content_info['catid'], $content_info['inputtime'], '',$content_info,'add');
   								$html->show($urls[1],$urls['data'],0);
 							}
							//更新到全站搜索
							$inputinfo = '';
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
						$urls = $this->url->show($id, 0, $content_info['catid'], $content_info['inputtime'], '',$content_info,'add');
						$html->show($urls[1],$urls['data'],0);
						}
						//更新到全站搜索
						$inputinfo = '';
						$inputinfo['system'] = $content_info;
						$this->db->search_api($id,$inputinfo);
					}
				}
				if($this->input->get('ajax_preview')) {
					$ids = $this->input->get('id');
				}
				$this->db->status($ids,$status);
		}
		dr_json(1, L('operation_success'));
	}
	/**
	 * 排序
	 */
	public function listorder() {
		if($this->input->get('dosubmit')) {
			$catid = intval($this->input->get('catid'));
			if(!$catid) dr_json(0, L('missing_part_parameters'));
			$modelid = $this->categorys[$catid]['modelid'];
			$this->db->set_model($modelid);
			$this->db->update(array('listorder'=>$this->input->post('listorder')),array('id'=>$this->input->post('id')));
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 显示栏目菜单列表
	 */
	public function public_categorys() {
		$show_header = '';
		$cfg = getcache('common','commons');
		$ajax_show = intval($cfg['category_ajax']);
		$from = $this->input->get('from') && in_array($this->input->get('from'),array('block')) ? $this->input->get('from') : 'content';
		$tree = pc_base::load_sys_class('tree');
		if($from=='content' && $_SESSION['roleid'] != 1) {	
			$this->priv_db = pc_base::load_model('category_priv_model');
			$priv_result = $this->priv_db->select(array('action'=>'init','roleid'=>$_SESSION['roleid'],'siteid'=>$this->siteid,'is_admin'=>1));
			$priv_catids = array();
			foreach($priv_result as $_v) {
				$priv_catids[] = $_v['catid'];
			}
			if(empty($priv_catids)) return '';
		}
		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $r) {
				$setting = string2array($r['setting']);
				if ($setting['disabled']) continue;
				if($r['siteid']!=$this->siteid ||  ($r['type']==2 && $r['child']==0)) continue;
				if($from=='content' && $_SESSION['roleid'] != 1 && !in_array($r['catid'],$priv_catids)) {
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
					$r['category_edit'] = " <a href='?m=content&c=content&a=add&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."' target='right'>".L('edit')."</a>";
					$r['add_icon'] = '';
					$r['type'] = 'add';
				} else {
					$r['icon_type'] = $r['vs_show'] = '';
					$r['category_edit'] = '';
					$r['type'] = 'init';
					$r['add_icon'] = "<a target='right' href='?m=content&c=content&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."' onclick=\"javascript:contentopen('?m=content&c=content&a=add&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."&hash_page=".$_SESSION['hash_page']."','".L('add_content')."')\"><img src='".IMG_PATH."add_content.gif' alt='".L('add')."'></a> ";
				}
				$categorys[$r['catid']] = $r;
			}
		}
		if(!empty($categorys)) {
			$tree->init($categorys);
				switch($from) {
					case 'block':
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&type=list' target='".$this->input->get('from')."_right'>\$catname</a> \$vs_show</span>";
						$strs2 = "<img src='".IMG_PATH."folder.gif'> <a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&type=category' target='".$this->input->get('from')."_right'>\$catname</a>";
					break;

					default:
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=content&c=content&a=\$type&menuid=".intval($this->input->get('menuid'))."&catid=\$catid' target='right'>\$catname</a></span>";
						$strs2 = "<span class='folder'>\$catname\$category_edit</span>";
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
		$modelid = $this->categorys[$catid]['modelid'];
		$this->db->set_model($modelid);
		$title = $this->input->get('data');
		if(CHARSET=='gbk') $title = iconv('utf-8','gbk',$title);
		$where = "title='".$title."'";
		if ($id) {
			$where .= ' AND id<>'.$id;
		}
		$r = $this->db->get_one($where);
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
			$this->db->table_name = $this->db->table_name.'_data';
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
		$this->userid = $_SESSION['userid'] ? $_SESSION['userid'] : (param::get_cookie('_userid') ? param::get_cookie('_userid') : sys_auth($this->input->post('userid_h5'),'DECODE'));
		$siteid = param::get_cookie('siteid');
		if(!$siteid) $siteid = $this->get_siteid() ? $this->get_siteid() : 1 ;
		if($this->input->post('filepath')){
			$x = $this->input->post('x');
			$y = $this->input->post('y');
			$w = $this->input->post('w');
			$h = $this->input->post('h');
			pc_base::load_sys_class('image');
			$image = new image();
			$filename = (SYS_ATTACHMENT_FILE ? $siteid.'/' : '').date('Y/md/').date("ymdhis").substr(md5(SYS_TIME.($this->input->post('filepath')).uniqid()), rand(0, 20), 15);
			//$new_file = date('Y/md/').'thumb_'.$w.'_'.$h.'_'.basename($this->input->post('filepath'));
			$filetype = fileext($this->input->post('filepath'));
			$fileinfo = $image->crops(CMS_PATH.$this->input->post('filepath'), SYS_UPLOAD_PATH.$filename.'.'.$filetype, $w, $h, $x, $y);
			//$fileinfo = $image->crops(CMS_PATH.$this->input->post('filepath'), SYS_UPLOAD_PATH.$new_file, $w, $h, $x, $y);
			if($fileinfo){
				if (is_image(SYS_UPLOAD_PATH.$filename.'.'.$filetype)) {
					$img = getimagesize(SYS_UPLOAD_PATH.$filename.'.'.$filetype);
					$info = array(
						'width' => $img[0],
						'height' => $img[1],
					);
				}
				$this->att_db = pc_base::load_model('attachment_model');
				$uploadedfile['module'] = $this->input->get('module');
				$uploadedfile['catid'] = $this->input->get('catid');
				$uploadedfile['siteid'] = $siteid;
				$uploadedfile['userid'] = $this->userid;
				$uploadedfile['uploadtime'] = SYS_TIME;
				$uploadedfile['uploadip'] = ip();
				$uploadedfile['status'] = pc_base::load_config('system','attachment_stat') ? 0 : 1;
				$uploadedfile['authcode'] = md5($filename.'.'.$filetype);
				$uploadedfile['filemd5'] = md5_file(SYS_UPLOAD_PATH.$filename.'.'.$filetype);
				$uploadedfile['remote'] = 0;
				$uploadedfile['attachinfo'] = dr_array2string($info);
				$uploadedfile['isimage'] = in_array($filetype, array('gif', 'jpg', 'jpeg', 'png', 'bmp')) ? 1 : 0;
				$uploadedfile['filepath'] = $filename.'.'.$filetype;
				$uploadedfile['filename'] = file_name($this->input->post('filepath'));
				$uploadedfile['filesize'] = $fileinfo['size'];
				$uploadedfile['fileext'] = $filetype;
				$aid = $this->att_db->api_add($uploadedfile);
				$this->upload_json($aid,SYS_UPLOAD_URL.$filename.'.'.$filetype,file_name($this->input->post('filepath')),format_file_size($fileinfo['size']));
				dr_json(1, L('operation_success'), array('filepath' => SYS_UPLOAD_URL.$filename.'.'.$filetype));
				//dr_json(1, L('operation_success'), array('filepath' => SYS_UPLOAD_URL.$new_file));
			}else{
				dr_json(0, L('operation_failure'));
			}
		}
		if ($this->input->get('picurl') && !empty($this->input->get('picurl'))) {
			$picurl = $this->input->get('picurl');
			$catid = intval($this->input->get('catid'));
			if ($this->input->get('module') && !empty($this->input->get('module'))) {
				$module = $this->input->get('module');
			}
			$show_header =  '';
			$filepath = $this->input->get('picurl') ? base64_decode($this->input->get('picurl')) : showmessage(L('lose_parameters'));
			if(strpos($filepath, '://')) $filepath = strstr($filepath, SYS_ATTACHMENT_PATH ? SYS_ATTACHMENT_PATH : 'uploadfile');
			if(!is_file(CMS_PATH.$filepath)) showmessage(L('请选择本地已存在的图像！'));
			$spec = $this->input->get('spec') ? intval($this->input->get('spec')) : 1; 
			$catid = intval($this->input->get('catid'));
			$input = $this->input->get('input') ? $this->input->get('input') : 'thumb';
			$preview = $this->input->get('preview') ? $this->input->get('preview') : 'thumb';
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
	}
	/**
	 * 设置upload上传的json格式cookie
	 */
	private function upload_json($aid,$src,$filename,$size) {
		$arr['aid'] = intval($aid);
		$arr['src'] = trim($src);
		$arr['filename'] = urlencode($filename);
		$arr['size'] = $size;
		$json_str = json_encode($arr);
		$att_arr_exist = getcache('att_json', 'commons');
		$att_arr_exist_tmp = explode('||', $att_arr_exist);
		if(is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
			return true;
		} else {
			$json_str = $att_arr_exist ? $att_arr_exist.'||'.$json_str : $json_str;
			setcache('att_json', $json_str, 'commons');
			return true;			
		}
	}
	/**
	 * 相关文章选择
	 */
	public function public_relationlist() {
		pc_base::load_sys_class('format','',0);
		$show_header = '';
		$model_cache = getcache('model','commons');
		if(!$this->input->get('modelid')) {
			showmessage(L('please_select_modelid'));
		} else {
			$page = intval($this->input->get('page'));
			
			$modelid = intval($this->input->get('modelid'));
			$this->db->set_model($modelid);
			$where = '';
			if($this->input->get('catid')) {
				$catid = intval($this->input->get('catid'));
				$where .= "catid='$catid'";
			}
			$where .= $where ?  ' AND status=99' : 'status=99';
			
			if($this->input->get('keywords')) {
				$keywords = trim($this->input->get('keywords'));
				$field = $this->input->get('field');
				if(in_array($field, array('id','title','keywords','description'))) {
					if($field=='id') {
						$where .= " AND `id` ='$keywords'";
					} else {
						$where .= " AND `$field` like '%$keywords%'";
					}
				}
			}
			$infos = $this->db->listinfo($where,'',$page,12);
			$pages = $this->db->pages;
			include $this->admin_tpl('relationlist');
		}
	}
	public function public_getjson_ids() {
		$modelid = intval($this->input->get('modelid'));
		$id = intval($this->input->get('id'));
		$this->db->set_model($modelid);
		$tablename = $this->db->table_name;
		$this->db->table_name = $tablename.'_data';
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
			echo json_encode($infos);
		}
	}

	//文章预览
	public function public_preview() {
		$catid = intval($this->input->get('catid'));
		$id = intval($this->input->get('id'));
		
		if(!$catid || !$id) showmessage(L('missing_part_parameters'),'blank');
		$page = intval($this->input->get('page'));
		$page = max($page,1);
		$CATEGORYS = getcache('category_content_'.$this->get_siteid(),'commons');
		
		if(!isset($CATEGORYS[$catid]) || $CATEGORYS[$catid]['type']!=0) showmessage(L('missing_part_parameters'),'blank');
		define('HTML', true);
		$CAT = $CATEGORYS[$catid];
		
		$siteid = $CAT['siteid'];
		$MODEL = getcache('model','commons');
		$modelid = $CAT['modelid'];

		$this->db->table_name = $this->db->db_tablepre.$MODEL[$modelid]['tablename'];
		$r = $this->db->get_one(array('id'=>$id));
		if(!$r) showmessage(L('information_does_not_exist'));
		$this->db->table_name = $this->db->table_name.'_data';
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
				$this->url = pc_base::load_app_class('url', 'content');
				$contents = array_filter(explode('[page]', $content));
				$pagenumber = count($contents);
				if (strpos($content, '[/page]')!==false && ($CONTENT_POS<7)) {
					$pagenumber--;
				}
				for($i=1; $i<=$pagenumber; $i++) {
					$pageurls[$i][0] = 'index.php?m=content&c=content&a=public_preview&steps='.intval($this->input->get('steps')).'&catid='.$catid.'&id='.$id.'&page='.$i;
				}
				$END_POS = strpos($content, '[/page]');
				if($END_POS !== false) {
					if($CONTENT_POS>7) {
						$content = '[page]'.$title.'[/page]'.$content;
					}
					if(preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
						foreach($m[1] as $k=>$v) {
							$p = $k+1;
							$titles[$p]['title'] = strip_tags($v);
							$titles[$p]['url'] = $pageurls[$p][0];
						}
					}
				}
				//当不存在 [/page]时，则使用下面分页
				$pages = content_pages($pagenumber,$page, $pageurls);
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
		include template('content',$template);
		$pc_hash = $_SESSION['pc_hash'];
		$steps = intval($this->input->get('steps'));
		if ($steps) {
			echo "
			<script language=\"javascript\" type=\"text/javascript\" src=\"".JS_PATH."jquery.min.js\"></script>
			<script language=\"javascript\" type=\"text/javascript\" src=\"".JS_PATH."Dialog/main.js\"></script>
			<script type=\"text/javascript\">var diag = new Dialog({id:'content_m',title:'".L('operations_manage')."',html:'<span id=cloading ><a href=\'javascript:ajax_manage(1)\'>".L('passed_checked')."</a> | <a href=\'javascript:ajax_manage(2)\'>".L('reject')."</a> |　<a href=\'javascript:ajax_manage(3)\'>".L('delete')."</a></span>',left:'100%',top:'100%',modal:false});diag.show();
			function ajax_manage(type) {
				if(type==1) {
					$.get('?m=content&c=content&a=pass&ajax_preview=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."');
				} else if(type==2) {
					$.get('?m=content&c=content&a=pass&ajax_preview=1&reject=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."');
				} else if(type==3) {
					$.get('?m=content&c=content&a=delete&ajax_preview=1&dosubmit=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."');
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
					$.get('?m=content&c=content&a=delete&ajax_preview=1&dosubmit=1&catid=".$catid."&steps=".$steps."&id=".$id."&pc_hash=".$pc_hash."');
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
	/**
	 * 过审内容
	 */
	public function ajax_pass() {
		$catid = intval($this->input->get('catid'));
		if(!$catid) dr_json(0, L('missing_part_parameters'));
		$category = $this->categorys[$catid];
		$modelid = $this->categorys[$catid]['modelid'];
		$this->db->set_model($modelid);
		$this->db->update(array('status'=>99),array('id'=>$this->input->get('id')));
		dr_json(1, L('operation_success'));
	}

	/**
	 * 审核所有内容
	 */
	public function public_checkall() {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		
		$show_header = '';
		$workflows = getcache('workflow_'.$this->siteid,'commons');
		$datas = array();
		$pagesize = 20;
		$sql = '';
		if (in_array($_SESSION['roleid'], array('1'))) {
			$super_admin = 1;
			$status = $this->input->get('status') ? $this->input->get('status') : -1;
		} else {
			$super_admin = 0;
			$status = $this->input->get('status') ? $this->input->get('status') : 1;
			if($status==-1) $status = 1;
		}
		if($status>4) $status = 4;
		$this->priv_db = pc_base::load_model('category_priv_model');;
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
				if (!$this->priv_db->get_one(array('catid'=>$catid, 'siteid'=>$this->siteid, 'roleid'=>$_SESSION['roleid'], 'is_admin'=>'1'))) {
					continue;
				}
				//如果栏目有设置工作流，进行权限检查。
				$workflow = array();
				$cat['setting'] = string2array($cat['setting']);
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
		$datas = $this->content_check_db->listinfo($sql,'inputtime DESC',$page);		
		$pages = $this->content_check_db->pages;
		include $this->admin_tpl('content_checkall');
	}
	
	/**
	 * 批量移动文章
	 */
	public function remove() {
		if($this->input->post('dosubmit')) {
			$this->content_check_db = pc_base::load_model('content_check_model');
			$this->hits_db = pc_base::load_model('hits_model');
			if($this->input->post('fromtype')==0) {
				if($this->input->post('ids')=='') showmessage(L('please_input_move_source'));
				if(!$this->input->post('tocatid')) showmessage(L('please_select_target_category'));
				$tocatid = intval($this->input->post('tocatid'));
				$modelid = $this->categorys[$tocatid]['modelid'];
				if(!$modelid) showmessage(L('illegal_operation'));
				$ids = array_filter(explode(',', $this->input->post('ids')),"is_numeric");
				foreach ($ids as $id) {
					$checkid = 'c-'.$id.'-'.$this->siteid;
					$this->content_check_db->update(array('catid'=>$tocatid), array('checkid'=>$checkid));
					$hitsid = 'c-'.$modelid.'-'.$id;
					$this->hits_db->update(array('catid'=>$tocatid),array('hitsid'=>$hitsid));
				}
				$ids = implode(',', $ids);
				$this->db->set_model($modelid);
				$this->db->update(array('catid'=>$tocatid),"id IN($ids)");
			} else {
				if(!$this->input->post('fromid')) showmessage(L('please_input_move_source'));
				if(!$this->input->post('tocatid')) showmessage(L('please_select_target_category'));
				$tocatid = intval($this->input->post('tocatid'));
				$modelid = $this->categorys[$tocatid]['modelid'];
				if(!$modelid) showmessage(L('illegal_operation'));
				$fromid = array_filter($this->input->post('fromid'),"is_numeric");
				$fromid = implode(',', $fromid);
				$this->db->set_model($modelid);
				$this->db->update(array('catid'=>$tocatid),"catid IN($fromid)");
				$this->hits_db->update(array('catid'=>$tocatid),"catid IN($fromid)");
			}
			showmessage(L('operation_success'), '', '', 'remove');
			//ids
		} else {
			$show_header = '';
			$catid = intval($this->input->get('catid'));
			$modelid = $this->categorys[$catid]['modelid'];
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;';
			$categorys = array();
			foreach($this->categorys as $cid=>$r) {
				if($this->siteid != $r['siteid'] || $r['type']) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$r['disabled'] = $r['child'] ? 'disabled' : '';
				$r['selected'] = $cid == $catid ? 'selected' : '';
				$categorys[$cid] = $r;
			}
			$str  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
 			$str  = "<option value='\$catid'>\$spacer \$catname</option>";
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
		$show_header = '';
		$sitelist = getcache('sitelist','commons');
		$siteid = $this->input->get('siteid');
		include $this->admin_tpl('add_othors');
		
	}
	/**
	 * 同时发布到其他栏目 异步加载栏目
	 */
	public function public_getsite_categorys() {
		$siteid = intval($this->input->get('siteid'));
		$this->categorys = getcache('category_content_'.$siteid,'commons');
		$models = getcache('model','commons');
		$tree = pc_base::load_sys_class('tree');
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys = array();
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
			$r['modelname'] = $models[$r['modelid']]['name'];
			$r['style'] = $r['child'] ? 'color:#8A8A8A;' : '';
			$r['click'] = $r['child'] ? '' : "onclick=\"select_list(this,'".safe_replace($r['catname'])."',".$r['catid'].")\" class='cu' title='".L('click_to_select')."'";
			$categorys[$r['catid']] = $r;
		}
		$str  = "<tr \$click >
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
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
		$tree = pc_base::load_sys_class('tree');
		if(!empty($this->categorys)) {
			foreach($this->categorys as $r) {
				if($r['siteid']!=$this->siteid ||  ($r['type']==2 && $r['child']==0)) continue;
				if($from=='content' && $_SESSION['roleid'] != 1 && !in_array($r['catid'],$priv_catids)) {
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
					$r['add_icon'] = "<a target='right' href='?m=content&c=content&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."' onclick=javascript:contentopen('?m=content&c=content&a=add&menuid=".intval($this->input->get('menuid'))."&catid=".$r['catid']."&hash_page=".$_SESSION['hash_page']."','".L('add_content')."')><img src='".IMG_PATH."add_content.gif' alt='".L('add')."'></a> ";
				}
				$categorys[$r['catid']] = $r;
			}
		}
		if(!empty($categorys)) {
			$tree->init($categorys);
				switch($from) {
					case 'block':
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=block&c=block_admin&a=public_visualization&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&type=list&pc_hash=".$_SESSION['pc_hash']."' target='right'>\$catname</a> \$vs_show</span>";
					break;

					default:
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=content&c=content&a=\$type&menuid=".intval($this->input->get('menuid'))."&catid=\$catid&pc_hash=".$_SESSION['pc_hash']."' target='right' onclick='open_list(this)'>\$catname</a></span>";
						break;
				}
			$data = $tree->creat_sub_json($catid,$strs);
		}		
		echo $data;
	}

	/**
	 * 一键清理演示数据
	 */
	public function clear_data() {
		//清理数据涉及到的数据表
		
		if ($this->input->post('dosubmit')) {
			set_time_limit(0);
			$models = array('category', 'content', 'hits', 'search', 'position_data', 'comment');
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
									$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$model_arr[$modelid]['tablename'].'.sql';
									$result = $data = $db->select();
									$this->create_sql_file($result, $db->db_tablepre.$model_arr[$modelid]['tablename'], $sql_file);
									$db->query('TRUNCATE TABLE `cms_'.$model_arr[$modelid]['tablename'].'`');
									//开始清理模型data表数据
									$db->table_name = $db->table_name.'_data';
									$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$model_arr[$modelid]['tablename'].'_data.sql';
									$result = $db->select();
									$this->create_sql_file($result, $db->db_tablepre.$model_arr[$modelid]['tablename'].'_data', $sql_file);
									$db->query('TRUNCATE TABLE `cms_'.$model_arr[$modelid]['tablename'].'_data`');
									//删除该模型中在hits表的数据
									$hits_db = pc_base::load_model('hits_model');
									$hitsid = 'c-'.$modelid.'-';
									$result = $hits_db->select("`hitsid` LIKE '%$hitsid%'");
									if (is_array($result)) {
										$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'hits-'.$modelid.'.sql';
										$this->create_sql_file($result, $hits_db->db_tablepre.'hits', $sql_file);
									}
									$hits_db->delete("`hitsid` LIKE '%$hitsid%'");
									//删除该模型在search中的数据
									$search_db = pc_base::load_model('search_model');
									$type_model = getcache('type_model_'.$model_arr[$modelid]['siteid'], 'search');
									$typeid = $type_model[$modelid];
									$result = $search_db->select("`typeid`=".$typeid);
									if (is_array($result)) {
										$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'search-'.$modelid.'.sql';
										$this->create_sql_file($result, $search_db->db_tablepre.'search', $sql_file);
									}
									$search_db->delete("`typeid`=".$typeid);
									//Delete the model data in the position table
									$position_db = pc_base::load_model('position_data_model');
									$result = $position_db->select('`modelid`='.$modelid.' AND `module`=\'content\'');
									if (is_array($result)) {
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
										$sql_file = CACHE_PATH.'bakup'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'comment_data_'.$i.'.sql';
										$result = $comment_db->select();
										$this->create_sql_file($result, $comment_db->db_tablepre.'comment_data_'.$i, $sql_file);
										$comment_db->query('TRUNCATE TABLE `cms_comment_data_'.$i.'`');
									}
								} else {
									break;
								}
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
			showmessage(L('clear_data_message'));
		} else {
			//读取网站的所有模型
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
				$sql .= "INSERT INTO `".$db.'` VALUES(';
				foreach ($d as $_f => $_v) {
					$sql .= $tag.'\''.addslashes($_v).'\'';
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