<?php
/**
 *  db_mysqli.class.php MYSQLI数据库实现类
 *
 * @copyright			(C) 2005-2015
 * @lastmodify			2016-02-01
 */

use MySQLi;
use mysqli_sql_exception;
use Error;
use Throwable;

final class db_mysqli {
	
	protected $pConnect = false;
	protected $encrypt = false;
	protected $compress = false;
	protected $strictOn = false;
	protected $failover = [];
	public $deleteHack = true;
	protected $connectTime = 0.0;
	protected $connectDuration = 0.0;
	protected $lastQuery;
	public $mysqli;
	public $resultMode = MYSQLI_STORE_RESULT;
	public $numberNative = false;
	public $dataCache = [];
	/**
	 * 数据库配置信息
	 */
	private $config = null;
	
	/**
	 * 数据库连接资源句柄
	 */
	public $link = false;
	
	/**
	 * 最近一次查询资源句柄
	 */
	public $lastqueryid = null;
	
	/**
	 * 统计数据库查询次数
	 */
	public $querycount = 0;
	
	public function __construct() {

	}
	
	/**
	 * 打开数据库连接,有可能不真实连接数据库
	 * @param $config	数据库连接参数
	 * 			
	 * @return void
	 */
	public function open($config) {
		$this->config = $config;
		if($config['autoconnect'] == 1) {
			$this->connect();
		}
	}
	
	public function conn(bool $persistent = false) {
		if ($this->config['hostname'][0] === '/') {
			$hostname = null;
			$port = null;
			$socket = $this->config['hostname'];
		} else {
			$hostname = ($persistent === true) ? 'p:' . $this->config['hostname'] : $this->config['hostname'];
			$port = empty($this->config['port']) ? null : $this->config['port'];
			$socket = '';
		}

		$clientFlags = ($this->compress === true) ? MYSQLI_CLIENT_COMPRESS : 0;
		$this->mysqli = mysqli_init();

		mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);

		$this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

		if ($this->numberNative === true) {
			$this->mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
		}

