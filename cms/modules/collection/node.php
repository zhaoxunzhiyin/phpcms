<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form', '', 0);

class node extends admin {

	private $input,$cache_api,$db,$siteid,$attachment_db;
	
	//HTML标签
	private static $html_tag = array("<p([^>]*)>(.*)</p>[|]"=>'<p>', "<a([^>]*)>(.*)</a>[|]"=>'<a>',"<script([^>]*)>(.*)</script>[|]"=>'<script>', "<iframe([^>]*)>(.*)</iframe>[|]"=>'<iframe>', "<table([^>]*)>(.*)</table>[|]"=>'<table>', "<span([^>]*)>(.*)</span>[|]"=>'<span>', "<b([^>]*)>(.*)</b>[|]"=>'<b>', "<img([^>]*)>[|]"=>'<img>', "<object([^>]*)>(.*)</object>[|]"=>'<object>', "<embed([^>]*)>(.*)</embed>[|]"=>'<embed>', "<param([^>]*)>(.*)</param>[|]"=>'<param>', '<div([^>]*)>[|]'=>'<div>', '</div>[|]'=>'</div>', '<!--([^>]*)-->[|]'=>'<!-- -->');
	
	//网址类型
	private $url_list_type = array();
	
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->db = pc_base::load_model('collection_node_model');
		$this->siteid = $this->get_siteid();
		$this->url_list_type = array('1'=>L('sequence'), '2'=>L('multiple_pages'), '3'=>L('single_page'), '4'=>'RSS');
		
	}

	/**
	 * node list
	 */
	public function manage() {
		$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$nodelist = $this->db->listinfo(array('siteid'=>$this->siteid), 'nodeid DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		pc_base::load_sys_class('format', '', 0);
		include $this->admin_tpl('node_list');
	}
		
	/**
	 * add node
	 */
	public function add() {
		header("Cache-control: private");
		if(IS_POST) {
			$data = $this->input->post('data') ? $this->input->post('data') :  dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$customize_config = $this->input->post('customize_config') ? $this->input->post('customize_config') : '';
			if (!$data['name'] = trim($data['name'])) {
				dr_admin_msg(0,L('nodename').L('empty'), HTTP_REFERER);
			}
			if ($this->db->get_one(array('name'=>$data['name']))) {
				dr_admin_msg(0,L('nodename').L('exists'), HTTP_REFERER);
			}
			$data['urlpage'] = $this->input->post('urlpage'.$data['sourcetype']) ? $this->input->post('urlpage'.$data['sourcetype']) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$data['siteid']= $this->siteid;
			$data['customize_config'] = array();
			if (is_array($customize_config)) foreach ($customize_config['en_name'] as $k => $v) {
				if (empty($v) || empty($customize_config['name'][$k])) continue;
				$data['customize_config'][] = array('name'=>$customize_config['name'][$k], 'en_name'=>$v, 'rule'=>$customize_config['rule'][$k], 'html_rule'=>$customize_config['html_rule'][$k]);
			}
			$data['customize_config'] = array2string($data['customize_config']);
			if ($this->db->insert($data)) {
				dr_admin_msg(1,L('operation_success'), '?m=collection&c=node&a=manage');
			} else {
				dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
			}
		} else {
			$show_dialog = $show_validator = true;
			include $this->admin_tpl('node_form');
		}
		
	}

	//修改采集配置
	public function edit() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$data = $this->db->get_one(array('nodeid'=>$nodeid));
		if(IS_POST) {
			$datas = $data;
			unset($data);
			$data = $this->input->post('data') ? $this->input->post('data') :  dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$customize_config = $this->input->post('customize_config') ? $this->input->post('customize_config') : '';
			if (!$data['name'] = trim($data['name'])) {
				dr_admin_msg(0,L('nodename').L('empty'), HTTP_REFERER);
			}
			
			if ($datas['name'] != $data['name']) {
				if ($this->db->get_one(array('name'=>$data['name']))) {
					dr_admin_msg(0,L('nodename').L('exists'), HTTP_REFERER);
				}
			}
			
			$data['urlpage'] = $this->input->post('urlpage'.$data['sourcetype']) ? $this->input->post('urlpage'.$data['sourcetype']) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$data['customize_config'] = array();
			if (is_array($customize_config)) foreach ($customize_config['en_name'] as $k => $v) {
				if (empty($v) || empty($customize_config['name'][$k])) continue;
				$data['customize_config'][] = array('name'=>$customize_config['name'][$k], 'en_name'=>$v, 'rule'=>$customize_config['rule'][$k], 'html_rule'=>$customize_config['html_rule'][$k]);
			}
			$data['customize_config'] = array2string($data['customize_config']);
			if ($this->db->update($data, array('nodeid'=>$nodeid))) {
				dr_admin_msg(1,L('operation_success'), '?m=collection&c=node&a=manage');
			} else {
				dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
			}
		} else {
			$model_cache = getcache('model', 'commons');
			$siteid = get_siteid();
			foreach($model_cache as $k=>$v) {
				$modellist[0] = L('select_model');
				if($v['siteid'] == $siteid) {
					$modellist[$k] = $v['name'];
				}
			}
			if (isset($data['customize_config'])) {
				$data['customize_config'] = string2array($data['customize_config']);
			}
			$show_dialog = $show_validator = true;
			//print_r($nodeinfo);exit;	
			include $this->admin_tpl('node_form');
		}
	}
	
	public function html_rule() {
		header("Cache-control: private");
		$show_header = $show_dialog = $show_pc_hash = true;
		include $this->admin_tpl('html_rule');
	}
	
	//复制采集
	public function copy() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('nodeid'=>$nodeid))) {
			if (IS_POST) {
				unset($data['nodeid']);
				$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
				if ($this->db->get_one(array('name'=>$name), 'nodeid')) {
					dr_admin_msg(0,L('nodename').L('exists'), HTTP_REFERER);
				}
				$data['name'] = $name;
				if ($this->db->insert($data)) {
					dr_admin_msg(1,L('operation_success'), '', '', 'test');
				} else {
					dr_admin_msg(0,L('operation_failure'));
				}
			} else {
				$show_validator = $show_header = true;
				include $this->admin_tpl('node_copy');
			}
		} else {
			dr_admin_msg(0,L('notfound'));
		}
	}
	
	//导入采集点
	public function node_import() {
		if (IS_POST) {
			$filename = $this->input->post('filename');
			if (strtolower(substr((string)$filename, -3, 3)) != 'txt') {
				dr_admin_msg(0,L('only_allowed_to_upload_txt_files'), HTTP_REFERER);
			}
			$data = json_decode(base64_decode(file_get_contents($filename)), true);
			if (pc_base::load_config('system', 'charset') == 'gbk') {
				$data = array_iconv($data, 'utf-8', 'gbk');
			}
			@unlink($filename);
			$name = $this->input->post('name') && trim($this->input->post('name')) ? trim($this->input->post('name')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			if ($this->db->get_one(array('name'=>$name), 'nodeid')) {
				dr_admin_msg(0,L('nodename').L('exists'), HTTP_REFERER);
			}
			$data['name'] = $name;
			$data['siteid'] = $this->siteid;
			if ($this->db->insert($data)) {
				dr_admin_msg(1,L('operation_success'));
			} else {
				dr_admin_msg(0,L('operation_failure'));
			}
		} else {
			$show_header = $show_validator = true;
			include $this->admin_tpl('node_import');
		}
	}

	// 上传文件
	public function public_upload_index() {
		pc_base::load_sys_class('upload','',0);
		$upload = new upload();
		$rt = $upload->upload_file(array(
			'save_name' => 'null',
			'save_path' => CACHE_PATH.'temp/',
			'form_name' => 'file_data',
			'file_exts' => array('txt'),
			'file_size' => 100 * 1024 * 1024,
			'attachment' => array(
				'value' => array(
					'path' => 'null'
				)
			),
		));
		if (!$rt['code']) {
			exit(dr_array2string($rt));
		}
		dr_json(1, L('上传成功'), $rt['data']);
	}
	
	//导出采集配置
	public function export() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('nodeid'=>$nodeid))) {
			unset($data['nodeid'], $data['name'], $data['siteid']);
			if (pc_base::load_config('system', 'charset') == 'gbk') {
				$data = array_iconv($data);
			}
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=pc_collection_".$nodeid.'.txt');
			exit(base64_encode(json_encode($data)));
		} else {
			dr_admin_msg(0,L('notfound'));
		}
	}
	
	//URL配置显示结果
	public function public_url() {
		$sourcetype = $this->input->get('sourcetype') && intval($this->input->get('sourcetype')) ? intval($this->input->get('sourcetype')) : dr_admin_msg(0,L('illegal_parameters'));
		$pagesize_start = $this->input->get('pagesize_start') && intval($this->input->get('pagesize_start')) ? intval($this->input->get('pagesize_start')) : 1;
		$pagesize_end = $this->input->get('pagesize_end') && intval($this->input->get('pagesize_end')) ? intval($this->input->get('pagesize_end')) : 10;
		$par_num = $this->input->get('par_num') && intval($this->input->get('par_num')) ? intval($this->input->get('par_num')) : 1;
		$urlpage = $this->input->get('urlpage') && trim($this->input->get('urlpage')) ? trim($this->input->get('urlpage')) : dr_admin_msg(0,L('illegal_parameters'));
		$show_header = true;
		include $this->admin_tpl('node_public_url');
	}
	
	//删除采集节点
	public function del() {
		$nodeid = $this->input->post('nodeid') ? $this->input->post('nodeid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		foreach ($nodeid as $k=>$v) {
			if(intval($v)) {
				$nodeid[$k] = intval($v);
			} else {
				unset($nodeid[$k]);
			}
		}
		$nodeid = implode('\',\'', $nodeid);
		$this->db->delete("nodeid in ('$nodeid')");
		$content_db = pc_base::load_model('collection_content_model');
		$content_db->delete("nodeid in ('$nodeid')");
		dr_admin_msg(1,L('operation_success'), '?m=collection&c=node&a=manage');
	}
	
	//测试文章URL采集
	public function public_test() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		pc_base::load_app_class('collection', '', 0);
		if ($data = $this->db->get_one(array('nodeid'=>$nodeid))) {
			$urls = collection::url_list($data, 1);
			if (!empty($urls)) foreach ($urls as $v) {
				$url = collection::get_url_lists($v, $data);
			}
			$show_header = $show_dialog = true;
			include $this->admin_tpl('public_test');
		} else {
			dr_admin_msg(0,L('notfound'));
		}
	}
	
	//测试文章内容采集
	public function public_test_content() {
		$url = $this->input->get('url') ? urldecode($this->input->get('url')) : exit('0');
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		pc_base::load_app_class('collection', '', 0);
		if ($data = $this->db->get_one(array('nodeid'=>$nodeid))) {
			$contents = collection::get_content($url, $data);
			foreach ($contents as $_key=>$_content) {
				if(trim($_content)=='') $contents[$_key] = '◆◆◆◆◆◆◆◆◆◆'.$_key.' empty◆◆◆◆◆◆◆◆◆◆';
			}
			print_r($contents);
		} else {
			dr_admin_msg(0,L('notfound'));
		}
	}
	
	//采集节点名验证
	public function public_name() {
		$name = $this->input->get('name') && trim($this->input->get('name')) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('name'))) : trim($this->input->get('name'))) : exit('0');
		$nodeid = $this->input->get('nodeid') && intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : '';
 		$data = array();
		if ($nodeid) {
			$data = $this->db->get_one(array('nodeid'=>$nodeid), 'name');
			if (!empty($data) && $data['name'] == $name) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('name'=>$name), 'nodeid')) {
			exit('0');
		} else {
			exit('1');
		}
	}
	
	//采集网址
	public function col_url_list() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('nodeid'=>$nodeid))) {
			pc_base::load_app_class('collection', '', 0);
			$urls = collection::url_list($data);
			$total_page = dr_count($urls);
			if ($total_page > 0) {
				$page = intval($this->input->get('page'));
				$url_list = $urls[$page];
				$url = collection::get_url_lists($url_list, $data);
				$history_db = pc_base::load_model('collection_history_model');
				$content_db = pc_base::load_model('collection_content_model');
				$total = dr_count($url);
				$re = 0;
				if (is_array($url) && !empty($url)) foreach ($url as $v) {
					if (empty($v['url']) || empty($v['title'])) continue;
					$v['title'] = clearhtml($v['title']);
					$md5 = md5($v['url']);
					if (!$history_db->get_one(array('md5'=>$md5, 'siteid'=>$this->siteid))) {
						$history_db->insert(array('md5'=>$md5, 'siteid'=>$this->siteid));
						$content_db->insert(array('nodeid'=>$nodeid, 'status'=>0, 'url'=>$v['url'], 'title'=>$v['title'], 'siteid'=>$this->siteid));
					} else {
						$re++;
					}
				}
				$show_dialog = true;
				if ($total_page <= $page) {
					$this->db->update(array('lastdate'=>SYS_TIME), array('nodeid'=>$nodeid));
				}
				include $this->admin_tpl('col_url_list');
			} else {
				dr_admin_msg(0,L('not_to_collect'));
			}
		} else {
			dr_admin_msg(0,L('notfound'));
		}
	}
	
	//采集文章
	public function col_content() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('nodeid'=>$nodeid))) {
			$content_db = pc_base::load_model('collection_content_model');
			//更新附件状态
			$attach_status = false;
			if(SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$attach_status = true;
			}
			pc_base::load_app_class('collection', '', 0);
			$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
			$total = intval($this->input->get('total'));
			if (empty($total)) $total = $content_db->count(array('nodeid'=>$nodeid, 'siteid'=>$this->siteid, 'status'=>0));
			$total_page = ceil($total/2);
			$list = $content_db->select(array('nodeid'=>$nodeid, 'siteid'=>$this->siteid, 'status'=>0), 'id,url', '2', 'id desc');
			$i = 0;
			if (!empty($list) && is_array($list)) {
				foreach ($list as $v) {
					$downloadfiles = pc_base::load_sys_class('cache')->get_data('downloadfiles-'.$this->siteid);
					$html = collection::get_content($v['url'], $data);
					//更新附件状态
					if($attach_status && $downloadfiles) {
						$this->attachment_db->api_update($downloadfiles,'cj-'.$v['id'],1);
						pc_base::load_sys_class('cache')->clear('downloadfiles-'.$this->siteid);
					}
					$content_db->update(array('status'=>1, 'data'=>array2string($html)), array('id'=>$v['id']));
					$i++;
				}
			} else {
				dr_admin_msg(0,L('url_collect_msg'), '?m=collection&c=node&a=manage');
			}
			
			if ($total_page > $page) {
				dr_admin_msg(1,L('collectioning').($i+($page-1)*2).'/'.$total, '?m=collection&c=node&a=col_content&page='.($page+1).'&nodeid='.$nodeid.'&total='.$total);
			} else {
				$this->db->update(array('lastdate'=>SYS_TIME), array('nodeid'=>$nodeid));
				dr_admin_msg(1,L('collection_success'), '?m=collection&c=node&a=manage');
			}
		}
	}
	
	//文章列表
	public function publist() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$node = $this->db->get_one(array('nodeid'=>$nodeid), 'name');
		$content_db = pc_base::load_model('collection_content_model');
		$status = intval($this->input->get('status')) ? intval($this->input->get('status')) : '';
		$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$sql = array('nodeid'=>$nodeid, 'siteid'=>$this->siteid);
		if ($status) {
			$sql['status'] = $status - 1;
		}
		$data = $content_db->listinfo($sql, 'id desc', $page, SYS_ADMIN_PAGESIZE);
		$pages = $content_db->pages;
		include $this->admin_tpl('publist');
	}
	
	//导入文章
	public function import() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$type = $this->input->get('type') ? trim($this->input->get('type')) : '';
		if ($type == 'all') {
			
		} else {
			$ids = implode(',', $id);
		}
		$program_db = pc_base::load_model('collection_program_model');
		$program_list = $program_db->select(array('nodeid'=>$nodeid, 'siteid'=>$this->siteid), 'id, catid');
		include $this->admin_tpl('import_program');
	}
	
	//删除文章
	public function content_del() {
		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$history = $this->input->get('history') ? $this->input->get('history') : '';
		if (is_array($id)) {
			$collection_content_db = pc_base::load_model('collection_content_model');
			$history_db = pc_base::load_model('collection_history_model');
			$del_array = $id;
			$ids = implode('\',\'', $id);
			if ($history) {
				$data = $collection_content_db->select("id in ('$ids')", 'url');
				foreach ($data as $v) {
					$list[] = md5($v['url']);
				}
				$md5 = implode('\',\'', $list);
				$history_db->delete("md5 in ('$md5')");
			}
			$collection_content_db->delete("id in ('$ids')");
			//同时删除关联附件
			if(!empty($del_array)) {
				$attachment = pc_base::load_model('attachment_model');
				foreach ($del_array as $id) {
					$attachment->api_delete('cj-'.$id);
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	
	//添加导入方案
	public function import_program_add() {
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$ids = $this->input->get('ids') ? $this->input->get('ids') : '';
		$catid = $this->input->get('catid') && intval($this->input->get('catid')) ? intval($this->input->get('catid')) : dr_admin_msg(0,L('please_select_cat'), HTTP_REFERER);
		$type = $this->input->get('type') ? trim($this->input->get('type')) : '';
		
		include dirname(__FILE__).DIRECTORY_SEPARATOR.'spider_funs'.DIRECTORY_SEPARATOR.'config.php';
		
		//读取栏目缓存
		$cat = dr_cat_value($catid);
		$cat['setting'] = dr_string2array($cat['setting']);
		if ($cat['siteid'] != $this->siteid || $cat['type'] != 0) dr_admin_msg(0,L('illegal_section_parameter'), HTTP_REFERER);
		
		if (IS_POST) {
			$config = array();
			$menuid = intval($this->input->post('menuid'));
			$model_field = $this->input->post('model_field') ? $this->input->post('model_field') :  dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$node_field = $this->input->post('node_field') ? $this->input->post('node_field') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$funcs = $this->input->post('funcs') ? $this->input->post('funcs') : array();
			
			$config['is_auto_description_content'] = intval($this->input->post('is_auto_description_content'));
			$config['is_auto_thumb_content'] = intval($this->input->post('is_auto_thumb_content'));
			$config['auto_description_content'] = intval($this->input->post('auto_description_content'));
			$config['auto_thumb_content'] = intval($this->input->post('auto_thumb_content'));
			$config['is_remove_a_content'] = intval($this->input->post('is_remove_a_content'));
			$config['content_status'] = $this->input->post('content_status') && intval($this->input->post('content_status')) ? intval($this->input->post('content_status')) : 1;
			
			foreach ($node_field as $k => $v) {
				if (empty($v)) continue;
				$config['map'][$model_field[$k]] = $v;
			}
			
			foreach ($funcs as $k=>$v) {
				if (empty($v)) continue;
				$config['funcs'][$model_field[$k]] = $v;
			} 
			
			$data = array('config'=>array2string($config), 'siteid'=>$this->siteid, 'nodeid'=>$nodeid, 'modelid'=>$cat['modelid'], 'catid'=>$catid);
			$program_db = pc_base::load_model('collection_program_model');
			if ($id = $program_db->insert($data, true)) {
				dr_admin_msg(1,L('program_add_operation_success'), array('url'=>'?m=collection&c=node&a=import_content&programid='.$id.'&nodeid='.$nodeid.'&ids='.$ids.'&type='.$type.'&menuid='.$menuid.'&pc_hash='.dr_get_csrf_token()));
			} else {
				dr_admin_msg(0,L('illegal_parameters'));
			}
		}
		
		
		//读取数据模型缓存
		$model = getcache('model_field_'.$cat['modelid'], 'model');
		if (empty($model)) dr_admin_msg(0,L('model_does_not_exist_please_update_the_cache_model'));
		$node_data = $this->db->get_one(array('nodeid'=>$nodeid), "customize_config");
		$node_data['customize_config'] = string2array($node_data['customize_config']);
		$node_field = array(''=>L('please_choose'),'title'=>L('title'), 'author'=>L('author'), 'comeform'=>L('comeform'), 'time'=>L('time'), 'content'=>L('content'));
		if (is_array($node_data['customize_config'])) foreach ($node_data['customize_config'] as $k=>$v) {
			if (empty($v['en_name']) || empty($v['name'])) continue;
			$node_field[$v['en_name']] = $v['name'];
		}
		include $this->admin_tpl('import_program_add');
	}
	
	public function import_program_del() {
		$id = $this->input->get('id') ? intval($this->input->get('id')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$program_db = pc_base::load_model('collection_program_model');
		if ($program_db->delete(array('id'=>$id))) {
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('illegal_parameters'));
		}
	}
	
	//导入文章到模型
	public function import_content() {
		define('IS_COLL', TRUE);
		$nodeid = intval($this->input->get('nodeid')) ? intval($this->input->get('nodeid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$programid = intval($this->input->get('programid')) ? intval($this->input->get('programid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$menuid = intval($this->input->get('menuid'));
		$ids = $this->input->get('ids') ? $this->input->get('ids') : '';
		$type = $this->input->get('type') ? trim($this->input->get('type')) : '';
		if (!$node = $this->db->get_one(array('nodeid'=>$nodeid), 'coll_order,content_page')) {
			dr_admin_msg(0,L('node_not_found'), '?m=collection&c=node&a=manage');
		}
		$program_db = pc_base::load_model('collection_program_model');
		$collection_content_db = pc_base::load_model('collection_content_model');
		$content_db = pc_base::load_model('content_model');
		//更新附件状态
		$attach_status = false;
		if(SYS_ATTACHMENT_STAT) {
			$attachment_db = pc_base::load_model('attachment_model');
			$att_index_db = pc_base::load_model('attachment_index_model');
			$attach_status = true;
		}
		$order = $node['coll_order'] == 1 ? 'id desc' : '';
		$str = L('operation_success');
		$url = '?m=collection&c=node&a=publist&nodeid='.$nodeid.'&status=2&menuid='.$menuid.'&pc_hash='.dr_get_csrf_token();
		if ($type == 'all') {
			$total = $this->input->get('total') && intval($this->input->get('total')) ? intval($this->input->get('total')) : '';
			if (empty($total)) $total = $collection_content_db->count(array('siteid'=>$this->siteid, 'nodeid'=>$nodeid, 'status'=>1));
			$total_page = ceil($total/20);
			$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
			$total_page = ceil($total/20);
			$data = $collection_content_db->select(array('siteid'=>$this->siteid, 'nodeid'=>$nodeid, 'status'=>1), 'id, data', '20', $order);
			
		} else {
			$ids = explode(',', $ids);
			$ids = implode('\',\'', $ids);
			$data = $collection_content_db->select("siteid='".$this->siteid."' AND id in ('$ids') AND nodeid = '$nodeid' AND status = '1'", 'id, data', '', $order);
			$total = dr_count($data);
			$str = L('operation_success').$total.L('article_was_imported');
		}
		$program = $program_db->get_one(array('id'=>$programid));
		$program['config'] = string2array($program['config']);
		$_POST['is_auto_description_content'] = $program['config']['is_auto_description_content'];
		$_POST['auto_description_content'] = $program['config']['auto_description_content'];
		$_POST['is_auto_thumb_content'] = $program['config']['is_auto_thumb_content'];
		$_POST['auto_thumb_content'] = $program['config']['auto_thumb_content'];
		$_POST['is_remove_a_content'] = $program['config']['is_remove_a_content'];
		$i = 0;
		$content_db->set_model($program['modelid']);
		$coll_contentid = array();
		
		//加载所有的处理函数
		$funcs_file_list = glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'spider_funs'.DIRECTORY_SEPARATOR.'*.php');
		foreach ($funcs_file_list as $v) {
			include $v;
		}
		foreach ($data as $k=>$v) {
			$sql = array('catid'=>$program['catid'], 'status'=>$program['config']['content_status']);
			$v['data'] = string2array($v['data']);
			
			foreach ($program['config']['map'] as $a=>$b) {
				if (isset($program['config']['funcs'][$a]) && function_exists($program['config']['funcs'][$a])) {
					$GLOBALS['field'] = $a;
					$sql[$a] = $program['config']['funcs'][$a]($v['data'][$b]);
				} else {
					$sql[$a] = isset($v['data'][$b]) && $v['data'][$b] ? $v['data'][$b] : '';
				}
			}
			!$sql['thumb'] && $sql['thumb'] = '';
			!$sql['description'] && $sql['description'] = '';
			if ($node['content_page'] == 1) $sql['paginationtype'] = 2;
			$contentid = $content_db->add_content($sql, 1);
			if ($contentid) {
				$coll_contentid[] = $v['id'];
				$i++;
				//更新附件状态,将采集关联重置到内容关联
				if($attach_status) {
					$datas = $att_index_db->select(array('keyid'=>'cj-'.$v['id']),'*',100,'','','aid');
					if(!empty($datas)) {
						$datas = array_keys($datas);
						$datas = implode(',',$datas);
						$att_index_db->update(array('keyid'=>'c-'.$program['catid'].'-'.$contentid),array('keyid'=>'cj-'.$v['id']));
						$attachment_db->update(array('module'=>'content')," aid IN ($datas)");
					}
				}
			} else {
				$collection_content_db->delete(array('id'=>$v['id']));
			}
		}
		$sql_id = implode('\',\'', $coll_contentid);
		$collection_content_db->update(array('status'=>2), " id IN ('$sql_id')");
		if ($type == 'all' && $total_page > $page) {
			$str = L('are_imported_the_import_process').(($page-1)*20+$i).'/'.$total.'<script type="text/javascript">location.href="?m=collection&c=node&a=import_content&nodeid='.$nodeid.'&programid='.$programid.'&type=all&page='.($page+1).'&total='.$total.'&menuid='.$menuid.'&pc_hash='.dr_get_csrf_token().'"</script>';
			$url = '';
		}
		$this->cache_api->cache('sitemodels');
		dr_admin_msg(1,$str, $url);
	}
}
?>