<?php
/**
 * 站点对外接口
 */
class sites {
	//数据库连接
	private $input,$cache,$db;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('site_model');
	}
	
	/**
	 * 获取站点列表
	 * @param string $roleid 角色ID 留空为获取所有站点列表
	 */
	public function get_list($roleid='') {
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
			$data[$val['siteid']]['url'] = $val['domain'] ? $val['domain'] : WEB_PATH.$val['dirname'].'/';
			$dataval = $val;
			unset($dataval['siteid'], $dataval['setting']);
			$cache[$val['siteid']]['config'] = $dataval;
			$cache[$val['siteid']]['param'] = dr_string2array($val['setting']);
		}
		setcache('sitelist', $data, 'commons');
		if (module_exists('client') && $this->db->table_exists('client')) {
			// 自定义终端
			$list = $this->db->table('client')->select();
			if ($list) {
				$_save = [];
				foreach ($list as $t) {
					$info = $this->db->table('site')->get_one(array('siteid'=>$t['siteid']));
					$_save[$t['dirname']] = $t['domain'] ? $t['domain'] : $info['domain'].$t['dirname'].'/';
				}
				$cache[$val['siteid']]['client'] = $_save;
			}
		}
		$this->cache->set_file('site', $cache);
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
		if(cleck_admin($roleid)) {
			$sitelists = $this->get_list();
			foreach($sitelists as $v) {
				$sitelist[] = $v['siteid'];
			}
		} else {
			$sitelist = getcache('role_siteid', 'commons');
			if (is_array(dr_string2array($roleid))) {
				$roleid = dr_string2array($roleid);
				$sitelist = $sitelist[$roleid[0]];
			} else {
				$sitelist = $sitelist[$roleid];
			}
		}
		if(is_array($sitelist)) {
			$siteid = implode(',',array_unique($sitelist));
			return $siteid;			
		} else {
			dr_admin_msg(0, L('no_site_permissions'), '?m=admin&c=index&a=login');
		}
	}
}