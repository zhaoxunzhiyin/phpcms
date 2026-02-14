<?php
/**
 * 会员前台投稿操作类
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('foreground');
pc_base::load_sys_class('format');
pc_base::load_sys_class('form');

class content extends foreground {
	private $input,$cache_api,$priv_db,$content_check_db,$content_db,$categorys,$model,$sitemodel_db,$siteinfo,$menu,$grouplist,$member_model;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->priv_db = pc_base::load_model('category_priv_model'); //加载栏目权限表数据模型
		$this->siteinfo = siteinfo($this->memberinfo['siteid']);
		$this->menu_db = pc_base::load_model('member_menu_model');
		$this->menu = $this->menu_db->select(array('display'=>1, 'parentid'=>0), '*', 20, 'listorder');
		$this->grouplist = getcache('grouplist');
		$this->member_model = getcache('member_model', 'commons');
		$this->memberinfo['groupname'] = $this->grouplist[$this->memberinfo['groupid']]['name'];
		pc_base::load_sys_class('service')->assign([
			'memberinfo' => $this->memberinfo,
			'grouplist' => $this->grouplist,
			'member_model' => $this->member_model,
			'siteinfo' => $this->siteinfo,
			'menu' => $this->menu,
		]);
	}
	public function publish() {
		//判断会员组是否允许投稿
		if(!$this->grouplist[$this->memberinfo['groupid']]['allowpost']) {
			showmessage(L('member_group').L('publish_deny'), HTTP_REFERER);
		}
		//判断每日投稿数
		$this->content_check_db = pc_base::load_model('content_check_model');
		$todaytime = strtotime(date('y-m-d',SYS_TIME));
		$_username = $this->memberinfo['username'];
		$allowpostnum = $this->content_check_db->count("`inputtime` > $todaytime AND `username`='$_username'");
		if($this->grouplist[$this->memberinfo['groupid']]['allowpostnum'] > 0 && $allowpostnum >= $this->grouplist[$this->memberinfo['groupid']]['allowpostnum']) {
			showmessage(L('allowpostnum_deny').$this->grouplist[$this->memberinfo['groupid']]['allowpostnum'], HTTP_REFERER);
		}
		$siteids = getcache('category_content', 'commons');
		header("Cache-control: private");
		if(IS_POST) {
			$info = $this->input->post('info');
			$catid = intval($info['catid']);
			//判断此类型用户是否有权限在此栏目下提交投稿
			if (!$this->priv_db->get_one(array('catid'=>$catid, 'roleid'=>$this->memberinfo['groupid'], 'is_admin'=>0, 'action'=>'add'))) showmessage(L('category').L('publish_deny'), APP_PATH.'index.php?m=member');
			
			$siteid = $siteids[$catid];
			$CATEGORYS = get_category($siteid);
			$category = dr_cat_value($catid);
			$modelid = $category['modelid'];
			if(!$modelid) showmessage(L('illegal_parameters'), HTTP_REFERER);
			$this->content_db = pc_base::load_model('content_model');
			$this->content_db->set_model($modelid);
			$table_name = $this->content_db->table_name;
			$fields_sys = $this->content_db->get_fields();
			$this->content_db->table_name = $table_name.'_data_0';
			
			$fields_attr = $this->content_db->get_fields();
			$fields = array_merge($fields_sys,$fields_attr);
			$fields = array_keys($fields);
			$post_fields = array_keys($info);
			$post_fields = array_intersect_assoc($fields,$post_fields);
			$setting = string2array($category['setting']);
			if (!$setting['presentpoint']) $setting['presentpoint'] = 0;
			if($setting['presentpoint'] < 0 && $this->memberinfo['point'] < abs($setting['presentpoint']))
			showmessage(L('points_less_than',array('point'=>$this->memberinfo['point'],'need_point'=>abs($setting['presentpoint']))),APP_PATH.'index.php?m=pay&c=deposit&a=pay&exchange=point',3000);
			
			//判断会员组投稿是否需要审核
			if($this->grouplist[$this->memberinfo['groupid']]['allowpostverify'] || !$setting['workflowid']) {
				$info['status'] = 99;
			} else {
				$info['status'] = 1;
			}
			$info['username'] = $this->memberinfo['username'];
			if(isset($info['title'])) $info['title'] = safe_replace($info['title']);
			$this->content_db->siteid = $siteid;
			
			$id = $this->content_db->add_content($info);
			$this->cache_api->cache('sitemodels');
			//检查投稿奖励或扣除积分
			if ($info['status']==99) {
				$flag = $catid.'_'.$id;
				if($setting['presentpoint']>0) {
					pc_base::load_app_class('receipts','pay',0);
					receipts::point($setting['presentpoint'],$this->memberinfo['userid'], $this->memberinfo['username'], $flag,'selfincome',L('contribute_add_point'),$this->memberinfo['username']);
				} else {
					pc_base::load_app_class('spend','pay',0);
					spend::point($setting['presentpoint'], L('contribute_del_point'), $this->memberinfo['userid'], $this->memberinfo['username'], '', '', $flag);
				}
			}
			//缓存结果
			$model_cache = getcache('model','commons');
			$infos = array();
			foreach ($model_cache as $modelid=>$model) {
				if($model['siteid']==$siteid) {
					$datas = array();
					$this->content_db->set_model($modelid);
					$datas = $this->content_db->select(array('username'=>$this->memberinfo['username'],'sysadd'=>0),'id,catid,title,url,username,sysadd,inputtime,status',100,'id DESC');
					if($datas) $infos = array_merge($infos,$datas);
				}
			}
			setcache('member_'.$this->memberinfo['userid'].'_'.$siteid, $infos,'content');
			//缓存结果 END
			if($info['status']==99) {
				showmessage(L('contributors_success'), APP_PATH.'index.php?m=member&c=content&a=published');
			} else {
				showmessage(L('contributors_checked'), APP_PATH.'index.php?m=member&c=content&a=published');
			}
		} else {
			$sitelist = getcache('sitelist','commons');
			if(!$this->input->get('siteid') && dr_count($sitelist)>1) {
				pc_base::load_sys_class('service')->assign('show_header', '');
				pc_base::load_sys_class('service')->assign('show_dialog', '');
				pc_base::load_sys_class('service')->assign('show_validator', '');
				pc_base::load_sys_class('service')->assign([
					'temp_language' => L('news','','content'),
					'siteid' => $siteid,
					'sitelist' => $sitelist,
				]);
				pc_base::load_sys_class('service')->display('member', 'content_publish_select_model');
				exit;
			}
			//设置cookie 在附件添加处调用
			param::set_cookie('module', 'content');
			$siteid = intval($this->input->get('siteid'));
			if(!$siteid) $siteid = 1;
			$CATEGORYS = get_category($siteid);
			foreach ($CATEGORYS as $catid=>$cat) {
				if($cat['siteid']==$siteid && $cat['child']==0 && $cat['type']==0 && $this->priv_db->get_one(array('catid'=>$catid, 'roleid'=>$this->memberinfo['groupid'], 'is_admin'=>0, 'action'=>'add'))) break;
			}
			$catid = $this->input->get('catid') ? intval($this->input->get('catid')) : $catid;
			if (!$catid) showmessage(L('category').L('publish_deny'), APP_PATH.'index.php?m=member');

			//判断本栏目是否允许投稿
			if (!$this->priv_db->get_one(array('catid'=>$catid, 'roleid'=>$this->memberinfo['groupid'], 'is_admin'=>0, 'action'=>'add'))) showmessage(L('category').L('publish_deny'), APP_PATH.'index.php?m=member');
			$category = dr_cat_value($catid);
			if($category['siteid']!=$siteid) showmessage(L('site_no_category'),'?m=member&c=content&a=publish');
			$setting = string2array($category['setting']);
			if (!$setting['presentpoint']) $setting['presentpoint'] = 0;
			if($setting['presentpoint'] < 0 && $this->memberinfo['point'] < abs($setting['presentpoint']))
			showmessage(L('points_less_than',array('point'=>$this->memberinfo['point'],'need_point'=>abs($setting['presentpoint']))),APP_PATH.'index.php?m=pay&c=deposit&a=pay&exchange=point',3000);
			if($category['type']!=0) showmessage(L('illegal_operation'));
			$modelid = $category['modelid'];
			$model_arr = getcache('model', 'commons');
			$MODEL = $model_arr[$modelid];
			unset($model_arr);
	
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form($modelid, $catid, $CATEGORYS);
			$forminfos_data = $content_form->get();
			$forminfos = array();
			foreach($forminfos_data as $_fk=>$_fv) {
				if($_fv['isomnipotent']) continue;
				if($_fv['formtype']=='omnipotent') {
					foreach($forminfos_data as $_fm=>$_fm_value) {
						if($_fm_value['isomnipotent']) {
							$_fv['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$_fv['form']);
						}
					}
				}
				$forminfos[$_fk] = $_fv;
			}
			$formValidator = $content_form->formValidator;
			//去掉栏目id
			unset($forminfos['catid']);
			$workflowid = $setting['workflowid'];
			header("Cache-control: private");
			$template = $MODEL['member_add_template'] ? $MODEL['member_add_template'] : 'content_publish';
			pc_base::load_sys_class('service')->assign('show_header', '');
			pc_base::load_sys_class('service')->assign('show_dialog', '');
			pc_base::load_sys_class('service')->assign('show_validator', '');
			pc_base::load_sys_class('service')->assign([
				'temp_language' => L('news','','content'),
				'siteid' => $siteid,
				'sitelist' => $sitelist,
				'catid' => $catid,
				'forminfos' => $forminfos,
				'formValidator' => $formValidator,
			]);
			pc_base::load_sys_class('service')->display('member', $template);
		}
	}
	
	public function published() {
		$sitelist = getcache('sitelist','commons');
		$_username = $this->memberinfo['username'];
		$_userid = $this->memberinfo['userid'];
		$siteid = intval($this->input->get('siteid'));
		if(!$this->input->get('siteid') && dr_count($sitelist)>1) {
			pc_base::load_sys_class('service')->assign([
				'siteid' => $siteid,
				'sitelist' => $sitelist,
				'_username' => $_username,
				'_userid' => $_userid,
			]);
			pc_base::load_sys_class('service')->display('member', 'content_publish_select_model');
			exit;
		}
		if(!$siteid) $siteid = 1;
		$CATEGORYS = get_category($siteid);
		$siteurl = siteurl($siteid);
		$page = max(intval($this->input->get('page')),1);
		$workflows = getcache('workflow_'.$siteid,'commons');	
		$this->content_check_db = pc_base::load_model('content_check_model');
		$infos = $this->content_check_db->listinfo(array('username'=>$_username, 'siteid'=>$siteid),'inputtime DESC',$page);
		$datas = array();
		foreach($infos as $_v) {
			$arr_checkid = explode('-',$_v['checkid']);
			$_v['id'] = $arr_checkid[1];
			$_v['modelid'] = $arr_checkid[2];
			$_v['url'] = $_v['status']==99 ? dr_go($_v['catid'],$_v['id']) : APP_PATH.'index.php?m=content&c=index&a=show&catid='.$_v['catid'].'&id='.$_v['id'];
			if(!isset($setting[$_v['catid']])) $setting[$_v['catid']] = dr_string2array(dr_cat_value($_v['catid'], 'setting'));
			$workflowid = $setting[$_v['catid']]['workflowid'];
			$_v['flag'] = $workflows[$workflowid]['flag'];
			$datas[] = $_v;
		}
		pc_base::load_sys_class('service')->assign([
			'siteid' => $siteid,
			'sitelist' => $sitelist,
			'_username' => $_username,
			'_userid' => $_userid,
			'CATEGORYS' => $CATEGORYS,
			'datas' => $datas,
			'pages' => $this->content_check_db->pages,
		]);
		pc_base::load_sys_class('service')->display('member', 'content_published');	
	}
	/**
	 * 编辑内容
	 */
	public function edit() {
		$_username = $this->memberinfo['username'];
		if(IS_POST) {
			$id = intval($this->input->post('id'));
			$info = $this->input->post('info');
			$catid = intval($info['catid']);
			$siteids = getcache('category_content', 'commons');
			$siteid = $siteids[$catid];
			$CATEGORYS = get_category($siteid);
			$category = dr_cat_value($catid);
			//判断此类型用户是否有权限在此栏目下提交投稿
			if (!$this->priv_db->get_one(array('catid'=>$catid, 'roleid'=>$this->memberinfo['groupid'], 'is_admin'=>0, 'action'=>'edit'))) showmessage(L('当前栏目['.$category['catname'].']没有修改权限'), APP_PATH.'index.php?m=member&c=content&a=published');
			if($category['type']==0) {
				$this->content_db = pc_base::load_model('content_model');
				$modelid = $category['modelid'];
				$this->content_db->set_model($modelid);
				//判断会员组投稿是否需要审核
				$setting = string2array($category['setting']);
				if($this->grouplist[$this->memberinfo['groupid']]['allowpostverify'] || !$setting['workflowid']) {
					$info['status'] = 99;
				} else {
					$info['status'] = 1;
				}
				$this->content_db->edit_content($info,$id);
				$forward = $this->input->post('forward');
				showmessage(L('update_success'),$forward);
			}
		} else {
			$temp_language = L('news','','content');
			//设置cookie 在附件添加处调用
			param::set_cookie('module', 'content');
			$id = intval($this->input->get('id'));
			if($this->input->get('catid')) {
				$catid = intval($this->input->get('catid'));
				param::set_cookie('catid', $catid);
				$siteids = getcache('category_content', 'commons');
				$siteid = $siteids[$catid];
				$CATEGORYS = get_category($siteid);
				$category = dr_cat_value($catid);
				//判断此类型用户是否有权限在此栏目下提交投稿
				if (!$this->priv_db->get_one(array('catid'=>$catid, 'roleid'=>$this->memberinfo['groupid'], 'is_admin'=>0, 'action'=>'edit'))) showmessage(L('当前栏目['.$category['catname'].']没有修改权限'), APP_PATH.'index.php?m=member&c=content&a=published');
				if($category['type']==0) {
					$modelid = $category['modelid'];
					$this->model = getcache('model', 'commons');
					$this->content_db = pc_base::load_model('content_model');
					$this->content_db->set_model($modelid);
		
					$this->content_db->table_name = $this->content_db->db_tablepre.$this->model[$modelid]['tablename'];
					$r = $this->content_db->get_one(array('id'=>$id,'username'=>$_username,'sysadd'=>0));
		
					if(!$r) showmessage(L('illegal_operation'));
					$this->content_db->table_name = $this->content_db->table_name.'_data_'.$r['tableid'];
					$r2 = $this->content_db->get_one(array('id'=>$id));
					$data = array_merge($r,$r2);
					require CACHE_MODEL_PATH.'content_form.class.php';
					$content_form = new content_form($modelid,$catid,$CATEGORYS);
				
					$forminfos_data = $content_form->get($data);
					$forminfos = array();
					foreach($forminfos_data as $_fk=>$_fv) {
						if($_fv['isomnipotent']) continue;
						if($_fv['formtype']=='omnipotent') {
							foreach($forminfos_data as $_fm=>$_fm_value) {
								if($_fm_value['isomnipotent']) {
									$_fv['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$_fv['form']);
								}
							}
						}
						$forminfos[$_fk] = $_fv;
					}
					$formValidator = $content_form->formValidator;

					pc_base::load_sys_class('service')->assign('show_header', '');
					pc_base::load_sys_class('service')->assign('show_dialog', '');
					pc_base::load_sys_class('service')->assign('show_validator', '');
					pc_base::load_sys_class('service')->assign([
						'temp_language' => L('news','','content'),
						'siteid' => $siteid,
						'sitelist' => $sitelist,
						'catid' => $catid,
						'id' => $id,
						'forminfos' => $forminfos,
						'formValidator' => $formValidator,
					]);
					pc_base::load_sys_class('service')->display('member', 'content_publish');
				}
			}
			header("Cache-control: private");
			
		}
	}
	
	/**
	 * 
	 * 会员删除投稿 ...
	 */
	public function delete(){
		$id = intval($this->input->get('id'));
		if(!$id){
			return false;
		}
		//判断该文章是否待审，并且属于该会员
		$username = param::get_cookie('_username');
		$userid = param::get_cookie('_userid');
		$siteid = get_siteid();
		$catid = intval($this->input->get('catid'));
		$siteids = getcache('category_content', 'commons');
		$siteid = $siteids[$catid];
		$CATEGORYS = get_category($siteid);
		$category = dr_cat_value($catid);
		//判断此类型用户是否有权限在此栏目下提交投稿
		if (!$this->priv_db->get_one(array('catid'=>$catid, 'roleid'=>$this->memberinfo['groupid'], 'is_admin'=>0, 'action'=>'delete'))) showmessage(L('当前栏目['.$category['catname'].']没有删除权限'), APP_PATH.'index.php?m=member&c=content&a=published');
		if(!$category){
			showmessage(L('operation_failure'), HTTP_REFERER); 
		}
		$modelid = $category['modelid'];
		$checkid = 'c-'.$id.'-'.$modelid;
		$where = " checkid='$checkid' and username='$username' ";
		$check_pushed_db = pc_base::load_model('content_check_model');
		$array = $check_pushed_db->get_one($where);
		if(!$array){
			showmessage(L('operation_failure'), HTTP_REFERER); 
		}else{
			$content_db = pc_base::load_model('content_model');
			$content_db->set_model($modelid);
			$r = $content_db->get_one(array('id'=>$id));
			$content_db->delete(array('id'=>$id));
			$content_db->table_name = $content_db->table_name.'_data_'.$r['tableid'];
			$content_db->delete(array('id'=>$id));
			$content_db->set_model($modelid);
			$total = $content_db->count();
			$this->sitemodel_db = pc_base::load_model('sitemodel_model');
			$this->sitemodel_db->update(array('items'=>$total),array('modelid'=>$modelid));
			$check_pushed_db->delete(array('checkid'=>$checkid));//删除对应投稿表
			showmessage(L('operation_success'), HTTP_REFERER); 
		}
	}
	function info_top() {
		$exist_posids = array();
		$_username = $this->memberinfo['username'];
		$id = intval($this->input->get('id'));
		
		$catid = $this->input->get('catid');
		$pos_data = pc_base::load_model('position_data_model');
		
		if(!$id || !$catid) showmessage(L('illegal_parameters'), HTTP_REFERER);	
		if(isset($catid) && $catid) {
			$siteids = getcache('category_content', 'commons');
			$siteid = $siteids[$catid];
			$CATEGORYS = get_category($siteid);
			$category = dr_cat_value($catid);	
			if($category['type']==0) {
				$modelid = $category['modelid'];
				$this->model = getcache('model', 'commons');
				$this->content_db = pc_base::load_model('content_model');
				$this->content_db->set_model($modelid);				
				$this->content_db->table_name = $this->content_db->db_tablepre.$this->model[$modelid]['tablename'];
				$r = $this->content_db->get_one(array('id'=>$id,'username'=>$_username,'sysadd'=>0));
				if(!$r) showmessage(L('illegal_operation'));

				//再次重新赋值，以数据库为准
				$catid = $CATEGORYS[$r['catid']]['catid'];
				$modelid = dr_cat_value($catid)['modelid'];
				
				require_once CACHE_MODEL_PATH.'content_output.class.php';
				$content_output = new content_output($modelid,$catid,$CATEGORYS);
				$data = $content_output->get($r);
				extract($data);								
			}
		}
		//置顶推荐位数组
			$infos = getcache('info_setting','commons'); 
		$toptype_posid = array('1'=>$infos['top_city_posid'],
			'2'=>$infos['top_zone_posid'],
			'3'=>$infos['top_district_posid'],
		);
		foreach($toptype_posid as $_k => $_v) {
			if($pos_data->get_one(array('id'=>$id,'catid'=>$catid,'posid'=>$_v))) {
				$exist_posids[$_k] = 1;
			}			
		}
		pc_base::load_sys_class('service')->display('member', 'info_top');
	}
	function info_top_cost() {
		$amount = $msg = '';
		$_username = $this->memberinfo['username'];	
		$_userid = $this->memberinfo['userid'];	
		$infos = getcache('info_setting','commons');
		$toptype_arr = array(1,2,3);
		//置顶积分数组
		$toptype_price = array('1'=>$infos['top_city'],
			'2'=>$infos['top_zone'],
			'3'=>$infos['top_district'],
		);
		//置顶推荐位数组
		$toptype_posid = array('1'=>$infos['top_city_posid'],
			'2'=>$infos['top_zone_posid'],
			'3'=>$infos['top_district_posid'],
		);
		if(IS_POST) {
			$posids = array();
			$push_api = pc_base::load_app_class('push_api','admin');
			$pos_data = pc_base::load_model('position_data_model');
			$catid = intval($this->input->post('catid'));
			$id = intval($this->input->post('id'));
			$flag = $catid.'_'.$id;			
			$toptime = intval($this->input->post('toptime'));
			if($toptime == 0 || empty($this->input->post('toptype'))) showmessage(L('info_top_not_setting_toptime'));
			//计算置顶扣费积分，时间
			if(is_array($this->input->post('toptype')) && !empty($this->input->post('toptype'))) {
				foreach($this->input->post('toptype') as $r) {
					if(is_numeric($r) && in_array($r, $toptype_arr)) {
						$posids[] = $toptype_posid[$r];
						$amount += $toptype_price[$r];
						$msg .= $r.'-';
					}				
				}
			}
			//应付总积分
			$amount = $amount * $toptime;
				
			//扣除置顶点数		
			pc_base::load_app_class('spend','pay',0);
			$pay_status = spend::point($amount, L('info_top').$msg, $_userid, $_username, '', '', $flag);
			if($pay_status == false) {
				$msg = spend::get_msg();
				showmessage($msg);
			}
			//置顶过期时间
			//TODO
			$expiration = SYS_TIME + $toptime * 3600;

			//获取置顶文章信息内容
			if(isset($catid) && $catid) {
				$siteids = getcache('category_content', 'commons');
				$siteid = $siteids[$catid];
				$CATEGORYS = get_category($siteid);
				$category = dr_cat_value($catid);	
				if($category['type']==0) {
					$modelid = $category['modelid'];
					$this->model = getcache('model', 'commons');
					$this->content_db = pc_base::load_model('content_model');
					$this->content_db->set_model($modelid);				
					$this->content_db->table_name = $this->content_db->db_tablepre.$this->model[$modelid]['tablename'];
					$r = $this->content_db->get_one(array('id'=>$id,'username'=>$_username,'sysadd'=>0));							
				}
			}
			if(!$r) showmessage(L('illegal_operation'));	
			
			$push_api->position_update($id, $modelid, $catid, $posids, $r, $expiration, 1);	
			$refer = $this->input->post('msg') ? $r['url'] : '';
			if($this->input->post('msg')) showmessage(L('ding_success'),$refer);
			else showmessage(L('ding_success'), '', '', 'top');
			
		} else {	
				
			$toptype = trim($this->input->post('toptype'));
			$toptime = trim($this->input->post('toptime'));
			$types = explode('_', $toptype);
			if(is_array($types) && !empty($types)) {
				foreach($types as $r) {
					if(is_numeric($r) && in_array($r, $toptype_arr)) {
						$amount += $toptype_price[$r];
					}				
				}
			}
			$amount = $amount * $toptime;
			echo $amount;
		}
	}
	/**
	 * 检查标题是否存在
	 */
	public function public_check_title() {
		$this->content_db = pc_base::load_model('content_model');
		$siteids = getcache('category_content', 'commons');
		if(!$this->input->get('data') || !$this->input->get('catid')) return '';
		$is_ajax = intval($this->input->get('is_ajax'));
		$catid = intval($this->input->get('catid'));
		$id = intval($this->input->get('id'));
		$siteid = $siteids[$catid];
		$this->categorys = get_category($siteid);
		$modelid = $this->categorys[$catid]['modelid'];
		$this->content_db->set_model($modelid);
		$title = $this->input->get('data');
		if(CHARSET=='gbk') $title = iconv('utf-8','gbk',$title);
		$r = $this->content_db->get_one(array('title'=>$title, 'id<>'=>$id));
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
}
?>