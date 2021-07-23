<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('global');
class tag {
	private $db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		$this->keyword_db = pc_base::load_model('keyword_model');
		$this->siteid = $this->input->get('siteid') ? intval($this->input->get('siteid')) : get_siteid() ;
	}
	
	public function init() {
		$siteid = $this->siteid;
		define('SITEID', $siteid);
		define('ISMOBILE', 1);
		define('IS_HTML', 0);
		$SEO = seo($this->siteid);
		$page = max($this->input->get('page'), 1);
		$pagesize = 20;
		if ($this->input->get('siteid')) {
			$where = '`siteid`='.$this->siteid;
		}
		$infos = $this->keyword_db->listinfo($where, '`searchnums` DESC, `videonum` DESC', $page, $pagesize);
		$pages = $this->keyword_db->pages;
		include template('mobile', 'tag');
	}

	/**
	 * 按照模型搜索
	 */
	public function lists() {
		$siteid = $this->siteid;
		define('SITEID', $siteid);
		define('ISMOBILE', 1);
		define('IS_HTML', 0);
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
		include template('mobile','tag_list');
	}
}
?>