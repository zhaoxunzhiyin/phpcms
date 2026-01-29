<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

pc_base::load_sys_class('form', '', 0);
pc_base::load_sys_class('format', '', 0);
class content extends admin {
	private $input, $special_db, $db, $data_db, $type_db, $search_db, $attachment_db;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->special_db = pc_base::load_model('special_model');
		$this->db = pc_base::load_model('special_content_model');
		$this->data_db = pc_base::load_model('special_c_data_model');
		$this->type_db = pc_base::load_model('type_model');
	}
	
	/**
	 * 添加信息
	 */
	public function add() {
		$specialid = intval($this->input->get('specialid'));
		if (!$specialid) dr_json(0, L('illegal_action'));
		if(IS_POST) {
			$info = $this->check($this->input->post('info'), 'info', 'add', $this->input->post('data')['content']); //验证数据的合法性
			//处理外部链接情况
			if ($info['islink']) {
				$info['url'] = $this->input->post('linkurl');
				$info['isdata'] = 0;
			} else {
				$info['isdata'] = 1;
			}
			$info['style'] = $this->input->post('style_color') && preg_match('/^#([0-9a-z]+)/i', $this->input->post('style_color')) ? $this->input->post('style_color') : '';
			if($this->input->post('style_font_weight')=='bold') $info['style'] = $info['style'].';'.clearhtml($this->input->post('style_font_weight'));
			$info['specialid'] = $this->input->get('specialid');
			//将基础数据添加到基础表，并返回ID
			$contentid = $this->db->insert($info, true);
			
			// 向数据统计表添加数据
			$count = pc_base::load_model('hits_model');
			$hitsid = 'special-c-'.$info['specialid'].'-'.$contentid;
			$count->insert(array('hitsid'=>$hitsid));
			//如果不是外部链接，将内容加到data表中
			$html = pc_base::load_app_class('html');
			if ($info['isdata']) {
				$data = $this->check($this->input->post('data'), 'data'); //验证数据的合法性
				$data['id'] = $contentid;
				$this->data_db->insert($data);
				$searchid = $this->search_api($contentid, $data, $info['title'], 'update', $info['inputtime']);
				$url = $html->_create_content($contentid);
				$this->db->update(array('url'=>$url[0], 'searchid'=>$searchid), array('id'=>$contentid, 'specialid'=>$specialid));
			}
			$html->_index($specialid, 20, 5);
			$special = $this->special_db->get_one(array('id'=>$specialid));
			if ($special['ishtml']) {
				$html->_list($info['typeid'], 20, 5);
			}
			//更新附件状态
			if(SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				if ($info['thunb']) {
					$this->attachment_db->api_update($info['thumb'],'special-c-'.$contentid, 1);
				}
				$this->attachment_db->api_update(stripslashes((string)$data['content']),'special-c-'.$contentid);
			}
			dr_json(1, L('content_add_success'));
		} else {
			$rs = $this->type_db->select(array('parentid'=>$specialid, 'siteid'=>$this->get_siteid()), 'typeid, name');
			$types = array();
			foreach ($rs as $r) {
				$types[$r['typeid']] = $r['name'];
			}
			//获取站点模板信息
			pc_base::load_app_func('global', 'admin');
			$template_list = template_list(get_siteid(), 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$info = $this->special_db->get_one(array('id'=>$specialid));
			if (!$info) dr_admin_msg(0,L('illegal_action'));
			@extract($info);
			include $this->admin_tpl('content_add');
		}
	}
	
	/**
	 * 信息修改
	 */
	public function edit() {
		$specialid = intval($this->input->get('specialid'));
		$id = intval($this->input->get('id'));
		if (!$specialid || !$id) dr_json(0, L('illegal_action'));
		if(IS_POST) {
			$info = $this->check($this->input->post('info'), 'info', 'edit', $this->input->post('data')['content']); //验证数据的合法性
			//处理外部链接更换情况
			$r = $this->db->get_one(array('id'=>$id, 'specialid'=>$specialid));
			
			if ($r['islink']!=$info['islink']) { //当外部链接和原来差别时进行操作
				// 向数据统计表添加数据
				$count = pc_base::load_model('hits_model');
				$hitsid = 'special-c-'.$specialid.'-'.$id;
				$count->delete(array('hitsid'=>$hitsid));
				$this->data_db->delete(array('id'=>$id));
				if ($info['islink']) {
					$info['url'] = $this->input->post('linkurl');
					$info['isdata'] = 0;
				} else {
					$data = $this->check($this->input->post('data'), 'data');
					$data['id'] = $id;
					$this->data_db->insert($data);
					$count->insert(array('hitsid'=>$hitsid));
				} 
			}
			//处理外部链接情况
			if ($info['islink']) {
				$info['url'] = $this->input->post('linkurl');
				$info['isdata'] = 0;
			} else {
				$info['isdata'] = 1;
			}
			$info['style'] = $this->input->post('style_color') && preg_match('/^#([0-9a-z]+)/i', $this->input->post('style_color')) ? $this->input->post('style_color') : '';
			if($this->input->post('style_font_weight')=='bold') $info['style'] = $info['style'].';'.clearhtml($this->input->post('style_font_weight'));
			$html = pc_base::load_app_class('html', 'special');
			if ($info['isdata']) {
				$data = $this->check($this->input->post('data'), 'data');
				$data_db_count = $this->data_db->count(array('id'=>$id));
				if ($data_db_count) {
					$this->data_db->update($data, array('id'=>$id));
				} else {
					$data['id'] = $id;
					$this->data_db->insert($data);
				}
				$url = $html->_create_content($id);
				if ($url[0]) {
					$info['url'] = $url[0];
					$searchid = $this->search_api($id, $data, $info['title'], 'update', $info['inputtime']);
					$this->db->update(array('url'=>$url[0], 'searchid'=>$searchid), array('id'=>$id, 'specialid'=>$specialid));
				}
			} else {
				$this->db->update(array('url'=>$info['url']), array('id'=>$id, 'specialid'=>$specialid));
			}
			$this->db->update($info, array('id'=>$id, 'specialid'=>$specialid));
			//更新附件状态
			if(SYS_ATTACHMENT_STAT) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				if ($info['thumb']) {
					$this->attachment_db->api_update($info['thumb'],'special-c-'.$id, 1);
				}
				$this->attachment_db->api_update(stripslashes((string)$data['content']),'special-c-'.$id);
			}
			$html->_index($specialid, 20, 5);
			$special = $this->special_db->get_one(array('id'=>$specialid));
			if ($special['ishtml']) {
				$html->_list($info['typeid'], 20, 5);
			}
			dr_json(1, L('content_edit_success'));
		} else {
			$info = $this->db->get_one(array('id'=>$id, 'specialid'=>$specialid));
			if($info['isdata']) $data = $this->data_db->get_one(array('id'=>$id));
			$style_arr = explode(';',$info['style']);
			$style_color = $style_arr[0];
			$style_font_weight = $style_arr[1] ? $style_arr[1] : '';
			$rs = $this->type_db->select(array('parentid'=>$specialid, 'siteid'=>$this->get_siteid()), 'typeid, name');
			$types = array();
			foreach ($rs as $r) {
				$types[$r['typeid']] = $r['name'];
			}
			//获取站点模板信息
			pc_base::load_app_func('global', 'admin');
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$s_info = $this->special_db->get_one(array('id'=>$specialid));
			@extract($s_info);
			include $this->admin_tpl('content_edit');
		}
	}
	
	/**
	 * 检查表题是否重复
	 */
	public function public_check_title() {
		if($this->input->get('data')=='' || (!$this->input->get('specialid'))) return '';
		$title = dr_safe_replace(html2code($this->input->get('data')));
		$is_ajax = intval($this->input->get('is_ajax'));
		$specialid = intval($this->input->get('specialid'));
		$id = intval($this->input->get('id'));
		$r = $this->db->get_one(array('title'=>$title,'id<>'=>$id,'specialid'=>$specialid));
		if ($is_ajax) {
			if($r) {
				exit(L('已经有相同的存在'));
			}
		} else {
			if($r) {
				exit('1');
			} else {
				exit('0');
			}
		}
	}
	
	/**
	 * 信息列表
	 */
	public function init() {
		$specialid = intval($this->input->get('specialid'));
		if(!$specialid) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		$types = $this->type_db->select(array('module'=>'special', 'parentid'=>$specialid), 'name, typeid', '', '`listorder` ASC, `typeid` ASC', '', 'typeid');
		$page = max(intval($this->input->get('page')), 1);
		$datas = $this->db->listinfo(array('specialid'=>$specialid), '`listorder` ASC , `id` DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$big_menu = array(array('javascript:dr_content_submit(\'?m=special&c=content&a=add&specialid='.$specialid.'\',\'add\');void(0);', L('add_content')), array('javascript:omnipotent(\'import\',\'?m=special&c=special&a=import&specialid='.$specialid.'\',\''.L('import_content').'\',0,\'60%\',\'60%\');void(0);', L('import_content')));
		include $this->admin_tpl('content_list');
	}
	
	/**
	 * 信息排序 信息调用时按排序从小到大排列
	 */
	public function listorder() {
		$specialid = intval($this->input->get('specialid'));
		if (!$specialid) dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
			foreach ($this->input->post('listorders') as $id => $v) {
				$this->db->update(array('listorder'=>$v), array('id'=>$id, 'specialid'=>$specialid));
			}
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 * 删除信息
	 */
	public function delete() {
		if (!$this->input->post('id') || empty($this->input->post('id')) || !$this->input->get('specialid')) {
			dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		}
		$specialid = intval($this->input->get('specialid'));
		$info = $this->special_db->get_one(array('id'=>$specialid));
		$special_api = pc_base::load_app_class('special_api', 'special');
		if (is_array($this->input->post('id'))) {
			foreach ($this->input->post('id') as $sid) {
				$sid = intval($sid);
				$special_api->_delete_content($sid, $info['siteid'], $info['ishtml']);
				if(SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
					$keyid = 'special-c-'.$sid;
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_delete($keyid);
				}
			}
		} elseif (is_numeric($this->input->post('id'))){
			$id = intval($this->input->post('id'));
			$special_api->_delete_content($id, $info['siteid'], $info['ishtml']);
			if(SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
				$keyid = 'special-c-'.$id;
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_delete($keyid);
			}
		}
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 * 添加到全站搜索
	 * @param intval $id 文章ID
	 * @param array $data 数组
	 * @param string $title 标题
	 * @param string $action 动作
	 */
	private function search_api($id = 0, $data = array(), $title = '', $action = 'update', $addtime = '') {
		$this->search_db = pc_base::load_model('search_model');
		$siteid = $this->get_siteid();
		$type_arr = getcache('type_module_'.$siteid,'search');
		$typeid = $type_arr['special'];
		if($action == 'update') {
			$fulltextcontent = $data['content'];
			return $this->search_db->update_search($typeid ,$id, $fulltextcontent,$title, $addtime);
		} elseif($action == 'delete') {
			$this->search_db->delete_search($typeid ,$id);
		}
	}
	
	/**
	 * 表单验证
	 * @param array $data 表单数据
	 * @param string $type 按数据表数据判断
	 * @param string $action 在添加时会加上默认数据
	 * @return array 数据检验后返回的数组
	 */
	private function check($data = array(), $type = 'info', $action = 'add', $content = '') {
		if ($type == 'info') {
			if (!$data['typeid']) dr_json(0 ,L('no_select_type'), array('field' => 'typeid'));
			if (!$data['title']) dr_json(0, L('title_no_empty'), array('field' => 'title'));
			$data['inputtime'] = $data['inputtime'] ? strtotime($data['inputtime']) : SYS_TIME;
			$data['islink'] = $data['islink'] ? intval($data['islink']) : 0;
			$data['style'] = '';
			if ($data['style_color']) {
				$data['style'] .= 'color:#00FF99;';
			} 
			if ($data['style_font_weight']) {
				$data['style'] .= 'font-weight:bold;';
			}
			
			//截取简介
			if ($this->input->post('add_introduce') && $data['description']=='' && !empty($content)) {
				$content = stripslashes((string)$content);
				$introcude_length = intval($this->input->post('introcude_length'));
				$data['description'] = str_cut(str_replace(array("\r\n","\t"), '', clearhtml($content)),$introcude_length);
			}
			
			//自动提取缩略图
			if ($this->input->post('auto_thumb') && $data['thumb'] == '' && !empty($content)) {
					$content = stripslashes((string)$content);
					$auto_thumb_no = intval($this->input->post('auto_thumb_no')) - 1;
					if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
						$data['thumb'] = $matches[3][$auto_thumb_no];
					}
			}
			unset($data['style_color'], $data['style_font_weight']);
			if ($action == 'add') {
				$data['updatetime'] = SYS_TIME;
				$data['username'] = param::get_cookie('admin_username');
				$data['userid'] = param::get_session('userid');
			}
		} elseif ($type == 'data') {
			if (!$data['content']) dr_json(0, L('content_no_empty'), (SYS_EDITOR ? array('field' => 'content', 'jscode' => 'CKEDITOR.instances.content.focus();') : $jscode = array('field' => 'content')));
		}
		return $data;
	}
}