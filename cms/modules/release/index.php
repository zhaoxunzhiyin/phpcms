<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class index extends admin {
	private $input, $db, $siteid, $site, $point;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('release_point_model');
		$this->siteid = $this->get_siteid();
		$site = pc_base::load_app_class('sites', 'admin');
		$this->site = $site->get_by_id($this->siteid);
		$this->point = explode(',', $this->site['release_point']);
		pc_base::load_app_func('global');
		del_queue();
	}
	
	public function init() {
		if (empty($this->point[0])) {
			dr_admin_msg(0,L('the_site_not_release'),'close');
		}
		$ids = $this->input->get('ids') && trim($this->input->get('ids')) ? trim($this->input->get('ids')) : 0;
		$statuses = $this->input->get('statuses') && intval($this->input->get('statuses')) ? intval($this->input->get('statuses')) : 0;
		if($this->input->get('iniframe')) $show_header = true;
		include $this->admin_tpl('release_list');
	}
	
	public function public_sync() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : 0;
		$ids = $this->input->get('ids') && trim($this->input->get('ids')) ? trim($this->input->get('ids')) : 0;
		$total = $this->input->get('total') && intval($this->input->get('total')) ? intval($this->input->get('total')) : 0;
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$statuses = $this->input->get('statuses') && intval($this->input->get('statuses')) ? intval($this->input->get('statuses')) : 0;
		$pagesize = 5;
		$queue = pc_base::load_model('queue_model');
		set_time_limit(600);
		if (!empty($ids)) {
			$ids = explode(',', $ids);
			if (empty($total)) {
				$total = dr_count($ids);
			}
			$sql = "siteid = '".$this->siteid."' AND status".($id+1)." = $statuses AND id in ('".implode('\',\'', $ids)."')";
			$data = $queue->select($sql, 'id, type, path');
		}else {
			if (empty($total)) {
				$total = $queue->count(array("siteid"=>$this->siteid, "status".($id+1)=>$statuses));
			}
			$totalpage = ceil($total/$pagesize);
			$data = $queue->select(array("siteid"=>$this->siteid, "status".($id+1)=>$statuses), 'id, type, path', $pagesize);
		}
		$release_point = $this->db->get_one(array('id'=>$this->point[$id]));
		$ftps = pc_base::load_sys_class('ftps');
		if(is_array($data) && !empty($data)) if ($ftps->connect($release_point['host'], $release_point['username'], $release_point['password'], $release_point['port'], $release_point['pasv'], $release_point['ssl'])) {
			if ($release_point['path']) {
				$ftps->chdir($release_point['path']);
			}
			foreach ($data as $v) {
				$status = -1;
				switch ($v['type']) {
					case 'del':
						if ($ftps->f_delete($release_point['path'].$v['path'])) {
							$status = 1;
						}
						break;
					case 'add':
					case 'edit':
						if ($ftps->put($release_point['path'].$v['path'], CMS_PATH.$v['path'])) {
							$status = 1;
						}
						break;
				}
				$queue->update(array('status'.($id+1)=>$status, 'times'=>SYS_TIME), array('id'=>$v['id']));
			}
		} else {
			exit('<script type="text/javascript">alert("'.L("release_point_connect_failure",array('name'=>$release_point['name'])).'");</script>');
		}
		
		include $this->admin_tpl('release_sync');
	}
	
	public function failed() {
		if (empty($this->point[0])) {
			dr_admin_msg(0,L("the_site_not_release"));
		}
		
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		
		$sql = '';
		$i = 1;
		foreach ($this->point as $v) {
			$sql .= $sql ? " or status".$i." = '-1'" :" status".$i." = '-1'";
			$i++;
		}
		$sql .= ' AND siteid = \''.$this->siteid.'\'';
		$queue = pc_base::load_model('queue_model');
		$list = $queue->listinfo($sql, 'id desc', $page, SYS_ADMIN_PAGESIZE);
		pc_base::load_sys_class('format', '', 0);
		include $this->admin_tpl('release_failed_list');
	}
	
	public function del() {
		$ids = $this->input->get_post_ids() ? $this->input->get_post_ids() : dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		if (is_array($ids))$ids = implode('\',\'', $ids);
		$queue = pc_base::load_model('queue_model');
		$queue->delete("id in ('$ids')");
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
}