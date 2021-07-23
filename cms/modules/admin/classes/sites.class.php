<?php
/**
 * 站点对外接口
 * @author chenzhouyu
 *
 */
class sites {
	//数据库连接
	private $db;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('site_model');
	}
	
	/**
	 * 获取站点列表
	 * @param string $roleid 角色ID 留空为获取所有站点列表
	 */
	public function get_list($roleid='') {
		$roleid = intval($roleid);
		if(empty($roleid)) {
			if ($data = getcache('sitelist', 'commons')) {
				return $data;
			} else {
				$this->set_cache();
				return $this->db->select();
			}			
		} else {
			$site_arr = $this->get_role_siteid($roleid);
			$sql = "`siteid` in($site_arr)";
			return $this->db->select($sql);
		}

	}
	
	/**
	 * 获取站点列表
	 * @param string $roleid 角色ID 留空为获取所有站点列表
	 */
	public function get_list_login($roleid='') {
		$roleid = intval($roleid);
		if(empty($roleid)) {
			if ($data = getcache('sitelist', 'commons')) {
				return $data;
			} else {
				$this->set_cache();
				return $this->db->select();
			}			
		} else {
			$site_arr = $this->get_role_siteid_login($roleid);
			$sql = "`siteid` in($site_arr)";
			return $this->db->select($sql);
		}

	}
	
	/**
	 * 按ID获取站点信息
	 * @param integer $siteid 站点ID号
	 */
	public function get_by_id($siteid) {
		return siteinfo($siteid);
	}
	
	/**
	 * 设置站点缓存
	 */
	public function set_cache() {
		$list = $this->db->select();
		$data = array();
		foreach ($list as $key=>$val) {
			$data[$val['siteid']] = $val;
			$data[$val['siteid']]['url'] = $val['domain'] ? $val['domain'] : pc_base::load_config('system', 'web_path').$val['dirname'].'/';
		}
		setcache('sitelist', $data, 'commons');
		$sites = array();
		foreach ($list as $t) {
			$domain = parse_url($t['domain']);
			if ($domain['port']) {
				$sites[$domain['host'].':'.$domain['port']] = $t['siteid'];
			} else {
				$sites[$domain['host']] = $t['siteid'];
			}
		}
		$body = '<?php'.PHP_EOL.PHP_EOL.
            '/**'.PHP_EOL.
            ' * 站点域名配置文件'.PHP_EOL.
            ' */'.PHP_EOL.PHP_EOL
        ;
		$body .= 'return array('.PHP_EOL.PHP_EOL;
		foreach ($sites as $name => $val) {
			if (is_array($val)) {
				continue;
			}
			$name = $this->_safe_replace($name);
			$body.= '	\''.$name.'\''.$this->_space($name).'=> '.$this->_format_value($val).','.PHP_EOL;
		}
		$body.= PHP_EOL.');';
		$body.= PHP_EOL.'?>';
		$file = CACHE_PATH.'caches_commons/caches_data/domain_site.cache.php';
		!is_dir(dirname($file)) && dr_mkdirs(dirname($file));

		// 重置Zend OPcache
		function_exists('opcache_reset') && opcache_reset();
		
		//return @file_put_contents($file, $body, LOCK_EX);
	}
	
	/**
	 * 补空格
	 */
	public function _space($name) {
		$len = strlen($name) + 2;
		$cha = $this->space - $len;
		$str = '';
		for ($i = 0; $i < $cha; $i ++) $str .= ' ';
		return $str;
	}

	/**
	 * 格式化值
	 */
	public function _format_value($value) {
		return is_numeric($value) && strlen($value) <= 10 ? $value : '\''.str_replace(array('\'', '\\'), '', $value).'\'';
	}

	// 安全替换
	public function _safe_replace($name) {
		return str_replace(
			array('..', "/", '\\', '<', '>', "{", '}', ';', '[', ']', '\'', '"', '*', '?'),
			'',
			$name
		);
	}
	
	/**
	 * PC标签中调用站点列表
	 */
	public function pc_tag_list() {
		$list = $this->db->select('', 'siteid,name');
		$sitelist = array(''=>L('please_select_a_site', '', 'admin'));
		foreach ($list as $k=>$v) {
			$sitelist[$v['siteid']] = $v['name'];
		}
		return $sitelist;
	}
	
	/**
	 * 按角色ID获取站点列表
	 * @param string $roleid 角色ID
	 */	
	
	public function get_role_siteid($roleid) {
		$roleid = intval($roleid);
		if($roleid == 1) {
			$sitelists = $this->get_list();
			foreach($sitelists as $v) {
				$sitelist[] = $v['siteid'];
			}
		} else {
			$sitelist = getcache('role_siteid', 'commons');
			$sitelist = $sitelist[$roleid];
		}
		if(is_array($sitelist)) 
		{
			$siteid = implode(',',array_unique($sitelist));
			return $siteid;			
		} else {
			showmessage(L('no_site_permissions'),'?m=admin&c=index&a=login');
		}
	}
	
	/**
	 * 按角色ID获取站点列表
	 * @param string $roleid 角色ID
	 */	
	
	public function get_role_siteid_login($roleid) {
		$roleid = intval($roleid);
		if($roleid == 1) {
			$sitelists = $this->get_list_login();
			foreach($sitelists as $v) {
				$sitelist[] = $v['siteid'];
			}
		} else {
			$sitelist = getcache('role_siteid', 'commons');
			$sitelist = $sitelist[$roleid];
		}
		if(is_array($sitelist)) 
		{
			$siteid = implode(',',array_unique($sitelist));
			return $siteid;			
		} else {
			dr_json(0, L('no_site_permissions'));
		}
	}
}