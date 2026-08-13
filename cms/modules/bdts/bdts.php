<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_sys_class('model', '', 0);

class bdts extends admin {
	private $input,$db,$siteid,$sitemodel_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
	}
	
	//百度推送
	public function log_index() {
		$data = $list = array();
		$page = max(1, (int)$this->input->get('page'));
		$file = CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'_log.php.php';
		if (is_file($file)) {
			$data = explode(PHP_EOL, file_get_contents($file));
			$data = $data ? array_reverse($data) : array();
			$limit = ($page - 1) * SYS_ADMIN_PAGESIZE;
			$i = $j = 0;
			foreach ($data as $v) {
				if ($v && $i > $limit && $j < SYS_ADMIN_PAGESIZE) {
					$list[] = $v;
					$j ++;
				}
				$i ++;
			}
		}
		$total = $data ? max(0, count($data) - 1) : 0;
		$pages = pages($total, $page, SYS_ADMIN_PAGESIZE);
		include $this->admin_tpl('index');
	}
	
	//参数配置
	public function config() {
		if(IS_POST) {
			$post = $this->input->post('data');
			if (isset($post['bdts']) && $post['bdts']) {
				$bdts = [];
				foreach ($post['bdts'] as $i => $t) {
					if (isset($t['site'])) {
						if (!$t['site']) {
							dr_json(0, L('域名必须填写'));
						} elseif (strpos($t['site'], '://') !== false) {
							dr_json(0, L('域名不能带有://符号，请联系纯域名'));
						}
						$bdts[$i]['site'] = $t['site'];
					} else {
						if (!$t['token']) {
							dr_json(0, L('token必须填写'));
						}
						$bdts[$i-1]['token'] = $t['token'];
					}
				}
				$post['bdts'] = $bdts;
			}
			pc_base::load_app_class('admin_bdts', 'bdts')->setConfig($post);
			dr_json(1,L('操作成功'));
		}else{
			$this->siteid = $this->get_siteid();
			$page = max(intval($this->input->get('page')), 0);
			$this->sitemodel_db = pc_base::load_model('sitemodel_model');
			$sitemodel_data = $this->sitemodel_db->select(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0), "*", '', 'sort,modelid');
			$data = pc_base::load_app_class('admin_bdts', 'bdts')->getConfig();
			$bdts = $data['bdts'];
			include $this->admin_tpl('config');
		}
	}
	
	public function del() {

		@unlink(CACHE_PATH.'caches_bdts/bdts'.$this->siteid.'_log.php.php');

		dr_admin_msg(1,L('操作成功'), '?m=bdts&c=bdts&a=log_index&menuid='.$this->input->get('menuid'));
	}
	
	//手动推送
	public function url_add() {

		if (IS_POST) {
			$url = $this->input->post('url');
			if (!$url) {
				dr_json(0, L('URL不能为空'), array('field' => 'url'));
			}
			
			$rt = pc_base::load_app_class('admin_bdts', 'bdts')->url_bdts($url, '手动');
			if (!$rt['code']) {
				dr_json(0, $rt['msg']);
			}

			dr_json(1,L('操作成功'));
		}

		$show_header = true;
		$page = max(intval($this->input->get('page')), 0);
		include $this->admin_tpl('url_add');
		exit;
	}
	
	//在线帮助
	public function help() {
		$show_header = $show_dialog = $show_pc_hash = true;
		include $this->admin_tpl('help');
	}
	
	//批量百度主动推送
	public function add() {

		$mid = intval($this->input->get('modelid'));
		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('所选数据不存在'));
		} elseif (!$mid) {
			dr_json(0, L('模块参数不存在'));
		}

		$this->db->set_model($mid);
		$sitemodel_model_db = pc_base::load_model('sitemodel_model');
		$sitemodel = $sitemodel_model_db->get_one(array('modelid'=>$mid));
		if($this->db->table_name==$this->db->db_tablepre) dr_json(0, L('模型被禁用或者是模型内容表不存在'));
		$data = $this->db->select(array('id'=>$ids));
		if (!$data) {
			dr_json(0, L('所选数据为空'));
		}

		$ct = 0;
		foreach ($data as $t) {
			if(strpos($t['url'],'http://')!==false || strpos($t['url'],'https://')!==false) {
			} else {
				$t['url'] = siteurl($this->siteid).$t['url'];
			}
			pc_base::load_app_class('admin_bdts', 'bdts')->module_bdts($sitemodel['tablename'], $t['url'], 'edit');
			$ct++;
		}

		dr_json(1, L('共批量'.$ct.'个URL'));
	}
}
?>