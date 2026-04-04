<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_sys_class('model', '', 0);
class index extends admin {
	private $input,$db,$db_charset,$db_tablepre;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('module_model');
	}
	
	public function init() {
		$show_header = true;
		$plugin_menus = array();
		$info = $this->db->get_one(array('module'=>'sqltoolplus'));
		extract($info);
		$sql_cache = pc_base::load_sys_class('file')->get_sql_cache();
		$tables = array();
		$table_list = $this->db->query('show table status');
		foreach ($table_list as $t) {
			$tables[$t['Name']] = $t;
		}
		$page = intval($this->input->get('page'));
		include $this->admin_tpl('sqltoolplus');
	}

	public function sqlquery() {
		if (IS_AJAX_POST) {
			$sqls = new_stripslashes($this->input->post('sqls'));
			$sql_data = explode(';SQL_CMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_CMS_EOL', str_replace('{tablepre}', $this->db->db_tablepre, $sqls))));
			if ($sql_data) {
				foreach($sql_data as $query){
					if (!$query) {
						continue;
					}
					$ret = '';
					$queries = explode('SQL_CMS_EOL', trim($query));
					foreach($queries as $query) {
						$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
					}
					$sql = trim($ret);
					if (!$sql) {
						continue;
					}
					$ck = 0;
					foreach (array('select', 'create', 'drop', 'alter', 'insert', 'replace', 'update', 'delete') as $key) {
						if (strpos(strtolower($sql), $key) === 0) {
							if (!IS_DEV && in_array($key, ['create', 'drop', 'delete', 'alter'])) {
								dr_json(0, L('为了安全起见，在开发者模式下才能运行'.$key.'语句'));
							}
							$ck = 1;
							break;
						}
					}
					if (!$ck) {
						dr_json(0, L('execute_sql').str_cut($sql, 20));
					}
					foreach (array('outfile', 'dumpfile', '.php', 'union') as $kw) {
						if (strpos(strtolower($sql), $kw) !== false) {
							dr_json(0, L('sql_keywords').$kw);
						}
					}
					if (stripos($sql, 'select') === 0) {
						// 查询语句
						$handle = $this->_sql_execute($sql);
						!$handle && dr_json(0, L('查询出错'));
						$rt = $this->db->fetch_array();
						if ($rt) {
							$msg .= var_export($rt, true);
						} else {
							$rt = $this->db->error();
							pc_base::load_sys_class('file')->add_sql_cache($sql);
							dr_json(0, $rt['message']);
						}
					} else {
						// 执行语句
						$handle = $this->_sql_execute($sql);
						if (!$handle) {
							$rt = $this->db->error();
							dr_json(0, L('查询错误：').$rt['message']);
						}
					}
				}
				dr_json(1, $msg ? $msg : L('sql_success'));
			} else {
				dr_json(0, L('sql_empty'));
			}
		}
	} 

	public function sqlreplace() {
		if (IS_AJAX_POST) {
			$database = pc_base::load_config('database');
			$this->db = db_factory::get_instance($database)->get_database('default');
			$db_table = $this->input->post('db_table');
			$db_field = dr_safe_replace($this->input->post('db_field'));
			if (!$db_table) {
				dr_json(0, L('表名不能为空'));
			}
			if (!$db_field) {
				dr_json(0, L('待替换字段必须填写'));
			} elseif ($db_field == $this->db->get_primary($db_table)) {
				dr_json(0, $this->db->get_primary($db_table).L('主键不支持替换'));
			}
			if (!strlen($this->input->post('search_rule'))) {
				dr_json(0,L('select_where'));
			} 
			if (!$db_table || !preg_match('/^[\w]+$/', $db_table)) {
				dr_json(0,L('select_table'));
			} 
			if (!$db_field || !preg_match('/^[\w]+$/', $db_field)) {
				dr_json(0,L('select_field'));
			} 
			if ($this->input->post('sql_where')) {
				$_sql = ' AND ' . new_stripslashes($this->input->post('sql_where'));
			} else {
				$_sql = '';
			} 
			if ($this->input->post('replace_type') == 2) {
				$sql = "UPDATE `{$db_table}` SET `{$db_field}`=REPLACE(`{$db_field}`,'{$this->input->post('search_rule')}','{$this->input->post('replace_data')}') WHERE `{$db_field}` LIKE '%{$this->input->post('search_rule')}%'$_sql;";

				$handle = $this->_sql_execute($sql);
				$count = $this->db->affected_rows();

				if ($count < 0) {
					dr_json(0,L('执行错误'));
				}

				dr_json(1,L('本次替换'.$count.'条数据'), $sql);
			} else {
				if (!$this->input->post('db_pr_field') || !preg_match('/^[\w]+$/', $this->input->post('db_pr_field'))) {
					dr_json(0,L('select_pr_field'));
				} 
				$ck_pr = $this->db->get_primary($db_table);
				if ($ck_pr && $ck_pr != $this->input->post('db_pr_field')) {
					dr_json(0,L('select_is_fieldfield') . $ck_pr . L('pleasereselect'));
				} 
				if ($this->input->post('replace_type') == 1) {
					$search_rule = str_replace(array('\\\\%', '\\\\_'), array('\\%', '\\_'),$this->input->post('search_rule'));
					$sql = "LIKE '{$search_rule}'";
				} else {
					$search_rule = str_replace(array('\\', '\'', '.*?', '.+?'), array('\\\\', '\\\'', '.*', '.+'), $this->input->post('search_rule'));
					$sql = "REGEXP '{$search_rule}'";
				} 
				$sql = "SELECT `{$this->input->post('db_pr_field')}`,`{$db_field}` FROM `{$db_table}` WHERE `{$db_field}` {$sql}$_sql";
				$handle = $this->db->query($sql);
				$success = $failse = 0;
				if ($handle) {
					$search_rule = str_replace('/', '\\/', new_stripslashes($this->input->post('search_rule')));
					if ($this->input->post('replace_type') == 1) {
						$search_rule = str_replace(array('\\%', '\\_'), array('[(P)]', '[(U)]'), $search_rule);
						$search_rule = preg_quote($search_rule);
						$search_rule = str_replace(array('%', '_'), array('(.*?)', '(.)'), $search_rule);
						$search_rule = str_replace(array('\\[\\(P\\)\\]', '\\[\\(U\\)\\]'), array('%', '_'), $search_rule);
					} 
					$replace_data = new_stripslashes($this->input->post('replace_data'));
					while ($r = $this->db->fetch_next()) {
						$preg = preg_replace('/' . $search_rule . '/i', $replace_data, $r[$db_field]);
						$preg = new_addslashes($preg);
						$id = $r[$this->input->post('db_pr_field')];
						$sql = "UPDATE `{$db_table}` SET `{$db_field}`='$preg' WHERE `{$this->input->post('db_pr_field')}`='$id'$_sql;";
						if ($this->_sql_execute($sql)) {
							$success++;
						} else {
							$failse++;
						}
						$this->db->lastqueryid = $handle;
					}
				}
				dr_json(1,L('replacefinished') . $success . L('replace_successmonths') . $failse . L('months'), $sql);
			}
		}
	} 

	/**
	 * 执行SQL
	 * 
	 * @param string $sql 要执行的sql语句
	 */
	private function _sql_execute($sql) {
		$sqls = $this->_sql_split($sql);
		if (is_array($sqls)) {
			foreach($sqls as $sql) {
				if (trim($sql) != '') {
					$handle = $this->db->query($sql);
					if (!$handle) return false;
				} 
			} 
		} else {
			$handle = $this->db->query($sqls);
		} 
		return $handle ? true : false;
	} 

	/**
	 * 分割SQL语句
	 * 
	 * @param string $sql 要执行的sql语句
	 */
	private function _sql_split($sql) {
		$database = pc_base::load_config('database');
		$db_charset = $database['default']['charset'];
		if ($this->db->version() > '4.1' && $db_charset) {
			$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=" . $db_charset, $sql);
		}
		$sql = str_replace("\r", "\n", $sql);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach($queriesarray as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			$queries = array_filter($queries);
			foreach($queries as $query) {
				$str1 = substr($query, 0, 1);
				if ($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
			} 
			$num++;
		} 
		return($ret);
	}

	// 联动加载字段
	public function public_field_index() {
		$table = dr_safe_replace($this->input->get('table'));
		$table = str_replace($this->db->db_tablepre, '', $table);
		if (!$table) {
			dr_json(0, L('表参数不能为空'));
		} elseif (!$this->db->table_exists($table)) {
			dr_json(0, L('表['.$table.']不存在'));
		}

		$fields = $this->db->query('SHOW FULL COLUMNS FROM `'.$this->db->db_tablepre.$table.'`');
		if (!$fields) {
			dr_json(0, L('表['.$table.']没有可用字段'));
		}

		$msg = '<select id="db_field" name="db_field" class="form-control">';
		foreach ($fields as $t) {
			$msg.= '<option value="'.$t['Field'].'">'.$t['Field'].($t['Comment'] ? '（'.$t['Comment'].'）' : '').'</option>';
		}
		$msg.= '</select>';

		dr_json(1, $msg);
	}
}
?>