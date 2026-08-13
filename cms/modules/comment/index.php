<?php
defined('IN_CMS') or exit('No permission resources.');
class index {
	protected $input, $commentid, $contentid, $modules, $siteid, $format, $callback;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		pc_base::load_app_func('global');
		pc_base::load_sys_class('format');
		$this->commentid = $this->input->get('commentid') && trim(urldecode($this->input->get('commentid'))) ? trim(urldecode($this->input->get('commentid'))) : $this->_show_msg(L('illegal_parameters'));
		if(!preg_match("/^[a-z0-9_\-]+$/i",$this->commentid)) $this->_show_msg(L('illegal_parameters'));
		$this->format = $this->input->get('format') ? $this->input->get('format') : '';
		list($this->modules, $this->contentid, $this->siteid) = decode_commentid($this->commentid);
		define('SITEID', $this->siteid);
		$this->callback = $this->input->get('callback') ? safe_replace($this->input->get('callback')) : '';
	}
	
	public function init() {
		$hot = $this->input->get('hot') && intval($this->input->get('hot')) ? intval($this->input->get('hot')) : 0;
		$page = max(1, intval($this->input->get('page')));
		pc_base::load_sys_class('form');
		$commentid =& $this->commentid;
		$modules =& $this->modules;
		$contentid =& $this->contentid;
		$siteid =& $this->siteid;
		$username = param::get_cookie('_username',L('friends'));
		$userid = param::get_cookie('_userid');
		
		$comment_setting_db = pc_base::load_model('comment_setting_model');
		$setting = $comment_setting_db->get_one(array('siteid'=>$this->siteid));
		//SEO
		$SEO = seo($siteid, '', $title);
		
		//通过API接口调用数据的标题、URL地址
		if (!$data = get_comment_api($commentid)) {
			$this->_show_msg(L('illegal_parameters'));
		} else {
			$title = $data['title'];
			$url = $data['url'];
			if (isset($data['allow_comment']) && empty($data['allow_comment'])) {
				showmessage(L('canot_allow_comment'));
			}
			unset($data);
		} 		
		
		if ($this->input->get('iframe')) {
			if (strpos($url,APP_PATH) === 0) {
				$domain = APP_PATH;
			} else {
				$urls = parse_url($url);
				$domain = $urls['scheme'].'://'.$urls['host'].(isset($urls['port']) && !empty($urls['port']) ? ":".$urls['port'] : '').'/';
			}
			include template('comment', 'show_list');exit();
		} else {
			include template('comment', 'list');
		}
	}
	
	public function post() {
		$comment = pc_base::load_app_class('comment');
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : '';
		$username = param::get_cookie('_username',dr_site_info('name', $this->siteid).L('friends'));
		$userid = param::get_cookie('_userid');
		$comment_setting_db = pc_base::load_model('comment_setting_model');
		$setting = $comment_setting_db->get_one(array('siteid'=>$this->siteid));
		if (!empty($setting)) {
			//是否允许游客
			if (!$setting['guest']) {
				if (!$username || !$userid) {
					$this->_show_msg(L('landing_users_to_comment'), HTTP_REFERER);
				}
			}
			if ($setting['code']) {
				if (!check_captcha('code')) {
					$this->_show_msg(L('code_error'), HTTP_REFERER);
				}
			}
		}
		
		//通过API接口调用数据的标题、URL地址
		if (!$data = get_comment_api($this->commentid)) {
			$this->_show_msg(L('illegal_parameters'));
		} else {
			$title = $data['title'];
			$url = $data['url'];
			unset($data);
		} 

		if (strpos($url,APP_PATH) === 0) {
			$domain = APP_PATH;
		} else {
			$urls = parse_url($url);
			$domain = $urls['scheme'].'://'.$urls['host'].(isset($urls['port']) && !empty($urls['port']) ? ":".$urls['port'] : '').'/';
		}
		
		$content = $this->input->post('content') && trim($this->input->post('content')) ? trim($this->input->post('content')) : $this->_show_msg(L('please_enter_content'), HTTP_REFERER);
		$direction = $this->input->post('direction') && intval($this->input->post('direction')) ? intval($this->input->post('direction')) : '';
		$data = array('userid'=>$userid, 'username'=>$username, 'content'=>$content, 'direction'=>$direction);
		$comment->add($this->commentid, $this->siteid, $data, $id, $title, $url);
		$this->_show_msg($comment->get_error()."<iframe width='0' id='top_src' height='0' src='$domain/js.html?200'></iframe>", (in_array($comment->msg_code, array(0,7)) ? HTTP_REFERER : ''), (in_array($comment->msg_code, array(0,7)) ? 1 : 0));
	}
	
	public function support() {
		$id = $this->input->get('id') && intval($this->input->get('id')) ? intval($this->input->get('id')) : $this->_show_msg(L('illegal_parameters'), HTTP_REFERER);
		unset($_GET);
		if (param::get_cookie('comment_'.$id)) {
			$this->_show_msg(L('dragonforce'), HTTP_REFERER);
		}
		$comment = pc_base::load_app_class('comment');
		if ($comment->support($this->commentid, $id)) {
			param::set_cookie('comment_'.$id, $id, 3600);
		}
		$this->_show_msg($comment->get_error(), ($comment->msg_code == 0 ? HTTP_REFERER : ''), ($comment->msg_code == 0 ? 1 : 0));
	}
	
	public function ajax() {
		$commentid =& $this->commentid;
		$siteid =& $this->siteid;
		$num = $this->input->get('num') && intval($this->input->get('num')) ? intval($this->input->get('num')) : 20;
		$direction = $this->input->get('direction') && intval($this->input->get('direction')) ? intval($this->input->get('direction')) : 0;
		$pc_tag = pc_base::load_app_class('comment_tag');
		$comment = array();
		if ($comment = $pc_tag->get_comment(array('commentid'=>$commentid))) {
			$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
			$offset = ($page-1)*$num;
			$data = array('commentid'=>$commentid, 'site'=>$siteid, 'limit'=>$offset.','.$num, 'direction'=>$direction);
			$comment['data'] = $pc_tag->lists($data);
			pc_base::load_sys_class('format');
			foreach ($comment['data'] as $k=>$v) {
				$comment['data'][$k]['format_time'] = format::date($v['creat_at'], 1);
			}
			switch ($direction) {
				case '1'://正
					$total = $comment['square'];
					break;
					
				case '2'://反
					$total = $comment['anti'];
					break;
					
				case '3'://中
					$total = $comment['neutral'];
					break;
					
				default:
					$total = $comment['total'];
					break;
			}
			$comment['pages'] = pages($total, $page, $num, 'javascript:comment_next_page({$page})');
			if (pc_base::load_config('system', 'charset') == 'gbk') {
				$comment = array_iconv($comment, 'gbk', 'utf-8');
			}
			echo json_encode($comment);
		} else {
			exit('0');
		}
	}
	
	//提示信息处理
	protected function _show_msg($msg, $url = '', $status = 0) {
		
		switch ($this->format) {
			case 'json':
				echo json_encode(array('msg'=>$msg, 'status'=>$status));
				exit;
			break;
			
			case 'jsonp':
				echo clearhtml($this->callback).'('.json_encode(array('msg'=>$msg, 'status'=>$status)).')';
				exit;
			break;
			
			default:
				showmessage($msg, $url);
			break;
		}
	}
}