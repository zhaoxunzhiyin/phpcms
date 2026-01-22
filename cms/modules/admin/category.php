<?php
defined('IN_CMS') or exit('No permission resources.');
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
class category extends admin {
	private $input,$cache,$pinyin,$db,$menu_db,$field_db,$content_db,$max_category,$is_link_update,$content_check_db,$position_data_db,$search_db,$comment,$privs,$attachment_db,$urlrule_db;
	public $siteid,$categorys,$cat_config,$repair_categorys,$sethtml,$cat_dir,$cat_cache,$cache_api,$priv_db,$url,$hits_db,$queue;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->pinyin = pc_base::load_sys_class('pinyin');
		$this->db = pc_base::load_model('category_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->field_db = pc_base::load_model('sitemodel_field_model');
		$this->content_db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
		$this->cat_config = $this->cache->get_file('category');
		if (!$this->cat_config) {
			$this->cat_config = [
				'sys_field' => ['listorder', 'ismenu', 'disabled', 'iscatpos', 'isleft', 'id', 'typename', 'modelname', 'html'],
				'list_field' => [],
			];
		}
		if (isset($this->cat_config['popen']) && $this->cat_config['popen']) {
			define('SYS_CAT_POPEN', 1);
		}
		if (!$this->cat_config['total']) {
			define('SYS_TOTAL_POPEN', 1);
		}
		$this->max_category = $this->cat_config['max_category'] ? intval($this->cat_config['max_category']) : 200;
		$this->is_link_update = $this->db->count(array('siteid'=>$this->siteid, 'module'=>'content')) > $this->max_category ? 1 : 0;
	}
	/**
	 * 管理栏目
	 */
	public function init() {
		$show_pc_hash = true;
		list($cat_head, $cat_list, $pcats, $tcats) = $this->_get_tree_list($this->cat_data(0));
		$move_select = $this->cat_select();
		include $this->admin_tpl('category_manage');
	}
	// 获取树形结构列表
	protected function _get_tree_list($data) {
		$models = getcache('model','commons');
		$html_root = SYS_HTML_ROOT;

		$list = "<tr class='\$class'>";
		$head = '<tr class="heading">';

		$list.= "<td class='myselect'>
					<label class='mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'>
						<input type='checkbox' class='checkboxes' name='ids[]' value='\$catid' />
						<span></span>
					</label>
				</td>";
		$head.= '<th class="myselect">
						<label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
							<input type="checkbox" class="group-checkable" data-set=".checkboxes" />
							<span></span>
						</label>
					</th>';

		if (dr_in_array('listorder', $this->cat_config['sys_field'])) {
			$head.= '<th width="70" style="text-align:center"> '.L('排序').' </th>';
			$list.= "<td style='text-align:center'>\$listorder_html</td>";
		}

		if (dr_in_array('ismenu', $this->cat_config['sys_field'])) {
			$head .= '<th width="50" style="text-align:center"> ' . L('导航') . ' </th>';
			$list .= "<td style='text-align:center'>\$is_menu_html</td>";
		}

		if (dr_in_array('disabled', $this->cat_config['sys_field'])) {
			$head .= '<th width="50" style="text-align:center"> ' . L('可用') . ' </th>';
			$list .= "<td style='text-align:center'>\$is_disabled_html</td>";
		}

		if (dr_in_array('iscatpos', $this->cat_config['sys_field'])) {
			$head .= '<th width="70" style="text-align:center"> ' . L('显示') . ' </th>';
			$list .= "<td style='text-align:center'>\$is_catpos_html</td>";
		}

		if (dr_in_array('isleft', $this->cat_config['sys_field'])) {
			$head .= '<th width="50" style="text-align:center"> ' . L('左侧') . ' </th>';
			$list .= "<td style='text-align:center'>\$is_left_html</td>";
		}

		if (dr_in_array('id', $this->cat_config['sys_field'])) {
			$head .= '<th width="70" style="text-align:center"> '.L('number').' </th>';
			$list .= "<td style='text-align:center'>\$catid</td>";
		}

		$head.= '<th> '.L('栏目信息').' </th>';
		$list.= "<td>\$spacer<a data-container='body' data-placement='right' data-original-title='\$parentdir\$catdir' class='tooltips' target='_blank' href='\$url'>\$catname</a>\$ctotal\$help</td>";

		if (dr_in_array('typename', $this->cat_config['sys_field'])) {
			$head.= '<th width="60" style="text-align:center"> '.L('类型').' </th>';
			$list.= "<td style='text-align:center'>\$type_html</td>";
		}
		if (dr_in_array('modelname', $this->cat_config['sys_field'])) {
			$head.= '<th width="150" style="text-align:center"> '.L('所属模型').' </th>';
			$list.= "<td style='text-align:center'>\$modelname</td>";
		}

		if (dr_in_array('html', $this->cat_config['sys_field'])) {
			$head.= '<th width="80" style="text-align:center"> ' . L('栏目静态') . ' </th>';
			$list.= "<td style='text-align:center'>\$is_page_html</td>";
			$head.= '<th width="80" style="text-align:center"> ' . L('内容静态') . ' </th>';
			$list.= "<td style='text-align:center'>\$is_content_html</td>";
		}

		if (isset($this->cat_config['list_field']) && $this->cat_config['list_field']) {
			foreach ($this->cat_config['list_field'] as $i => $t) {
				if ($t['use']) {
					$head.= '<th '.($t['width'] ? ' width="'.$t['width'].'"' : '').' '.($t['center'] ? ' class=\"table-center\" style="text-align:center"' : '').'>'.L($t['name']).'</th>';
					$list.= '<td '.($t['center'] ? ' class=\"table-center\"' : '').'>$'.$i.'_html</td>';
				}
			}
		}

		$head.= '<th>'.L('操作').'</th>';
		$list.= "<td>\$option</td>";

		$head.= '</tr>';
		$list.= "</tr>";

		$tree = '';
		$tcats = $pcats = [];
		foreach($data as $k => $t) {
			$option = '';
			$setting = dr_string2array($t['setting']);
			$t['modelname'] = isset($models[$t['modelid']]['name']) && $models[$t['modelid']]['name'] ? $models[$t['modelid']]['name'] : '';
			$t['catname'] = isset($this->cat_config['name_size']) && $this->cat_config['name_size'] ? str_cut($t['catname'], intval($this->cat_config['name_size'])) : $t['catname'];
			$parentid = explode(',', $t['arrparentid']);
			$t['listorder_html'] = '<input type="text" onblur="dr_ajax_save(this.value, \'?m=admin&c=category&a=listorder&catid='.$t['catid'].'&pc_hash=\'+pc_hash, \'listorder\')" value="'.$t['listorder'].'" class="displayorder form-control input-sm input-inline input-mini">';
			$t['help'] = '';
			if($t['url']) {
				if(preg_match('/^(http|https):\/\//', $t['url'])) {
					$catdir = $t['catdir'];
					$prefix = $t['sethtml'] ? '' : $html_root;
					if($this->siteid==1) {
						$catdir = $prefix.'/'.$t['parentdir'].$catdir;
					} else {
						$catdir = $prefix.'/'.dr_site_info('dirname', $this->siteid).$html_root.'/'.$catdir;
					}
					if($t['type']==0 && $setting['ishtml'] && strpos($t['url'], '?')===false && substr_count($t['url'],'/')<4) $t['help'] = '<span class="btn btn-xs green tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="'.L('tips_domain').$t['url'].'<br>'.L('directory_binding').'<br>'.$catdir.'/"><i class="fa fa-question-circle-o"></i></span>';
					$t['url'] = $t['url'];
				} else {
					$t['url'] = substr(dr_site_info('domain', $this->siteid),0,-1).$t['url'];
				}
			} else {
				$t['url'] = $t['url'];
			}
			$t['iscatpos'] = $setting['iscatpos'];
			$t['isleft'] = $setting['isleft'];
			$t['ishtml'] = $setting['ishtml'];
			$t['content_ishtml'] = $setting['content_ishtml'];
			$option.= '<a class="btn btn-xs blue" href="javascript:dr_iframe(\'add\', \'?m=admin&c=category&a=add&parentid='.$t['catid'].'&menuid='.$this->input->get('menuid').'&s='.$t['type'].'\', \'80%\', \'80%\', \'nogo\')"> <i class="fa fa-plus"></i> '.L('子类').'</a>';
			$option.= '<a class="btn btn-xs green" href="javascript:dr_iframe(\'edit\', \'?m=admin&c=category&a=edit&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'&type='.$t['type'].'\', \'80%\', \'80%\', \'nogo\')"> <i class="fa fa-edit"></i> '.L('edit').'</a>';
			$option.= '<a href="?m=admin&c=category&a=remove&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token().'" class="btn btn-xs yellow"><i class="fa fa-arrows"></i> '.L('move').'</a>';
			$t['ctotal'] = '';
			if(!$t['type'] && $t['modelid']) {
				$t['ctotal'] = '<span class="cat-total-'.$t['catid'].' dr_total">（'.$t['items'].'）</span>';
				$tcats[] = $t['catid'].'-'.$t['modelid'];
			}
			$t['option'] = $option;
			// 判断显示和隐藏开关
			$t['is_menu_html'] = '<a data-container="body" data-placement="right" data-original-title="'.L('前端循环调用不会显示，但可以正常访问').'" href="javascript:;" onclick="dr_ajax_open_close(this, \'?m=admin&c=category&a=public_show_edit&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'\', 0);" class="tooltips badge badge-'.(!$t['ismenu'] ? 'no' : 'yes').'"><i class="fa fa-'.(!$t['ismenu'] ? 'times' : 'check').'"></i></a>';
			$t['is_disabled_html'] = '<a data-container="body" data-placement="right" data-original-title="'.L('禁用状态下此栏目不能正常访问').'" href="javascript:;" onclick="dr_ajax_open_close(this, \'?m=admin&c=category&a=public_show_edit&at=disabled&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'\', 1);" class="tooltips badge badge-'.($t['disabled'] ? 'no' : 'yes').'"><i class="fa fa-'.($t['disabled'] ? 'times' : 'check').'"></i></a>';
			$t['is_catpos_html'] = '<a data-container="body" data-placement="right" data-original-title="'.L('前端栏目面包屑导航调用不会显示，但可以正常访问，您现在的位置不显示').'" href="javascript:;" onclick="dr_ajax_open_close(this, \'?m=admin&c=category&a=public_show_edit&at=iscatpos&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'\', 0);" class="tooltips badge badge-'.(!$t['iscatpos'] ? 'no' : 'yes').'"><i class="fa fa-'.(!$t['iscatpos'] ? 'times' : 'check').'"></i></a>';
			$t['is_left_html'] = '<a data-container="body" data-placement="right" data-original-title="'.L('前端栏目调用左侧不会显示，但可以正常访问').'" href="javascript:;" onclick="dr_ajax_open_close(this, \'?m=admin&c=category&a=public_show_edit&at=isleft&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'\', 0);" class="tooltips badge badge-'.(!$t['isleft'] ? 'no' : 'yes').'"><i class="fa fa-'.(!$t['isleft'] ? 'times' : 'check').'"></i></a>';
			if ($t['type'] == 0) {
				if ($t['child']) {
					$t['type_html'] = '<a class="tooltips badge badge-danger" data-container="body" data-placement="right" data-original-title="'.L('当栏目存在子栏目时我们称之为封面').'"> '.L('封面').' </a>';
				} else {
					$t['type_html'] = '<a class="tooltips badge badge-success" data-container="body" data-placement="right" data-original-title="'.L('最终的栏目我们称之为列表').'"> '.L('列表').' </a>';
				}
			} elseif ($t['type'] == 2) {
				$t['type_html'] = '<a class="tooltips badge badge-warning" data-container="body" data-placement="right" data-original-title="'.L('属于外部链接').'"> '.L('外链').' </a>';
			} else {
				$t['type_html'] = '<a class="tooltips badge badge-info" data-container="body" data-placement="right" data-original-title="'.L('不可发布内容的介绍性质页面，例如关于我们等页面').'"> '.L('单页').' </a>';
			}
			// 判断是否生成静态
			$t['is_page_html'] = '';
			$t['is_content_html'] = '';
			if ($t['type'] != 2) {
				$t['is_page_html'] = '<a href="javascript:;" onclick="dr_ajax_open_close(this, \'?m=admin&c=category&a=public_show_edit&at=ishtml&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'\', 0);" class="tooltips badge badge-'.(!$t['ishtml'] ? 'no' : 'yes').'"><i class="fa fa-'.(!$t['ishtml'] ? 'times' : 'check').'"></i></a>';
				if ($t['type'] != 1) {
					$t['is_content_html'] = '<a href="javascript:;" onclick="dr_ajax_open_close(this, \'?m=admin&c=category&a=public_show_edit&at=content_ishtml&catid='.$t['catid'].'&menuid='.$this->input->get('menuid').'\', 0);" class="tooltips badge badge-'.(!$t['content_ishtml'] ? 'no' : 'yes').'"><i class="fa fa-'.(!$t['content_ishtml'] ? 'times' : 'check').'"></i></a>';
				}
			}
			if (isset($this->cat_config['list_field']) && $this->cat_config['list_field']) {
				foreach ($this->cat_config['list_field'] as $i => $tt) {
					if ($tt['use']) {
						$t[$i . '_html'] = dr_list_function($tt['func'], $t[$i], [], $t);
					}
				}
			}
			if ($t['child']) {
				$pcats[] = $t['catid'];
				$t['spacer'] = $this->_get_spacer($t['arrparentid']).'<a href="javascript:dr_tree_data('.$t['catid'].');" class="blue select-cat-'.$t['catid'].'">[+]</a>&nbsp;';
			} else {
				$t['spacer'] = $this->_get_spacer($t['arrparentid']);
			}

			$t['class'] = 'dr_catid_'.$t['catid']. ' dr_pid_'.$t['parentid'];
			$arr = explode(',', $t['arrparentid']);
			if ($arr) {
				foreach ($arr as $a) {
					$t['class'].= ' dr_pid_'.$a;
				}
			}
			extract($t);
			eval("\$nstr = \"$list\";");
			$tree.= $nstr;
		}

		return [$head, $tree, $pcats, $tcats];
	}
	public function public_list_index() {
		$pid = intval($this->input->get('pid'));
		list($cat_head, $cat_list, $pcats, $tcats) = $this->_get_tree_list($this->cat_data($pid));
		dr_json(1, $cat_list, json_encode($tcats));
	}
	// 替换空格填充符号
	protected function _get_spacer($str) {
		$rt = '';
		$num = substr_count((string)$str, ',') * 2;
		if ($num) {
			for ($i = 0; $i < $num; $i ++) {
				$rt.= '&nbsp;&nbsp;&nbsp;';
			}
		}
		return $rt;
	}
	/**
	 * 获取菜单数据
	 */
	public function cat_data($pid) {
		return $this->db->select(array('siteid'=>$this->siteid, 'parentid'=>$pid, 'module'=>'content'),'*','','listorder ASC,catid ASC');
	}
	// 统计栏目
	public function public_ctotal() {
		$rt = '';
		$where = array();
		if (IS_POST) {
			$catids = dr_string2array($this->input->post('catid'));
			if ($catids) {
				foreach ($catids as $t) {
					list($catid, $modelid) = explode('-', $t);
					if ($catid && $modelid) {
						$data = $this->db->get_one(array('catid'=>$catid), 'arrchildid');
						$this->content_db->set_model($modelid);
						$num = $this->content_db->count('catid in ('.$data['arrchildid'].')');
						$rt.= '$(".cat-total-'.$catid.'").html("'.L('（'.$num.'）').'");';
					}
				}
			}
		}
		dr_json(1, $rt);
	}
	/**
	 * 添加栏目
	 */
	public function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			$info['type'] = intval($this->input->post('type'));
			if(!$info['type']) {
				if(!$info['modelid']) dr_json(0, L('select_model'), array('field' => 'modelid'));
			}
			if ($info['parentid']) {
				$pcat = $this->db->get_one(array('catid'=>$info['parentid']));
				if (!$pcat) {
					dr_json(0, L('父栏目不存在'));
				}
				if ($pcat && $info['type'] != 2 && $pcat['type'] == 2) {
					dr_json(0, L('父级栏目是外部地址类型，下级栏目只能选择外部地址'));
				}
				$modelid = dr_cat_value($info['parentid'], 'modelid');
				if ($modelid) {
					$this->content_db->set_model($modelid);
					$total = $this->content_db->count(array('catid'=>$info['parentid']));
					if ($total) {
						dr_json(0, L('目标栏目【'.dr_cat_value($info['parentid'], 'catname').'】存在内容数据，无法作为父栏目'));
					}
				}
			}
			if(!$this->input->post('addtype')) {
				$info['catname'] = safe_replace($info['catname']);
				$info['catname'] = str_replace(array('%'),'',$info['catname']);
				if(!$info['catname']) dr_json(0, L('input_catname'), array('field' => 'catname'));
				if(!$info['catdir']) dr_json(0, L('input_dirname'), array('field' => 'catdir'));
				$rt = $this->check_dirname(0, $info['parentid'], $info['catdir']);
				if (!$rt['code']) {
					dr_json(0, $rt['msg'], array('field' => 'catdir'));
				}
				$info['type']==2 && !$info['url'] && dr_json(0, L('input_linkurl'), array('field' => 'url'));
			}
			!$info['type'] && $info['url'] && !preg_match('/http(s?):\/\/(.+)\/$/i', $info['url']) && dr_json(0, L('domain_end_string'), array('field' => 'url'));
			
