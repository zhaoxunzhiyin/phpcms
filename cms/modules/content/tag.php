<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util','content');
pc_base::load_app_func('global','content');
class tag {
	private $db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		$this->keyword_db = pc_base::load_model('keyword_model');
		$this->siteid = $this->input->get('siteid') ? intval($this->input->get('siteid')) : get_siteid() ;
	}
	
	public function init() {
		define('SITEID', $this->siteid);
		$sitelist = getcache('sitelist','commons');
		$default_style = $sitelist[$this->siteid]['default_style'];
		if(!$default_style) $default_style = 'default';
		$SEO = seo($this->siteid);
		$page = max($this->input->get('page'), 1);
		$pagesize = 20;
		if ($this->input->get('siteid')) {
			$where = '`siteid`='.$this->siteid;
		}
		$infos = $this->keyword_db->listinfo($where, '`searchnums` DESC, `videonum` DESC', $page, $pagesize);
		$pages = $this->keyword_db->pages;
		if (is_mobile($this->siteid) && $sitelist[$this->siteid]['mobileauto'] || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(PC_PATH.'templates'.DIRECTORY_SEPARATOR.$default_style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'tag.html')) {
				define('ISMOBILE', 0);
				define('IS_HTML', $sitelist[$this->siteid]['ishtml']);
				include template('content', 'tag');
			} else {
				if ($sitelist[$this->siteid]['mobile_domain']) {
					//header('location:'.$sitelist[$this->siteid]['mobile_domain']);
					//exit;
				}
				pc_base::load_app_func('global','mobile');
				define('ISMOBILE', 1);
				if($sitelist[$this->siteid]['ishtml'] && $sitelist[$this->siteid]['mobilehtml']) {
					define('IS_HTML', 1);
				} else {
					define('IS_HTML', 0);
				}
				include template('mobile','tag');
			}
		}else{
			define('ISMOBILE', 0);
			define('IS_HTML', $sitelist[$this->siteid]['ishtml']);
			include template('content', 'tag');
		}
	}

	/**
	 * 按照模型搜索
	 */
	public function lists() {
		$sitelist = getcache('sitelist','commons');
		define('SITEID', $this->siteid);
		$default_style = $sitelist[$this->siteid]['default_style'];
		if(!$default_style) $default_style = 'default';
		$tag = safe_replace(addslashes($this->input->get('tag')));
		$keyword_data_db = pc_base::load_model('keyword_data_model');
		//获取标签id
		$r = $this->keyword_db->get_one(array('keyword'=>$tag, 'siteid'=>$this->siteid), 'id');
		if (!$r['id']) showmessage('不存在此关键字！');
		$tagid = intval($r['id']);

		$page = max($this->input->get('page'), 1);
		$pagesize = 20;
		$where = '`tagid`=\''.$tagid.'\' AND `siteid`='.$this->siteid;
		$infos = $keyword_data_db->listinfo($where, '`id` DESC', $page, $pagesize);
		$pages = $keyword_data_db->pages;
		$total = $keyword_data_db->number;
		if (is_array($infos)) {
			$datas = array();
			foreach ($infos as $info) {
				list($contentid, $modelid) = explode('-', $info['contentid']);
				$this->db->set_model($modelid);
				$res = $this->db->get_one(array('id'=>$contentid), 'id, catid, thumb, title, description, url, inputtime, updatetime, style');
				$res['title'] = str_replace($tag, '<font color="#f00">'.$tag.'</font>', $res['title']);
				$res['description'] = str_replace($tag, '<font color="#f00">'.$tag.'</font>', $res['description']);
				$datas[] = $res;
			}
		}

		$SEO = seo($this->siteid, '', $tag);
		if (is_mobile($this->siteid) && $sitelist[$this->siteid]['mobileauto'] || defined('IS_MOBILE') && IS_MOBILE) {
			if (!file_exists(PC_PATH.'templates'.DIRECTORY_SEPARATOR.$default_style.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'tag_list.html')) {
				define('ISMOBILE', 0);
				define('IS_HTML', $sitelist[$this->siteid]['ishtml']);
				include template('content', 'tag_list');
			} else {
				if ($sitelist[$this->siteid]['mobile_domain']) {
					//header('location:'.$sitelist[$this->siteid]['mobile_domain']);
					//exit;
				}
				pc_base::load_app_func('global','mobile');
				define('ISMOBILE', 1);
				if($sitelist[$this->siteid]['ishtml'] && $sitelist[$this->siteid]['mobilehtml']) {
					define('IS_HTML', 1);
				} else {
					define('IS_HTML', 0);
				}
				include template('mobile','tag_list',$default_style);
			}
		}else{
			define('ISMOBILE', 0);
			define('IS_HTML', $sitelist[$this->siteid]['ishtml']);
			include template('content', 'tag_list');
		}
	}
}
?>