		if (isset($this->strictOn)) {
			if ($this->strictOn) {
				$this->mysqli->options(
					MYSQLI_INIT_COMMAND,
					"SET SESSION sql_mode = CONCAT(@@sql_mode, ',', 'STRICT_ALL_TABLES')"
				);
			} else {
				$this->mysqli->options(
					MYSQLI_INIT_COMMAND,
					"SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
										@@sql_mode,
										'STRICT_ALL_TABLES,', ''),
									',STRICT_ALL_TABLES', ''),
								'STRICT_ALL_TABLES', ''),
							'STRICT_TRANS_TABLES,', ''),
						',STRICT_TRANS_TABLES', ''),
					'STRICT_TRANS_TABLES', '')"
				);
			}
		}

		if (is_array($this->encrypt)) {
			$ssl = [];

			if (! empty($this->encrypt['ssl_key'])) {
				$ssl['key'] = $this->encrypt['ssl_key'];
			}
			if (! empty($this->encrypt['ssl_cert'])) {
				$ssl['cert'] = $this->encrypt['ssl_cert'];
			}
			if (! empty($this->encrypt['ssl_ca'])) {
				$ssl['ca'] = $this->encrypt['ssl_ca'];
			}
			if (! empty($this->encrypt['ssl_capath'])) {
				$ssl['capath'] = $this->encrypt['ssl_capath'];
			}
			if (! empty($this->encrypt['ssl_cipher'])) {
				$ssl['cipher'] = $this->encrypt['ssl_cipher'];
			}

			if (! empty($ssl)) {
				if (isset($this->encrypt['ssl_verify'])) {
					if ($this->encrypt['ssl_verify']) {
						if (defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT')) {
							$this->mysqli->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, 1);
						}
					}
					elseif (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT') && version_compare($this->mysqli->client_info, 'mysqlnd 5.6', '>=')) {
						$clientFlags += MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
					}
				}

				$this->mysqli->ssl_set(
					$ssl['key'] ?? null,
					$ssl['cert'] ?? null,
					$ssl['ca'] ?? null,
					$ssl['capath'] ?? null,
					$ssl['cipher'] ?? null
				);
			}

			$clientFlags += MYSQLI_CLIENT_SSL;
		}

		try {
			if ($this->mysqli->real_connect(
				$hostname,
				$this->config['username'],
				$this->config['password'],
				$this->config['database'],
				$port,
				$socket,
				$clientFlags
			)) {
				if (($clientFlags & MYSQLI_CLIENT_SSL) && version_compare($this->mysqli->client_info, 'mysqlnd 5.7.3', '<=')
					&& empty($this->mysqli->query("SHOW STATUS LIKE 'ssl_cipher'")->fetch_object()->Value)
				) {
					$this->mysqli->close();
					$message = 'MySQLi was configured for an SSL connection, but got an unencrypted connection instead!';
					log_message('error', $message);

					if ($this->config['debug']) {
						throw new \Error($message);
					}

					return false;
				}

				if (! $this->mysqli->set_charset($this->config['charset'])) {
					log_message('error', "Database: Unable to set the configured connection charset ('{$this->config['charset']}').");

					$this->mysqli->close();

					if ($this->config['debug']) {
						throw new \Error('Unable to set client connection character set: ' . $this->config['charset']);
					}

					return false;
				}

				return $this->mysqli;
			}
		} catch (Throwable $e) {
			$msg = $e->getMessage();

			$msg = str_replace($this->config['username'], '****', $msg);
			$msg = str_replace($this->config['password'], '****', $msg);

			throw new \Error($msg, $e->getCode(), $e);
		}

		return false;
	}

	/**
	 * 真正开启数据库连接
	 * 			
	 * @return void
	 */
	public function connect() {
		if ($this->link) {
			return;
		}
		$this->connectTime = microtime(true);
		$this->pConnect = $this->config['pconnect'] ? true : false;
		$connectionErrors = [];
		try {
			$this->link = $this->conn($this->pConnect);
		} catch (Throwable $e) {
			$connectionErrors[] = sprintf('Main connection [%s]: %s', 'MySQLi', $e->getMessage());
			log_message('error', 'Error connecting to the database: ' . $e);
		}
		if (!$this->link) {
			if (!empty($this->failover) && is_array($this->failover)) {
				foreach ($this->failover as $index => $failover) {
					foreach ($failover as $key => $val) {
						if (property_exists($this, $key)) {
							$this->{$key} = $val;
						}
					}

					try {
						$this->link = $this->conn($this->pConnect);
					} catch (Throwable $e) {
						$connectionErrors[] = sprintf('Failover #%d [%s]: %s', ++$index, 'MySQLi', $e->getMessage());
						log_message('error', 'Error connecting to the database: ' . $e);
					}

					if ($this->link) {
						break;
					}
				}
			}
			if (!$this->link) {
				throw new \Error(sprintf(
					'Unable to connect to the database.%s%s',
					PHP_EOL,
					implode(PHP_EOL, $connectionErrors)
				));
			}
		}
		$this->connectDuration = microtime(true) - $this->connectTime;
	}

	/**
	 * 数据库查询执行方法
	 * @param $sql 要执行的sql语句
	 * @return 查询资源句柄
	 */
	private function execute(string $sql) {
		if(empty($this->link)) {
			$this->connect();
		}
		while ($this->link->more_results()) {
			$this->link->next_result();
			if ($res = $this->link->store_result()) {
				$res->free();
			}
		}
		$startTime = microtime(true);
		$this->lastQuery = $sql;
		try {
			$this->lastqueryid = $this->link->query($this->prepQuery($sql), $this->resultMode);
		} catch (mysqli_sql_exception $e) {
			log_message('error', (string) $e);
			if ($this->config['debug']) {
				throw $e;
			}
		}
		pc_base::load_sys_class('debug')::addmsg($sql, 1, $startTime);
		$this->querycount++;
		return $this->lastqueryid;
	}
	
	protected function prepQuery(string $sql): string {
		if ($this->deleteHack === true && preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql)) {
			return trim($sql) . ' WHERE 1=1';
		}
		return $sql;
	}
	
	public function getLastQuery() {
		return $this->lastQuery;
	}

	/**
	 * 执行sql查询
	 * @param $data 		需要查询的字段值[例`name`,`gender`,`birthday`]
	 * @param $table 		数据表
	 * @param $where 		查询条件[例`name`='$name']
	 * @param $limit 		返回结果范围[例：10或10,10 默认为空]
	 * @param $order 		排序方式	[默认按数据库默认方式排序]
	 * @param $group 		分组方式	[默认为空]
	 * @param $key 			返回数组按键名排序
	 * @return array		查询结果集数组
	 */
	public function select($data, $table, $where = '', $limit = '', $order = '', $group = '', $key = '') {
		$where = $where == '' ? '' : ' WHERE '.$where;
		$order = $order == '' ? '' : ' ORDER BY '.$order;
		$group = $group == '' ? '' : ' GROUP BY '.$group;
		$limit = $limit == '' ? '' : ' LIMIT '.$limit;
		$field = explode(',', $data);
		array_walk($field, array($this, 'add_special_char'));
		$data = implode(',', $field);

		$sql = 'SELECT '.$data.' FROM `'.$this->config['database'].'`.`'.$table.'`'.$where.$group.$order.$limit;
		$this->execute($sql);
		if(!is_object($this->lastqueryid)) {
			return $this->lastqueryid;
		}

		$datalist = array();
		while(($rs = $this->fetch_next()) != false) {
			if($key) {
				$datalist[$rs[$key]] = $rs;
			} else {
				$datalist[] = $rs;
			}
		}
		$this->free_result();
		return $datalist;
	}

	/**
	 * 获取单条记录查询
	 * @param $data 		需要查询的字段值[例`name`,`gender`,`birthday`]
	 * @param $table 		数据表
	 * @param $where 		查询条件
	 * @param $order 		排序方式	[默认按数据库默认方式排序]
	 * @param $group 		分组方式	[默认为空]
	 * @return array/null	数据查询结果集,如果不存在，则返回空
	 */
	public function get_one($data, $table, $where = '', $order = '', $group = '') {
		$where = $where == '' ? '' : ' WHERE '.$where;
		$order = $order == '' ? '' : ' ORDER BY '.$order;
		$group = $group == '' ? '' : ' GROUP BY '.$group;
		$limit = ' LIMIT 1';
		$field = explode(',', $data);
		array_walk($field, array($this, 'add_special_char'));
		$data = implode(',', $field);

		$sql = 'SELECT '.$data.' FROM `'.$this->config['database'].'`.`'.$table.'`'.$where.$group.$order.$limit;
		$this->execute($sql);
		$res = $this->fetch_next();
		$this->free_result();
		return $res;
	}
	
	/**
	 * 遍历查询结果集
	 * @param $type		返回结果集类型	
	 * 					MYSQLI_ASSOC, MYSQLI_NUM, or MYSQLI_BOTH
	 * @return array
	 */
	public function fetch_next($type=MYSQLI_ASSOC) {
		$res = $this->lastqueryid->fetch_array($type);
		if(!$res) {
			$this->free_result();
		}
		return $res;
	}
	
	/**
	 * 释放查询资源
	 * @return void
	 */
	public function free_result() {
		if(is_resource($this->lastqueryid)) {
			$this->lastqueryid->free();
			$this->lastqueryid = null;
		}
	}
	
	/**
	 * 直接执行sql查询
	 * @param $sql							查询sql语句
	 * @return	boolean/query resource		如果为查询语句，返回资源句柄，否则返回true/false
	 */
	public function query($sql) {
		return $this->execute($sql);
	}
	
	/**
	 * 执行添加记录操作
	 * @param $data 		要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
	 * @param $table 		数据表
	 * @return boolean
	 */
	public function insert($data, $table, $return_insert_id = false, $replace = false) {
		if(!is_array( $data ) || $table == '' || count($data) == 0) {
			return false;
		}
		
		$fielddata = array_keys($data);
		$valuedata = array_values($data);
		array_walk($fielddata, array($this, 'add_special_char'));
		array_walk($valuedata, array($this, 'escape_string'));
		
		$field = implode(',', $fielddata);
		$value = implode(',', $valuedata);

		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		$sql = $cmd.' `'.$this->config['database'].'`.`'.$table.'`('.$field.') VALUES ('.$value.')';
		$return = $this->execute($sql);
		return $return_insert_id ? $this->insert_id() : $return;
	}
	
	/**
	 * 获取最后一次添加记录的主键号
	 * @return int 
	 */
	public function insert_id() {
		if(empty($this->link)) {
			$this->connect();
		}
		return $this->link->insert_id;
	}
	
	/**
	 * 执行更新记录操作
	 * @param $data 		要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
	 * 						为数组时数组key为字段值，数组值为数据取值
	 * 						为字符串时[例：`name`='cms',`hits`=`hits`+1]。
	 *						为数组时[例: array('name'=>'cms','password'=>'123456')]
	 *						数组可使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
	 * @param $table 		数据表
	 * @param $where 		更新数据时的条件
	 * @return boolean
	 */
	public function update($data, $table, $where = '') {
		if($table == '' or $where == '') {
			return false;
		}

		$where = ' WHERE '.$where;
		$field = '';
		if(is_string($data) && $data != '') {
			$field = $data;
		} elseif (is_array($data) && count($data) > 0) {
			$fields = array();
			foreach($data as $k=>$v) {
				switch (substr((string)$v, 0, 2)) {
					case '+=':
						$v = substr($v,2);
						if (is_numeric($v)) {
							$fields[] = $this->add_special_char($k).'='.$this->add_special_char($k).'+'.$this->escape_string($v, '', false);
						} else {
							continue 2;
						}
						
						break;
					case '-=':
						$v = substr($v,2);
						if (is_numeric($v)) {
							$fields[] = $this->add_special_char($k).'='.$this->add_special_char($k).'-'.$this->escape_string($v, '', false);
						} else {
							continue 2;
						}
						break;
					default:
						$fields[] = $this->add_special_char($k).'='.$this->escape_string($v);
				}
			}
			$field = implode(',', $fields);
		} else {
			return false;
		}

		$sql = 'UPDATE `'.$this->config['database'].'`.`'.$table.'` SET '.$field.$where;
		return $this->execute($sql);
	}
	
	/**
	 * 执行删除记录操作
	 * @param $table 		数据表
	 * @param $where 		删除数据条件,不充许为空。
	 * 						如果要清空表，使用empty方法
	 * @return boolean
	 */
	public function delete($table, $where) {
		if ($table == '' || $where == '') {
			return false;
		}
		$where = ' WHERE '.$where;
		$sql = 'DELETE FROM `'.$this->config['database'].'`.`'.$table.'`'.$where;
		return $this->execute($sql);
	}
	
	/**
	 * 获取最后数据库操作影响到的条数
	 * @return int
	 */
	public function affected_rows() {
		if(empty($this->link)) {
			$this->connect();
		}
		return $this->link->affected_rows;
	}
	
	/**
	 * 获取数据表主键
	 * @param $table 		数据表
	 * @return array
	 */
	public function get_primary(string $table) {
		$this->execute("SHOW FULL COLUMNS FROM $table");
		while($r = $this->fetch_next()) {
			if($r['Key'] == 'PRI') break;
		}
		return $r['Field'];
	}

	/**
	 * 获取表字段
	 * @param $table 		数据表
	 * @return array
	 */
	public function get_fields(string $table) {
		if (isset($this->dataCache['field_names'][$table])) {
			return $this->dataCache['field_names'][$table];
		}
		$this->dataCache['field_names'][$table] = array();
		$this->execute("SHOW FULL COLUMNS FROM $table");
		while($r = $this->fetch_next()) {
			$this->dataCache['field_names'][$table][$r['Field']] = $r['Type'];
		}
		return $this->dataCache['field_names'][$table];
	}

	/**
	 * 检查不存在的字段
	 * @param $table 表名
	 * @return array
	 */
	public function check_fields($table, $array) {
		$fields = $this->get_fields($table);
		$nofields = array();
		foreach($array as $v) {
			if(!array_key_exists($v, $fields)) {
				$nofields[] = $v;
			}
		}
		return $nofields;
	}

	/**
	 * 检查表是否存在
	 * @param $table 表名
	 * @return boolean
	 */
	public function table_exists($table) {
		$tables = $this->list_tables();
		return in_array($table, $tables) ? 1 : 0;
	}
	
	public function list_tables() {
		if (isset($this->dataCache['table_names']) && $this->dataCache['table_names']) {
			return $this->dataCache['table_names'];
		}
		$this->dataCache['table_names'] = array();
		$this->execute('SHOW TABLES FROM ' . $this->config['database'] . (($this->config['tablepre'] !== '') ? ' LIKE \'' . $this->escape($this->config['tablepre']) . '%\'' : ''));
		while($r = $this->fetch_next()) {
			$this->dataCache['table_names'][] = $r[array_key_first($r)];
		}
		return $this->dataCache['table_names'];
	}

	/**
	 * 检查字段是否存在
	 * @param $table 表名
	 * @return boolean
	 */
	public function field_exists($table, $field) {
		$fields = $this->get_fields($table);
		return array_key_exists($field, $fields);
	}

	public function num_rows($sql) {
		$this->lastqueryid = $this->execute($sql);
		return $this->lastqueryid ? $this->lastqueryid->num_rows : 0;
	}

	public function num_fields($sql) {
		$this->lastqueryid = $this->execute($sql);
		return $this->lastqueryid ? $this->lastqueryid->field_count : null;
	}

	public function result($sql, $row = 0) {
		$this->lastqueryid = $this->execute($sql);
		$this->lastqueryid->data_seek($row);
		$assocs = $this->lastqueryid->fetch_row();
		return $assocs[0];
	}

	public function error() {
		if(empty($this->link)) {
			$this->connect();
		}
		if (!empty($this->mysqli->connect_errno)) {
			return [
				'code' => $this->mysqli->connect_errno,
				'message' => $this->mysqli->connect_error,
			];
		}
		return [
			'code' => $this->link->errno,
			'message' => $this->link->error,
		];
	}

	public function version() {
		if (isset($this->dataCache['version'])) {
			return $this->dataCache['version'];
		}
		if(empty($this->link)) {
			$this->connect();
		}
		return $this->dataCache['version'] = $this->link->server_info;
	}

	public function close() {
		if ($this->link) {
			$this->link->close();
			$this->link = false;
		}
	}

	public function escape($str){
		if(empty($this->link)) {
			$this->connect();
		}
		return $this->link->real_escape_string((string)$str);
	}

	/**
	 * 对字段两边加反引号，以保证数据库安全
	 * @param $value 数组值
	 */
	public function add_special_char(&$value) {
		if('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos ( $value, '`')) {
			//不处理包含* 或者 使用了sql方法。
		} else {
			$value = '`'.trim($value).'`';
		}
		if (preg_match("/\b(select|insert|update|delete)\b/i", $value)) {
			$value = preg_replace("/\b(select|insert|update|delete)\b/i", '', $value);
		}
		return $value;
	}
	
	/**
	 * 对字段值两边加引号，以保证数据库安全
	 * @param $value 数组值
	 * @param $key 数组key
	 * @param $quotation 
	 */
	public function escape_string(&$value, $key='', $quotation = 1) {
		if ($quotation) {
			$q = '\'';
		} else {
			$q = '';
		}
		$value = $q.$this->escape($value).$q;
		return $value;
	}
}
?>