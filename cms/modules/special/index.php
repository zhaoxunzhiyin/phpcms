<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_func('global', 'special');
class index {
	
	private $input,$db;
	
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('special_model');
	}
	
	/**
	 * 专题列表 
	 */
	public function special() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		$SEO = seo($siteid);
		$page = max(intval($this->input->get('page')), 1);
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'page' => $page,
		]);
		pc_base::load_sys_class('service')->display('special', 'special_list');
	}
	
	/**
	 * 专题首页
	 */
	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$specialid = $this->input->get('id') ? intval($this->input->get('id')) : ($this->input->get('specialid') ? intval($this->input->get('specialid')) : 0);
		if (!$specialid) showmessage(L('illegal_action'));
		$info = $this->db->get_one(array('id'=>$specialid, 'disabled'=>0));
		if(!$info) showmessage(L('special_not_exist'));
		extract($info);
		$css = get_css(unserialize($css));
		if(!$ispage) {
			$type_db = pc_base::load_model('type_model');
			$types = $type_db->select(array('module'=>'special', 'parentid'=>$specialid), '*', '', '`listorder` ASC, `typeid` ASC', '', 'listorder');
		}
		if ($pics) {
			$pic_data = get_pic_content($pics);
			unset($pics);
		}
		if ($voteid) {
			$vote_info = explode('|', $voteid);
			$voteid = $vote_info[1];
		}
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID ? SITE_ID : get_siteid());
		$SEO = seo($siteid, '', $title, $description);
		$commentid = id_encode('special', $id, $siteid);
		$template = $info['index_template'] ? $info['index_template'] : 'index';
		define('STYLE',$info['style']);
		pc_base::load_sys_class('service')->assign($info);
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'specialid' => $specialid,
			'commentid' => $commentid,
			'types' => $types,
			'pic_data' => $pic_data,
			'voteid' => $voteid,
		]);
		pc_base::load_sys_class('service')->display('special', $template);
	}
	
	/**
	 * 专题分类
	 */
	public function type() {
		$typeid = intval($this->input->get('typeid'));
		$specialid = intval($this->input->get('specialid'));
		if (!$specialid || !$typeid) showmessage(L('illegal_action'));
		$info = $this->db->get_one(array('id'=>$specialid, 'disabled'=>0));
		if(!$info) showmessage(L('special_not_exist'));
		$page = max(intval($this->input->get('page')), 1);
		extract($info);
		$css = get_css(unserialize($css));
		if(!$typeid) showmessage(L('illegal_action'));
		$type_db = pc_base::load_model('type_model');
		$info = $type_db->get_one(array('typeid'=>$typeid));
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID ? SITE_ID : get_siteid());
		$SEO = seo($siteid, '', $info['typename'], '');
		$template = $list_template ? $list_template : 'list';
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'info' => $info,
			'specialid' => $specialid,
			'typeid' => $typeid,
			'page' => $page,
		]);
		pc_base::load_sys_class('service')->display('special', $template);
	}
	
	/**
	 * 专题展示
	 */
	public function show() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$id = intval($this->input->get('id'));
		if(!$id) showmessage(L('content_not_exist'),'blank');
		
		$page = $this->input->get('page');
		$page = max($page,1);
		$c_db = pc_base::load_model('special_content_model');
		$c_data_db = pc_base::load_model('special_c_data_model');
		$rs = $c_db->get_one(array('id'=>$id));
 		if(!$rs) showmessage(L('content_checking'),'blank');
		extract($rs);
		if ($isdata) {
			$arr_content = $c_data_db->get_one(array('id'=>$id));
			if (is_array($arr_content)) {
				extract($arr_content);
				pc_base::load_sys_class('service')->assign($arr_content);
			}
		}
		$siteid = intval($this->input->get('siteid')) ? intval($this->input->get('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		if ($paginationtype) { 			//文章使用分页时
			if($paginationtype==1) {
				if (strpos((string)$content, '[/page]')!==false) {
					$content = preg_replace("|\[page\](.*)\[/page\]|U", '', $content);
				}
				if (strpos((string)$content, '[page]')!==false) {
					$content = str_replace('[page]', '', $content);
				}
				$contentpage = pc_base::load_app_class('contentpage', 'content'); //调用自动分页类
				$content = $contentpage->get_data($content, $maxcharperpage); //自动分页，自动添加上[page]
			}
		} else {
			if (strpos((string)$content, '[/page]')!==false) {
				$content = preg_replace("|\[page\](.*)\[/page\]|U", '', $content);
			}
			if (strpos((string)$content, '[page]')!==false) {
				$content = str_replace('[page]', '', $content);
			}
		}
		$template = $show_template ? $show_template : 'show'; //调用模板
		$CONTENT_POS = strpos((string)$content, '[page]');
		if ($CONTENT_POS !== false) {
			$contents = array_filter(explode('[page]', $content));
			$pagenumber = dr_count($contents);
			$END_POS = strpos((string)$content, '[/page]');
			if ($END_POS!==false && ($CONTENT_POS<7)) {
				$pagenumber--;
			}
			for ($i=1; $i<=$pagenumber; $i++) {
				list($pageurls[$i], $showurls[$i]) = content_url($id, $i, $inputtime, 'php');
			}
			if ($END_POS !== false) {
				if($CONTENT_POS>7) {
					$content = '[page]'.$title.'[/page]'.$content;
				}
				if (preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
					foreach ($m[1] as $k=>$v) {
						$p = $k+1;
						$titles[$p]['title'] = clearhtml($v);
						$titles[$p]['url'] = $pageurls[$p][1];
					}
				}
			}
			//判断[page]出现的位置是否在第一位 
			if($CONTENT_POS<7) {
				$content = $contents[$page];
			} else {
				if ($page==1 && !empty($titles)) {
					$content = $title.'[/page]'.$contents[$page-1];
				} else {
					$content = $contents[(int)$page-1];
				}
			}
			if($titles) {
				list($title, $content) = explode('[/page]', (string)$content);
				$content = trim((string)$content);
				if(strpos((string)$content,'</p>')===0) {
					$content = '<p>'.$content;
				}
				if(stripos((string)$content,'<p>')===0) {
					$content = $content.'</p>';
				}
			}
			pc_base::load_app_func('util', 'content');
			$title_pages = content_pages($pagenumber,$page,$showurls);
		}
		$_special = $this->db->get_one(array('id'=>$specialid), '`title`, `url`, `show_template`, `isvideo`');
		pc_base::load_sys_class('format');
		$inputtime = format::date($inputtime,1);
		$SEO = seo($siteid, '', $title);
		$template = $show_template ? $show_template : ($_special['show_template'] ? $_special['show_template'] : 'show');
		$style = $style ? $style : 'default';
		pc_base::load_sys_class('service')->assign($rs);
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'rs' => $rs,
			'id' => $id,
			'title' => clearhtml($title) ? $title : $rs['title'],
			'inputtime' => $inputtime,
			'content' => $content,
			'specialid' => $specialid,
			'typeid' => $typeid,
			'title_pages' => $title_pages,
		]);
		pc_base::load_sys_class('service')->display('special', $template, $style);
	}
	
	public function comment_show() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$commentid = intval($this->input->get('commentid'));
		$url = $this->input->get('url') ? $this->input->get('url') : HTTP_REFERER;
		$id = $this->input->get('id') ? intval($this->input->get('id')) : 0;
		$userid = param::get_cookie('_userid');
		pc_base::load_sys_class('service')->assign([
			'commentid' => $commentid,
			'url' => $url,
			'id' => $id,
			'userid' => $userid,
		]);
		pc_base::load_sys_class('service')->display('special', 'comment_show');
	}
	
	public function comment() {
		if (!$this->input->get('id')) return '0';
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID ? SITE_ID : get_siteid());
		$id = intval($this->input->get('id'));
		$commentid = id_encode('special', $id, $siteid);
		$username = param::get_cookie('_username');
		$userid = param::get_cookie('_userid');
		if (!$userid) {
			showmessage(L('login_website'), APP_PATH.'index.php?m=member&c=index');
		}
		$date = date('m-d H:i', SYS_TIME);
		if (IS_POST) {
			$r = $this->db->get_one(array('id'=>intval($this->input->post('id'))), '`title`, `url`');
			$comment = pc_base::load_app_class('comment', 'comment');
			if ($comment->add($commentid, $siteid, array('userid'=>$userid, 'username'=>$username, 'content'=>$this->input->post('content')), '', $r['title'], $r['url'])) {
				exit(remove_xss($username.'|'.SYS_TIME.'|'.$this->input->post('content')));
			} else {
				exit(0);
			}
		} else {
			pc_base::load_sys_class('form');
			pc_base::load_sys_class('service')->assign([
				'siteid' => $siteid,
				'commentid' => $commentid,
				'url' => $url,
				'id' => $id,
				'userid' => $userid,
				'username' => $username,
				'date' => $date,
			]);
			pc_base::load_sys_class('service')->display('special', 'comment');
		}
	}
}
?>