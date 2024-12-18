<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('global');
class tag {
	private $input,$db,$keyword_db,$siteid;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		$this->keyword_db = pc_base::load_model('keyword_model');
		$this->siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
	}
	
	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		define('SITEID', $this->siteid);
		define('ISMOBILE', 1);
		define('IS_HTML', 0);
		$SEO = seo($this->siteid);
		$page = max($this->input->get('page'), 1);
		$pagesize = 20;
		$where = '`siteid`='.$this->siteid;
		$infos = $this->keyword_db->listinfo($where, '`searchnums` DESC, `videonum` DESC', $page, $pagesize);
		define('ISMOBILE', 1);
		define('IS_HTML', 0);
		pc_base::load_sys_class('service')->assign([
			'siteid' => $this->siteid,
			'SEO' => $SEO,
			'infos' => $infos,
			'pages' => $this->keyword_db->pages,
		]);
		pc_base::load_sys_class('service')->display('mobile', 'tag');
	}

	/**
	 * 按照模型搜索
	 */
	public function lists() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		define('SITEID', $this->siteid);
		define('ISMOBILE', 1);
		define('IS_HTML', 0);
		$tag = safe_replace($this->input->get('tag'));
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
				$res = $this->db->get_one(array('id'=>$contentid), 'id, catid, thumb, title, keywords, description, url, inputtime, updatetime, style');
				$datas[] = $res;
			}
		}

		$SEO = seo($this->siteid, '', $tag);
		define('ISMOBILE', 1);
		define('IS_HTML', 0);
		pc_base::load_sys_class('service')->assign([
			'siteid' => $this->siteid,
			'tag' => $tag,
			'SEO' => $SEO,
			'datas' => $datas,
			'pages' => $keyword_data_db->pages,
			'total' => $keyword_data_db->number,
		]);
		pc_base::load_sys_class('service')->display('mobile','tag_list');
	}
}
?>