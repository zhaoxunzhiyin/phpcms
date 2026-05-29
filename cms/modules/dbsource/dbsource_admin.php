<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class dbsource_admin extends admin {
	private $input,$db;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('dbsource_model');
		pc_base::load_app_func('global');
	}
	
	public function init() {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$list = $this->db->listinfo(array('siteid'=>$this->get_siteid()), '', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('dbsource_list');
	}
	
	public function add() {
		if (IS_POST) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('dbsource_name').L('empty'), array('field' => 'name'));
			$host = $this->input->post('host') && trim($this->input->post('host')) ? trim($this->input->post('host')) : dr_admin_msg(0,L('server_address').L('empty'), array('field' => 'host'));
			$port = $this->input->post('port') && intval($this->input->post('port')) ? intval($this->input->post('port')) : dr_admin_msg(0,L('server_port').L('empty'), array('field' => 'port'));
			$username = $this->input->post('username') && trim($this->input->post('username')) ? trim($this->input->post('username')) : dr_admin_msg(0,L('username').L('empty'), array('field' => 'username'));
			$password = $this->input->post('password') && trim($this->input->post('password')) ? trim($this->input->post('password')) : dr_admin_msg(0,L('password').L('empty'), array('field' => 'password'));
			$dbname = $this->input->post('dbname') && trim($this->input->post('dbname')) ? trim($this->input->post('dbname')) : dr_admin_msg(0,L('database').L('empty'), array('field' => 'dbname'));
			$dbtablepre = $this->input->post('dbtablepre') && trim($this->input->post('dbtablepre')) ? trim($this->input->post('dbtablepre')) : '';
			$charset = $this->input->post('charset') && in_array(trim($this->input->post('charset')), array('gbk','utf8','utf8mb4', 'gb2312', 'latin1')) ? trim($this->input->post('charset')) : dr_admin_msg(0,L('charset').L('illegal_parameters'));
			$siteid = $this->get_siteid();
			if (!preg_match('/^\\w+$/i', $name)) {
				dr_admin_msg(0,L('data_source_of_the_letters_and_figures'));
			}
			//检察数据源名是否已经存在
			if ($this->db->get_one(array('siteid'=>$siteid, 'name'=>$name), 'id')) {
				dr_admin_msg(0,L('dbsource_name').L('exists'), array('field' => 'name'));
			}
			
			if ($this->db->insert(array('siteid'=>$siteid, 'name'=>$name,'host'=>$host,'port'=>$port,'username'=>$username,'password'=>$password,'dbname'=>$dbname,'dbtablepre'=>$dbtablepre,'charset'=>$charset))) {
				dbsource_cache();
				dr_admin_msg(1,L('operation_success'), '', '', 'add');
			} else {
				dr_admin_msg(0,L('operation_failure'));
			}
			
		} else {
			pc_base::load_sys_class('form', '', 0);
			$show_header = $show_validator = true;
			include $this->admin_tpl('dbsource_add');
		}
	}
	
	public function edit() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : dr_admin_msg(0,'ID'.L('empty'));
		$data = $this->db->get_one(array('id'=>$id));
		if (!$data) {
			dr_admin_msg(0,L('notfound'));
		}
		if (IS_POST) {
			$host = $this->input->post('host') && trim($this->input->post('host')) ? trim($this->input->post('host')) : dr_admin_msg(0,L('server_address').L('empty'), array('field' => 'host'));
			$port = $this->input->post('port') && intval($this->input->post('port')) ? intval($this->input->post('port')) : dr_admin_msg(0,L('server_port').L('empty'), array('field' => 'port'));
			$username = $this->input->post('username') && trim($this->input->post('username')) ? trim($this->input->post('username')) : dr_admin_msg(0,L('username').L('empty'), array('field' => 'username'));
			$password = $this->input->post('password') && trim($this->input->post('password')) ? trim($this->input->post('password')) : dr_admin_msg(0,L('password').L('empty'), array('field' => 'password'));
			$dbname = $this->input->post('dbname') && trim($this->input->post('dbname')) ? trim($this->input->post('dbname')) : dr_admin_msg(0,L('database').L('empty'), array('field' => 'dbname'));
			$dbtablepre = $this->input->post('dbtablepre') && trim($this->input->post('dbtablepre')) ? trim($this->input->post('dbtablepre')) : '';
			$charset = $this->input->post('charset') && in_array(trim($this->input->post('charset')), array('gbk','utf8','utf8mb4', 'gb2312', 'latin1')) ? trim($this->input->post('charset')) : dr_admin_msg(0,L('charset').L('illegal_parameters'));
			$siteid = $this->get_siteid();
			$sql = array('siteid'=>$siteid, 'host'=>$host,'port'=>$port,'username'=>$username,'password'=>$password,'dbname'=>$dbname, 'dbtablepre'=>$dbtablepre, 'charset'=>$charset);
			
			if ($this->db->update($sql, array('id'=>$id))) {
				dbsource_cache();
				dr_admin_msg(1,L('operation_success'), '', '', 'edit');
			} else {
				dr_admin_msg(0,L('operation_failure'));
			}
			
		} else {
			
			pc_base::load_sys_class('form', '', 0);
			$show_header = $show_validator = true;
			include $this->admin_tpl('dbsource_edit');
		}
	}
	
	public function del() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : '';
		if ($this->db->get_one(array('id'=>$id))) {
			if ($this->db->delete(array('id'=>$id))) {
				dbsource_cache();
				dr_admin_msg(1,L('operation_success'), '?m=dbsource&c=dbsource_admin&a=init');
			} else {
				dr_admin_msg(0,L('operation_failure'),  '?m=dbsource&c=dbsource_admin&a=init');
			}
		} else {
			dr_admin_msg(0,L('notfound'), '?m=dbsource&c=dbsource_admin&a=init');
		}
	}
		
	public function public_test_mysql_connect() {
		$host = $this->input->get('host') && trim($this->input->get('host')) ? trim($this->input->get('host')) : exit('0');
		$password = $this->input->get('password') && trim($this->input->get('password')) ? trim($this->input->get('password')) : exit('0');
		$port = $this->input->get('port') && intval($this->input->get('port')) ? intval($this->input->get('port')) : exit('0');
		$username = $this->input->get('username') && trim($this->input->get('username')) ? trim($this->input->get('username')) : exit('0');
		if(function_exists('mysql_connect')){
			if (@mysql_connect($host.':'.$port, $username, $password)) {
				exit('1');
			} else {
				exit('0');
			}
		}else{
			if (@mysqli_connect($host, $username, $password, null, $port)){
				exit('1');
			} else {
				exit('0');
			}
		}
	}
}