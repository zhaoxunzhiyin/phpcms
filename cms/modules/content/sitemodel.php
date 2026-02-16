<?php
defined('IN_CMS') or exit('No permission resources.');
//模型原型存储路径
define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin','admin',0);
class sitemodel extends admin {
	private $input,$cache,$db,$cache_api,$m_db,$field,$type_db,$sitemodel_field_db,$category_db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('sitemodel_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->siteid = $this->get_siteid();
	}
	
	public function init() {
		$order = $this->input->get('order') ? $this->input->get('order') : 'sort,modelid';
		$datas = $this->db->listinfo(array('siteid'=>$this->siteid,'type'=>0),$order,$this->input->get('page'),SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$this->cache_api->cache('sitemodel');
		include $this->admin_tpl('sitemodel_manage');
	}
	
	public function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			!$setting['list_field'] && $setting['list_field'] = array(
				'title' => array(
					'use' => 1,
					'name' => L('主题'),
					'width' => '',
					'func' => 'title',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '100',
					'func' => 'author',
				),
				'updatetime' => array(
					'use' => 1,
					'name' => L('更新时间'),
					'width' => '160',
					'func' => 'datetime',
				),
				'listorder' => array(
					'use' => 1,
					'name' => L('排序'),
					'width' => '100',
					'center' => 1,
					'func' => 'save_text_value',
				),
			);
			if (!$info['name']) {
				dr_admin_msg(0, L('input').L('model_name'), array('field'=>'name'));
			}
			if (!$info['tablename']) {
				dr_admin_msg(0, L('input').L('model_tablename'), array('field'=>'tablename'));
			}
			if ($this->db->table_exists(clearhtml($info['tablename']))) {
				dr_admin_msg(0, L('model_tablename').L('exists'), array('field'=>'tablename'));
			}
			$info['setting'] = array2string($setting);
			$info['siteid'] = $this->siteid;
			$info['category_template'] = $setting['category_template'];
			$info['list_template'] = $setting['list_template'];
			$info['show_template'] = $setting['show_template'];
			if ($this->input->post('other')) {
				$info['admin_list_template'] = $setting['admin_list_template'];
				$info['member_add_template'] = $setting['member_add_template'];
				$info['member_list_template'] = $setting['member_list_template'];
			} else {
				unset($setting['admin_list_template'], $setting['member_add_template'], $setting['member_list_template']);
			}
			$modelid = $this->db->insert($info, true);
			$model_sql = file_get_contents(MODEL_PATH.'model.sql');
			$tablepre = $this->db->db_tablepre;
			$tablename = $info['tablename'];
			$model_sql = str_replace('$basic_table', $tablepre.$tablename, $model_sql);
			$model_sql = str_replace('$table_data',$tablepre.$tablename.'_data_0', $model_sql);
			$model_sql = str_replace('$table_model_field',$tablepre.'model_field', $model_sql);
			$model_sql = str_replace('$modelid',$modelid,$model_sql);
			$model_sql = str_replace('$siteid',$this->siteid,$model_sql);
			
			$this->db->sql_execute($model_sql);
			//调用全站搜索类别接口
			$this->type_db = pc_base::load_model('type_model');
			$this->type_db->insert(array('name'=>$info['name'],'module'=>'search','modelid'=>$modelid,'siteid'=>$this->siteid));
			$this->cache_api->cache('sitemodel');
			$this->cache_api->cache('type', 'search');
			$this->cache_api->del_file();
			dr_admin_msg(1,L('add_success'), '', '', 'add');
		} else {
			pc_base::load_sys_class('form','',0);
			$show_header = $show_validator = true;
			$style_list = template_list($this->siteid, 0);
			foreach ($style_list as $k=>$v) {
				$style_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($style_list[$k]);
			}
			$admin_list_template = $this->admin_list_template('content_list', 'name="setting[admin_list_template]"');
			include $this->admin_tpl('sitemodel_add');
		}
	}
	public function edit() {
		if(IS_AJAX_POST) {
			$modelid = intval($this->input->post('modelid'));
			$this->m_db = pc_base::load_model('sitemodel_field_model');
			$this->field = $this->m_db->select(array('siteid'=>$this->siteid, 'modelid'=>$modelid, 'issystem'=>1, 'disabled'=>0),'*','','listorder ASC,fieldid ASC');
			$sys_field = sys_field(array('id', 'title', 'username', 'updatetime', 'listorder'));
			$data = $this->db->get_one(array('modelid'=>$modelid));
			$data['setting'] = dr_string2array($data['setting']);
			$field = dr_list_field_value($data['setting']['list_field'], $sys_field, $this->field);
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			if ($setting['list_field']) {
				foreach ($setting['list_field'] as $t) {
					if ($t['func']
						&& !method_exists(pc_base::load_sys_class('function_list'), $t['func']) && !function_exists($t['func'])) {
						dr_json(0, L('列表回调函数['.$t['func'].']未定义'));
					}
				}
			}
			$setting['list_field'] = dr_list_field_order($setting['list_field']);
			!$setting['list_field'] && $setting['list_field'] = array(
				'title' => array(
					'use' => 1,
					'name' => L('主题'),
					'width' => '',
					'func' => 'title',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '100',
					'func' => 'author',
				),
				'updatetime' => array(
					'use' => 1,
					'name' => L('更新时间'),
					'width' => '160',
					'func' => 'datetime',
				),
				'listorder' => array(
					'use' => 1,
					'name' => L('排序'),
					'width' => '100',
					'center' => 1,
					'func' => 'save_text_value',
				),
			);
			if ($setting['search_time'] && !isset($field[$setting['search_time']])) {
				dr_json(0, L('后台列表时间搜索字段'.$setting['search_time'].'不存在'));
			}
			if ($setting['order']) {
				$arr = explode(',', $setting['order']);
				foreach ($arr as $t) {
					list($a) = explode(' ', $t);
					if ($a && !isset($field[$a])) {
						dr_json(0, L('后台列表的默认排序字段'.$a.'不存在'));
					}
				}
			}
			if ($setting['search']['search_param'] && !$this->input->post('search_field')) {
				dr_json(0, L('开启搜索参数为空时不显示结果，请选择关键词匹配字段'));
			}
			$setting['search']['field'] = ($this->input->post('search_field') ? implode(',', $this->input->post('search_field')) : '');
			$info['setting'] = array2string($setting);
			$info['category_template'] = $setting['category_template'];
			$info['list_template'] = $setting['list_template'];
			$info['show_template'] = $setting['show_template'];
			if ($this->input->post('other')) {
				$info['admin_list_template'] = $setting['admin_list_template'];
				$info['member_add_template'] = $setting['member_add_template'];
				$info['member_list_template'] = $setting['member_list_template'];
			} else {
				unset($setting['admin_list_template'], $setting['member_add_template'], $setting['member_list_template']);
			}
			
			$this->db->update($info,array('modelid'=>$modelid,'siteid'=>$this->siteid));
			$this->cache_api->cache('sitemodel');
			$this->cache_api->cache('type', 'search');
			$this->cache_api->del_file();
			dr_json(1, L('update_success'));
		} else {
			pc_base::load_sys_class('form','',0);
			$show_validator = true;
			$style_list = template_list($this->siteid, 0);
			foreach ($style_list as $k=>$v) {
				$style_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($style_list[$k]);
			}
			$modelid = intval($this->input->get('modelid'));
			$this->m_db = pc_base::load_model('sitemodel_field_model');
			$this->field = $this->m_db->select(array('siteid'=>$this->siteid, 'modelid'=>$modelid, 'issystem'=>1, 'disabled'=>0),'*','','listorder ASC,fieldid ASC');
			$sys_field = sys_field(array('id', 'title', 'username', 'updatetime', 'hits', 'listorder'));
			$r = $this->db->get_one(array('modelid'=>$modelid));
			extract($r);
			if ($r['setting']) {
				extract(string2array($r['setting']));
			}
			!$list_field && $list_field = array(
				'title' => array(
					'use' => 1,
					'name' => L('主题'),
					'width' => '',
					'func' => 'title',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '100',
					'func' => 'author',
				),
				'updatetime' => array(
					'use' => 1,
					'name' => L('更新时间'),
					'width' => '160',
					'func' => 'datetime',
				),
				'listorder' => array(
					'use' => 1,
					'name' => L('排序'),
					'width' => '100',
					'center' => 1,
					'func' => 'save_text_value',
				),
			);
			$field = dr_list_field_value($list_field, $sys_field, $this->field);
			$search_field = $this->m_db->select(array('siteid'=>$this->siteid, 'modelid'=>$modelid, 'issystem'=>1, 'issearch'=>1, 'disabled'=>0),'*','','listorder ASC,fieldid ASC');
			$is_iframe = intval($this->input->get('is_iframe'));
			$page = intval($this->input->get('page'));
			$admin_list_template_f = $this->admin_list_template($admin_list_template, 'name="setting[admin_list_template]"');
			$reply_url = '?m=content&c=sitemodel&a=init&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('sitemodel_edit');
		}
	}
	public function delete() {
		if(IS_AJAX_POST) {
			$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
			$modelid = intval($this->input->post('modelid'));
			$model_cache = getcache('model','commons');
			$model_table = $model_cache[$modelid]['tablename'];
			$this->sitemodel_field_db->delete(array('modelid'=>$modelid,'siteid'=>$this->siteid));
			if ($this->db->table_exists($model_table)) {
				$this->db->drop_table($model_table);
			}
			for ($i = 0;; $i ++) {
				$tablename_data = $this->db->db_tablepre.$model_table.'_data_'.$i;
				$this->db->query("SHOW TABLES LIKE '".$tablename_data."'");
				$table_exists = $this->db->fetch_array();
				if (!$table_exists) {
					break;
				}
				$tablename_data = '';
				$this->db->drop_table($model_table.'_data_'.$i);
			}
			$this->category_db = pc_base::load_model('category_model');
			$this->category_db->delete(array('modelid'=>$modelid,'siteid'=>$this->siteid));
			$this->db->delete(array('modelid'=>$modelid,'siteid'=>$this->siteid));
			//删除全站搜索接口数据
			$this->type_db = pc_base::load_model('type_model');
			$this->type_db->delete(array('module'=>'search','modelid'=>$modelid,'siteid'=>$this->siteid));
			$this->cache_api->cache('sitemodel');
			$this->cache_api->cache('type', 'search');
			$this->cache_api->del_file();
			dr_json(1, L('operation_success'));
		}
	}
	// 隐藏或者启用
	public function disabled() {
		$modelid = intval($this->input->get('modelid'));
		$r = $this->db->get_one(array('modelid'=>$modelid,'siteid'=>$this->siteid));
		$value = $r['disabled'] ? '0' : '1';
		$this->db->update(array('disabled'=>$value),array('modelid'=>$modelid,'siteid'=>$this->siteid));
		$this->cache_api->cache('sitemodel');
		dr_json(1, L($value ? '设置为禁用状态' : '设置为可用状态'), array('value' => $value));
	}
	// 排序
	public function public_order_edit() {
		$modelid = intval($this->input->get('modelid'));
		// 查询数据
		$r = $this->db->get_one(array('modelid'=>$modelid,'siteid'=>$this->siteid));
		if (!$r) {
			dr_json(0, L('数据#'.$modelid.'不存在'));
		}
		$value = (int)$this->input->get('value');
		$this->db->update(array('sort'=>$value),array('modelid'=>$modelid,'siteid'=>$this->siteid));
		$this->cache_api->cache('sitemodel');
		dr_json(1, L('操作成功'));
	}
	/**
	 * 导出模型
	 */
	function export() {
		$modelid = $this->input->get('modelid') ? $this->input->get('modelid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$modelarr = getcache('model', 'commons');
		//定义系统字段排除
		//$system_field = array('id','title','style','catid','url','listorder','status','userid','username','inputtime','updatetime','pages','readpoint','template','groupids_view','posids','content','keywords','description','thumb','typeid','relation','islink','allow_comment');
		$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
		$modelinfo = $this->sitemodel_field_db->select(array('modelid'=>$modelid));
		foreach($modelinfo as $k=>$v) {
			//if(in_array($v['field'],$system_field)) continue;
			$modelinfoarr[$k] = $v;
			$modelinfoarr[$k]['setting'] = string2array($v['setting']);
		}
		$res = array2string($modelinfoarr);
		header('Content-Disposition: attachment; filename="'.$modelarr[$modelid]['tablename'].'.model"');
		exit($res);
	}
	/**
	 * 导入模型
	 */
	function import(){
		if(IS_POST) {
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			!$setting['list_field'] && $setting['list_field'] = array(
				'title' => array(
					'use' => 1,
					'name' => L('主题'),
					'width' => '',
					'func' => 'title',
				),
				'username' => array(
					'use' => 1,
					'name' => L('用户名'),
					'width' => '100',
					'func' => 'author',
				),
				'updatetime' => array(
					'use' => 1,
					'name' => L('更新时间'),
					'width' => '160',
					'func' => 'datetime',
				),
				'listorder' => array(
					'use' => 1,
					'name' => L('排序'),
					'width' => '100',
					'center' => 1,
					'func' => 'save_text_value',
				),
			);
			$info['name'] = $info['modelname'];
			unset($info['modelname']);
			//主表表名
			$basic_table = $info['tablename'];
			//从表表名
			$table_data = $basic_table.'_data_0';
			$info['description'] = $info['description'];
			$info['setting'] = array2string($setting);
			$info['type'] = 0;
			$info['siteid'] = $this->siteid;
			
			$info['default_style'] = $this->input->post('default_style');
			$info['category_template'] = $setting['category_template'];
			$info['list_template'] = $setting['list_template'];
			$info['show_template'] = $setting['show_template'];
			
			if(!empty($_FILES['model_import']['tmp_name'])) {
				$model_import = @file_get_contents($_FILES['model_import']['tmp_name']);
				if(!empty($model_import)) {
					$model_import_data = string2array($model_import);				
				}
			}
			$is_exists = $this->db->table_exists($basic_table);
			if($is_exists) dr_admin_msg(0,L('operation_failure'),'?m=content&c=sitemodel&a=init');
			$modelid = $this->db->insert($info, 1);
			if($modelid){
				$tablepre = $this->db->db_tablepre;
				//建立数据表
				$model_sql = file_get_contents(MODEL_PATH.'model.sql');
				$model_sql = str_replace('$basic_table', $tablepre.$basic_table, $model_sql);
				$model_sql = str_replace('$table_data',$tablepre.$table_data, $model_sql);
				$model_sql = str_replace('$table_model_field',$tablepre.'model_field', $model_sql);
				$model_sql = str_replace('$modelid',$modelid,$model_sql);
				$model_sql = str_replace('$siteid',$this->siteid,$model_sql);
				$this->db->sql_execute($model_sql);
				
				if(!empty($model_import_data)) {
					$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
					$system_field = array('title','style','catid','url','listorder','status','userid','username','inputtime','updatetime','pages','readpoint','template','groupids_view','posids','content','keywords','description','thumb','typeid','relation','islink','allow_comment');
					foreach($model_import_data as $v) {
						$field = $v['field'];
						if(in_array($field,$system_field)) {
							$v['siteid'] = $this->siteid;
							unset($v['fieldid'],$v['modelid'],$v['field']);
							$v['setting'] = array2string($v['setting']);
							
							$this->sitemodel_field_db->update($v,array('modelid'=>$modelid,'field'=>$field));
						} else {
							$tablename = $v['issystem'] ? $tablepre.$basic_table : $tablepre.$table_data;
							//重组模型表字段属性
							
							$minlength = $v['minlength'] ? $v['minlength'] : 0;
							$maxlength = $v['maxlength'] ? $v['maxlength'] : 0;
							$field_type = $v['formtype'];
							require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';	
							if(isset($v['setting']['fieldtype'])) {
								$field_type = $v['setting']['fieldtype'];
							}
							$tips = $v['name'] ? ' COMMENT \''.$v['name'].'\'' : '';
							$defaultvalue = isset($v['setting']['defaultvalue']) ? $v['setting']['defaultvalue'] : '';
							//正整数 UNSIGNED && SIGNED
							$minnumber = isset($v['setting']['minnumber']) ? $v['setting']['minnumber'] : 1;
							$decimaldigits = isset($v['setting']['decimaldigits']) ? $v['setting']['decimaldigits'] : '';
							switch($field_type) {
								case 'varchar':
									if(!$maxlength) $maxlength = 255;
									$maxlength = min($maxlength, 255);
									$sql = "ALTER TABLE `$tablename` ADD `$field` VARCHAR( $maxlength ) NOT NULL DEFAULT '$defaultvalue' $tips";
									$this->db->query($sql);
								break;

								case 'tinyint':
									if(!$maxlength) $maxlength = 3;
									$minnumber = intval($minnumber);
									$defaultvalue = intval($defaultvalue);
									$sql = "ALTER TABLE `$tablename` ADD `$field` TINYINT( $maxlength ) ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
									$this->db->query($sql);
								break;
								
								case 'number':
									$minnumber = intval($minnumber);
									$defaultvalue = $decimaldigits == 0 ? intval($defaultvalue) : floatval($defaultvalue);
									$sql = "ALTER TABLE `$tablename` ADD `$field` ".($decimaldigits == 0 ? 'INT' : 'FLOAT')." ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
									$this->db->query($sql);
								break;

								case 'smallint':
									$minnumber = intval($minnumber);
									$sql = "ALTER TABLE `$tablename` ADD `$field` SMALLINT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL $tips";
									$this->db->query($sql);
								break;

								case 'int':
									$minnumber = intval($minnumber);
									$defaultvalue = intval($defaultvalue);
									$sql = "ALTER TABLE `$tablename` ADD `$field` INT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
									$this->db->query($sql);
								break;

								case 'mediumint':
									$minnumber = intval($minnumber);
									$defaultvalue = intval($defaultvalue);
									$sql = "ALTER TABLE `$tablename` ADD `$field` INT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
									$this->db->query($sql);
								break;

								case 'mediumtext':
									$sql = "ALTER TABLE `$tablename` ADD `$field` MEDIUMTEXT NOT NULL $tips";
									$this->db->query($sql);
								break;
								
								case 'text':
									$sql = "ALTER TABLE `$tablename` ADD `$field` TEXT NOT NULL $tips";
									$this->db->query($sql);
								break;

								case 'date':
									$sql = "ALTER TABLE `$tablename` ADD `$field` DATE NULL $tips";
									$this->db->query($sql);
								break;
								
								case 'datetime':
									$sql = "ALTER TABLE `$tablename` ADD `$field` DATETIME NULL $tips";
									$this->db->query($sql);
								break;
								
								case 'timestamp':
									$sql = "ALTER TABLE `$tablename` ADD `$field` TIMESTAMP NOT NULL $tips";
									$this->db->query($sql);
								break;
								//特殊自定义字段
								case 'pages':
									$this->db->query("ALTER TABLE `$tablename` ADD `paginationtype` TINYINT( 1 ) NOT NULL DEFAULT '0' $tips");
									$this->db->query("ALTER TABLE `$tablename` ADD `maxcharperpage` MEDIUMINT( 6 ) NOT NULL DEFAULT '0' $tips");
								break;
								case 'readpoint':
									$defaultvalue = intval($defaultvalue);
									$this->db->query("ALTER TABLE `$tablename` ADD `readpoint` smallint(5) unsigned NOT NULL default '$defaultvalue' $tips");
									$this->db->query("ALTER TABLE `$tablename` ADD `paytype` tinyint(1) unsigned NOT NULL default '0' $tips");
								break;
							}
							$v['setting'] = array2string($v['setting']);
							$v['modelid'] = $modelid;
							$v['siteid'] = $this->siteid;
							unset($v['fieldid']);
							
							$this->sitemodel_field_db->insert($v);
						}
					}
				}
				$this->cache_api->cache('sitemodel');
				$this->cache_api->cache('type', 'search');
				$this->cache_api->del_file();
				dr_admin_msg(1,L('operation_success'),'?m=content&c=sitemodel&a=init');
			}
		} else {
			pc_base::load_sys_class('form','',0);
			$show_validator = '';
			$style_list = template_list($this->siteid, 0);
			foreach ($style_list as $k=>$v) {
				$style_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($style_list[$k]);
			}
			include $this->admin_tpl('sitemodel_import');
		}
	}
	/**
	 * 在线帮助
	 */
	public function public_help() {
		$show_header = $show_validator = true;
		include $this->admin_tpl('sitemodel_help');
	}
	/**
	 * 检查表是否存在
	 */
	public function public_check_tablename() {
		$r = $this->db->table_exists(clearhtml($this->input->get('tablename')));
		if(!$r) echo '1';
	}
}
?>