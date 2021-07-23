<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class release_point extends admin {
	private $db;
	public $ssl = 0;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('release_point_model');
		if (function_exists('ftp_ssl_connect')) {
			$this->ssl = 1;
		}
	}
	
	public function init() {
		$list = $this->db->select();
		$big_menu = array('javascript:artdialog(\'add\',\'?m=admin&c=release_point&a=add\',\''.L('release_point_add').'\',700,500);void(0);', L('release_point_add'));
		include $this->admin_tpl('release_point_list');
	}
	
	public function add() {
		if ($this->input->post('dosubmit')) {
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : showmessage(L('release_point_name').L('empty'));
			$host = $this->input->post('host') && trim($this->input->post('host')) ? trim($this->input->post('host')) : showmessage(L('server_address').L('empty'));
			$port = $this->input->post('port') && intval($this->input->post('port')) ? intval($this->input->post('port')) : showmessage(L('server_port').L('empty'));
			$username = $this->input->post('username') && trim($this->input->post('username')) ? trim($this->input->post('username')) : showmessage(L('username').L('empty'));
			$password = $this->input->post('password') && trim($this->input->post('password')) ? trim($this->input->post('password')) : showmessage(L('password').L('empty'));
			$path = $this->input->post('path') && trim($this->input->post('path')) ? trim($this->input->post('path')) : showmessage(L('path').L('empty'));
			$pasv = $this->input->post('pasv') && trim($this->input->post('pasv')) ? trim($this->input->post('pasv')) : 0;
			$ssl = $this->input->post('ssl') && trim($this->input->post('ssl')) ? trim($this->input->post('ssl')) : 0;
			if ($this->db->get_one(array("name"=>$name))) {
				showmessage(L('release_point_name').L('exists'));
			}
			if ($this->db->insert(array('name'=>$name,'host'=>$host,'port'=>$port,'username'=>$username, 'password'=>$password, 'path'=>$path, 'pasv'=>$pasv, 'ssl'=>$ssl))) {
				showmessage(L('operation_success'), '', '', 'add');
			} else {
				showmessage(L('operation_failure'));
			}
		}
		$show_header = $show_validator = true;
		include $this->admin_tpl('release_point_add');
	}
	
	public function edit() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : showmessage(L('illegal_parameters'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('id'=>$id))) {
			if ($this->input->post('dosubmit')) {
				$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : showmessage(L('release_point_name').L('empty'));
				$host = $this->input->post('host') && trim($this->input->post('host')) ? trim($this->input->post('host')) : showmessage(L('server_address').L('empty'));
				$port = $this->input->post('port') && intval($this->input->post('port')) ? intval($this->input->post('port')) : showmessage(L('server_port').L('empty'));
				$username = $this->input->post('username') && trim($this->input->post('username')) ? trim($this->input->post('username')) : showmessage(L('username').L('empty'));
				$password = $this->input->post('password') && trim($this->input->post('password')) ? trim($this->input->post('password')) : showmessage(L('password').L('empty'));
				$path = $this->input->post('path') && trim($this->input->post('path')) ? trim($this->input->post('path')) : showmessage(L('path').L('empty'));
				$pasv = $this->input->post('pasv') && trim($this->input->post('pasv')) ? trim($this->input->post('pasv')) : 0;
				$ssl = $this->input->post('ssl') && trim($this->input->post('ssl')) ? trim($this->input->post('ssl')) : 0;
				if ($data['name'] != $name && $this->db->get_one(array("name"=>$name))) {
					showmessage(L('release_point_name').L('exists'));
				}
				if ($this->db->update(array('name'=>$name,'host'=>$host,'port'=>$port,'username'=>$username, 'password'=>$password, 'path'=>$path, 'pasv'=>$pasv, 'ssl'=>$ssl), array('id'=>$id))) {
					showmessage(L('operation_success'), '', '', 'edit');
				} else {
					showmessage(L('operation_failure'));
				}
			}
			$show_header = $show_validator = true;
			include $this->admin_tpl('release_point_edit');
		} else {
			showmessage(L('notfound'), HTTP_REFERER);
		}

	}
	
	public function public_name() {
		$name = $this->input->get('name') && trim($this->input->get('name')) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('name'))) : trim($this->input->get('name'))) : exit('0');
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : '';
		$data = array();
		if ($id) {
			$data = $this->db->get_one(array('id'=>$id), 'name');
			if (!empty($data) && $data['name'] == $name) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('name'=>$name), 'id')) {
			exit('0');
		} else {
			exit('1');
		}
	}
	
	public function del() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : showmessage(L('illegal_parameters'), HTTP_REFERER);
		if ($this->db->get_one(array('id'=>$id))) {
			if ($this->db->delete(array('id'=>$id))) {
				showmessage(L('operation_success'), HTTP_REFERER);
			} else {
				showmessage(L('operation_failure'), HTTP_REFERER);
			}
		} else {
			showmessage(L('notfound'), HTTP_REFERER);
		}
	}
	
	public function public_test_ftp() {
		$host = $this->input->get('host') && trim($this->input->get('host')) ? trim($this->input->get('host')) : exit('0');
		$port = $this->input->get('port') && intval($this->input->get('port')) ? intval($this->input->get('port')) : exit('0');
		$username = $this->input->get('username') && trim($this->input->get('username')) ? trim($this->input->get('username')) : exit('0');
		$password = $this->input->get('password') && trim($this->input->get('password')) ? trim($this->input->get('password')) : exit('0');
		$pasv = $this->input->get('pasv') && trim($this->input->get('pasv')) ? trim($this->input->get('pasv')) : 0;
		$ssl = $this->input->get('ssl') && trim($this->input->get('ssl')) ? trim($this->input->get('ssl')) : 0;
		$ftp = pc_base::load_sys_class('ftps');
		if ($ftp->connect($host, $username, $password, $port, $pasv, $ssl, 25)) {
			if ($ftp->link_time > 15) {
				exit(L('ftp_connection_a_long_time'));
			}
			exit('1');
		} else {
			exit(L('can_ftp_server_connections'));
		}
	}
}