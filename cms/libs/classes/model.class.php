<?php 
/**
 *  model.class.php 数据模型基类
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-6-7
 */
defined('IN_CMS') or exit('Access Denied');
pc_base::load_sys_class('db_factory', '', 0);
class model {

	//数据库配置
	protected $db_config = array();
	//数据库连接
	protected $db;
	//调用数据库的配置项
	protected $db_setting = 'default';
	//数据表名
	protected $table_name;
	//表前缀
	public $db_tablepre;

	public $number;
	public $pages;

	public function __construct() {
		if (!isset($this->db_config[$this->db_setting])) {
			$this->db_setting = 'default';
		}
		$this->table_name = $this->db_config[$this->db_setting]['tablepre'].$this->table_name;
		$this->db_tablepre = $this->db_config[$this->db_setting]['tablepre'];
		$this->db = db_factory::get_instance($this->db_config)->get_database($this->db_setting);
	}

	/**
	 * 执行sql查询
	 * @param $where 		查询条件[例`name`='$name']
	 * @param $data 		需要查询的字段值[例`name`,`gender`,`birthday`]
	 * @param $limit 		返回结果范围[例：10或10,10 默认为空]
	 * @param $order 		排序方式	[默认按数据库默认方式排序]
	 * @param $group 		分组方式	[默认为空]
	 * @param $key 			返回数组按键名排序
	 * @return array		查询结果集数组
	 */
	final public function select($where = '', $data = '*', $limit = '', $order = '', $group = '', $key='') {
		if (is_array($where)) $where = $this->sqls($where);
		return $this->db->select($data, $this->table_name, $where, $limit, $order, $group, $key);
	}

	/**
	 * 查询多条数据并分页
	 * @param $where
	 * @param $order
	 * @param $page
	 * @param $pagesize
	 * @return unknown_type
	 */
	final public function listinfo($where = '', $order = '', $page = 1, $pagesize = 10, $key='',$urlrule = '',$array = array(), $data = '*') {
		if (is_array($where)) $where = $this->sqls($where);
		$this->number = $this->count($where);
		$page = max(intval($page), 1);
		$offset = $pagesize*($page-1);
		$this->pages = pages($this->number, $page, $pagesize, $urlrule, $array);
		$array = array();
		if ($this->number > 0) {
			return $this->select($where, $data, "$offset, $pagesize", $order, '', $key);
		} else {
			return array();
		}
	}

	/**
	 * 获取单条记录查询
	 * @param $where 		查询条件
	 * @param $data 		需要查询的字段值[例`name`,`gender`,`birthday`]
	 * @param $order 		排序方式	[默认按数据库默认方式排序]
	 * @param $group 		分组方式	[默认为空]
	 * @return array/null	数据查询结果集,如果不存在，则返回空
	 */
	final public function get_one($where = '', $data = '*', $order = '', $group = '') {
		if (is_array($where)) $where = $this->sqls($where);
		return $this->db->get_one($data, $this->table_name, $where, $order, $group);
	}

	/**
	 * 直接执行sql查询
	 * @param $sql							查询sql语句
	 * @return	boolean/query resource		如果为查询语句，返回资源句柄，否则返回true/false
	 */
	final public function query($sql) {
		$sql = str_replace('phpcms_', 'cms_', $sql);
		$sql = str_replace('cms_', $this->db_tablepre, $sql);
		$id = $this->db->query($sql);
		if (!$id) {
			$error = $this->db->error();
			log_message('error', $sql.': '.$error['message']);
			return $this->_return_error($error['message']);
		}
		return $id;
	}

	/**
	 * 执行添加记录操作
	 * @param $data 		要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
	 * @param $return_insert_id 是否返回新建ID号
	 * @param $replace 是否采用 replace into的方式添加数据
	 * @return boolean
	 */
	final public function insert($data, $return_insert_id = false, $replace = false) {
		$id = $this->db->insert($data, $this->table_name, $return_insert_id, $replace);
		$rt = $this->db->error();
		if ($rt['code']) {
			log_message('error', $this->table_name.': '.$rt['message']);
			return $this->_return_error($this->table_name.': '.$rt['message']);
		}
		return $id;
	}

	/**
	 * 获取最后一次添加记录的主键号
	 * @return int 
	 */
	final public function insert_id() {
		return $this->db->insert_id();
	}

	/**
	 * 执行更新记录操作
	 * @param $data 		要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
	 * 						为数组时数组key为字段值，数组值为数据取值
	 * 						为字符串时[例：`name`='cms',`hits`=`hits`+1]。
	 *						为数组时[例: array('name'=>'cms','password'=>'123456')]
	 *						数组的另一种使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
	 * @param $where 		更新数据时的条件,可为数组或字符串
	 * @return boolean
	 */
	final public function update($data, $where = '') {
		if (is_array($where)) $where = $this->sqls($where);
		if (!$data) {
			log_message('debug', $this->table_name.': update() data值为空');
			return;
		}
		$id = $this->db->update($data, $this->table_name, $where);
		$rt = $this->db->error();
		if ($rt['code']) {
			log_message('error', $this->table_name.': '.$rt['message']);
			return $this->_return_error($this->table_name.': '.$rt['message']);
		}
		return $id;
	}

	/**
	 * 执行删除记录操作
	 * @param $where 		删除数据条件,不充许为空。
	 * @return boolean
	 */
	final public function delete($where) {
		if (is_array($where)) $where = $this->sqls($where);
		$id = $this->db->delete($this->table_name, $where);
		$rt = $this->db->error();
		if ($rt['code']) {
			log_message('error', $this->table_name.': '.$rt['message']);
			return $this->_return_error($this->table_name.': '.$rt['message']);
		}
		return $id;
	}

