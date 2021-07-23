<?php
defined('IN_CMS') or exit('No permission resources.');
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
class category extends admin {
	private $db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('category_model');
		$this->siteid = $this->get_siteid();
	}
	/**
	 * 管理栏目
	 */
	public function init () {
		$show_header = $show_dialog = $show_pc_hash = '';
		if (IS_POST) {
			$tree = pc_base::load_sys_class('tree');
			$models = getcache('model','commons');
			$sitelist = getcache('sitelist','commons');
			$category_items = array();
			foreach ($models as $modelid=>$model) {
				$category_items[$modelid] = getcache('category_items_'.$modelid,'commons');
			}
			$array = array();
			//读取缓存
			$result = getcache('category_content_'.$this->siteid,'commons');
			$html_root = pc_base::load_config('system','html_root');
			if(!empty($result)) {
				foreach($result as $r) {
					$rs['id'] = $r['catid'];
					$rs['title'] = $r['catname'];
					$rs['pid'] = $r['parentid'];
					$rs['modelname'] = $models[$r['modelid']]['name'];
					$rs['type'] = $r['type'];
					if ($r['type'] == 0) {
						if ($r['child']) {
							$rs['typename'] = '<span class="badge badge-danger"> '.L('封面').' </span>';
						} else {
							$rs['typename'] = '<span class="badge badge-success"> '.L('列表').' </span>';
						}
					} elseif ($r['type'] == 2) {
						$rs['typename'] = '<span class="badge badge-warning"> '.L('外链').' </span>';
					} else {
						$rs['typename'] = '<span class="badge badge-info"> '.L('单页').' </span>';
					}
					$rs['display_icon'] = $r['ismenu'] ? '' : ' <img src ="'.IMG_PATH.'icon/gear_disable.png" onmouseover="layer.tips(\''.L('not_display_in_menu').'\',this,{tips: [1, \'#000\']});" onmouseout="layer.closeAll();">';
					if($r['type'] || $r['child']) {
						$rs['items'] = '-';
					} else {
						$rs['items'] = $category_items[$r['modelid']][$r['catid']];
					}
					$setting = string2array($r['setting']);
					if($r['url']) {
						if(preg_match('/^(http|https):\/\//', $r['url'])) {
							$catdir = $r['catdir'];
							$prefix = $r['sethtml'] ? '' : $html_root;
							if($this->siteid==1) {
								$catdir = $prefix.'/'.$r['parentdir'].$catdir;
							} else {
								$catdir = $prefix.'/'.$sitelist[$this->siteid]['dirname'].$html_root.'/'.$catdir;
							}
							if($r['type']==0 && $setting['ishtml'] && strpos($r['url'], '?')===false && substr_count($r['url'],'/')<4) $rs['help'] = '<img src="'.IMG_PATH.'icon/help.png" onmouseover="layer.tips(\''.L('tips_domain').$r['url'].'<br>'.L('directory_binding').'<br>'.$catdir.'/\',this,{tips: [1, \'#000\']});" onmouseout="layer.closeAll();">';
							$rs['url'] = $r['url'];
						} else {
							$rs['url'] = substr($sitelist[$this->siteid]['domain'],0,-1).$r['url'];
						}
					} else {
						$rs['url'] = $r['url'];
					}
					$rs['ismenu'] = $r['ismenu'];
					$rs['disabled'] = $setting['disabled'];
					$rs['iscatpos'] = $setting['iscatpos'];
					$rs['isleft'] = $setting['isleft'];
					$rs['listorder'] = $r['listorder'];
					$rs['manage'] = '<a href="javascript:addedit(\'?m=admin&c=category&a=add&parentid='.$r['catid'].'&menuid='.$this->input->get('menuid').'&s='.$r['type'].'&pc_hash='.$this->input->get('pc_hash').'\', \''.L('add_sub_category').'\')" class="layui-btn layui-btn-xs"><i class="fa fa-plus"></i> '.L('add_sub_category').'</a><a href="javascript:addedit(\'?m=admin&c=category&a=edit&catid='.$r['catid'].'&menuid='.$this->input->get('menuid').'&type='.$r['type'].'&pc_hash='.$this->input->get('pc_hash').'\', \''.L('edit').'\')" class="layui-btn layui-btn-xs"><i class="fa fa-edit"></i> '.L('edit').'</a><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="fa fa-trash-o"></i> '.L('delete').'</a><a href="?m=admin&c=category&a=remove&catid='.$r['catid'].'&menuid='.$this->input->get('menuid').'&pc_hash='.$this->input->get('pc_hash').'" class="layui-btn layui-btn-danger layui-btn-xs"><i class="fa fa-arrows"></i> '.L('remove','','content').'</a>';
					$array[] = $rs;
				}
			}
			exit(json_encode(array('code'=>0,'msg'=>L('to_success'),'data'=>$array,'rel'=>1)));
		}
		include $this->admin_tpl('category_manage');
	}
	/**
	 * 添加栏目
	 */
	public function add() {
		if($this->input->post('dosubmit')) {
			pc_base::load_sys_func('iconv');
			$info = $this->input->post('info');
			$info['type'] = intval($this->input->post('type'));
			
			if($this->input->post('batch_add') && empty($this->input->post('batch_add'))) {
				if($info['catname']=='') showmessage(L('input_catname'));
				$info['catname'] = safe_replace($info['catname']);
				$info['catname'] = str_replace(array('%'),'',$info['catname']);
				if($info['type']!=2) {
					if($info['catdir']=='') showmessage(L('input_dirname'));
					if(!$this->public_check_catdir(0,$info['catdir'])) showmessage(L('catname_have_exists'));
				}
			}
			
			$info['siteid'] = $this->siteid;
			$info['module'] = 'content';
			$setting = $this->input->post('setting');
			if($info['type']!=2) {
				//栏目生成静态配置
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $this->input->post('category_html_ruleid');
				} else {
					$setting['category_ruleid'] = $this->input->post('category_php_ruleid');
					$info['url'] = '';
				}
			}
			
			//内容生成静态配置
			if($setting['content_ishtml']) {
				$setting['show_ruleid'] = $this->input->post('show_html_ruleid');
			} else {
				$setting['show_ruleid'] = $this->input->post('show_php_ruleid');
			}
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$info['sethtml'] = $setting['create_to_html_root'];
			$info['setting'] = array2string($setting);
			
			require_once CACHE_MODEL_PATH.'content_input.class.php';
			require_once CACHE_MODEL_PATH.'content_update.class.php';
			$content_input = new content_input(-1);
			$inputinfo = $content_input->get($info);
			$systeminfo = $inputinfo['system'];
			
			$end_str = $old_end =  '<script type="text/javascript">Dialog.warn("'.L("operation_success").L("edit_following_operation").'",function(){window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location = "?m=admin&c=category&a=public_cache&menuid=43&module=admin";});</script>';
			if(!$this->input->post('batch_add') || empty($this->input->post('batch_add'))) {
				$catname = CHARSET == 'gbk' ? $info['catname'] : iconv('utf-8','gbk',$info['catname']);
				$letters = gbk_to_pinyin($catname);
				$info['letter'] = strtolower(implode('', $letters));
				$catid = $this->db->insert($info, true);
				$this->db->update($systeminfo,array('catid'=>$catid,'siteid'=>$this->siteid));
				$this->update_priv($catid, $this->input->post('priv_roleid'));
				$this->update_priv($catid, $this->input->post('priv_groupid'),0);
			} else {//批量添加
				$end_str = '';
				$batch_adds = explode("\n", $this->input->post('batch_add'));
				foreach ($batch_adds as $_v) {
					if(trim($_v)=='') continue;
					$names = explode('|', $_v);
					$catname = $names[0];
					$info['catname'] = trim($names[0]);
					$letters = gbk_to_pinyin($catname);
					$info['letter'] = strtolower(implode('', $letters));
					$info['catdir'] = trim($names[1]) ? trim($names[1]) : trim($info['letter']);
					if(!$this->public_check_catdir(0,$info['catdir'])) {
						$end_str .= $end_str ? ','.$info['catname'].'('.$info['catdir'].')' : $info['catname'].'('.$info['catdir'].')';
						continue;
					}
					$catid = $this->db->insert($info, true);
					$this->db->update($systeminfo,array('catid'=>$catid,'siteid'=>$this->siteid));
					$this->update_priv($catid, $this->input->post('priv_roleid'));
					$this->update_priv($catid, $this->input->post('priv_groupid'),0);
				}
				$end_str = $end_str ? L('follow_catname_have_exists').$end_str : $old_end;
			}
			$this->cache();
			showmessage(L('add_success').$end_str);
		} else {
			$show_header = $show_dialog = '';
			//获取站点模板信息
			pc_base::load_app_func('global');

			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$show_validator = '';
			if($this->input->get('parentid')) {
				$parentid = $this->input->get('parentid');
				$r = $this->db->get_one(array('catid'=>$parentid));
				if($r) extract($r,EXTR_SKIP);
				$setting = string2array($setting);
			}
			
			pc_base::load_sys_class('form','',0);
			$type = $this->input->get('s');
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form(-1);
			$forminfos = $content_form->get();
 			$formValidator = $content_form->formValidator;
 			$checkall = $content_form->checkall;
			if($type==0) {
				$exists_model = false;
				$models = getcache('model','commons');	
				foreach($models as $_m) {
					if($this->siteid == $_m['siteid']) {
						$exists_model = true;
						break;
					}
				}
				if(!$exists_model) showmessage(L('please_add_model'),'?m=content&c=sitemodel&a=init&menuid=59',5000);
				include $this->admin_tpl('category_add');
			} elseif ($type==1) {
				include $this->admin_tpl('category_page_add');
			} else {
				include $this->admin_tpl('category_link_add');
			}
		}
	}
	/**
	 * 修改栏目
	 */
	public function edit() {
		if($this->input->post('dosubmit')) {
			pc_base::load_sys_func('iconv');
			$catid = intval($this->input->post('catid'));
			$setting = $this->input->post('setting');
			$info = $this->input->post('info');
			//上级栏目不能是自身
			//if($info['parentid']==$catid){
				//showmessage(L('operation_failure'));
			//}
			//上级栏目不能是自身  ---也不能是自己的子栏目
			$arrchildid = $this->db->get_one(array('catid'=>$catid), 'arrchildid');
			$arrchildid_arr = explode(',',$arrchildid['arrchildid']); 
			if(in_array($info['parentid'],$arrchildid_arr,true)){
				showmessage(L('operation_failure'));
			}
			$this->content_db = pc_base::load_model('content_model');
			$this->categorys = getcache('category_content_'.$this->siteid,'commons');
			$modelid = $this->categorys[$catid]['modelid'];
			if ($modelid) {
				$this->content_db->set_model($modelid);
				$table_name = $this->content_db->table_name;
				$rs = $this->content_db->query("SELECT COUNT(*) AS `count` FROM `$table_name` WHERE catid IN(".$arrchildid['arrchildid'].")");
				$result = $this->content_db->fetch_array($rs);
				$total = $result[0]['count'];
				if ($total && $setting['disabled']) {
					showmessage(L('当前栏目存在内容数据，无法禁用'));
				}
			}
			//栏目生成静态配置
			if($this->input->post('type') != 2) {
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $this->input->post('category_html_ruleid');
				} else {
					$setting['category_ruleid'] = $this->input->post('category_php_ruleid');
					$info['url'] = '';
				}
			}
			//内容生成静态配置
			if($setting['content_ishtml']) {
				$setting['show_ruleid'] = $this->input->post('show_html_ruleid');
			} else {
				$setting['show_ruleid'] = $this->input->post('show_php_ruleid');
			}
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$info['sethtml'] = $setting['create_to_html_root'];
			$info['setting'] = array2string($setting);
			$info['module'] = 'content';
			$catname = CHARSET == 'gbk' ? safe_replace($info['catname']) : iconv('utf-8','gbk',safe_replace($info['catname']));
			$catname = str_replace(array('%'),'',$catname);
			$letters = gbk_to_pinyin($catname);
			$info['letter'] = strtolower(implode('', $letters));
			
			//应用权限设置到子栏目
			if($this->input->post('priv_child')) {
				$arrchildid = $this->db->get_one(array('catid'=>$catid), 'arrchildid');
				if(!empty($arrchildid['arrchildid'])) {
					$arrchildid_arr = explode(',', $arrchildid['arrchildid']);
					if(!empty($arrchildid_arr)) {
						foreach ($arrchildid_arr as $arr_v) {
							$this->update_priv($arr_v, $this->input->post('priv_groupid'), 0);
						}
					}
				}
				
			}
			
			//应用模板到所有子栏目
			if($this->input->post('template_child')){
                                $this->categorys = $categorys = $this->db->select(array('siteid'=>$this->siteid,'module'=>'content'), '*', '', 'listorder ASC, catid ASC', '', 'catid');
                                $idstr = $this->get_arrchildid($catid);
                                 if(!empty($idstr)){
                                        $sql = "select catid,setting from cms_category where catid in($idstr)";
                                        $this->db->query($sql);
                                        $arr = $this->db->fetch_array();
                                         if(!empty($arr)){
                                                foreach ($arr as $v){
                                                        $new_setting = array2string(
														array_merge(string2array($v['setting']), array('category_template' => $this->input->post('setting')['category_template'],'list_template' =>  $this->input->post('setting')['list_template'],'show_template' =>  $this->input->post('setting')['show_template'])
                                                                                )
                                                        );
                                                        $this->db->update(array('setting'=>$new_setting), 'catid='.$v['catid']);
                                                }
                                        }                                
                                }
			}
			
			require_once CACHE_MODEL_PATH.'content_input.class.php';
			require_once CACHE_MODEL_PATH.'content_update.class.php';
			$content_input = new content_input(-1);
			$inputinfo = $content_input->get($info);
			$systeminfo = $inputinfo['system'];
			
			$this->db->update($info,array('catid'=>$catid,'siteid'=>$this->siteid));
			$this->db->update($systeminfo,array('catid'=>$catid,'siteid'=>$this->siteid));
			$this->update_priv($catid, $this->input->post('priv_roleid'));
			$this->update_priv($catid, $this->input->post('priv_groupid'),0);
			$this->cache();
			//更新附件状态
			if($info['image'] && pc_base::load_config('system','attachment_stat')) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($info['image'],'catid-'.$catid,1);
			}
			showmessage(L('operation_success').'<script type="text/javascript">Dialog.warn("'.L("operation_success").L("edit_following_operation").'",function(){window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location = "?m=admin&c=category&a=public_cache&menuid=43&module=admin";});</script>');
		} else {
			$show_header = $show_dialog = '';
			//获取站点模板信息
			pc_base::load_app_func('global');
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			
			
			$show_validator = $catid = $r = '';
			$catid = intval($this->input->get('catid'));
			pc_base::load_sys_class('form','',0);
			$r = $this->db->get_one(array('catid'=>$catid));
			if($r) extract($r);
			$setting = string2array($setting);
			
			$this->priv_db = pc_base::load_model('category_priv_model');
			$this->privs = $this->priv_db->select(array('catid'=>$catid));
			
			$data = array_map('htmlspecialchars_decode',$r);
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form(-1);
			$forminfos = $content_form->get($data);
			$formValidator = $content_form->formValidator;
			$checkall = $content_form->checkall;
			$type = $this->input->get('type');
			if($type==0) {
				include $this->admin_tpl('category_edit');
			} elseif ($type==1) {
				include $this->admin_tpl('category_page_edit');
			} else {
				include $this->admin_tpl('category_link_edit');
			}
		}	
	}
	/**
	 * 更新
	 */
	function ismenu() {
		if($this->input->get('dosubmit')) {
			$this->db->update(array('ismenu'=>$this->input->post('ismenu')),array('catid'=>$this->input->post('catid')));
			$this->cache();
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 更新禁用可用
	 */
	function disabled() {
		if($this->input->get('dosubmit')) {
			$row = $this->db->get_one(array('catid'=>$this->input->post('catid')));
			if (!$row) {
				dr_json(0, L('栏目数据不存在'));
			}
			$row['setting'] = string2array($row['setting']);
			$row['setting']['disabled'] = $row['setting']['disabled'] ? 0 : 1;
			$this->content_db = pc_base::load_model('content_model');
			$this->categorys = getcache('category_content_'.$this->siteid,'commons');
			$modelid = $this->categorys[$this->input->post('catid')]['modelid'];
			if ($modelid) {
				$this->content_db->set_model($modelid);
				$table_name = $this->content_db->table_name;
				$rs = $this->content_db->query("SELECT COUNT(*) AS `count` FROM `$table_name` WHERE catid IN(".$row['arrchildid'].")");
				$result = $this->content_db->fetch_array($rs);
				$total = $result[0]['count'];
				if ($total && $row['setting']['disabled']) {
					dr_json(0, L('当前栏目存在内容数据，无法禁用'));
				}
			}
			if ($this->input->post('disabled')) {
				$this->db->update(array('setting'=>array2string($row['setting'])),array('catid'=>$this->input->post('catid')));
			}
			$this->cache();
			dr_json(1, L($row['setting']['disabled'] ? '禁用状态' : '可用状态'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 更新显示隐藏
	 */
	function iscatpos() {
		if($this->input->get('dosubmit')) {
			$row = $this->db->get_one(array('catid'=>$this->input->post('catid')));
			if (!$row) {
				dr_json(0, L('栏目数据不存在'));
			}
			$row['setting'] = string2array($row['setting']);
			$row['setting']['iscatpos'] = $row['setting']['iscatpos'] ? 0 : 1;
			if ($this->input->post('iscatpos')) {
				$this->db->update(array('setting'=>array2string($row['setting'])),array('catid'=>$this->input->post('catid')));
			}
			$this->cache();
			dr_json(1, L($row['setting']['iscatpos'] ? '显示状态' : '隐藏状态'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 更新左侧显示隐藏
	 */
	function isleft() {
		if($this->input->get('dosubmit')) {
			$row = $this->db->get_one(array('catid'=>$this->input->post('catid')));
			if (!$row) {
				dr_json(0, L('栏目数据不存在'));
			}
			$row['setting'] = string2array($row['setting']);
			$row['setting']['isleft'] = $row['setting']['isleft'] ? 0 : 1;
			if ($this->input->post('isleft')) {
				$this->db->update(array('setting'=>array2string($row['setting'])),array('catid'=>$this->input->post('catid')));
			}
			$this->cache();
			dr_json(1, L($row['setting']['isleft'] ? '显示状态' : '隐藏状态'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 排序
	 */
	public function listorder() {
		if($this->input->get('dosubmit')) {
			$this->db->update(array('listorder'=>$this->input->post('listorder')),array('catid'=>$this->input->post('catid')));
			$this->cache();
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 删除栏目
	 */
	public function delete() {
		if($this->input->get('dosubmit')) {
			$catid = intval($this->input->post('catid'));
			$categorys = getcache('category_content_'.$this->siteid,'commons');
			$sethtml = $categorys[$catid]['sethtml'];
			$html_root = pc_base::load_config('system','html_root');
			if($sethtml) $html_root = '';
			$setting = string2array($categorys[$catid]['setting']);
			$ishtml = $setting['ishtml'];
			$this->url = pc_base::load_app_class('url', 'content');
			$sitelist = getcache('sitelist','commons');
			$modelid = $categorys[$catid]['modelid'];
			$items = getcache('category_items_'.$modelid,'commons');
			//if($items[$catid]) showmessage(L('category_does_not_allow_delete'));
			if($ishtml) {
				pc_base::load_sys_func('dir');
				$fileurl = $html_root.'/'.$this->url->category_url($catid, 1);
				if($this->siteid != 1) {
					$fileurl = $sitelist[$this->siteid]['dirname'].$fileurl;
				}
				dir_delete(CMS_PATH.$fileurl);
				if($sitelist[$this->siteid]['mobilehtml']==1) {
					$mobilefileurl = pc_base::load_config('system','mobile_root').$fileurl;
					dir_delete(CMS_PATH.$mobilefileurl);	
				}
			}
			$this->delete_child($catid, $modelid);
			$this->db->delete(array('catid'=>$catid));
			if ($modelid != 0) {
				$this->delete_category_content($catid, $modelid);
			}
			$this->cache();
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 递归删除栏目
	 * @param $catid 要删除的栏目id
	 */
	private function delete_child($catid, $modelid) {
		$catid = intval($catid);
		if (empty($catid)) return false;
		$categorys = getcache('category_content_'.$this->siteid,'commons');
		$sethtml = $categorys[$catid]['sethtml'];
		$html_root = pc_base::load_config('system','html_root');
		if($sethtml) $html_root = '';
		$setting = string2array($categorys[$catid]['setting']);
		$ishtml = $setting['ishtml'];
		$this->url = pc_base::load_app_class('url', 'content');
		$sitelist = getcache('sitelist','commons');
		$list = $this->db->select(array('parentid'=>$catid));
		foreach($list as $r) {
			if($ishtml) {
				pc_base::load_sys_func('dir');
				$fileurl = $html_root.'/'.$this->url->category_url($r['catid'], 1);
				if($this->siteid != 1) {
					$fileurl = $sitelist[$this->siteid]['dirname'].$fileurl;
				}
				dir_delete(CMS_PATH.$fileurl);
				if($sitelist[$this->siteid]['mobilehtml']==1) {
					$mobilefileurl = pc_base::load_config('system','mobile_root').$fileurl;
					dir_delete(CMS_PATH.$mobilefileurl);	
				}
			}
			$this->delete_child($r['catid'], $modelid);
			$this->db->delete(array('catid'=>$r['catid']));
			if ($modelid != 0) {
				$this->delete_category_content($r['catid'], $modelid);
			}
		}
		return true;
	}
	/**
	 * 删除栏目分类下的内容
	 * @param $catid 要删除内容的栏目id
	 */
	private function delete_category_content($catid, $modelid) {
		$content_model = pc_base::load_model('content_model');
		$categorys = getcache('category_content_'.$this->siteid,'commons');
		$sethtml = $categorys[$catid]['sethtml'];
		$html_root = pc_base::load_config('system','html_root');
		if($sethtml) $html_root = '';
		$setting = string2array($categorys[$catid]['setting']);
		$content_ishtml = $setting['content_ishtml'];
		$content_model->set_model($modelid);
		$this->hits_db = pc_base::load_model('hits_model');
		$this->queue = pc_base::load_model('queue_model');
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
		$result = $content_model->select(array('catid'=>$catid), 'id,inputtime');
		if (is_array($result) && !empty($result)) {
			foreach ($result as $key=>$val) {
				if($content_ishtml && !$val['islink']) {
					$urls = $this->url->show($val['id'], 0, $catid, $val['inputtime']);
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
				$content_model->delete_content($val['id'],$fileurl,$catid);
				//删除统计表数据
				$this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$val['id']));
				//删除附件
				$attachment->api_delete('c-'.$catid.'-'.$val['id']);
				//删除审核表数据
				$this->content_check_db->delete(array('checkid'=>'c-'.$val['id'].'-'.$modelid));
				//删除推荐位数据
				$this->position_data_db->delete(array('id'=>$val['id'],'catid'=>$catid,'module'=>'content'));
				//删除全站搜索中数据
				$this->search_db->delete_search($typeid,$val['id']);
				//删除关键词和关键词数量重新统计
				$keyword_db = pc_base::load_model('keyword_model');
				$keyword_data_db = pc_base::load_model('keyword_data_model');
				$keyword_arr = $keyword_data_db->select(array('siteid'=>$this->siteid,'contentid'=>$val['id'].'-'.$modelid));
				if($keyword_arr){
					foreach ($keyword_arr as $val){
						$keyword_db->update(array('videonum'=>'-=1'),array('id'=>$val['tagid']));
					}
					$keyword_data_db->delete(array('siteid'=>$this->siteid,'contentid'=>$val['id'].'-'.$modelid));
					$keyword_db->delete(array('videonum'=>'0'));
				}
				
				//删除相关的评论,删除前应该判断是否还存在此模块
				if(module_exists('comment')){
					$commentid = id_encode('content_'.$catid, $val['id'], $this->siteid);
					$this->comment->del($commentid, $this->siteid, $val['id'], $catid);
				}
				
			}
		}
	}
	/**
	 * 更新缓存
	 */
	public function cache() {
		$categorys = array();
		$models = getcache('model','commons');
		foreach ($models as $modelid=>$model) {
			$datas = $this->db->select(array('modelid'=>$modelid),'catid,type,items',10000);
			$array = array();
			foreach ($datas as $r) {
				if($r['type']==0) $array[$r['catid']] = $r['items'];
			}
			setcache('category_items_'.$modelid, $array,'commons');
		}
		$array = array();
		$categorys = $this->db->select('`module`=\'content\'','catid,siteid',20000,'listorder ASC');
		foreach ($categorys as $r) {
			$array[$r['catid']] = $r['siteid'];
		}
		setcache('category_content',$array,'commons');
		$categorys = $this->categorys = array();
		$this->categorys = $this->db->select(array('siteid'=>$this->siteid, 'module'=>'content'),'*',10000,'listorder ASC');
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
		return true;
	}
	/**
	 * 更新缓存并修复栏目
	 */
	public function public_cache() {
		$this->repair();
		$this->cache();
		showmessage(L('operation_success'),'?m=admin&c=category&a=init&module=admin&menuid=43');
	}
	/**
	* 修复栏目数据
	*/
	private function repair() {
		pc_base::load_sys_func('iconv');
		@set_time_limit(600);
		$html_root = pc_base::load_config('system','html_root');
		$this->categorys = $categorys = array();
		$this->categorys = $categorys = $this->db->select(array('siteid'=>$this->siteid,'module'=>'content'), '*', '', 'listorder ASC, catid ASC', '', 'catid');
		
		$this->get_categorys($categorys);
		if(is_array($this->categorys)) {
			foreach($this->categorys as $catid => $cat) {
				if($cat['type'] == 2) continue;
				$arrparentid = $this->get_arrparentid($catid);
				$setting = string2array($cat['setting']);
				$arrchildid = $this->get_arrchildid($catid);
				$child = is_numeric($arrchildid) ? 0 : 1;
				if($categorys[$catid]['arrparentid']!=$arrparentid || $categorys[$catid]['arrchildid']!=$arrchildid || $categorys[$catid]['child']!=$child) $this->db->update(array('arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'child'=>$child),array('catid'=>$catid));

				$parentdir = $this->get_parentdir($catid);
				$catname = $cat['catname'];
				$letters = gbk_to_pinyin($catname);
				$letter = strtolower(implode('', $letters));
				$listorder = $cat['listorder'] ? $cat['listorder'] : $catid;
				
				$this->sethtml = $setting['create_to_html_root'];
				//检查是否生成到根目录
				$this->get_sethtml($catid);
				$sethtml = $this->sethtml ? 1 : 0;
				
				if($setting['ishtml']) {
				//生成静态时
					$url = $this->update_url($catid);
					if(!preg_match('/^(http|https):\/\//i', $url)) {
						$url = $sethtml ? '/'.$url : $html_root.'/'.$url;
					}
				} else {
				//不生成静态时
					$url = $this->update_url($catid);
					$url = APP_PATH.$url;
				}
				if($cat['url']!=$url) $this->db->update(array('url'=>$url), array('catid'=>$catid));
				
				
				
				if($categorys[$catid]['parentdir']!=$parentdir || $categorys[$catid]['sethtml']!=$sethtml || $categorys[$catid]['letter']!=$letter || $categorys[$catid]['listorder']!=$listorder) $this->db->update(array('parentdir'=>$parentdir,'sethtml'=>$sethtml,'letter'=>$letter,'listorder'=>$listorder), array('catid'=>$catid));
			}
		}
		
		//删除在非正常显示的栏目
		foreach($this->categorys as $catid => $cat) {
			if($cat['parentid'] != 0 && !isset($this->categorys[$cat['parentid']])) {
				$this->db->delete(array('catid'=>$catid));
			}
		}
		return true;
	}
	/**
	 * 获取父栏目是否生成到根目录
	 */
	private function get_sethtml($catid) {
		foreach($this->categorys as $id => $cat) {
			if($catid==$id) {
				$parentid = $cat['parentid'];
				if($this->categorys[$parentid]['sethtml']) {
					$this->sethtml = 1;
				}
				if($parentid) {
					$this->get_sethtml($parentid);
				}
			}
		}
	}
	
	/**
	 * 找出子目录列表
	 * @param array $categorys
	 */
	private function get_categorys($categorys = array()) {
		if (is_array($categorys) && !empty($categorys)) {
			foreach ($categorys as $catid => $c) {
				$this->categorys[$catid] = $c;
				$result = array();
				foreach ($this->categorys as $_k=>$_v) {
					if($_v['parentid']) $result[] = $_v;
				}
				$this->get_categorys($r);
			}
		} 
		return true;
	}
	/**
	* 更新栏目链接地址
	*/
	private function update_url($catid) {
		$catid = intval($catid);
		if (!$catid) return false;
		$url = pc_base::load_app_class('url', 'content'); //调用URL实例
		return $url->category_url($catid);
	}

	/**
	 * 
	 * 获取父栏目ID列表
	 * @param integer $catid              栏目ID
	 * @param array $arrparentid          父目录ID
	 * @param integer $n                  查找的层次
	 */
	private function get_arrparentid($catid, $arrparentid = '', $n = 1) {
		if($n > 5 || !is_array($this->categorys) || !isset($this->categorys[$catid])) return false;
		$parentid = $this->categorys[$catid]['parentid'];
		$arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
		if($parentid) {
			$arrparentid = $this->get_arrparentid($parentid, $arrparentid, ++$n);
		} else {
			$this->categorys[$catid]['arrparentid'] = $arrparentid;
		}
		$parentid = $this->categorys[$catid]['parentid'];
		return $arrparentid;
	}

	/**
	 * 
	 * 获取子栏目ID列表
	 * @param $catid 栏目ID
	 */
	private function get_arrchildid($catid) {
		$arrchildid = $catid;
		if(is_array($this->categorys)) {
			foreach($this->categorys as $id => $cat) {
				if($cat['parentid'] && $id != $catid && $cat['parentid']==$catid) {
					$arrchildid .= ','.$this->get_arrchildid($id);
				}
			}
		}
		return $arrchildid;
	}
	/**
	 * 获取父栏目路径
	 * @param  $catid
	 */
	function get_parentdir($catid) {
		if($this->categorys[$catid]['parentid']==0) return '';
		$r = $this->categorys[$catid];
		$setting = string2array($r['setting']);
		$url = $r['url'];
		$arrparentid = $r['arrparentid'];
		unset($r);
		if (strpos($url, '://')===false) {
			if ($setting['creat_to_html_root']) {
				return '';
			} else {
				$arrparentid = explode(',', $arrparentid);
				$arrcatdir = array();
				foreach($arrparentid as $id) {
					if($id==0) continue;
					$arrcatdir[] = $this->categorys[$id]['catdir'];
				}
				return implode('/', $arrcatdir).'/';
			}
		} else {
			if ($setting['create_to_html_root']) {
				if (preg_match('/^((http|https):\/\/)?([^\/]+)/i', $url, $matches)) {
					$url = $matches[0].'/';
					$rs = $this->db->get_one(array('url'=>$url), '`parentdir`,`catid`');
					if ($catid == $rs['catid']) return '';
					else return $rs['parentdir'];
				} else {
					return '';
				}
			} else {
				$arrparentid = explode(',', $arrparentid);
				$arrcatdir = array();
				krsort($arrparentid);
				foreach ($arrparentid as $id) {
					if ($id==0) continue;
					$arrcatdir[] = $this->categorys[$id]['catdir'];
					if ($this->categorys[$id]['parentdir'] == '') break;
				}
				krsort($arrcatdir);
				return implode('/', $arrcatdir).'/';
			}
		}
	}
	/**
	 * 检查目录是否存在
	 * @param  $return_method 返回方法
	 * @param  $catdir 目录
	 */
	public function public_check_catdir($return_method = 1,$catdir = '') {
		$old_dir = '';
		$catdir = $catdir ? $catdir : $this->input->get('catdir');
		$parentid = intval($this->input->get('parentid'));
		$old_dir = $this->input->get('old_dir');
		$r = $this->db->get_one(array('siteid'=>$this->siteid,'module'=>'content','catdir'=>$catdir,'parentid'=>$parentid));
		if($r && $old_dir != $r['catdir']) {
			//目录存在
			if($return_method) {
				exit('0');
			} else {
				return false;
			}
		} else {
			if($return_method) {
				exit('1');
			} else {
				return true;
			}
		}
	}
	
	/**
	 * 更新权限
	 * @param  $catid
	 * @param  $priv_datas
	 * @param  $is_admin
	 */
	private function update_priv($catid,$priv_datas,$is_admin = 1) {
		$this->priv_db = pc_base::load_model('category_priv_model');
		$this->priv_db->delete(array('catid'=>$catid,'is_admin'=>$is_admin));
		if(is_array($priv_datas) && !empty($priv_datas)) {
			foreach ($priv_datas as $r) {
				$r = explode(',', $r);
				$action = $r[0];
				$roleid = $r[1];
				$this->priv_db->insert(array('catid'=>$catid,'roleid'=>$roleid,'is_admin'=>$is_admin,'action'=>$action,'siteid'=>$this->siteid));
			}
		}
	}

	/**
	 * 检查栏目权限
	 * @param $action 动作
	 * @param $roleid 角色
	 * @param $is_admin 是否为管理组
	 */
	private function check_category_priv($action,$roleid,$is_admin = 1) {
		$checked = '';
		foreach ($this->privs as $priv) {
			if($priv['is_admin']==$is_admin && $priv['roleid']==$roleid && $priv['action']==$action) $checked = 'checked';
		}
		return $checked;
	}
	/**
	 * 重新统计栏目信息数量
	 */
	public function count_items() {
		$this->content_db = pc_base::load_model('content_model');
		$result = getcache('category_content_'.$this->siteid,'commons');
		foreach($result as $r) {
			if($r['type'] == 0) {
				$modelid = $r['modelid'];
				$this->content_db->set_model($modelid);
				$number = $this->content_db->count(array('catid'=>$r['catid']));
				$this->db->update(array('items'=>$number),array('catid'=>$r['catid']));
			}
		}
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * json方式加载模板
	 */
	public function public_tpl_file_list() {
		$style = $this->input->get('style') && trim($this->input->get('style')) ? trim($this->input->get('style')) : exit(0);
		$catid = $this->input->get('catid') && intval($this->input->get('catid')) ? intval($this->input->get('catid')) : 0;
		$batch_str = $this->input->get('batch_str') ? '['.$catid.']' : '';
		if ($catid) {
			$cat = getcache('category_content_'.$this->siteid,'commons');
			$cat = $cat[$catid];
			$cat['setting'] = string2array($cat['setting']);
		}
		pc_base::load_sys_class('form','',0);
		if($this->input->get('type')==1) {
			$html = array('page_template'=>form::select_template($style, 'content',(isset($cat['setting']['page_template']) && !empty($cat['setting']['page_template']) ? $cat['setting']['page_template'] : 'page'),'name="setting'.$batch_str.'[page_template]"','page'));
		} else {
			$html = array('category_template'=> form::select_template($style, 'content',(isset($cat['setting']['category_template']) && !empty($cat['setting']['category_template']) ? $cat['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'), 
				'list_template'=>form::select_template($style, 'content',(isset($cat['setting']['list_template']) && !empty($cat['setting']['list_template']) ? $cat['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',(isset($cat['setting']['show_template']) && !empty($cat['setting']['show_template']) ? $cat['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
			);
		}
		if ($this->input->get('module')) {
			unset($html);
			if ($this->input->get('templates')) {
				$templates = explode('|', $this->input->get('templates'));
				if ($this->input->get('id')) $id = explode('|', $this->input->get('id'));
				if (is_array($templates)) {
					foreach ($templates as $k => $tem) {
						$t = $tem.'_template';
						if ($id[$k]=='') $id[$k] = $tem;
						$html[$t] = form::select_template($style, $this->input->get('module'), $id[$k], 'name="'.$this->input->get('name').'['.$t.']" id="'.$t.'"', $tem);
					}
				}
			}
			
		}
		if (CHARSET == 'gbk') {
			$html = array_iconv($html, 'gbk', 'utf-8');
		}
		echo json_encode($html);
	}

	/**
	 * 快速进入搜索
	 */
	public function public_ajax_search() {
		if($this->input->get('catname')) {
			if(preg_match('/([a-z]+)/i',$this->input->get('catname'))) {
				$field = 'letter';
				$catname = strtolower(trim($this->input->get('catname')));
			} else {
				$field = 'catname';
				$catname = trim($this->input->get('catname'));
				if (CHARSET == 'gbk') $catname = iconv('utf-8','gbk',$catname);
			}
			$result = $this->db->select("$field LIKE('$catname%') AND siteid='$this->siteid' AND child=0",'catid,type,catname,letter',10);
			if (CHARSET == 'gbk') {
				$result = array_iconv($result, 'gbk', 'utf-8');
			}
			echo json_encode($result);
		}
	}
	/**
	 * json方式读取风格列表，推送部分调用
	 */
	public function public_change_tpl() {
		pc_base::load_sys_class('form','',0);
		$models = getcache('model','commons');
		$modelid = intval($this->input->get('modelid'));
		if($this->input->get('modelid')) {
			$style = $models[$modelid]['default_style'];
			$category_template = $models[$modelid]['category_template'];
			$list_template = $models[$modelid]['list_template'];
			$show_template = $models[$modelid]['show_template'];
			$html = array(
				'template_list'=> $style, 
				'category_template'=> form::select_template($style, 'content',$category_template,'name="setting[category_template]"','category'), 
				'list_template'=>form::select_template($style, 'content',$list_template,'name="setting[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',$show_template,'name="setting[show_template]"','show')
			);
			if (CHARSET == 'gbk') {
				$html = array_iconv($html, 'gbk', 'utf-8');
			}
			echo json_encode($html);
		}
	}
	/**
	 * 批量修改
	 */
	public function batch_edit() {
		$categorys = getcache('category_content_'.$this->siteid,'commons');
		if($this->input->post('dosubmit')) {
			
			pc_base::load_sys_func('iconv');	
			$catid = intval($this->input->post('catid'));
			$post_setting = $this->input->post('setting');
			//栏目生成静态配置
			$infos = $info = array();
			$infos = $this->input->post('info');
			if(empty($infos)) showmessage(L('operation_success'));
			$this->attachment_db = pc_base::load_model('attachment_model');
			foreach ($infos as $catid=>$info) {
				$setting = string2array($categorys[$catid]['setting']);
				if($this->input->post('type') != 2) {
					if($post_setting[$catid]['ishtml']) {
						$setting['category_ruleid'] = $this->input->post('category_html_ruleid')[$catid];
					} else {
						$setting['category_ruleid'] = $this->input->post('category_php_ruleid')[$catid];
						$info['url'] = '';
					}
				}
				foreach($post_setting[$catid] as $_k=>$_setting) {
					$setting[$_k] = $_setting;
				}
				//内容生成静态配置
				if($post_setting[$catid]['content_ishtml']) {
					$setting['show_ruleid'] = $this->input->post('show_html_ruleid')[$catid];
				} else {
					$setting['show_ruleid'] = $this->input->post('show_php_ruleid')[$catid];
				}
				if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
				$row = $this->db->get_one(array('catid'=>$catid));
				$this->content_db = pc_base::load_model('content_model');
				$modelid = $categorys[$catid]['modelid'];
				if ($modelid) {
					$this->content_db->set_model($modelid);
					$table_name = $this->content_db->table_name;
					$rs = $this->content_db->query("SELECT COUNT(*) AS `count` FROM `$table_name` WHERE catid IN(".$row['arrchildid'].")");
					$result = $this->content_db->fetch_array($rs);
					$total = $result[0]['count'];
					if ($total && $setting['disabled']) {
						showmessage(L('当前栏目存在内容数据，无法禁用'), HTTP_REFERER);
					}
				}
				$info['sethtml'] = $post_setting[$catid]['create_to_html_root'];
				$info['setting'] = array2string($setting);
				
				$info['module'] = 'content';
				$catname = CHARSET == 'gbk' ? $info['catname'] : iconv('utf-8','gbk',$info['catname']);
				$letters = gbk_to_pinyin($catname);
				$info['letter'] = strtolower(implode('', $letters));
				$this->db->update($info,array('catid'=>$catid,'siteid'=>$this->siteid));

				//更新附件状态
				if($info['image'] && pc_base::load_config('system','attachment_stat')) {
					$this->attachment_db->api_update($info['image'],'catid-'.$catid,1);
				}
			}
			$this->public_cache();
			showmessage(L('operation_success'),'?m=admin&c=category&a=init&module=admin&menuid=43');
		} else {
			if($this->input->post('catids')) {
				//获取站点模板信息
				pc_base::load_app_func('global');
				$template_list = template_list($this->siteid, 0);
				foreach ($template_list as $k=>$v) {
					$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
					unset($template_list[$k]);
				}
				
				$show_validator = $show_header = '';
				$catid = intval($this->input->get('catid'));
				$type = $this->input->post('type') ? intval($this->input->post('type')) : 0;
				pc_base::load_sys_class('form','',0);
				
				if(empty($this->input->post('catids'))) showmessage(L('illegal_parameters'));
				$batch_array = $workflows = array();
				foreach ($categorys as $catid=>$cat) {
					if($cat['type']==$type && in_array($catid, $this->input->post('catids'))) {
						$batch_array[$catid] = $cat;
					}
				}
				if(empty($batch_array)) showmessage(L('please_select_category')); 
				$workflows = getcache('workflow_'.$this->siteid,'commons');
				if($workflows) {
					$workflows_datas = array();
					foreach($workflows as $_k=>$_v) {
						$workflows_datas[$_v['workflowid']] = $_v['workname'];
					}
				}
				
				if($type==1) {
					include $this->admin_tpl('category_batch_edit_page');
				} else {
					include $this->admin_tpl('category_batch_edit');
				}
			} else {
				$show_header = $show_dialog = '';
				$type = $this->input->get('select_type') ? intval($this->input->get('select_type')) : 0;
				
				$tree = pc_base::load_sys_class('tree');
				$tree->icon = array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
				$tree->nbsp = '&nbsp;&nbsp;';
				$category = array();
				foreach($categorys as $catid=>$r) {
					if($this->siteid != $r['siteid'] || ($r['type']==2 && $r['child']==0)) continue;
					$category[$catid] = $r;
				}
				$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
	
				$tree->init($category);
				$string .= $tree->get_tree(0, $str);
				include $this->admin_tpl('category_batch_select');
			}
		}	
	} 
	/**
	 * 批量移动文章
	 */
	public function remove() {
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
		$this->content_db = pc_base::load_model('content_model');
		if($this->input->post('dosubmit')) {
			$this->content_check_db = pc_base::load_model('content_check_model'); 
			if(!$this->input->post('fromid')) showmessage(L('please_input_move_source','','content'));
			if(!$this->input->post('tocatid')) showmessage(L('please_select_target_category','','content'));
			$tocatid = intval($this->input->post('tocatid'));
			$modelid = $this->categorys[$tocatid]['modelid'];
			if(!$modelid) showmessage(L('illegal_operation','','content'));
			$fromid = array_filter($this->input->post('fromid'),"is_numeric");
			$fromid = implode(',', $fromid);
			$this->content_db->set_model($modelid);
			$this->content_db->update(array('catid'=>$tocatid),"catid IN($fromid)");
 			showmessage(L('operation_success'),HTTP_REFERER);
 		} else {
			$show_header = '';
			$catid = intval($this->input->get('catid'));
			$categorys = array();
 			
  			$modelid = $this->categorys[$catid]['modelid'];
  			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;';
 			foreach($this->categorys as $cid=>$r) {
				if($this->siteid != $r['siteid'] || $r['type']) continue;
				if($modelid && $modelid != $r['modelid']) continue;
				$r['disabled'] = $r['child'] ? 'disabled' : '';
				$r['selected'] = $cid == $catid ? 'selected' : '';
				$categorys[$cid] = $r;
			}
			$str  = "<option value='\$catid' \$disabled>\$spacer \$catname</option>";
 			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			
			
			$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
			$source_string = '';
			$tree->init($categorys);
			$source_string .= $tree->get_tree(0, $str);
			include $this->admin_tpl('category_remove');
 		}
	}
	/**
	 * 汉字转换拼音
	 */
	public function public_ajax_pinyin() {
		$pinyin = pc_base::load_sys_class('pinyin');
		$name = dr_safe_replace($this->input->get('name'));
		if (!$name) {
			exit('');
		}
		$py = $pinyin->result($name);
		if (strlen($py) > 12) {
			$sx = $pinyin->result($name, 0);
			if ($sx) {
				exit($sx);
			}
		}
		exit($py);
	}
}
?>