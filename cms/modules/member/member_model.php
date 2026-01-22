<?php
/**
 * 管理员后台会员模型操作类
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form', '', 0);

class member_model extends admin {
	
	private $input,$db,$sitemodel_field_db,$member_db,$cache_api;
	
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('sitemodel_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}

	/**
	 * 会员模型列表
	 */
	function manage() {
		$page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
		$member_model_list = $this->db->listinfo(array('type'=>2, 'siteid'=>$this->get_siteid()), 'sort,modelid', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('member_model_list');
	}
			
	/**
	 * 添加会员模型
	 */
	function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$info['type'] = 2;
			$info['siteid'] = $this->get_siteid();
			if(!$info['name']) dr_admin_msg(0,L('input').L('model_name'), array('field' => 'name'));
			if ($this->db->count(array('type'=>$info['type'], 'name'=>$info['name'], 'siteid'=>$info['siteid']))) {
				dr_admin_msg(0,L('model_name').L('exists'), array('field' => 'name'));
			}
			if(!$info['tablename']) dr_admin_msg(0,L('input').L('table_name'), array('field' => 'tablename'));
			$info['tablename'] = 'member_'.$info['tablename'];
			if ($this->db->count(array('type'=>$info['type'], 'tablename'=>$info['tablename'], 'siteid'=>$info['siteid']))) {
				dr_admin_msg(0,L('table_name').L('exists'), array('field' => 'tablename'));
			}
			
			$is_exists = $this->db->table_exists($info['tablename']);
			if($is_exists) dr_admin_msg(0,L('operation_failure'),'?m=member&c=member_model&a=manage', '', 'add');

			$modelid = $this->db->insert($info, true);
			if($modelid) {
				define('MEMBER_MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'member'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
				$model_sql = file_get_contents(MEMBER_MODEL_PATH.'model.sql');
				$tablepre = $this->db->db_tablepre;
				$tablename = $info['tablename'];
				$model_sql = str_replace('$tablename', $tablepre.$tablename, $model_sql);
				$this->db->sql_execute($model_sql);
				//更新模型缓存
				$this->cache();
				dr_admin_msg(1,L('operation_success'),'?m=member&c=member_model&a=manage', '', 'add');
			} else {
				dr_admin_msg(0,L('operation_failue'),'?m=member&c=member_model&a=manage', '', 'add');
			}
		} else {
			$show_header = $show_scroll = true;
			include $this->admin_tpl('member_model_add');
		}
		
	}
	
	/**
	 * 修改会员模型
	 */
	function edit() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$modelid = isset($info['modelid']) ? $info['modelid'] : dr_admin_msg(0,L('operation_success'),'?m=member&c=member_model&a=manage', '', 'edit');
			if(!$info['name']) dr_admin_msg(0,L('input').L('model_name'), array('field' => 'name'));
			if ($this->db->count(array('type'=>2, 'modelid<>'=>$modelid, 'name'=>$info['name'], 'siteid'=>$this->get_siteid()))) {
				dr_admin_msg(0,L('model_name').L('exists'), array('field' => 'name'));
			}
			$info['disabled'] = $info['disabled'] ? 1 : 0;
			$info['description'] = $info['description'];
			
			$this->db->update($info, array('modelid'=>$modelid));
			
			//更新模型缓存
			$this->cache();
			dr_admin_msg(1,L('operation_success'),'?m=member&c=member_model&a=manage', '', 'edit');
		} else {					
			$show_header = $show_scroll = true;
			$modelinfo = $this->db->get_one(array('modelid'=>$this->input->get('modelid')));
			include $this->admin_tpl('member_model_edit');		
		}
	}
	
	/**
	 * 删除会员模型
	 */
	function delete() {
		$modelidarr = $this->input->post('modelid') ? $this->input->post('modelid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		foreach($modelidarr as $id) {
			$v = $this->db->get_one(array('modelid'=>$id));
			$this->db->drop_table($v['tablename']);
			
			if ($this->db->delete(array('modelid'=>$id))) {
				//删除模型字段
				$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
				$this->sitemodel_field_db->delete(array('modelid'=>$id));
				//修改用户模型组为普通会员
				$this->member_db = pc_base::load_model('members_model');
				$this->member_db->update(array('modelid'=>10), array('modelid'=>$id));
			}
		}
		
		//更新模型缓存
		$this->cache();
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}

	public function public_disabled() {
		$modelid = intval($this->input->get('modelid'));
		$r = $this->db->get_one(array('modelid'=>$modelid,'siteid'=>$this->get_siteid()));
		
		$value = $r['disabled'] ? '0' : '1';
		$this->db->update(array('disabled'=>$value),array('modelid'=>$modelid,'siteid'=>$this->get_siteid()));
		//更新模型缓存
		$this->cache();
		dr_json(1, L($value ? '设置为禁用状态' : '设置为可用状态'), array('value' => $value));
	}
	/**
	 * 导入会员模型
	 */
	function import(){
		if(IS_POST) {
			$info = $this->input->post('info');
			$info['name'] = $info['modelname'];
			$info['tablename'] = 'member_'.$info['tablename'];
			$info['description'] = $info['description'];
			$info['type'] = 2;
			$info['siteid'] = $this->get_siteid();
			unset($info['modelname']);
			
			if(!empty($_FILES['model_import']['tmp_name'])) {
				$model_import = @file_get_contents($_FILES['model_import']['tmp_name']);
				if(!empty($model_import)) {
					$model_import_data = string2array($model_import);				
				}
			}

			$is_exists = $this->db->table_exists($info['tablename']);
			if($is_exists) dr_admin_msg(0,L('operation_failure'),'?m=member&c=member_model&a=manage');

			$modelid = $this->db->insert($info, 1);
			if($modelid) {
				define('MEMBER_MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'member'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
				$model_sql = file_get_contents(MEMBER_MODEL_PATH.'model.sql');
				$tablepre = $this->db->db_tablepre;
				$tablename = $info['tablename'];
				$model_sql = str_replace('$tablename', $tablepre.$tablename, $model_sql);
				$this->db->sql_execute($model_sql);
				if(!empty($model_import_data)) {
					$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
					$tablename = $tablepre.$tablename;
					foreach($model_import_data as $v) {
						//修改模型表字段
						$field = $v['field'];
						$minlength = $v['minlength'] ? $v['minlength'] : 0;
						$maxlength = $v['maxlength'] ? $v['maxlength'] : 0;
						$field_type = $v['formtype'];
						require MEMBER_MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';	
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
						unset($v['fieldid']);
							
						$fieldid = $this->sitemodel_field_db->insert($v, 1);
					}
				}

				//更新模型缓存
				$this->cache();
				dr_admin_msg(1,L('operation_success'),'?m=member&c=member_model&a=manage');
			}
		} else {
			include $this->admin_tpl('member_model_import');
		}
	}
	
	/**
	 * 导出会员模型
	 */
	function export() {
		$modelid = $this->input->get('modelid') ? $this->input->get('modelid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$modelarr = getcache('member_model', 'commons');
		
		$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
		$modelinfo = $this->sitemodel_field_db->select(array('modelid'=>$modelid));
		foreach($modelinfo as $k=>$v) {
			$modelinfoarr[$k] = $v;
			$modelinfoarr[$k]['setting'] = string2array($v['setting']);
		}

		$res = array2string($modelinfoarr);
		header('Content-Disposition: attachment; filename="'.$modelarr[$modelid]['tablename'].'.model"');
		exit($res);
	}
	
	/**
	 * 修改会员模型
	 */
	function move() {
		if(IS_POST) {
			$from_modelid = $this->input->post('from_modelid') ? $this->input->post('from_modelid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$to_modelid = !empty($this->input->post('to_modelid')) && $this->input->post('to_modelid') != $from_modelid ? $this->input->post('to_modelid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			
			//更新会员表modelid
			$this->db->change_member_modelid($from_modelid, $to_modelid);
			
			dr_admin_msg(1,L('member_move').L('operation_success'), HTTP_REFERER, '', 'move');
		} else {
			$show_header = $show_scroll = true;
			$modelarr = $this->db->select(array('type'=>2));
			foreach($modelarr as $v) {
				$modellist[$v['modelid']] = $v['name'];
			}
					
			include $this->admin_tpl('member_model_move');
		}
	}
	
	/**
	 * 排序会员模型
	 */
	function sort() {
		$modelid = intval($this->input->get('modelid'));
		// 查询数据
		$r = $this->db->get_one(array('modelid'=>$modelid,'siteid'=>$this->get_siteid()));
		if (!$r) {
			dr_json(0, L('数据#'.$modelid.'不存在'));
		}
		$value = (int)$this->input->get('value');
		$this->db->update(array('sort'=>$value),array('modelid'=>$modelid,'siteid'=>$this->get_siteid()));
		//更新模型缓存
		$this->cache();
		dr_json(1, L('操作成功'));
	}
	
	/**
	 * 检查模型名称
	 * @param string $username	模型名
	 * @return $status {0:模型名已经存在 ;1:成功}
	 */
	public function public_checkmodelname_ajax() {
		$modelname = $this->input->get('modelname') ? trim($this->input->get('modelname')) : exit('0');
		if(CHARSET != 'utf-8') {
			$modelname = iconv('utf-8', CHARSET, $modelname);
		}
		
		$oldmodelname = $this->input->get('oldmodelname');
		if($modelname==$oldmodelname) exit('1');
		
		$status = $this->db->get_one(array('name'=>$modelname));		
		if($status) {
			exit('0');
		} else {
			exit('1');
		}
		
	}
	
	/**
	 * 检查模型表是否存在
	 * @param string $username	模型名
	 * @return $status {0:模型表名已经存在 ;1:成功}
	 */
	public function public_checktablename_ajax() {
		$tablename = $this->input->get('tablename') ? trim($this->input->get('tablename')) : exit('0');
		
		$status = $this->db->table_exists('member_'.$tablename);
		if($status) {
			exit('0');
		} else {
			exit('1');
		}
		
	}

	// 缓存
	public function cache() {
		$this->cache_api->cache('membermodel');
		$this->cache_api->cache('sitemodel');
		$this->cache_api->del_file();
	}
}
?>