	/**
	 * 计算记录数
	 * @param string/array $where 查询条件
	 */
	final public function count($where = '') {
		$r = $this->get_one($where, "COUNT(*) AS num");
		return $r['num'];
	}

	/**
	 * 将数组转换为SQL语句
	 * @param array $where 要生成的数组
	 * @param string $font 连接串。
	 */
	final public function sqls($where, $font = ' AND ') {
		if (is_array($where)) {
			$sql = '';
			foreach ($where as $key=>$val) {
				if(is_array($val)){
					$sql .= $sql ? " $font `$key` IN ('".str_replace(',', '\',\'', implode(',', $val))."') " : " `$key` IN ('".str_replace(',', '\',\'', implode(',', $val))."')";
				}else if(!strpos($key,'>') && !strpos($key,'<') && !strpos($key,'=') && substr((string)$val, 0, 1) != '%' && substr((string)$val, -1) != '%'){
					$val = $this->escape($val);
					$sql .= $sql ? " $font `$key` = '$val' " : " `$key` = '$val'";
				}else if(substr((string)$val, 0, 1) == '%' || substr((string)$val, -1) == '%'){
					$val = $this->escape($val);
					$sql .= $sql ? " $font `$key` LIKE '$val' " : " `$key` LIKE '$val'";
				}else{
					$val = $this->escape($val);
					$sql .= $sql ? " $font $key '$val' " : " $key '$val'";
				}
			}
			return $sql;
		} else {
			return $where;
		}
	}

	/**
	 * 获取最后数据库操作影响到的条数
	 * @return int
	 */
	final public function affected_rows() {
		return $this->db->affected_rows();
	}

	// 设置操作表
	final public function table($name = '') {
		$name && $this->table_name = strpos($name, $this->db_tablepre) === 0 ? $name : $this->dbprefix($name);
		return $this;
	}

	// 获取表前缀
	final public function dbprefix($name = '') {
		return $this->db_tablepre.$name;
	}

	// 附表不存在时创建附表
	public function is_data_table($table, $tid) {
		if ($tid > 0 && !$this->table_exists($table.$tid)) {
			// 附表不存在时创建附表
			$this->query("SHOW CREATE TABLE `".$table."0`");
			$sql = $this->fetch_array();
			$this->query(str_replace(
				array($sql[0]['Table'], 'CREATE TABLE '),
				array($table.$tid, 'CREATE TABLE IF NOT EXISTS '),
				$sql[0]['Create Table']
			));
		}
	}

	/**
	 * 获取数据表主键
	 * @return array
	 */
	final public function get_primary($table = '') {
		if (empty($table)) {
			$table = $this->table_name;
		}
		$table = strpos($table, $this->db_tablepre) === 0 ? $table : $this->dbprefix($table);
		return $this->db->get_primary($table);
	}

	/**
	 * 获取表字段
	 * @param string $table 	表名
	 * @return array
	 */
	final public function get_fields($table = '') {
		if (empty($table)) {
			$table = $this->table_name;
		}
		$table = strpos($table, $this->db_tablepre) === 0 ? $table : $this->dbprefix($table);
		return $this->db->get_fields($table);
	}

	/**
	 * 检查表是否存在
	 * @param $table 表名
	 * @return boolean
	 */
	final public function table_exists($table){
		if (empty($table)) {
			$table = $this->table_name;
		}
		$table = strpos($table, $this->db_tablepre) === 0 ? $table : $this->dbprefix($table);
		return $this->db->table_exists($table);
	}

	/**
	 * 检查字段是否存在
	 * @param $field 字段名
	 * @return boolean
	 */
	public function field_exists($field, $table = '') {
		if (empty($field)) {
			return 0;
		}
		if (empty($table)) {
			$table = $this->table_name;
		}
		$table = strpos($table, $this->db_tablepre) === 0 ? $table : $this->dbprefix($table);
		$fields = $this->db->get_fields($table);
		return array_key_exists($field, $fields);
	}

	// 获取所有表
	final public function list_tables() {
		return $this->db->list_tables();
	}

	// 字段值是否存在
	public function is_exists($id, $name, $value, $where = '') {
		$key = $this->get_primary();
		$a = array($key.'<>'=>$id, $name=>$value);
		$rt = $this->count(dr_array22array($where, $a));
		return $rt;
	}

	/**
	 * 返回数据结果集
	 * @param $query （mysql_query返回值）
	 * @return array
	 */
	final public function fetch_array() {
		$data = array();
		while($r = $this->db->fetch_next()) {
			$data[] = $r;		
		}
		return $data;
	}

	// 获取当前执行后的sql语句
	public function get_sql_query() {

		if (!$this->db) {
			return '';
		} elseif (!method_exists($this->db, 'getLastQuery')) {
			return '';
		}

		$my = $this->db->getLastQuery();
		if (!$my) {
			return '';
		}

		return (string)$my;
	}

	public function escape($str){
		return $this->db->escape($str);
	}

	public function error() {
		return $this->db->error();
	}

	/**
	 * 返回数据库版本号
	 */
	final public function version() {
		return $this->db->version();
	}

	// 附表分表规则
	public function get_table_id($id) {
		return floor($id / 100000);
	}

	// 显示数据库错误
	private function _return_error($msg) {
		return IS_ADMIN || IS_DEV ? dr_json(0, $msg) : dr_json(0, L('系统错误'));
	}
}