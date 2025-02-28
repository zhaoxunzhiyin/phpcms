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
		$model = getcache('model','commons');
		$model_array=array();
		foreach($model as $_m) {
			$db = new sqltoolplus($_m['tablename']);
			$model_array[$_m['modelid']] = $_m['name'] . '(' . $_m['tablename'] . ')'.($db->is_patitioned()&&($pt=$db->get_patition_info())?'已有分区:每个分区'.$pt['descr'].'条记录':'');
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

	public function dbtpatition() {
		if (IS_AJAX_POST) {
			if($this->input->post('modelid')){
				$model = getcache('model','commons');
				$modelid = intval($this->input->post('modelid'));
				$dbtp_range = intval($this->input->post('dbtp_range'));
				$dbtp_num = intval($this->input->post('dbtp_num'));
				if(!isset($model[$modelid]))dr_json(0,L('modelnotexist'));
				$m = $model[$modelid];
				$db = new sqltoolplus($m['tablename']);
				
				$sql="SHOW PLUGINS;";
				$handle = $db->query($sql);
				$enabled = false;
				if($handle){
					while($r=$db->fetch_next()){
						if($r['Name']=='partition'){
							$enabled = true;
							break;
						}
					}
				}
				if(!$enabled)dr_json(0,L('unfortunately'));
				if(!$db->is_patitioned()){
					if($dbtp_num<1){
						$r = $db->get_one('', 'MAX(`id`) AS max');
						$end = ceil($r['max']/$dbtp_range);
					}else{
						$end= $dbtp_num;
					}
					$sql="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}` PARTITION BY RANGE (`id`)(";
					$sql2="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}_data_0` PARTITION BY RANGE (`id`)(";
					$_sql='PARTITION p0 VALUES LESS THAN ('.($dbtp_range).'),';
					for($i=1;$i<=$end;$i++) {
						$_sql.='PARTITION p'.$i.' VALUES LESS THAN ('.($dbtp_range*($i+1)).'),';
					}
					$_sql.='PARTITION pmax VALUES LESS THAN MAXVALUE';
					$_sql.=');';
					for ($i = 1;; $i ++) {
						$tablename_data = $db->db_tablepre.$m['tablename'].'_data_'.$i;
						$db->query("SHOW TABLES LIKE '".$tablename_data."'");
						$table_exists = $db->fetch_next();
						if (!$table_exists) {
							break;
						}
						$sql3="ALTER TABLE `".$tablename_data."` PARTITION BY RANGE (`id`)(";
						$db->query($sql3.$_sql);
					}
				}else{
					$pt=$db->get_patition_info();
					$number = preg_replace('|^p|i','',end(explode(',',str_replace(',pmax','',(string)$pt['partitions']))));
					$db->query("EXPLAIN PARTITIONS SELECT * FROM `{$db->db_tablepre}{$m['tablename']}` WHERE `id`=(SELECT MAX(`id`) FROM `{$db->db_tablepre}{$m['tablename']}` LIMIT 1)");
					$r = $db->fetch_next();
					
					if($r['partitions']!='pmax'){
						dr_json(0,L('createanew'));
					}

					$sql="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}` REORGANIZE PARTITION pmax INTO (";
					$sql2="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}_data_0` REORGANIZE PARTITION pmax INTO (";
					$_sql='';
					$start=$number+1;
					$end=$start+$dbtp_num;
					for($i=$start;$i<$end;$i++) {
						$_sql.='PARTITION p'.$i.' VALUES LESS THAN ('.($pt['descr']*($i+1)).'),';
					}
					$_sql.="PARTITION pmax VALUES LESS THAN MAXVALUE";
					$_sql.=");";
					for ($i = 1;; $i ++) {
						$tablename_data = $db->db_tablepre.$m['tablename'].'_data_'.$i;
						$db->query("SHOW TABLES LIKE '".$tablename_data."'");
						$table_exists = $db->fetch_next();
						if (!$table_exists) {
							continue;
						}
						$sql3="ALTER TABLE `".$tablename_data."` PARTITION BY RANGE (`id`)(";
						$db->query($sql3.$_sql);
					}
				}
				if($db->query($sql.$_sql) && $db->query($sql2.$_sql)){
					dr_json(1,L('success'));
				}else{
					dr_json(0,L('failure'));
				}
			}else{
				dr_json(0,L('select').L('modelmx'));
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

class sqltoolplus extends model {
	public $table_name = '';
	public function __construct($table) {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = $table;
		parent::__construct();
	}

	function get_patition_info($table=''){
		$table=!$table?$this->table_name:$this->db_tablepre.$table;
		$sql='SELECT partition_name part,partition_expression expr,partition_description descr,table_rows FROM INFORMATION_SCHEMA.partitions WHERE TABLE_SCHEMA=schema()  AND TABLE_NAME=\''.$table.'\'';
		$this->query($sql);
		$P=$this->fetch_next();
		$sql='EXPLAIN PARTITIONS SELECT * FROM `'.$table.'`;';
		$this->query($sql);
		$P=@array_merge($P,$this->fetch_next());
		return $P;
	}

	function get_table_status($table='',$db=''){
		$table=!$table?$this->table_name:$this->db_tablepre.$table;
		$sql='SHOW TABLE STATUS';
		if($db)$sql.=' FROM `'.$db.'`';
		$sql.=' LIKE \''.$table.'\'';
		$handle=$this->query($sql);
		return $handle ? $this->fetch_next() : false;
	}

	function is_patitioned($table='',$db='') {
		$return = $this->get_table_status($table,$db);
		return $return && $return['Create_options']=='partitioned';
	}

	public function fetch_next() {
		return $this->db->fetch_next();
	}
}
?>