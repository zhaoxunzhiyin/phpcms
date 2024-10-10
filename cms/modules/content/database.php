<?php
defined('IN_CMS') or exit('No permission resources.');
class database {
	private $input,$db,$database,$userid;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		pc_base::load_sys_class('db_factory');
		$this->database = pc_base::load_config('database');
		$this->db = db_factory::get_instance($database)->get_database('default');
		$this->userid = param::get_session('userid');
	}

	/**
	 * 还原数据库
	 */
	public function recovery() {
		if(ADMIN_FOUNDERS && !dr_in_array($this->userid, ADMIN_FOUNDERS)) {
			dr_json(0, L('only_fonder_operation'));
		}
		$backupDir = CACHE_PATH.'bakup/default/';
		if (IS_POST) {
			$file = $this->input->post('file');
			if (fileext($file)=='sql') {
				try {
					if (!is_file($backupDir.$file)) {
						dr_json(0, L('未找到SQL文件'));
					}
					dr_json(1, 'ok', array('url' => WEB_PATH.'index.php?m=content&c=database&a=import_index&file='.$file));
				} catch (Exception $e) {
					dr_json(0, L($e->getMessage()));
				} catch (PDOException $e) {
					dr_json(0, L($e->getMessage()));
				}
			}
		} else {
            pc_base::load_sys_class('backup', '', 0);
			$time = $this->input->get('time');
			$part = $this->input->get('part');
			$start = $this->input->get('start');
			if (is_numeric($time) && $time && !$part && !$start) { //初始化
				//获取备份文件信息
				$name = date('Ymd-His', $time) . '-*.sql*';
				$path = realpath($backupDir) . DIRECTORY_SEPARATOR . $name;
				$files = glob($path);
				$list = array();
				foreach ($files as $name) {
					$basename = basename($name);
					$match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
					$gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
					$list[$match[6]] = array($match[6], $name, $gz);
				}
				ksort($list);

				//检测文件正确性
				$last = end($list);
				if (dr_count($list) === $last[0]) {
					param::set_session('backup_list', $list); //缓存备份列表
					dr_json(1, L('初始化完成！'), array('part' => 1, 'start' => 0));
				} else {
					dr_json(0, L('备份文件可能已经损坏，请检查！'));
				}
			} elseif (is_numeric($part) && is_numeric($start)) {
				$list = param::get_session('backup_list');
				$db = new backup($list[$part], array(
					'path' => realpath($backupDir) . DIRECTORY_SEPARATOR,
					'compress' => $list[$part][2]
				));

				$start = $db->import($start);

				if (false === $start) {
					dr_json(0, L('还原数据出错！'));
				} elseif (0 === $start) { //下一卷
					if (isset($list[++$part])) {
						$data = array('part' => $part, 'start' => 0);
						dr_json(1, L('正在还原...#'.$part), $data);
					} else {
						param::del_session('backup_list');
						dr_json(1, L('还原完成！'));
					}
				} else {
					$data = array('part' => $part, 'start' => $start[0]);
					if ($start[1]) {
						$rate = floor(100 * ($start[0] / $start[1]));
						dr_json(1, L('正在还原...#'.$part.' ('.$rate.'%)'), $data);
					} else {
						$data['gz'] = 1;
						dr_json(1, L('正在还原...#'.$part), $data);
					}
				}

			} else {
				dr_json(0, L('参数错误！'));
			}
		}
	}

	/**
	 * 还原
	 */
	public function import_index() {
		$file = $this->input->get('file');
		$todo_url = WEB_PATH.'index.php?m=content&c=database&a=todo_import&file='.$file;
		include admin_template('database_import_bfb', 'admin');
	}

	/**
	 * 还原
	 */
	public function todo_import() {
		$backupDir = CACHE_PATH.'bakup/default/';

		$file = $this->input->get('file');
		$page = max(1, intval($this->input->get('page')));
		if (!$file) {
			dr_json(0, L('数据缓存不存在'));
		}

		if (fileext($file)=='sql') {
			$sqlFile = $backupDir.$file;
		} else {
			$filedir = $backupDir.'database/';
			$sqlFile = $filedir.file_name($file).'.sql';
		}
		if (!is_file($sqlFile)) {
			dr_json(0, L('未找到SQL文件'));
		}

		// 导入数据结构
		if ($page) {
			$sql = file_get_contents($sqlFile);
			$sql = str_replace('phpcms_', 'cms_', $sql);
			if($this->database['tablepre'] != "cms_") $sql = str_replace("`cms_", '`'.$this->database['tablepre'], $sql);
			$rows = $this->query_rows($sql, 10);
			$key = $page - 1;
			if (isset($rows[$key]) && $rows[$key]) {
				// 安装本次结构
				foreach($rows[$key] as $query){
					if (!$query) {
						continue;
					}
					$ret = '';
					$queries = explode('SQL_CMS_EOL', trim($query));
					foreach($queries as $query) {
						$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
					}
					if (!$ret) {
						continue;
					}
					$this->db->query($ret);
				}
				dr_json(1, '<p class="'.$class.'"><label class="rleft">正在执行：'.str_cut($ret, 70).'</label><label class="rright">完成</label></p>', ['page' => $page + 1]);
			} else {
				isset($filedir) && $filedir && dr_dir_delete($filedir, true);
				dr_json(1, '<p class="'.$class.'"><label class="rleft">'.L('还原成功！').'</label>', ['page' => 0]);
			}
		}
	}

	// 数据分组
	private function query_rows($sql, $num = 0) {

		if (!$sql) {
			return '';
		}

		$rt = array();
		$sql = format_create_sql($sql);
		$sql_data = explode(';SQL_CMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_CMS_EOL', $sql)));

		foreach($sql_data as $query){
			if (!$query) {
				continue;
			}
			$ret = '';
			$queries = explode('SQL_CMS_EOL', trim($query));
			foreach($queries as $query) {
				$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
			}
			if (!$ret) {
				continue;
			}
			$rt[] = $ret;
		}
		
		return $num ? array_chunk($rt, $num) : $rt;
	}
}
?>