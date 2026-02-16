<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

pc_base::load_sys_class('form', '', 0);
pc_base::load_sys_class('format', '', 0);
class special extends admin {
	private $input, $db, $special_api, $attachment_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('special_model');
		$this->special_api = pc_base::load_app_class('special_api', 'special');
	}
	
	/**
	 * 专题列表
	 */
	public function init() {
		$menu_db = pc_base::load_model('menu_model');
		$menu_data = $menu_db->get_one(array('name' => 'special', 'm' => 'special', 'c' => 'special', 'a' => 'init'));
		$page = max(intval($this->input->get('page')), 1);
		$infos = $this->db->listinfo(array('siteid'=>$this->get_siteid()), '`listorder` DESC, `id` DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		pc_base::load_sys_class('format', '', 0);
		include $this->admin_tpl('special_list');
	}
	
	/**
	 * 添加专题
	 */
	public function add() {
		if (IS_POST) {
			$special = $this->check($this->input->post('special'));
			$type = $this->input->post('type');
			if (!$type[1]['name']) {
				dr_json(0, L('type_name').L('empty'));
			}
			if (!$type[1]['typedir']) {
				dr_json(0, L('type_path').L('empty'));
			}
			$id = $this->db->insert($special, true);
			if ($id) {
				$this->special_api->_update_type($id, $type);
				if ($special['siteid']>1) {
					$site = pc_base::load_app_class('sites', 'admin');
					$site_info = $site->get_by_id($special['siteid']);
					if ($special['ishtml']) {
						$special['filename'] = str_replace('..','',$special['filename']);
						$url =  $site_info['domain'].'special/'.$special['filename'].'/';
					} else {
						$url = $site_info['domain'].'index.php?m=special&c=index&id='.$id;
					}
				} else {
					$url = $special['ishtml'] ? APP_PATH.substr(SYS_HTML_ROOT, 1).'/special/'.$special['filename'].'/' : APP_PATH.'index.php?m=special&c=index&id='.$id;
				}
				$this->db->update(array('url'=>$url), array('id'=>$id, 'siteid'=>$this->get_siteid()));
				
				//调用生成静态类
				if ($special['ishtml']) {
					$html = pc_base::load_app_class('html', 'special'); 
					$html->_index($id, 20, 5);
				}
				//更新附件状态
				if(SYS_ATTACHMENT_STAT) {
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_update(array($special['thumb'], $special['banner']),'special-'.$id, 1);
				}
				$this->special_cache();
			}
			dr_json(1, L('add_special_success'), array('url' => '?m=special&c=special&a=init&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			//获取站点模板信息
			pc_base::load_app_func('global', 'admin');
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			include $this->admin_tpl('special_add');
		}
	}
	
	/**
	 * 专题修改
	 */
	public function edit() {
		if (!$this->input->get('specialid') || empty($this->input->get('specialid'))) {
			dr_json(0,L('illegal_action'), HTTP_REFERER);
		}
		$specialid = intval($this->input->get('specialid'));
		if (IS_POST) {
			$special = $this->check($this->input->post('special'), 'edit');
			$type = $this->input->post('type');
			if (!$type[1]['name']) {
				dr_json(0, L('type_name').L('empty'));
			}
			if (!$type[1]['typedir']) {
				dr_json(0, L('type_path').L('empty'));
			}
			$siteid = get_siteid();
			$site = pc_base::load_app_class('sites', 'admin');
			$site_info = $site->get_by_id($siteid);
			if ($special['ishtml'] && $special['filename']) {
				$special['filename'] = str_replace('..','',$special['filename']);
				if ($siteid>1) {
					$special['url'] =  $site_info['domain'].'special/'.$special['filename'].'/';
				} else {
					$special['url'] = APP_PATH.substr(SYS_HTML_ROOT, 1).'/special/'.$special['filename'].'/';
				}
			} elseif ($special['ishtml']=='0') {
				if ($siteid>1) {
					$special['url'] = $site_info['domain'].'index.php?m=special&c=index&specialid='.$specialid;
				} else {
					$special['url'] = APP_PATH.'index.php?m=special&c=index&specialid='.$specialid;
				}
			}
			$this->db->update($special, array('id'=>$specialid, 'siteid'=>$this->get_siteid()));
			$this->special_api->_update_type($specialid, $type, 'edit');
			
			//调用生成静态类
			if ($special['ishtml']) {
				$html = pc_base::load_app_class('html', 'special'); 
				$html->_index($specialid, 20, 5);
			}
			//更新附件状态
			if(SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update(array($special['thumb'], $special['banner']),'special-'.$specialid, 1);
			}
			$this->special_cache();
			dr_json(1, L('edit_special_success'), array('url' => '?m=special&c=special&a=init&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			$info = $this->db->get_one(array('id'=>$specialid, 'siteid'=>$this->get_siteid()));
			//获取站点模板信息
			pc_base::load_app_func('global', 'admin');
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			if ($info['pics']) {
				$pics = explode('|', $info['pics']);
			}
			if ($info['voteid']) {
				$vote_info = explode('|', $info['voteid']);
			}
			$type_db = pc_base::load_model('type_model');
			$types = $type_db->select(array('module'=>'special', 'parentid'=>$specialid, 'siteid'=>$this->get_siteid()), '`typeid`, `name`, `listorder`, `typedir`', '', '`listorder` ASC, `typeid` ASC');
			include $this->admin_tpl('special_edit');
		}
	}
	
	/**
	 * 信息导入专题
	 */
	public function import() {
		if(IS_POST) {
			$ids = $this->input->get_post_ids();
			if(!is_array($ids) || empty($ids) || !$this->input->post('modelid')) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
			if(!$this->input->post('typeid') || empty($this->input->post('typeid'))) dr_admin_msg(0,L('select_type'), HTTP_REFERER);
			foreach($ids as $id) {
				$this->special_api->_import($this->input->post('modelid'), $this->input->get('specialid'), $id, $this->input->post('typeid'), $this->input->post('listorder')[$id]);
			}
			$html = pc_base::load_app_class('html', 'special'); 
			$html->_index($this->input->get('specialid'), 20, 5);
			dr_admin_msg(1,L('import_success'), '', '', 'import');
		} else {
			if(!$this->input->get('specialid')) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
			$modelid = $this->input->get('modelid') ? intval($this->input->get('modelid')) : 0;
			$catid = $this->input->get('catid') ? intval($this->input->get('catid')) : 0;
			$page = max(intval($this->input->get('page')), 1);
			$where = '';
			if($catid) $where .= get_sql_catid('module/category-'.$this->get_siteid().'-data', $catid)." AND `status`=99";
			else $where .= " `status`=99";
			if($this->input->get('start_time')) {
				$where .= ' AND `inputtime` BETWEEN ' . max((int)strtotime(strpos($this->input->get('start_time'), ' ') ? $this->input->get('start_time') : $this->input->get('start_time').' 00:00:00'), 1) . ' AND ' . ($this->input->get('end_time') ? (int)strtotime(strpos($this->input->get('end_time'), ' ') ? $this->input->get('end_time') : $this->input->get('end_time').' 23:59:59') : SYS_TIME);
			}
			if ($this->input->get('key')) {
				$where .= " AND `title` LIKE '%".$this->db->escape($this->input->get('key'))."%' OR `keywords` LIKE '%".$this->db->escape($this->input->get('key'))."%'";
			}
			$data = $this->special_api->_get_import_data($modelid, $where, $page);
			$pages = $this->special_api->pages;
			$models = getcache('model','commons');
			$model_datas = array();
			foreach($models as $_k=>$_v) {
				if($_v['siteid']==$this->get_siteid()) {
					$model_datas[$_v['modelid']] = $_v['name'];
				}
			}
			$model_form = form::select($model_datas, $modelid, 'name="modelid" onchange="select_categorys(this.value)"', L('select_model'));
			$types = $this->special_api->_get_types($this->input->get('specialid'));
			include $this->admin_tpl('import_content');
		}
	}
	
	public function public_get_pics() {
		$modelid = $this->input->get('modelid') ? intval($this->input->get('modelid')) : 0;
		$catid = $this->input->get('catid') ? intval($this->input->get('catid')) : 0;
		$page = max(intval($this->input->get('page')), 1);
		$where = '';
		if($catid) $where .= get_sql_catid('module/category-'.$this->get_siteid().'-data', $catid)." AND `status`=99";
		else $where .= " `status`=99";
		if ($this->input->get('title')) {
			$where .= " AND `title` LIKE '%".$this->db->escape($this->input->get('title'))."%'";
		}
		if($this->input->get('start_time')) {
			$where .= ' AND `inputtime` BETWEEN ' . max((int)strtotime(strpos($this->input->get('start_time'), ' ') ? $this->input->get('start_time') : $this->input->get('start_time').' 00:00:00'), 1) . ' AND ' . ($this->input->get('end_time') ? (int)strtotime(strpos($this->input->get('end_time'), ' ') ? $this->input->get('end_time') : $this->input->get('end_time').' 23:59:59') : SYS_TIME);
		}
		$data = $this->special_api->_get_import_data($modelid, $where, $page);
		$pages = $this->special_api->pages;
		$models = getcache('model','commons');
		$model_datas = array();
		foreach($models as $_k=>$_v) {
			if($_v['siteid']==$this->get_siteid()) {
				$model_datas[$_v['modelid']] = $_v['name'];
			}
		}
		$model_form = form::select($model_datas, $modelid, 'name="modelid" onchange="select_categorys(this.value)"', L('select_model'));
		$types = $this->special_api->_get_types($this->input->get('specialid'));
		include $this->admin_tpl('import_pics');
	}
	
	public function html() {
		if(!$this->input->post('id') || empty($this->input->post('id'))) {
			$result = $this->db->select(array('disabled'=>0, 'siteid'=>$this->get_siteid()), 'id', '', '', '', 'id');
			$id = array_keys($result);
		} else {
			$id = $this->input->post('id');
		}
		setcache('create_specials', $id, 'commons');
		$this->public_create_html();
	}
	
	public function create_special_list() {
		$siteid = get_siteid();
		$html = pc_base::load_app_class('html');
		$c = pc_base::load_model('special_model');
		$result = $c->get_one(array('siteid'=>$siteid), 'COUNT(*) AS total');
		$total = $result['total'];
		$pages = ceil($total/10);
		for ( $i=1; $i <= $pages; $i++ ){ 
			$size = $html->create_list($i);
		}
		dr_admin_msg(1,L('index_create_finish',array('size'=>format_file_size($size))));
	}
	
	/**
	 * 专题排序
	 */
	public function listorder() {
		if(IS_POST) {
			$listorder = $this->input->post('listorder');
			if (isset($listorder) && is_array($listorder)) {
				foreach($listorder as $id => $order) {
					$id = intval($id);
					$order = intval($order);
					$this->db->update(array('listorder'=>$order), array('id'=>$id));
				}
			}
			$this->special_cache();
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('please_in_admin'), HTTP_REFERER);
		}
	}
	
	//生成专题首页控制中心
	public function public_create_html() {
		
		$specials = getcache('create_specials', 'commons');
		if (is_array($specials) && !empty($specials)) {
			$specialid = array_shift($specials);
			setcache('create_specials', $specials, 'commons');
			$this->create_index($specialid);
		} else {
			delcache('create_specials', 'commons');
			dr_admin_msg(1,L('update_special_success'), '?m=special&c=special&a=init');
		}
	}
	
	//生成某专题首页
	private function create_index($specialid) {
		$info = $this->db->get_one(array('id'=>$specialid));
		if (!$info['ishtml']) {
			dr_admin_msg(0,$info['title'].L('ishtml').L('close'), '?m=special&c=special&a=public_create_html');
		}
		$html = pc_base::load_app_class('html');
		$html->_index($specialid);
		dr_admin_msg(1,$info['title'].L('index_update_success'), '?m=special&c=special&a=public_create_type&specialid='.$specialid);
	}
	
	//生成专题里列表页
	public function public_create_type() {
		$specialid = $this->input->get('specialid') ? intval($this->input->get('specialid')) : 0;
		if (!$specialid) dr_admin_msg(0,L('illegal_action'));
		$page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
		$pages = $this->input->get('pages') ? intval($this->input->get('pages')) : 0;
		$types = getcache('create_types', 'commons');
		if (is_array($types) && !empty($types) || $pages) {
			if (!isset($page) || $page==1) {
				$typeids = array_keys($types);
				$typeid = array_shift($typeids);
				$typename = $types[$typeid];
				unset($types[$typeid]);
				setcache('create_types', $types, 'commons');
			}
			if (!$pages) {
				$c = pc_base::load_model('special_content_model');
				$result = $c->get_one(array('typeid'=>$typeid), 'COUNT(*) AS total');
				$total = $result['total'];
				$pages = ceil($total/10);
			}
			if ($this->input->get('typeid')) {
				$typeid = intval($this->input->get('typeid'));
				$typename = $this->input->get('typename');
			}
			$maxpage = $page+10;
			if ($maxpage>$pages) {
				$maxpage = $pages;
			}
			for ($page; $page<=$maxpage; $page++) {
				$html = pc_base::load_app_class('html');
				$html->create_type($typeid, $page);
			}
			if (empty($types) && $pages==$maxpage) {
				delcache('create_types', 'commons');
				dr_admin_msg(1,$typename.L('type_update_success'), '?m=special&c=special&a=public_create_content&specialid='.$specialid);
			}
			if ($pages<=$maxpage) {
				dr_admin_msg(1,$typename.L('update_success'), '?m=special&c=special&a=public_create_type&specialid='.$specialid);
			} else {
				dr_admin_msg(1,$typename.L('type_from').($this->input->get('page') ? $this->input->get('page') : 1).L('type_end').$maxpage.'</font> '.L('update_success'), '?m=special&c=special&a=public_create_type&typeid='.$typeid.'&typename='.$typename.'&page='.$page.'&pages='.$pages.'&specialid='.$specialid);
			}
			
		} else {
			$special_api = pc_base::load_app_class('special_api');
			$types = $special_api->_get_types($specialid);
			setcache('create_types', $types, 'commons');
			dr_admin_msg(1,L('start_update_type'), '?m=special&c=special&a=public_create_type&specialid='.$specialid);
		}
	}
	
	//生成内容页
	public function public_create_content() {
		$specialid = $this->input->get('specialid') ? intval($this->input->get('specialid')) : 0;
		if (!$specialid) dr_admin_msg(0,L('illegal_action'));
		$pages = $this->input->get('pages') ? intval($this->input->get('pages')) : 0;
		$page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
		$c = pc_base::load_model('special_content_model');
		if (!$pages) {
			$result = $c->get_one(array('specialid'=>$specialid, 'isdata'=>1), 'COUNT(*) AS total');
			$total = $result['total'];
			$pages = ceil($total/10);
		}
		$offset = ($page-1)*10;
		$result = $c->select(array('specialid'=>$specialid, 'isdata'=>1), 'id', $offset.', 10', 'listorder ASC, id ASC');
		foreach ($result as $r) {
			$html = pc_base::load_app_class('html');
			$urls = $html->_create_content($r['id']);
			$c->update(array('url'=>$urls[0]), array('id'=>$r['id']));
		}
		if ($page>=$pages) {
			dr_admin_msg(1,L('content_update_success'), '?m=special&c=special&a=public_create_html&specialid='.$specialid);
		} else {
			$page++;
			dr_admin_msg(1,L('content_from').' <font color="red">'.intval($offset+1).L('type_end').intval($offset+10).'</font> '.L('update_success'), '?m=special&c=special&a=public_create_content&specialid='.$specialid.'&page='.$page.'&pages='.$pages);
		}
	}
	
	/**
	 * 推荐专题
	 */
	public function elite() {
		if(!$this->input->get('id') || empty($this->input->get('id'))) {
			dr_admin_msg(0,L('illegal_action'));
		}
		$value = $this->input->get('value') ? intval($this->input->get('value')) : 0;
		$this->db->update(array('elite'=>$value), array('id'=>$this->input->get('id'), 'siteid'=>get_siteid()));
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 * 删除专题 未执行删除操作，仅进行递归循环
	 */
	public function delete($id = 0) {
		if((!$this->input->get('id') || empty($this->input->get('id'))) && (!$this->input->post('id') || empty($this->input->post('id'))) && !$id) {
			dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		}
		if(is_array($this->input->post('id')) && !$id) {
			foreach($this->input->post('id') as $sid) {
				$this->special_api->_del_special($sid);
			}
			$this->special_cache();
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} elseif(is_numeric($id) && $id) {
			$id = $this->input->get('id') ? intval($this->input->get('id')) : intval($id);
			$this->special_api->_del_special($id);
			return true;
		} else {
			$id = $this->input->get('id') ? intval($this->input->get('id')) : intval($id);
			$this->special_api->_del_special($id);
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	
	/**
	 * 专题缓存
	 */
	private function special_cache() {
		$specials = array();
		$result = $this->db->select(array('disabled'=>0), '`id`, `siteid`, `title`, `url`, `thumb`, `banner`, `ishtml`', '', '`listorder` DESC, `id` DESC');
		foreach($result as $r) {
			$specials[$r['id']] = $r;
		}
		setcache('special', $specials, 'commons');
		return true;
	}
	
	/**
	 * 获取专题的分类 
	 * 
	 * @param intval $specialid 专题ID
	 * @return 返回此专题分类的下拉列表
	 */
	public function public_get_type() {
		$specialid = intval($this->input->get('specialid'));
		if(!$specialid) return '';
		$datas = $this->special_api->_get_types($specialid);
		echo form::select($types, 0, 'name="typeid" id="typeid" onchange="import_c('.$specialid.', this.value)"', L('please_select'));
	}
	
	/**
	 * 按模型ID列出模型下的栏目
	 */
	public function public_categorys_list() {
		$modelid = intval($this->input->get('modelid'));
		if(!isset($modelid) || empty($modelid)) exit('');
		exit(form::select_category('', $this->input->get('catid'), 'name="catid" id="catid"', L('please_select'), $modelid, 0, 1));
	}
	
	/**
	 * ajax验证专题是否已存在
	 */
	public function public_check_special() {
		$title = $this->input->get('title');
		if(!$title) exit(0);
		if(pc_base::load_config('system', 'charset')=='gbk') {
			$title = safe_replace(iconv('UTF-8', 'GBK', $title));
		}
		if($this->input->get('id')) {
			$id = intval($this->input->get('id'));
			if($this->db->get_one(array('title'=>$title, 'id'=>$id, 'siteid'=>$this->get_siteid()))) {
				exit('1');
			}
		}
		$r = $this->db->get_one(array('siteid' => $this->get_siteid(), 'title' => $title), 'id');
		if($r['id']) {
			exit('0');
		} else {
			exit('1');
		}
	}
	
	/**
	 * ajax检验专题静态文件名是否存在，避免专题页覆盖
	 */
	public function public_check_dir() {
		if(!$this->input->get('filename')) exit(1);
		if($this->input->get('id')) {
			$id = intval($this->input->get('id'));
			$r = $this->db->get_one(array('id'=>$id, 'siteid'=>$this->get_siteid()));
			if($r['filename'] = $this->input->get('filename')) {
				exit('1');
			}
		}
		$r = $this->db->get_one(array('siteid'=>$this->get_siteid(), 'filename'=>$this->input->get('filename')), 'id');
		if($r['id']) {
			exit('0');
		} else {
			exit('1');
		}
	}
	
	/**
	 * 表单验证
	 * @param array $data 表单传递的值
	 * @param string $a add/edit添加操作时，自动加上默认值
	 */
	private function check($data, $a = 'add') {
		$security = pc_base::load_sys_class('security');
		$data = new_html_special_chars($data);
		$data = $security->xss_clean($data, true);
		if(!$data['title']) dr_admin_msg(0,L('title_cannot_empty'), HTTP_REFERER);
		if(!$data['banner']) dr_admin_msg(0,L('banner_no_empty'), HTTP_REFERER);
		if(!$data['thumb']) dr_admin_msg(0,L('thumb_no_empty'), HTTP_REFERER);
		if(is_array($data['catids']) && !empty($data['catids'])) {
			$data['catids'] = ','.implode(',', $data['catids']).',';
		}
		if($a=='add') {
			if(!$data['index_template']) $data['index_template'] = 'index';
			$data['siteid'] = $this->get_siteid();
			$data['createtime'] = SYS_TIME;
			$data['username'] = param::get_cookie('admin_username');
			$data['userid'] = param::get_session('userid');
		}
		if ($data['voteid']) {
			if (strpos($data['voteid'], '|')===false) {
				$vote_db = pc_base::load_model('vote_subject_model');
				$r = $vote_db->get_one(array('subject'=>$data['voteid'], 'siteid'=>$this->get_siteid()), 'subjectid, subject', 'addtime DESC');
				if ($r) {
					$data['voteid'] = 'vote|'.$r['subjectid'].'|'.$r['subject'];
				}
			}
		}
		return $data;
	}
}
?>