			$info['siteid'] = $this->siteid;
			$info['module'] = 'content';
			$setting['pagesize'] = (int)$setting['pagesize'];
			$setting['maxsize'] = (int)$setting['maxsize'];
			$setting['maxsize'] && $setting['maxsize'] > 20 && dr_json(0, L('列表最大分页限制不能大于20'), array('field' => 'maxsize'));
			if($info['type']!=2) {
				if(!$setting['template_list']) dr_json(0, L('template_setting'), array('field' => 'template_list'));
				//栏目生成静态配置
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $this->input->post('category_html_ruleid');
				} else {
					$setting['category_ruleid'] = $this->input->post('category_php_ruleid');
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
			
			if(!$this->input->post('addtype')) {
				$info['letter'] = $this->pinyin->result($info['catname']);
				$catid = $this->db->insert($info, true);
				$systeminfo && $this->db->update($systeminfo,array('catid'=>$catid,'siteid'=>$this->siteid));
				$this->update_priv($catid, $this->input->post('priv_roleid'));
				$this->update_priv($catid, $this->input->post('priv_groupid'),0);
				$info['catid'] = $catid;
				$this->repair_sync($info);
				unset($info['catid']);
				$this->repair_cat($catid);
				//更新附件状态
				if($info['image'] && SYS_ATTACHMENT_STAT) {
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_update($info['image'],'catid-'.$catid,1);
				}
				if(SYS_ATTACHMENT_STAT) {
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_update('','catid-'.$catid,2);
				}
			} else {//批量添加
				if (!$this->input->post('batch_add')) dr_json(0, L('input_catname'), array('field' => 'catname', 'batch' => 'batch'));
				$batch_adds = explode(PHP_EOL, $this->input->post('batch_add'));
				$count = 0;
				foreach ($batch_adds as $_v) {
					if(trim($_v)=='') continue;
					list($name, $dir) = explode('|', $_v);
					$info['catname'] = trim((string)$name);
					if(!$info['catname']) dr_json(0, L('input_catname'), array('field' => 'catname', 'batch' => 'batch'));
					$info['letter'] = $this->pinyin->result($info['catname']);
					$info['catdir'] = trim((string)$dir) ? trim((string)$dir) : trim((string)$info['letter']);
					$cf = $this->check_dirname(0, $info['parentid'], $info['catdir']);
					$catid = $this->db->insert($info, true);
					$systeminfo && $this->db->update($systeminfo,array('catid'=>$catid,'siteid'=>$this->siteid));
					$this->update_priv($catid, $this->input->post('priv_roleid'));
					$this->update_priv($catid, $this->input->post('priv_groupid'),0);
					$this->repair_cat($catid);
					//更新附件状态
					if($info['image'] && SYS_ATTACHMENT_STAT) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_update($info['image'],'catid-'.$catid,1);
					}
					if(SYS_ATTACHMENT_STAT) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_update('','catid-'.$catid,2);
					}
					if (!$cf['code']) {
						// 重复验证
						$infocf['catdir'] = $info['catdir'].$catid;
						$this->db->update($infocf,array('catid'=>$catid,'siteid'=>$this->siteid));
					}
					$count ++;
				}
			}
			if ($this->is_link_update) {
				dr_json(1, ($this->input->post('addtype') ? L('批量添加'.$count.'个栏目') : L('operation_success')).'<script type="text/javascript">Dialog.confirm("'.($this->input->post('addtype') ? L('批量添加'.$count.'个栏目！') : '').L("需要更新栏目后才会生效，现在更新吗？").'",function() {iframe_show("'.L('更新栏目').'","?m=admin&c=category&a=public_repair&pc_hash="+pc_hash,"500px","300px","load");});</script>');
			} else {
				dr_json(1, ($this->input->post('addtype') ? L('批量添加'.$count.'个栏目！') : '').L('正在自动更新栏目缓存，请等待...').'<script type="text/javascript">dr_link_ajax_url(\'?m=admin&c=category&a=public_repair\');</script>');
			}
		} else {
			$show_header = $show_dialog = true;
			$catid = 0;
			$parentid = intval($this->input->get('parentid'));
			$image = '';
			//获取站点模板信息
			pc_base::load_app_func('global');
			$page = max(0, intval($this->input->get('page')));
			
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$show_validator = '';
			if($parentid) {
				$r = $this->db->get_one(array('catid'=>$parentid));
				if($r) extract($r,EXTR_SKIP);
				$setting = string2array($setting);
			} else {
				$setting = array('ishtml'=>'', 'category_ruleid'=>'', 'content_ishtml'=>'', 'show_ruleid'=>'', 'create_to_html_root'=>'', 'template_list'=>$this->input->get('s')==0 ? '' : dr_site_info('default_style', get_siteid()));
			}
			
			pc_base::load_sys_class('form','',0);
			$type = $this->input->get('s');
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form(-1);
			$forminfos = $content_form->get();
 			$formValidator = $content_form->formValidator;
			if($type==0) {
				$exists_model = false;
				$models = getcache('model','commons');	
				foreach($models as $_m) {
					if($this->siteid == $_m['siteid']) {
						$exists_model = true;
						break;
					}
				}
				if(!$exists_model) dr_admin_msg(0,L('please_add_model'),'','',1);
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
		if(IS_POST) {
			$catid = intval($this->input->post('catid'));
			$data = $this->db->get_one(array('catid'=>$catid));
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			if(!$info['catname']) dr_json(0, L('input_catname'), array('field' => 'catname'));
			if(!$info['catdir']) dr_json(0, L('input_dirname'), array('field' => 'catdir'));
			$rt = $this->check_dirname($catid, $info['parentid'], $info['catdir']);
			if (!$rt['code']) {
				dr_json(0, $rt['msg'], array('field' => 'catdir'));
			}
			$this->input->post('type')==2 && !$info['url'] && dr_json(0, L('input_linkurl'), array('field' => 'url'));
			!$info['type'] && $info['url'] && !preg_match('/http(s?):\/\/(.+)\/$/i', $info['url']) && dr_json(0, L('domain_end_string'), array('field' => 'url'));
			if ($info['parentid']) {
				$pcat = $this->db->get_one(array('catid'=>$info['parentid']));
				if (!$pcat) {
					dr_json(0, L('父栏目不存在'));
				}
				if ($pcat && $info['type'] != 2 && $pcat['type'] == 2) {
					dr_json(0, L('父级栏目是外部地址类型，下级栏目只能选择外部地址'));
				}
				$modelid = dr_cat_value($info['parentid'], 'modelid');
				if ($modelid) {
					$this->content_db->set_model($modelid);
					$total = $this->content_db->count(array('catid'=>$info['parentid']));
					if ($total) {
						dr_json(0, L('目标栏目【'.dr_cat_value($info['parentid'], 'catname').'】存在内容数据，无法作为父栏目'));
					}
				}
			}
			//上级栏目不能是自身  ---也不能是自己的子栏目
			$arrchildid = $this->db->get_one(array('catid'=>$catid), 'arrchildid');
			$arrchildid_arr = explode(',',$arrchildid['arrchildid']);
			if(dr_in_array($info['parentid'],$arrchildid_arr)){
				dr_json(0, L('operation_failure'));
			}
			$modelid = dr_cat_value($catid, 'modelid');
			if ($modelid) {
				$this->content_db->set_model($modelid);
				$where = "catid IN(".$arrchildid['arrchildid'].")";
				$total = $this->content_db->count($where);
				if ($total && $info['disabled']) {
					dr_json(0, L('当前栏目存在内容数据，无法禁用'));
				}
			}
			$setting['pagesize'] = (int)$setting['pagesize'];
			$setting['maxsize'] = (int)$setting['maxsize'];
			$setting['maxsize'] && $setting['maxsize'] > 20 && dr_json(0, L('列表最大分页限制不能大于20'), array('field' => 'maxsize'));
			//栏目生成静态配置
			if($this->input->post('type') != 2) {
				if(!$setting['template_list']) dr_json(0, L('template_setting'), array('field' => 'template_list'));
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $this->input->post('category_html_ruleid');
				} else {
					$setting['category_ruleid'] = $this->input->post('category_php_ruleid');
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
			$info['catname'] = safe_replace($info['catname']);
			$info['catname'] = str_replace(array('%'),'',$info['catname']);
			$info['letter'] = $this->pinyin->result($info['catname']);
			
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
				$this->repair_categorys = $this->db->select(array('siteid'=>$this->siteid, 'module'=>'content'), '*', '', 'listorder ASC, catid ASC', '', 'catid');
				$idstr = $this->get_arrchildid($catid);
				if(!empty($idstr)){
					$arr = $this->db->select(array('catid'=>explode(',', $idstr)), 'catid,setting');
					if(!empty($arr)){
						foreach ($arr as $v){
							$new_setting = array2string(
							array_merge(string2array($v['setting']), array('template_list' => $setting['template_list'],'category_template' => $setting['category_template'],'list_template' =>  $setting['list_template'],'show_template' =>  $setting['show_template']))
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
			$systeminfo && $this->db->update($systeminfo,array('catid'=>$catid,'siteid'=>$this->siteid));
			$this->update_priv($catid, $this->input->post('priv_roleid'));
			$this->update_priv($catid, $this->input->post('priv_groupid'),0);
			if ($info['parentid'] != $data['parentid']) {
				$info['catid'] = $catid;
				$this->repair_sync($info);
			}
			$this->repair_cat($catid);
			//更新附件状态
			if($info['image'] && SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($info['image'],'catid-'.$catid,1);
			}
			if(SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update('','catid-'.$catid,2);
			}
			if ($this->is_link_update) {
				dr_json(1, L('operation_success').'<script type="text/javascript">Dialog.confirm("'.L("需要更新栏目后才会生效，现在更新吗？").'",function() {iframe_show("'.L('更新栏目').'","?m=admin&c=category&a=public_repair&pc_hash="+pc_hash,"500px","300px","load");});</script>');
			} else {
				dr_json(1, L('正在自动更新栏目缓存，请等待...').'<script type="text/javascript">dr_link_ajax_url(\'?m=admin&c=category&a=public_repair\');</script>');
			}
		} else {
			$show_header = $show_dialog = true;
			//获取站点模板信息
			pc_base::load_app_func('global');
			$page = max(0, intval($this->input->get('page')));
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			
			
			$show_validator = $catid = '';
			$catid = intval($this->input->get('catid'));
			pc_base::load_sys_class('form','',0);
			$data = $this->db->get_one(array('catid'=>$catid));
			if($data) extract($data);
			$setting = string2array($setting);
			
			$this->priv_db = pc_base::load_model('category_priv_model');
			$this->privs = $this->priv_db->select(array('catid'=>$catid));
			
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form(-1);
			$forminfos = $content_form->get($data);
			$formValidator = $content_form->formValidator;
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
	 * 设置栏目属性
	 */
	function config_add() {
		$sysfield = [
			'listorder' => ['排序', '设置栏目的排列顺序'],
			'ismenu' => ['导航显示', '前端循环调用不会显示'],
			'disabled' => ['可用', '设置栏目是否可用的快捷开关'],
			'iscatpos' => ['显示', '前端栏目面包屑导航调用不会显示，您现在的位置不显示'],
			'isleft' => ['左侧', '前端栏目调用左侧不会显示'],
			'id' => ['编号', '显示栏目的id号'],
			'typename' => ['类型', '显示栏目的类型，有：单页、模型、外链'],
			'modelname' => ['模型', '显示所属模型的名称'],
			'html' => ['静态', '设置栏目是否生成静态的开关'],
		];

		if (IS_POST) {
			$cat_config = $this->input->post('data');
			$this->cache->set_file('category', $cat_config);
			dr_json(1, L('operation_success'));
		}
		$data = $this->cat_config;
		// 主表字段
		$field = $this->field_db->select(array('modelid'=>'-1', 'disabled'=>0),'*','','listorder ASC,fieldid ASC');
		$field = dr_list_field_value($data['list_field'], [], $field);
		include $this->admin_tpl('category_config');
	}
	/**
	 * 保存显示状态
	 */
	function public_show_edit() {
		$catid = (int)$this->input->get('catid');
		$row = $this->db->get_one(array('catid'=>$catid));
		if (!$row) {
			dr_json(0, L('栏目数据不存在'));
		}
		$at = $this->input->get('at');
		if ($at == 'disabled') {
			// 可用状态
			$v = $row['disabled'] ? 0 : 1;
			$modelid = dr_cat_value($catid, 'modelid');
			if ($v && $modelid) {
				$this->content_db->set_model($modelid);
				$where = "catid IN(".$row['arrchildid'].")";
				$total = $this->content_db->count($where);
				if ($total) {
					dr_json(0, L('当前栏目存在内容数据，无法禁用'));
				}
			}
			$this->db->update(array('disabled'=>$v),array('catid'=>$catid));
			// 自动更新缓存
			$this->cache();
			dr_json(1, L($v ? '设置为禁用状态' : '设置为可用状态'), array('value' => $v));
		} elseif ($at == 'iscatpos') {
			// 显示状态
			$row['setting'] = string2array($row['setting']);
			$row['setting']['iscatpos'] = $row['setting']['iscatpos'] ? 0 : 1;
			$this->db->update(array('setting'=>array2string($row['setting'])),array('catid'=>$catid));
			// 自动更新缓存
			$this->cache();
			dr_json(1, L($row['setting']['iscatpos'] ? '设置为显示状态' : '设置为隐藏状态'), array('value' => $row['setting']['iscatpos']));
		} elseif ($at == 'isleft') {
			// 显示状态
			$row['setting'] = string2array($row['setting']);
			$row['setting']['isleft'] = $row['setting']['isleft'] ? 0 : 1;
			$this->db->update(array('setting'=>array2string($row['setting'])),array('catid'=>$catid));
			// 自动更新缓存
			$this->cache();
			dr_json(1, L($row['setting']['isleft'] ? '设置为显示状态' : '设置为隐藏状态'), array('value' => $row['setting']['isleft']));
		} elseif ($at == 'ishtml') {
			// 显示状态
			$this->urlrule_db = pc_base::load_model('urlrule_model');
			$row['setting'] = string2array($row['setting']);
			$row['setting']['ishtml'] = $row['setting']['ishtml'] ? 0 : 1;
			$urlrule = $this->urlrule_db->get_one(array('module'=>'content', 'file'=>'category', 'ishtml'=>$row['setting']['ishtml']), '*', 'urlruleid asc');
			$row['setting']['category_ruleid'] = $urlrule['urlruleid'];
			$this->db->update(array('setting'=>array2string($row['setting'])),array('catid'=>$catid));
			// 自动更新缓存
			$this->cache();
			dr_json(1, L($row['setting']['ishtml'] ? '设置为栏目静态模式' : '设置为栏目动态模式'), array('value' => $row['setting']['ishtml']));
		} elseif ($at == 'content_ishtml') {
			// 显示状态
			$this->urlrule_db = pc_base::load_model('urlrule_model');
			$row['setting'] = string2array($row['setting']);
			$row['setting']['content_ishtml'] = $row['setting']['content_ishtml'] ? 0 : 1;
			$urlrule = $this->urlrule_db->get_one(array('module'=>'content', 'file'=>'show', 'ishtml'=>$row['setting']['content_ishtml']), '*', 'urlruleid asc');
			$row['setting']['show_ruleid'] = $urlrule['urlruleid'];
			$this->db->update(array('setting'=>array2string($row['setting'])),array('catid'=>$catid));
			// 自动更新缓存
			$this->cache();
			dr_json(1, L($row['setting']['content_ishtml'] ? '设置为内容静态模式' : '设置为内容动态模式'), array('value' => $row['setting']['content_ishtml']));
		} else {
			// 显示状态
			$v = $row['ismenu'] ? 0 : 1;
			$this->db->update(array('ismenu'=>$v),array('catid'=>$catid));
			// 自动更新缓存
			$this->cache();
			dr_json(1, L($v ? '设置为显示状态' : '设置为隐藏状态'), array('value' => $v));
		}
	}
	/**
	 * 排序
	 */
	public function listorder() {
		// 查询数据
		$catid = (int)$this->input->get('catid');
		$value = (int)$this->input->get('value');
		$row = $this->db->get_one(array('catid'=>$catid));
		if (!$row) {
			dr_json(0, L('数据#'.$catid.'不存在'));
		} elseif ($row['listorder'] == $value) {
			dr_json(1, L('没有变化'));
		}
		$this->db->update(array('listorder'=>$value),array('catid'=>$catid));
		// 自动更新缓存
		$this->cache();
		dr_json(1, L('operation_success'));
	}
	/**
	 * 删除栏目
	 */
	public function delete() {

		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('所选栏目不存在'));
		}

		// 筛选栏目id
		$catid = '';
		foreach ($ids as $id) {
			$catid.= ','.(dr_cat_value($id, 'arrchildid') ? dr_cat_value($id, 'arrchildid') : $id);
		}

		$catid = explode(',', trim($catid, ','));
		$catid = array_flip(array_flip($catid));

		foreach ($catid as $id) {
			$modelid = dr_cat_value($id, 'modelid');
			if ($modelid) {
				$this->content_db->set_model($modelid);
				$num = $this->content_db->count(array('catid'=>$id));
				if ($num) {
					dr_json(0, L('目标栏目【'.dr_cat_value($id, 'catname').'】内容存在'.$num.'条数据，无法删除'));
				}
			}
		}

		foreach ($catid as $id) {
			$sethtml = dr_cat_value($id, 'sethtml');
			$html_root = SYS_HTML_ROOT;
			if($sethtml) $html_root = '';
			$setting = dr_string2array(dr_cat_value($id, 'setting'));
			$ishtml = $setting['ishtml'];
			$this->url = pc_base::load_app_class('url', 'content');
			$modelid = dr_cat_value($id, 'modelid');
			if($ishtml) {
				$fileurl = $html_root.'/'.$this->url->category_url($id, 1);
				if($this->siteid != 1) {
					$fileurl = dr_site_info('dirname', $this->siteid).$fileurl;
				}
				$fileurl != '/\/' && dr_dir_delete(CMS_PATH.$fileurl, true);
				if(dr_site_info('mobilehtml', $this->siteid)==1) {
					$mobilefileurl = SYS_MOBILE_ROOT.$fileurl;
					$fileurl != '/\/' && dr_dir_delete(CMS_PATH.$mobilefileurl, true);	
				}
			}
			$this->delete_child($id, $modelid);
			$this->db->delete(array('catid'=>$id));
			//删除附件
			$this->attachment_db = pc_base::load_model('attachment_model');
			$this->attachment_db->api_delete('catid-'.$id);
			if ($modelid != 0) {
				$this->delete_category_content($id, $modelid);
			}
		}

		// 自动更新缓存
		$this->cache();

		dr_json(1, L('operation_success'));
	}
	/**
	 * 递归删除栏目
	 * @param $catid 要删除的栏目id
	 */
	private function delete_child($catid, $modelid) {
		$catid = intval($catid);
		if (empty($catid)) return false;
		$sethtml = dr_cat_value($catid, 'sethtml');
		$html_root = SYS_HTML_ROOT;
		if($sethtml) $html_root = '';
		$setting = dr_string2array(dr_cat_value($catid, 'setting'));
		$ishtml = $setting['ishtml'];
		$this->url = pc_base::load_app_class('url', 'content');
		$list = $this->db->select(array('parentid'=>$catid));
		foreach($list as $r) {
			if($ishtml) {
				$fileurl = $html_root.'/'.$this->url->category_url($r['catid'], 1);
				if($this->siteid != 1) {
					$fileurl = dr_site_info('dirname', $this->siteid).$fileurl;
				}
				$fileurl != '/\/' && dr_dir_delete(CMS_PATH.$fileurl, true);
				if(dr_site_info('mobilehtml', $this->siteid)==1) {
					$mobilefileurl = SYS_MOBILE_ROOT.$fileurl;
					$fileurl != '/\/' && dr_dir_delete(CMS_PATH.$mobilefileurl, true);	
				}
			}
			$this->delete_child($r['catid'], $modelid);
			$this->db->delete(array('catid'=>$r['catid']));
			//删除附件
			$this->attachment_db = pc_base::load_model('attachment_model');
			$this->attachment_db->api_delete('catid-'.$r['catid']);
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
		$sethtml = dr_cat_value($catid, 'sethtml');
		$html_root = SYS_HTML_ROOT;
		if($sethtml) $html_root = '';
		$setting = dr_string2array(dr_cat_value($catid, 'setting'));
		$content_ishtml = $setting['content_ishtml'];
		$this->content_db->set_model($modelid);
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
		$result = $this->content_db->select(array('catid'=>$catid), 'id');
		if (is_array($result) && !empty($result)) {
			foreach ($result as $key=>$val) {
				if($content_ishtml && !$val['islink']) {
					list($urls) = $this->url->show($val['id'], 0, $catid, $val['inputtime'], dr_value($modelid, $val['id'], 'prefix'));
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
				$this->content_db->delete_content($val['id'],$catid);
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
	// 批量设置动态模式
	public function public_phpall_edit() {

		$this->urlrule_db = pc_base::load_model('urlrule_model');
		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('所选栏目不存在'));
		}
		$type = intval($this->input->get('type'));

		foreach ($ids as $id) {

			$row = $this->db->get_one(array('catid'=>$id));
			if (!$row) {
				dr_json(0, L('栏目数据不存在'));
			}

			$row['setting'] = dr_string2array($row['setting']);
			if ($row['type'] != 2) {
				if ($type && !$row['type']) {
					$row['setting']['content_ishtml'] = 0;
					$urlrule = $this->urlrule_db->get_one(array('module'=>'content', 'file'=>'show', 'ishtml'=>$row['setting']['content_ishtml']), '*', 'urlruleid asc');
					$row['setting']['show_ruleid'] = $urlrule['urlruleid'];
				} else {
					$row['setting']['ishtml'] = 0;
					$urlrule = $this->urlrule_db->get_one(array('module'=>'content', 'file'=>'category', 'ishtml'=>$row['setting']['ishtml']), '*', 'urlruleid asc');
					$row['setting']['category_ruleid'] = $urlrule['urlruleid'];
				}
				$this->db->update(array('setting'=>dr_array2string($row['setting'])),array('catid'=>$id));
			}
		}

		// 自动更新缓存
		$this->cache();

		dr_json(1, L('operation_success'));
	}
	// 批量设置静态模式
	public function public_htmlall_edit() {

		$this->urlrule_db = pc_base::load_model('urlrule_model');
		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('所选栏目不存在'));
		}
		$type = intval($this->input->get('type'));

		foreach ($ids as $id) {

			$row = $this->db->get_one(array('catid'=>$id));
			if (!$row) {
				dr_json(0, L('栏目数据不存在'));
			}

			$row['setting'] = dr_string2array($row['setting']);
			if ($row['type'] != 2) {
				if ($type && !$row['type']) {
					$row['setting']['content_ishtml'] = 1;
					$urlrule = $this->urlrule_db->get_one(array('module'=>'content', 'file'=>'show', 'ishtml'=>$row['setting']['content_ishtml']), '*', 'urlruleid asc');
					$row['setting']['show_ruleid'] = $urlrule['urlruleid'];
				} else {
					$row['setting']['ishtml'] = 1;
					$urlrule = $this->urlrule_db->get_one(array('module'=>'content', 'file'=>'category', 'ishtml'=>$row['setting']['ishtml']), '*', 'urlruleid asc');
					$row['setting']['category_ruleid'] = $urlrule['urlruleid'];
				}
				$this->db->update(array('setting'=>dr_array2string($row['setting'])),array('catid'=>$id));
			}
		}

		// 自动更新缓存
		$this->cache();

		dr_json(1, L('operation_success'));
	}
	// 生成栏目静态
	public function public_scjt_edit() {

		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('没有选择任何栏目'));
		}

		dr_json(1, '?m=content&c=create_html&a=category&catids='.implode(',', $ids).'&dosubmit=1&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token());
	}
	/**
	 * 更新缓存
	 */
	public function cache() {
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->cache_api->cache('category');
		return true;
	}
	// 更新栏目缓存配置
	public function public_cache() {
		$url = '?m=admin&c=category&a=public_cache';
		$page = (int)$this->input->get('page');
		$psize = $this->max_category; // 每页处理的数量
		$total = (int)$this->input->get('total');

		if (!$page) {
			// 修复栏目数据
			$total = $this->repair_data($psize, 'cache');
			dr_dir_delete(CACHE_PATH.'caches_module/category-'.$this->siteid.'-data/');
			if (!$total) {
				html_msg(1, L('更新完成'));
			}
			$this->cache->set_auth_data(
				'category-cache-cache',
				[],
				$this->siteid
			);
			$this->cache->set_auth_data(
				'category-cache-dir',
				[],
				$this->siteid
			);
			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数

		$cats = $this->cache->get_auth_data('category-cache-page', $this->siteid);
		if (!$cats) {
			html_msg(0, L('临时数据读取失败'));
		} elseif (!isset($cats[$page-1]) || $page > $tpage) {
			$this->cache->set_file(
				'cache',
				$this->cache->get_auth_data('category-cache-cache', $this->siteid),
				'module/category-'.$this->siteid.'-data'
			);
			$this->cache->set_file(
				'dir',
				$this->cache->get_auth_data('category-cache-dir', $this->siteid),
				'module/category-'.$this->siteid.'-data'
			);
			$this->cache();
			$this->cache->del_auth_data('category-cache-cache', $this->siteid);
			$this->cache->del_auth_data('category-cache-page', $this->siteid);
			$this->cache->del_auth_data('category-cache-data', $this->siteid);
			$this->cache->del_auth_data('category-cache-dir', $this->siteid);
			html_msg(1, L('更新完成'));
		}

		$cat = $this->cache->get_auth_data('category-cache-data', $this->siteid);
		$this->cat_init([
			'cat_dir' => $this->cache->get_auth_data('category-cache-dir', $this->siteid),
			'cat_cache' => $this->cache->get_auth_data('category-cache-cache', $this->siteid),
			'repair_categorys' => $cat,
		]);
		foreach ($cats[$page-1] as $v) {
			$cat[$v['catid']] && $this->get_cache($v['catid']);
		}

		$this->cache->set_auth_data(
			'category-cache-cache',
			$this->get_cat_cache(),
			$this->siteid
		);
		$this->cache->set_auth_data(
			'category-cache-dir',
			$this->get_cat_dir(),
			$this->siteid
		);

		html_msg( 1, L('正在更新栏目配置【'.$tpage.'/'.$page.'】...'),
			$url.'&total='.$total.'&page='.($page+1)
		);
	}
	// 批量更新栏目关系
	public function public_repair() {
		$url = '?m=admin&c=category&a=public_repair';
		$page = (int)$this->input->get('page');
		$psize = $this->max_category; // 每页处理的数量
		$total = (int)$this->input->get('total');

		if (!$page) {
			// 修复栏目数据
			$total = $this->repair_data($psize, 'repair');
			if (!$total) {
				html_msg(1, L('更新完成'));
			}
			dr_dir_delete(CACHE_PATH.'caches_module/category-'.$this->siteid.'-child');
			dr_dir_delete(CACHE_PATH.'caches_module/category-'.$this->siteid.'-data');
			dr_dir_delete(CACHE_PATH.'caches_module/category-'.$this->siteid.'-min');
			html_msg(1, L('正在执行中...'), $url.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数
		$cats = $this->cache->get_auth_data('category-repair-page', $this->siteid);
		if (!$cats) {
			html_msg(0, L('临时数据读取失败'));
		} elseif (!isset($cats[$page-1]) || $page > $tpage) {
			// 更新完成,新生成顶级分类的关系
			$this->repair_top_nextids();
			$this->cache->del_auth_data('category-repair-page', $this->siteid);
			$this->cache->del_auth_data('category-repair-data', $this->siteid);
			$this->cache->del_auth_data('category-repair-dir', $this->siteid);
			html_msg(1, L('正在执行中...'), '?m=admin&c=category&a=public_cache');
		}

		foreach ($cats[$page-1] as $cat) {
			$this->repair_sync($cat);
		}

		html_msg(1, L('正在更新栏目关系【'.$tpage.'/'.$page.'】...'),
			$url.'&total='.$total.'&page='.($page+1)
		);
	}
	private function cat_init($cat) {
		$this->cat_dir = $cat['cat_dir'] ? $cat['cat_dir'] : [];
		$this->cat_cache = $cat['cat_cache'] ? $cat['cat_cache'] : [];
		$this->repair_categorys = $cat['repair_categorys'];
		return $this;
	}
	private function get_cat_dir() {
		return $this->cat_dir;
	}
	private function get_cat_cache() {
		return $this->cat_cache;
	}
	// 修复栏目数据格式分组存储
	private function repair_data($psize, $mid) {
		$_data = $this->db->select(array('siteid'=>$this->siteid, 'module'=>'content', 'disabled'=>0),'*','','listorder ASC,catid ASC');
		if (!$_data) {
			return 0;
		}

		// 全部栏目数据
		$data = $dir = $rt = array();
		foreach ($_data as $t) {
			if($mid == 'cache' && $t['type'] == 0) {
				$modelid = $t['modelid'];
				$this->content_db->set_model($modelid);
				$ctotal = $this->db->get_one(array('catid'=>$t['catid']), 'arrchildid');
				$number = $this->content_db->count('catid in ('.($ctotal['arrchildid'] ? $ctotal['arrchildid'] : $t['catid']).')');
				$this->db->update(array('items'=>$number),array('catid'=>$t['catid']));
			}
			$t['setting'] = dr_string2array($t['setting']);
			$dir[$t['catdir']] = $t['catid'];
			$rt[$t['catid']] = [
				'catid' => $t['catid'],
				'parentid' => $t['parentid'],
				'arrparentid' => $t['arrparentid'],
				'child' => $t['child'],
				'arrchildid' => $t['arrchildid'],
				'catdir' => $t['catdir'],
				'parentdir' => $t['parentdir'],
			];
			$data[$t['catid']] = $t;
		}

		$this->cache->set_auth_data('category-'.$mid.'-page', array_chunk($rt, $psize), $this->siteid);
		$this->cache->set_auth_data('category-'.$mid.'-data', $data, $this->siteid);
		$this->cache->set_auth_data('category-'.$mid.'-dir', $dir, $this->siteid);

		return count($rt);
	}
	// 同步修复栏目
	private function repair_sync($cat) {
		$html_root = SYS_HTML_ROOT;
		if (!$this->repair_categorys) {
			$_data = $this->db->select(array('siteid'=>$this->siteid, 'module'=>'content', 'disabled'=>0),'*','','listorder ASC,catid ASC');
			if (!$_data) {
				return;
			}
			// 全部栏目数据
			$this->repair_categorys = array();
			foreach ($_data as $t) {
				$t['setting'] = dr_string2array($t['setting']);
				$this->repair_categorys[$t['catid']] = [
					'catid' => $t['catid'],
					'catname' => $t['catname'],
					'parentid' => (int)$t['parentid'],
					'arrparentid' => $t['arrparentid'],
					'child' => $t['child'],
					'arrchildid' => $t['arrchildid'],
					'catdir' => $t['catdir'],
					'parentdir' => $t['parentdir'],
					'type' => $t['type'],
					'url' => $t['url'],
					'ishtml' => $t['setting']['ishtml'],
					'create_to_html_root' => $t['setting']['create_to_html_root'],
				];
			}
		}

		$this->repair_categorys[$cat['catid']]['arrparentid'] = $this->get_arrparentid($cat['catid']);
		$this->repair_categorys[$cat['catid']]['arrchildid'] = $this->get_arrchildid($cat['catid']);
		$this->repair_categorys[$cat['catid']]['child'] = is_numeric($this->repair_categorys[$cat['catid']]['arrchildid']) ? 0 : 1;
		$this->repair_categorys[$cat['catid']]['parentdir'] = $this->get_parentdir($cat['catid']);

		$this->sethtml = $this->repair_categorys[$cat['catid']]['create_to_html_root'];
		//检查是否生成到根目录
		$this->get_sethtml($cat['catid']);
		$sethtml = $this->sethtml ? 1 : 0;

		$url = $this->update_url($cat['catid']);
		if($this->repair_categorys[$cat['catid']]['ishtml']) {
			//生成静态时
			if(!preg_match('/^(http|https):\/\//i', $url)) {
				$url = $sethtml ? '/'.$url : $html_root.'/'.$url;
			}
		} else {
			//不生成静态时
			if($this->siteid!=1) {
				$url = (string)dr_site_info('domain', $this->siteid).$url;
			} else {
				$url = APP_PATH.$url;
			}
		}

		if($this->repair_categorys[$cat['catid']]['type']!=2 && $this->repair_categorys[$cat['catid']]['url']!=$url) $this->db->update(array('url'=>$url), array('catid'=>$cat['catid']));

		$this->db->update(array(
			'parentid' => (int)$this->repair_categorys[$cat['catid']]['parentid'],
			'arrparentid' => $this->repair_categorys[$cat['catid']]['arrparentid'],
			'child' => $this->repair_categorys[$cat['catid']]['child'],
			'arrchildid' => $this->repair_categorys[$cat['catid']]['arrchildid'],
			'parentdir' => $this->repair_categorys[$cat['catid']]['parentdir'],
			'sethtml' => $sethtml,
			'letter' => $this->pinyin->result($this->repair_categorys[$cat['catid']]['catname'])
		), array('catid'=>$cat['catid']));

		// 单独存储栏目关系
		if ($this->repair_categorys[$cat['catid']]['child']) {
			$this->cache->set_file($cat['catid'], $this->_get_nextids($cat['catid']), 'module/category-'.$this->siteid.'-child');
		}
	}
	//生成顶级栏目下级
	private function repair_top_nextids() {
		$this->cache->set_file(0, $this->_get_nextids(0), 'module/category-'.$this->siteid.'-child');
	}
	/**
	 * 获取栏目的下级子栏目
	 */
	protected function _get_nextids($catid) {
		$rt = [];
		$data = $this->db->select(array('siteid'=>$this->siteid, 'parentid'=>$catid, 'module'=>'content'),'*','','listorder ASC,catid ASC');
		if ($data) {
			foreach($data as $cat) {
				$rt[] = $cat['catid'];
			}
		}
		return $rt;
	}
	// 单个栏目缓存文件
	private function get_cache($catid) {

		$cat = $this->repair_categorys[$catid];
		unset($cat['module']);

		if(!preg_match('/^(http|https):\/\//', $cat['url'])) {
			$cat['url'] = siteurl($cat['siteid']).$cat['url'];
		}

		$pid = explode(',', (string)$cat['arrparentid']);
		$cat['topid'] = isset($pid[1]) ? $pid[1] : $cat['catid'];
		$cat['catids'] = explode(',', (string)$cat['arrchildid']);

		// 统计栏目文章数量
		$cat['total'] = '-';
		if ($cat['parentid']) {
			$this->cat_dir[$cat['parentdir'].$cat['catdir']] = $cat['catid'];
		}
		$this->cat_dir[$cat['catdir']] = $cat['catid'];
		// 缓存数据
		$this->cat_cache[$cat['catid']] = [
			'catid' => $cat['catid'],
			'siteid' => $cat['siteid'],
			'url' => $cat['url'],
			'type' => $cat['type'],
			'parentid' => $cat['parentid'],
			'modelid' => $cat['modelid'],
			'catname' => $cat['catname'],
			'image' => $cat['image'],
			'arrparentid' => $cat['arrparentid'],
			'child' => $cat['child'],
			'catids' => $cat['catids'],
			'arrchildid' => $cat['arrchildid'],
			'catdir' => $cat['catdir'],
			'parentdir' => $cat['parentdir'],
			'topid' => $cat['topid'],
		];
		$this->cat_cache[$cat['catid']];
		$this->cache->set_file($cat['catid'], $cat, 'module/category-'.$this->siteid.'-data');
		$this->cache->set_file($cat['catid'], $this->cat_cache[$cat['catid']], 'module/category-'.$this->siteid.'-min');
	}
	// 修改后修复栏目
	private function repair_cat($catid) {
		// 存储单个数据
		$cat = $this->db->get_one(array('catid'=>$catid));
		$cat['setting'] = dr_string2array($cat['setting']);
		$this->repair_categorys = [
			$catid => $cat,
		];
		$this->get_cache($catid);
	}
	/**
	 * 获取父栏目是否生成到根目录
	 */
	private function get_sethtml($catid) {
		foreach($this->repair_categorys as $id => $cat) {
			if($catid==$id) {
				$parentid = $cat['parentid'];
				if($this->repair_categorys[$parentid]['sethtml']) {
					$this->sethtml = 1;
				}
				if($parentid) {
					$this->get_sethtml($parentid);
				}
			}
		}
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
	 * 获取父栏目ID列表
	 */
	private function get_arrparentid($catid, $arrparentid = '', $n = 1) {
		if($n > 100 || !is_array($this->repair_categorys) || !isset($this->repair_categorys[$catid])) return false;
		$parentid = $this->repair_categorys[$catid]['parentid'];
		$arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
		if($parentid) {
			$arrparentid = $this->get_arrparentid($parentid, $arrparentid, ++$n);
		} else {
			$this->repair_categorys[$catid]['arrparentid'] = $arrparentid;
		}
		$parentid = $this->repair_categorys[$catid]['parentid'];
		return $arrparentid;
	}

	/**
	 * 获取栏目的全部子栏目
	 */
	private function get_arrchildid($catid, $n = 1) {
		$arrchildid = $catid;
		if ($n > 100 || !is_array($this->repair_categorys) || !isset($this->repair_categorys[$catid])) {
			return $arrchildid;
		}
		$data = $this->db->select(array('parentid>'=>0, 'catid<>'=>$catid, 'parentid'=>$catid),'*','','listorder ASC,catid ASC');
		if ($data) {
			foreach($data as $cat) {
				$arrchildid .= ','.$this->get_arrchildid($cat['catid'], ++$n);
			}
		}
		return $arrchildid;
	}
	/**
	 * 获取父栏目路径
	 * @param  $catid
	 */
	function get_parentdir($catid) {
		if($this->repair_categorys[$catid]['parentid']==0) return '';
		$r = $this->repair_categorys[$catid];
		$url = $r['url'];
		$arrparentid = $r['arrparentid'];
		$create_to_html_root = $r['create_to_html_root'];
		unset($r);
		if (strpos($url, '://')===false) {
			if ($create_to_html_root) {
				return '';
			} else {
				$arrparentid = explode(',', $arrparentid);
				$arrcatdir = array();
				foreach($arrparentid as $id) {
					if($id==0) continue;
					$arrcatdir[] = $this->repair_categorys[$id]['catdir'];
				}
				return implode('/', $arrcatdir).'/';
			}
		} else {
			if ($create_to_html_root) {
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
					$arrcatdir[] = $this->repair_categorys[$id]['catdir'];
					if ($this->repair_categorys[$id]['parentdir'] == '') break;
				}
				krsort($arrcatdir);
				return implode('/', $arrcatdir).'/';
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
	 * json方式加载模板
	 */
	public function public_tpl_file_list() {
		$style = $this->input->get('style') && trim($this->input->get('style')) ? trim($this->input->get('style')) : exit(0);
		$catid = $this->input->get('catid') && intval($this->input->get('catid')) ? intval($this->input->get('catid')) : 0;
		$batch_str = $this->input->get('batch_str') ? '['.$catid.']' : '';
		if ($catid) {
			$this->categorys = dr_cat_value($catid);
			$this->categorys['setting'] = string2array($this->categorys['setting']);
		}
		pc_base::load_sys_class('form','',0);
		if($this->input->get('type')==1) {
			$html = array('page_template'=>form::select_template($style, 'content',(isset($this->categorys['setting']['page_template']) && !empty($this->categorys['setting']['page_template']) ? $this->categorys['setting']['page_template'] : 'page'),'name="setting'.$batch_str.'[page_template]"','page'));
		} else {
			$html = array('category_template'=> form::select_template($style, 'content',(isset($this->categorys['setting']['category_template']) && !empty($this->categorys['setting']['category_template']) ? $this->categorys['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'), 
				'list_template'=>form::select_template($style, 'content',(isset($this->categorys['setting']['list_template']) && !empty($this->categorys['setting']['list_template']) ? $this->categorys['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',(isset($this->categorys['setting']['show_template']) && !empty($this->categorys['setting']['show_template']) ? $this->categorys['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
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
		echo dr_array2string($html);exit();
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
			$result = $this->db->select(array($field=>'%'.$catname.'%', 'siteid'=>$this->siteid, 'child'=>0),'catid,type,catname,letter',10);
			if (CHARSET == 'gbk') {
				$result = array_iconv($result, 'gbk', 'utf-8');
			}
			echo dr_array2string($result);exit();
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
			echo dr_array2string($html);exit();
		}
	}
	/**
	 * 批量修改
	 */
	public function batch_edit() {

		if($this->input->post('dosubmit')) {
			$catid = intval($this->input->post('catid'));
			$post_setting = $this->input->post('setting');
			//栏目生成静态配置
			$infos = $info = array();
			$infos = $this->input->post('info');
			if(empty($infos)) dr_admin_msg(0,L('operation_success'));
			$this->attachment_db = pc_base::load_model('attachment_model');
			foreach ($infos as $catid=>$info) {
				$row = $this->db->get_one(array('catid'=>$catid));
				$modelid = dr_cat_value($catid, 'modelid');
				if ($modelid) {
					$this->content_db->set_model($modelid);
					$where = "catid IN(".$row['arrchildid'].")";
					$total = $this->content_db->count($where);
				}
				if ($total && $info['disabled']) {
					$info['disabled'] = 0;
				}
				$setting = dr_string2array(dr_cat_value($catid, 'setting'));
				if($this->input->post('type') != 2) {
					if($post_setting[$catid]['ishtml']) {
						$setting['category_ruleid'] = $this->input->post('category_html_ruleid')[$catid];
					} else {
						$setting['category_ruleid'] = $this->input->post('category_php_ruleid')[$catid];
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
				$info['sethtml'] = $post_setting[$catid]['create_to_html_root'];
				$info['setting'] = array2string($setting);
				
				$info['module'] = 'content';
				$info['letter'] = $this->pinyin->result($info['catname']);
				$this->db->update($info,array('catid'=>$catid,'siteid'=>$this->siteid));
			}
			$this->cache();
			dr_admin_msg(1,L('operation_success'), array('url'=>'?m=admin&c=category&a=init&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			if($this->input->post('catids')) {
				//获取站点模板信息
				pc_base::load_app_func('global');
				$template_list = template_list($this->siteid, 0);
				foreach ($template_list as $k=>$v) {
					$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
					unset($template_list[$k]);
				}
				
				$show_validator = $show_header = true;
				$catid = intval($this->input->get('catid'));
				$type = $this->input->post('type') ? intval($this->input->post('type')) : 0;
				pc_base::load_sys_class('form','',0);
				
				if(empty($this->input->post('catids'))) dr_admin_msg(0,L('illegal_parameters'));
				$batch_array = $workflows = array();
				$this->categorys = get_category($this->siteid);
				foreach ($this->categorys as $catid=>$cat) {
					if($cat['type']==$type && in_array($catid, $this->input->post('catids'))) {
						$batch_array[$catid] = $cat;
					}
				}
				if(empty($batch_array)) dr_admin_msg(0,L('please_select_category')); 
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
				$show_header = $show_dialog = true;
				$type = $this->input->get('select_type') ? intval($this->input->get('select_type')) : 0;
				
				$tree = pc_base::load_sys_class('tree');
				$category = array();
				$this->categorys = get_category($this->siteid);
				foreach($this->categorys as $catid=>$r) {
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
	// 统一设置
	public function public_batch_category() {
		$show_header = $show_dialog  = true;
		if(IS_AJAX_POST) {
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			$row = $this->db->select(array('siteid'=>$this->siteid, 'module'=>'content'));
			foreach($row as $r) {
				if ($r['modelid']) {
					$this->content_db->set_model($r['modelid']);
					$where = "catid IN(".$r['arrchildid'].")";
					$total = $this->content_db->count($where);
				}
				if ($total && $info['disabled']) {
					$disabled = 0;
				} else {
					$disabled = $info['disabled'];
				}
				$r['setting'] = dr_string2array($r['setting']);
				$r['setting']['getchild'] = $setting['getchild'];
				$r['setting']['iscatpos'] = $setting['iscatpos'];
				$r['setting']['isleft'] = $setting['isleft'];
				$this->db->update(array('ismenu'=>$info['ismenu'], 'disabled'=>$disabled, 'setting'=>dr_array2string($r['setting'])), array('catid'=>$r['catid']));
			}
			$this->cache();
			dr_json(1, L('operation_success'));
		} else {
			include $this->admin_tpl('category_batch_save');
		}
	}
	// 后台批量移动栏目
	public function public_move_edit() {

		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('所选栏目不存在'));
		}

		$topid = (int)$this->input->post('catid');
		foreach ($ids as $id) {
			if ($id == $topid) {
				dr_json(0, L('栏目上级不能为本身'));
			}
			$arrchildid = $this->db->get_one(array('catid'=>$id), 'arrchildid');
			$arrchildid_arr = explode(',',$arrchildid['arrchildid']);
			if(dr_in_array($topid,$arrchildid_arr)){
				dr_json(0, L('栏目上级不能为本身'));
			}
		}

		if ($topid) {
			// 重新获取数据
			if (!dr_cat_value($topid)) {
				dr_json(0, L('目标栏目不存在'));
			}
			$modelid = dr_cat_value($topid, 'modelid');
			if ($modelid) {
				$this->content_db->set_model($modelid);
				$total = $this->content_db->count(array('catid'=>$topid));
				if ($total) {
					dr_json(0, L('目标栏目【'.dr_cat_value($topid, 'catname').'】存在内容数据，无法作为父栏目'));
				}
			}
		}

		$i = 0;
		foreach ($ids as $id) {
			$this->db->update(array('parentid'=>$topid), array('catid'=>$id));
			$i++;
		}

		$this->cache();
		dr_json(1, L('修改'.$i.'个栏目成功，请更新栏目缓存'));
	}
	/**
	 * 批量移动文章
	 */
	public function remove() {
		if(IS_POST) {
			$this->content_check_db = pc_base::load_model('content_check_model'); 
			if(!$this->input->post('fromid')) dr_admin_msg(0,L('please_input_move_source','','content'));
			if(!$this->input->post('tocatid')) dr_admin_msg(0,L('please_select_target_category','','content'));
			$tocatid = intval($this->input->post('tocatid'));
			$modelid = dr_cat_value($tocatid, 'modelid');
			if(!$modelid) dr_admin_msg(0,L('illegal_operation','','content'));
			$fromid = array_filter($this->input->post('fromid'),"is_numeric");
			$fromid = implode(',', $fromid);
			$this->content_db->set_model($modelid);
			$this->content_db->update(array('catid'=>$tocatid),"catid IN($fromid)");
 			dr_admin_msg(1,L('operation_success'),array('url' => '?m=admin&c=category&a=remove&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
 		} else {
			$show_header = true;
			$catid = intval($this->input->get('catid'));
			$categorys = array();
 			
  			$modelid = dr_cat_value($catid, 'modelid');
  			$tree = pc_base::load_sys_class('tree');
			$this->categorys = get_category($this->siteid);
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
	//生成常用栏目选择框
	private function cat_select() {
		$this->categorys = get_category($this->siteid);
		$tree = pc_base::load_sys_class('tree');
		$str = "<option value='\$catid'>\$spacer \$catname</option>";
		$tree->init($this->categorys);
		$cat_select = '<select class="bs-select form-control"'.(dr_count($this->categorys) > 30 ? ' data-live-search="true"' : '').' name="catid">
<option value=\'0\'>顶级栏目</option>';
		$cat_select .= $tree->get_tree(0, $str);
		$cat_select .= '</select>
<script type="text/javascript"> var bs_selectAllText = \'全选\';var bs_deselectAllText = \'全删\';var bs_noneSelectedText = \'没有选择\'; var bs_noneResultsText = \'没有找到 {0}\';</script>
<link href="'.JS_PATH.'bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="'.JS_PATH.'bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function() {$(\'.bs-select\').selectpicker();});</script>';
		return $cat_select;
	}
	// 检查目录是否可用
	public function check_dirname($id, $pid, $value) {
		if (!$value) {
			return dr_return_data(0, L('目录不能为空'));
		} elseif (!preg_match('/^[a-z0-9 \_\-]*$/i', $value)) {
			return dr_return_data(0, L('目录格式不能包含特殊符号或文字'));
		} else {
			if ($pid) {
				$pcat = $this->db->get_one(array('catid'=>$pid));
				if ($pcat && $this->db->count(array('catid<>'=>$id, 'parentdir'=>$pcat['catdir'].'/', 'catdir'=>$value, 'siteid'=>$this->siteid, 'module'=>'content'))) {
					return dr_return_data(0, L('目录不能重复'));
				}
			} elseif ($this->db->count(array('catid<>'=>$id, 'parentdir'=>'', 'catdir'=>$value, 'siteid'=>$this->siteid, 'module'=>'content'))) {
				return dr_return_data(0, L('目录不能重复'));
			}
		}
		return dr_return_data(1);
	}
}
?>