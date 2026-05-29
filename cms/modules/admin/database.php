<?php
@set_time_limit(0);
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class database extends admin {
	private $input,$cache,$db,$isadmin;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->isadmin = IS_ADMIN && param::get_session('roleid') ? 1 : 0;
		$this->userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
		pc_base::load_sys_class('db_factory');
		pc_base::load_sys_class('form');
	}
	/**
	 * 数据字典
	 */
	public function export() {
		$database = pc_base::load_config('database');
		$r = array();
		if ($database['default']['type']!='sqlite3') {
			$db = db_factory::get_instance($database)->get_database('default');
			$tbl_show = $db->query("SHOW TABLE STATUS FROM `".$database['default']['database']."`");
			while(($rs = $db->fetch_next()) != false) {
				$r[] = $rs;
			}
			$infos = $this->status($r,$database['default']['tablepre']);
			$db->free_result($tbl_show);
		}
		include $this->admin_tpl('database_export');
	}
	
	/**
	 * 还原
	 */
	public function import() {
		$database = pc_base::load_config('database');
		$backupDir = CACHE_PATH.'bakup/default/';
		if ($database['default']['type']=='sqlite3') {
			include $this->admin_tpl('database_export');
		} else {
			if(ADMIN_FOUNDERS && dr_in_array($this->userid, ADMIN_FOUNDERS)) {
				$infos = $list = array();
				//列出备份文件列表
				$i = 1;
				$path = realpath($backupDir);
				$flag = \FilesystemIterator::KEY_AS_FILENAME;
				$glob = new \FilesystemIterator($path, $flag);
				foreach ($glob as $name => $file) {
					if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
						$info['id'] = $i;
						$name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

						$date = "{$name[0]}-{$name[1]}-{$name[2]}";
						$time = "{$name[3]}:{$name[4]}:{$name[5]}";
						$part = $name[6];

						if (isset($infos["{$date} {$time}"])) {
							$info = $infos["{$date} {$time}"];
							$info['part'] = max($info['part'], $part);
							$info['size'] = $info['size'] + $file->getSize();
						} else {
							$info['part'] = $part;
							$info['size'] = $file->getSize();
						}
						$extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
						$info['compress'] = ($extension === 'SQL') ? '-' : $extension;
						$info['time'] = strtotime("{$date} {$time}");

						$infos["{$date} {$time}"] = $info;
						$i++;
					} else {
						if (fileext($name)!='sql') {
							continue;
						}
						$time = filemtime($backupDir.$name);
						$list[] =
							[
								'file' => $name,
								'part' => 1,
								'compress' => '-',
								'date' => dr_date($time, "Y-m-d H:i:s", 'red'),
								'size' => format_file_size(filesize($backupDir.$name))
							];
					}
				}
			}
			$show_validator = true;
			include $this->admin_tpl('database_import');
		}
	}
	
	/**
	 * 备份数据库
	 * @param String $tables 表名
	 * @param Integer $id 表ID
	 * @param Integer $start 起始行数
	 */
	public function backup() {
		pc_base::load_sys_class('backup', '', 0);
		$backupDir = CACHE_PATH.'bakup/default/';
		if (IS_POST) {
			$database = pc_base::load_config('database');
			if ($database['default']['type']=='sqlite3') {
				$date = date('YmdHis');
				$name = file_name($database['default']['database']);
				$filename = $backupDir.'backup-'.$name.'-'.$date.'.db';
				if (copy(CMS_PATH.$database['default']['database'], $filename)) {
					dr_json(1, L('备份数据库成功'));
				} else {
					dr_json(0, L('备份失败'));
				}
			} else {
				$tables = $this->input->post('tables');
				$sizelimit = intval($this->input->post('sizelimit'));
				$compress = intval($this->input->post('compress'));
				$level = intval($this->input->post('level'));
				!$sizelimit && $sizelimit = 2;
				if (!empty($tables) && is_array($tables)) {
					$config = array(
						'path' => realpath($backupDir) . DIRECTORY_SEPARATOR,
						'part' => $sizelimit * 1024 * 1024,
						'compress' => $compress,
						'level' => $level,
					);
					//检查是否有正在执行的任务
					if ($this->cache->get_data('database_lock')) {
						dr_json(0, L('检测到有一个备份任务正在执行，请稍后再试！'));
					} else {
						$this->cache->set_data('database_lock', SYS_TIME, 3600);
					}
					//检查备份目录是否可写 创建备份目录
					param::set_session('backup_config', $config);

					//生成备份文件信息
					$file = array(
						'name' => dr_date(SYS_TIME, 'Ymd-His'),
						'part' => 1,
					);
					param::set_session('backup_file', $file);

					//缓存要备份的表
					param::set_session('backup_tables', $tables);

					//创建备份文件
					$backup = new backup($file, $config);
					if (false !== $backup->create()) {
						$tab = array('id' => 0, 'start' => 0);
						dr_json(1, L('初始化成功！'), array('tables' => $tables, 'tab' => $tab));
					} else {
						dr_json(0, L('初始化失败，备份文件创建失败！'));
					}
				} else {
					dr_json(0, L('参数错误！'));
				}
			}
		} else { //备份数据
			$id = intval($this->input->get('id'));
			$start = intval($this->input->get('start'));
			if (is_numeric($id) && is_numeric($start)) {
				$tables = param::get_session('backup_tables');
				//备份指定表
				$backup = new backup(param::get_session('backup_file'), param::get_session('backup_config'));
				$start = $backup->backup($tables[$id], $start);
				if (false === $start) { //出错
					dr_json(0, L('备份出错！'));
				} elseif (0 === $start) { //下一表
					if (isset($tables[++$id])) {
						$tab = array('id' => $id, 'start' => 0);
						dr_json(1, L('备份完成！'), array('tab' => $tab));
					} else { 
						//备份完成，清空缓存
						$this->cache->clear('database_lock');
						param::del_session('backup_tables');
						param::del_session('backup_file');
						param::del_session('backup_config');
						dr_json(1, L('备份完成！'));
					}
				} else {
					$tab = array('id' => $id, 'start' => $start[0]);
					$rate = floor(100 * ($start[0] / $start[1]));
					dr_json(1, L('正在备份...('.$rate.'%)'), array('tab' => $tab));
				}
			} else {
				dr_json(0, L('参数错误！'));
			}
		}
	}

	/**
	 * 删除备份文件
	 */
	public function delete() {
		if(ADMIN_FOUNDERS && !dr_in_array($this->userid, ADMIN_FOUNDERS)) {
			dr_json(0, L('only_fonder_operation'));
		}
		$backupDir = CACHE_PATH.'bakup/default/';
		if (IS_POST) {
			$time = $this->input->post('time');
			if (is_numeric($time)) {
				$name = dr_date($time, 'Ymd-His') . '-*.sql*';
				$path = realpath($backupDir) . DIRECTORY_SEPARATOR . $name;
				array_map("unlink", glob($path));
				if (dr_count(glob($path))) {
					dr_json(0, L('备份文件删除失败，请检查权限！'));
				} else {
					dr_json(1, L('备份文件删除成功！'));
				}
			} else if ($time) {
				$file = $backupDir.$time;
				unlink($file);
				dr_json(1, L('删除成功'));
			} else {
				dr_json(0, L('参数错误！'));
			}
		}
	}
	
	/**
	 * 数据库修复、优化
	 */
	public function public_repair() {
		$database = pc_base::load_config('database');
		$tables = $this->input->post('tables') ? $this->input->post('tables') : trim($this->input->get('tables'));
		$operation = trim($this->input->get('operation'));
		$this->db = db_factory::get_instance($database)->get_database('default');
		$tables = is_array($tables) ? implode(',',$tables) : $tables;
		if($tables && in_array($operation,array('repair','optimize','flush'))) {
			$this->db->query("$operation TABLE $tables");
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=database&a=export&menuid='.$this->input->get('menuid'));
		} elseif ($tables && $operation == 'showcreat') {
			$this->db->query("SHOW CREATE TABLE $tables");
			$structure = $this->db->fetch_next();
			$structure = $structure['Create Table'];
			$show_header = true;
			include $this->admin_tpl('database_structure');
		} elseif ($tables && $operation == 'show') {
			$structure = $this->db->query('SHOW FULL COLUMNS FROM `'.$tables.'`');
			$show_header = true;
			include $this->admin_tpl('database_show');
		} elseif ($tables && $operation == 'ut') {
			$this->db->query('ALTER DATABASE '.$database['default']['database'].' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
			foreach ($this->input->post('tables') as $table) {
				$this->db->query('ALTER TABLE `'.$table.'` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
			}
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=database&a=export&menuid='.$this->input->get('menuid'));
		} elseif ($tables && $operation == 'jc') {
			$data = $this->db->query("CHECK TABLE $tables");
			if (!$data) {
				dr_admin_msg(0,L('database_table'),'?m=admin&c=database&a=export&menuid='.$this->input->get('menuid'));
			} else {
				dr_admin_msg(1,L('operation_success'),'?m=admin&c=database&a=export&menuid='.$this->input->get('menuid'));
			}
		} else {
			dr_admin_msg(0,L('select_tbl'),'?m=admin&c=database&a=export&menuid='.$this->input->get('menuid'));
		}
	}
	/**
	 * 获取数据表
	 * @param unknown_type 数据表数组
	 * @param unknown_type 表前缀
	 */
	private function status($tables,$tablepre) {
		$cms = array();
		$other = array();
		foreach($tables as $table) {
			$name = $table['Name'];
			$row = array('name'=>$name,'comment'=>$table['Comment'],'rows'=>$table['Rows'],'size'=>$table['Data_length']+$table['Index_length'],'engine'=>$table['Engine'],'data_free'=>$table['Data_free'],'collation'=>$table['Collation'],'updatetime'=>$table['Update_time'] ? strtotime($table['Update_time']) : '');
			if(strpos($name, $tablepre) === 0) {
				$cms[] = $row;
			} else {
				$other[] = $row;
			}				
		}
		return array('cmstables'=>$cms, 'othertables'=>$other);
	}

	// 批量操作
	public function public_add() {
		$show_header = true;
		$operation = $this->input->get('operation');
		$ids = $this->input->post('tables');
		if (!$ids) {
			dr_json(0, L('database_no_table'));
		}
		$cache = dr_save_bfb_data($ids);
		// 存储文件
		$this->cache->set_data('db-todo-'.$operation, $cache, 3600);
		dr_json(1, 'ok', array('url' => '?m=admin&c=database&a=public_count_index&operation='.$operation));
	}
	
	public function public_count_index() {
		$show_header = true;
		$operation = $this->input->get('operation');
		$todo_url = '?m=admin&c=database&a=public_todo_index&operation='.$operation;
		include $this->admin_tpl('database_bfb');
	}
	
	public function public_todo_index() {
		$show_header = true;
		$database = pc_base::load_config('database');
		$operation = $this->input->get('operation');
		$this->db = db_factory::get_instance($database)->get_database('default');
		$page = max(1, intval($this->input->get('page')));
		$cache = $this->cache->get_data('db-todo-'.$operation);
		if (!$cache) {
			dr_json(0, L('database_cache'));
		}
		$data = $cache[$page];
		if ($data) {
			$html = '';
			if ($operation=='ut') {
				$this->db->query('ALTER DATABASE '.$database['default']['database'].' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
			}
			foreach ($data as $table) {
				$ok = L('database_success');
				$class = '';
				switch ($operation) {
					case 'x':
						$this->db->query('REPAIR TABLE `'.$table.'`');
						break;
					case 'y':
						$this->db->query('OPTIMIZE TABLE `'.$table.'`');
						break;
					case 's':
						$this->db->query('FLUSH TABLE `'.$table.'`');
						break;
					case 'ut':
						$this->db->query('ALTER TABLE `'.$table.'` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
						break;
					case 'jc':
						$data = $this->db->query('CHECK TABLE `'.$table.'`');
						if (!$data) {
							$class = 'p_error';
							$ok = "<span class='error'>".L('database_table')."</span>";
						} else {
							$ok = L('database_success');
						}
						break;
				}
				$html.= '<p class="'.$class.'"><label class="rleft">'.$table.'</label><label class="rright">'.$ok.'</label></p>';
			}
			dr_json($page + 1, $html);
		}
		// 完成
		$this->cache->clear('db-todo-'.$operation);
		dr_json(100, '');
	}
}
?>