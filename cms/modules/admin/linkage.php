<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
set_time_limit(0);
class linkage extends admin {
	private $input,$cache,$db,$categorys,$pids,$child_pids;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('linkage_model');
		pc_base::load_sys_class('form', '', 0);
		$this->siteid = $this->get_siteid();
	}
	
	/**
	 * 联动菜单列表
	 */
	public function init() {
		$dt_data = array(
			1 => '导入省级',
			2 => '导入省市',
			3 => '导入省市县',
		);
		$infos = $this->db->select();
		$items = array();
		foreach ($infos as $k=>$r) {
			$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$r['id'];
			$number = $this->db->count();
			$infos[$k]['count'] = $number;
		}
		include $this->admin_tpl('linkage_list');
	}
	
	/**
	 * 添加联动菜单
	 */
	function add() {
		if(IS_AJAX_POST) {
			$data = $this->input->post('data');
			$this->_validation(0, $data);
			$rt = $this->create($data);
			if (!$rt['code']) {
				dr_json(0, $rt['msg']);
			}
			dr_json(1, L('操作成功'));
		} else {
			$show_header = $show_validator = true;
			include $this->admin_tpl('linkage_add');
		}
	}
	/**
	 * 编辑联动菜单
	 */
	public function edit() {
		if(IS_AJAX_POST) {
			$id = intval($this->input->post('id'));
			$data = $this->input->post('data');
			$data['code'] = strtolower($data['code']);
			$this->_validation($id, $data);
			$this->db->update($data,array('id'=>$id));
			dr_json(1,L('operation_success'));
		} else {
			$id = intval($this->input->get('id'));
			$data = $this->db->get_one(array('id'=>$id));
			if (!$data) {
				dr_admin_msg(0, L('联动菜单（'.$id.'）不存在'));
			}
			$show_header = $show_validator = true;
			include $this->admin_tpl('linkage_edit');
		}
		
	}
	public function public_import() {
		
		$id = (int)$this->input->get('id');
		$code = (int)$this->input->get('code');
		$page = (int)$this->input->get('page');
		$tpage = (int)$this->input->get('tpage');

		$path = CONFIGPATH.'linkage/import-file-'.$code.'/';
		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$id;

		if (!$page) {
			$files = dr_file_map($path);
			if (!$files) {
				html_msg(0, '文件分析失败');
			}
			foreach ($files as $t) {
				if (stripos($t, '.php')) {
					@unlink($path.$t);
				}
			}
			$this->db->query('TRUNCATE `'.$this->db->table_name.'`');
			html_msg(1, L('正在准备导入数据'), '?m=admin&c=linkage&a=public_import&code='.$code.'&id='.$id.'&page=1&tpage='.dr_count($files));
		}

		if (!is_file($path.$page.'.json')) {
			$nums = $this->db->count();
			html_msg(1, L('导入完毕，共计'.$nums.'条数据'), '', L('请关闭本窗口'));
		}

		// 开始导入
		$data = dr_string2array(file_get_contents($path.$page.'.json'));
		if (!is_array($data)) {
			html_msg(0, L('导入信息验证失败'));
		}
		foreach ($data as $t) {
			if (is_numeric($t['cname'])) {
				$t['cname'] = 'a'.$t['cname'];
			} elseif (!preg_match('/^[a-z]+[a-z0-9\_]+$/i', $t['cname'])) {
				$t['cname'] = dr_safe_filename($t['cname']);
			}
			$rt = $this->db->insert($t, true);
			if ($rt) {
				$count++;
			}
		}

		html_msg(1, L('正在导入数据【'.$tpage.'/'.$page.'】...'),  '?m=admin&c=linkage&a=public_import&code='.$code.'&id='.$id.'&page='.($page+1).'&tpage='.$tpage);
	}
	/**
	 * 删除菜单
	 */
	public function delete() {
		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_admin_msg(0, L('你还没有选择呢'));
		}
		foreach ($ids as $id) {
			$row = $this->db->get_one(array('id' => intval($id)));
			if (!$row) {
				dr_admin_msg(0, L('数据不存在(id:'.$id.')'));
			}
			$this->db->delete(array('id' => $id));
			// 删除表数据
			$table = $this->db->db_tablepre.'linkage_data_'.$id;
			$this->db->query('DROP TABLE IF EXISTS `'.$table.'`');
		}
		dr_admin_msg(1, L('operation_success'), HTTP_REFERER);
	}

	// 验证数据
	private function _validation($id, $data) {
		if (!$data['name']) {
			dr_json(0, L('名称不能为空'), array('field' => 'name'));
		} elseif (!$data['code']) {
			dr_json(0, L('别名不能为空'), array('field' => 'code'));
		} elseif ($this->db->is_exists($id, 'code', $data['code'])) {
			dr_json(0, L('别名已经存在'), array('field' => 'code'));
		}
	}

	// 创建菜单
	public function create($data) {
		if ($this->db->is_exists(0, 'code', $data['code'])) {
			return dr_return_data(0, L('别名已经存在'));
		}

		$insert_id = $this->db->insert(array(
			'name' => $data['name'],
			'code' => strtolower($data['code']),
			'type' => (int)$data['type'],
		), true);
		if (!$insert_id) {
			return $insert_id;
		}

		// 返回id
		$id = intval($insert_id);

		// 创建数据表
		$table = $this->db->db_tablepre.'linkage_data_'.$id;
		$this->db->query('DROP TABLE IF EXISTS `'.$table.'`');
		$this->db->query(trim("CREATE TABLE IF NOT EXISTS `{$table}` (
		  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		  `site` smallint(5) unsigned NOT NULL,
		  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
		  `pids` varchar(255) DEFAULT NULL COMMENT '所有上级id',
		  `name` varchar(255) NOT NULL COMMENT '菜单名称',
		  `cname` varchar(255) NOT NULL COMMENT '菜单别名',
		  `child` tinyint(1) unsigned DEFAULT NULL DEFAULT '0' COMMENT '是否有下级',
		  `hidden` tinyint(1) unsigned DEFAULT NULL DEFAULT '0' COMMENT '前端隐藏',
		  `childids` text DEFAULT NULL COMMENT '下级所有id',
		  `displayorder` int(10) DEFAULT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `cname` (`cname`),
		  KEY `hidden` (`hidden`),
		  KEY `list` (`site`,`displayorder`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT='联动菜单".dr_safe_replace($data['name'])."数据表'"));

		return dr_return_data($id);
	}
	
	public function public_cache() {
		$key = (int)$this->input->get('key');
		$link = $this->db->get_one(array('id'=>$key));
		if (!$link) {
			html_msg(0, L('联动菜单不存在'));
		}

		$page = (int)$this->input->get('page');
		$psize = 10; // 每页处理的数量
		$total = (int)$this->input->get('total');

		if (!$page) {
			$path = CACHE_PATH.'caches_linkage/'.$link['code'].'/';
			dr_dir_delete($path);
			$links = $this->repair($link); // 修复菜单
			$pids = $this->child_pids;
			$total = dr_count($pids);
			if (!$total) {
				html_msg(0, L('无可用数据'));
			}
			// 存储执行
			$this->cache->set_auth_data('linkage-all-'.$key, $links, $this->siteid);
			$this->cache->set_auth_data('linkage-cache-'.$key, array_chunk($pids, $psize), $this->siteid);
			html_msg(1, L('正在执行中...'), '?m=admin&c=linkage&a=public_cache&key='.$key.'&total='.$total.'&page='.($page+1));
		}

		$tpage = ceil($total / $psize); // 总页数
		$pids = $this->cache->get_auth_data('linkage-cache-'.$key, $this->siteid);
		if (!$pids) {
			html_msg(0, L('临时数据读取失败'));
		} elseif (!isset($pids[$page-1]) || $page > $tpage) {
			// 生成级联关系
			$links = $this->cache->get_auth_data('linkage-all-'.$key, $this->siteid);
			$this->get_json($link, $links);
			$this->cache->del_auth_data('linkage-all-'.$key, $this->siteid);
			$this->cache->del_auth_data('linkage-cache-'.$key, $this->siteid);
			html_msg(1, L('更新完成'));
		}

		foreach ($pids[$page-1] as $pid) {
			$this->cache_list($link, $pid);
		}

		html_msg(1, L('正在执行中'.$tpage.'/'.$page.'...'),'?m=admin&c=linkage&a=public_cache&key='.$key.'&total='.$total.'&page='.($page+1));
	}

	/**
	 * 修复菜单数据
	 */
	public function repair($link) {

		if (!$link) {
			return;
		}

		$this->categorys = $categorys = [];
		
		// 站点独立 // 共享共享
		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$link['id'];
		$_data = $link['type']
			? $this->db->select(array('site'=>$link['type']),'*','','displayorder ASC,id ASC')
			: $this->db->select('','*','','displayorder ASC,id ASC');
		if (!$_data) {
			return;
		}

		// 全部栏目数据
		foreach ($_data as $t) {
			$this->pids[$t['pid']][] = $t['id']; // 归类
			$categorys[$t['id']] = $this->categorys[$t['id']] = $t;
		}

		$this->child_pids = [0];
		foreach ($this->categorys as $catid => $cat) {
			$this->categorys[$catid]['pids'] = $this->get_pids($catid);
			$this->categorys[$catid]['childids'] = $this->get_childids($catid);
			$this->categorys[$catid]['child'] = is_numeric($this->categorys[$catid]['childids']) ? 0 : 1;
			if ($this->categorys[$catid]['child']) {
				$this->child_pids[] = $catid;
			}
			// 当库中与实际不符合才更新数据表
			if ($categorys[$catid]['pids'] != $this->categorys[$catid]['pids']
				|| $categorys[$catid]['childids'] != $this->categorys[$catid]['childids']
				|| $categorys[$catid]['child'] != $this->categorys[$catid]['child']) {
				$this->db->update(array(
					'pids' => $this->categorys[$catid]['pids'],
					'child' => $this->categorys[$catid]['child'],
					'childids' => $this->categorys[$catid]['childids']
				), array('id'=>$cat['id']));
			}
		}
		
		return $this->categorys;
	}

	public function get_child_row($pid) {
		$newArr = [];
		foreach ($this->categorys as $cat) {
			$item = [
				'value' => $cat['id'],
				'label' => $cat['name'],
				'children' => [],
			];
			if ($pid == $cat['pid']) {
				$item['children'] = $this->get_child_row($cat['id']);
				$newArr[] = $item;
			}

		}
		return $newArr;
	}

	/**
	 * 获取父栏目ID列表
	 *
	 * @param	integer	$catid	栏目ID
	 * @param	array	$pids	父目录ID
	 * @param	integer	$n		查找的层次
	 * @return	string
	 */
	protected function get_pids($catid, $pids = '', $n = 1) {

		if ($n > 100 || !$this->categorys || !isset($this->categorys[$catid])) {
			return FALSE;
		}

		$pid = $this->categorys[$catid]['pid'];
		$pids = $pids ? $pid.','.$pids : $pid;
		$pid && $pids = $this->get_pids($pid, $pids, ++$n);

		return $pids;
	}

	/**
	 * 获取子栏目ID列表
	 *
	 * @param	$catid	栏目ID
	 * @return	string
	 */
	protected function get_childids($catid, $n = 1) {

		$childids = $catid;

		if ($n > 100 || !is_array($this->categorys)
			|| !isset($this->categorys[$catid])) {
			return $childids;
		}

		if ($this->pids[$catid]) {
			foreach ($this->pids[$catid] as $id) {
				$cat = $this->categorys[$id];
				// 避免造成死循环
				$cat['pid']
				&& $id != $catid
				&& $cat['pid'] == $catid
				&& $this->categorys[$catid]['pid'] != $id
				&& $childids.= ','.$this->get_childids($id, ++$n);
			}
		}

		return $childids;
	}

	public function get_json($link, $links) {

		$json = [];
		if ($links) {
			$this->categorys = $links;
			$json = $this->get_child_row(0);
		}

		$data_path = 'linkage/'.$link['code'].'/';
		$this->cache->set_file('json', $json, $data_path);

	}

	/**
	 * 分组缓存菜单数据
	 */
	public function cache_list($link, $pid) {

		$data_path = 'linkage/'.$link['code'].'/';

		// 格式返回数据
		$lv = $data = array();
		$cid = $this->cache->get_file('id', $data_path, false);
		!$cid && $cid = array();

		// 执行程序
		$key = (int)$link['id'];
		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
		$link['type'] && $where = array('site'=>$link['type']); // 站点查询
		$where = array('pid'=>(int)$pid);
		$menu = $this->db->select($where,'*','','displayorder ASC,id ASC');
		if ($menu) {
			foreach ($menu as $t) {
				if ($t['hidden']) {
					continue;
				}
				$lv[] = substr_count($t['pids'], ',');
				$t['ii'] = $t['id'];
				$t['id'] = $t['cname'];
				$cid[$t['ii']] = $t['id'];
				$data[$t['cname']] = $t;
				$this->cache->set_file('data-'.$t['cname'], $data[$t['cname']], $data_path);
			}
		}

		$this->cache->set_file('list-'.$pid, $data, $data_path);

		$this->cache->set_file('id', $cid, $data_path);
		$this->cache->set_file('key', $key, $data_path);

		$level_data = (int)$this->cache->get_file('level', $data_path, false);
		$this->cache->set_file('level', max($lv ? max($lv) : 0, $level_data), $data_path);

		return $data;
	}

	/**
	 * 管理联动菜单子菜单
	 */
	public function public_manage_submenu() {
		$key = (int)$this->input->get('key');
		$pid = (int)$this->input->get('pid');
		$link = $this->db->get_one(array('id'=>$key));
		if (!$link) {
			dr_admin_msg(0, L('联动菜单不存在'));
		}
		$linkage = dr_linkage_list($link['code'], 0);
		if (!$linkage) {
			if (CI_DEBUG) {
				$select = '<div class="form-control-static" style="color:red">联动菜单【'.$link['code'].'】没有数据</div>';
			} else {
				$select = '';
			}
		} else {
			$select = dr_rp(menu_linkage($link['code'], 'pid', 0), 'info[pid]', 'pid');
		}
		$list = $this->getList($link, $pid);
		include $this->admin_tpl('linkage_submenu');
	}

	// 批量启用
	public function public_list_open() {

		$ids = $this->input->get_post_ids();
		$key = (int)$this->input->get('key');
		if (!$ids) {
			dr_json(0, L('你还没有选择呢'));
		}

		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
		foreach ($ids as $id) {
			$this->db->update(array('hidden' => 0), array('id' => $id));
		}

		dr_json(1, L('操作成功'));
	}

	// 批量禁用
	public function public_list_close() {

		$ids = $this->input->get_post_ids();
		$key = (int)$this->input->get('key');
		if (!$ids) {
			dr_json(0, L('你还没有选择呢'));
		}

		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
		foreach ($ids as $id) {
			$this->db->update(array('hidden' => 1), array('id' => $id));
		}

		dr_json(1, L('操作成功'));
	}

	// 删除子菜单
	public function public_list_del() {

		$ids = $this->input->get_post_ids();
		$key = (int)$this->input->get('key');
		if (!$ids) {
			dr_json(0, L('你还没有选择呢'));
		}

		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
		foreach ($ids as $id) {
			$this->db->delete(array('id'=>$id));
		}

		dr_json(1, L('操作成功'), ['ids' => $ids]);
	}

	// 变更分类
	public function public_pid_edit() {

		$ids = $this->input->get_post_ids();
		$key = (int)$this->input->get('key');
		$pid = (int)$this->input->post('pid');
		if (!$ids) {
			dr_json(0, L('你还没有选择呢'));
		}

		$rt = $this->edit_pid_all($key, $pid, $ids);
		if (!$rt['code']) {
			dr_json(0, $rt['msg']);
		}

		dr_json(1, L('操作成功'), array('ids' => $ids));
	}

	/**
	 * 全部子菜单数据
	 *
	 * @param	array	$link
	 * @param	intval	$pid
	 * @return	array
	 */
	public function getList($link, $pid = 'NULL') {

		$key = (int)$link['id'];

		if ($pid === 'NULL') {
			$name = 'linkage-cahce-list-'.$key.'-'.$pid;
			$data = $this->cache->get_data($name);
			if ($data) {
				return $data;
			}
			$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
			// 获取菜单数据
			$menu = $this->db->select('','*','','displayorder ASC,id ASC');
			if (!$menu) {
				return array();
			}
			// 格式返回数据
			$data = array();
			foreach ($menu as $t) {
				$data[$t['id']]	= $t;
			}
			$this->cache->set_data($name, $data);
		} else {
			$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
			// 站点查询
			$link['type'] && $where2 = array('site'=>$link['type']);
			$where = array('pid'=>(int)$pid);
			$where = dr_array22array($where, $where2);
			$menu = $this->db->select($where,'*','','displayorder ASC,id ASC');
			if (!$menu) {
				return array();
			}
			// 格式返回数据
			$data = array();
			foreach ($menu as $t) {
				$data[$t['id']]	= $t;
			}
		}

		return $data;
	}
	
	/**
	 * 子菜单添加
	 */
	public function public_listk_add() {
		if(IS_AJAX_POST) {
			$key = (int)$this->input->post('key');
			$all = (int)$this->input->post('all');
			$data = $this->input->post('data');
			$link = $this->db->get_one(array('id'=>$key));
			$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
			$pid = intval($data['pid']);

			if ($all) {
				// 批量
				if (!$data['all']) {
					dr_json(0, L('名称不能为空'), array('field' => 'all'));
				}
				$c = 0;
				$py = pc_base::load_sys_class('pinyin'); // 拼音转换类
				$names = explode(PHP_EOL, trim($data['all']));
				foreach ($names as $t) {
					$t = trim($t);
					if (!$t) {
						continue;
					}
					$cname = $py->result($t);
					if (is_numeric($cname)) {
						$cname = 'a'.$cname;
					}
					$cf = $this->db->count(array('cname'=>$cname));
					$rt = $this->db->insert(array(
						'pid' => $pid,
						'pids' => '',
						'name' => $t,
						'site' => 1,
						'child' => 0,
						'cname' => $cname,
						'hidden' => 0,
						'childids' => '',
						'displayorder' => 0
					), true);
					if (!$rt) {
						return $rt;
					}
					if ($cf) {
						// 重复验证
						$this->db->update(array('cname' => $cname.$rt), array('id' => $rt));
					}
					$c++;
				}
				// 更新pid
				$pid && $this->db->update(array('child' => 1), array('id' => $pid));
				dr_json(1, L('批量添加'.$c.'个'));
			} else {
				// 单个
				$data['name'] = trim($data['name']);
				if (!$data['name']) {
					dr_json(0, L('名称不能为空'), array('field' => 'name'));
				} elseif (!$data['cname']) {
					dr_json(0, L('别名不能为空'), array('field' => 'cname'));
				}
				if (is_numeric($data['cname'])) {
					$data['cname'] = 'a'.$data['cname'];
				}
				if ($this->db->count(array('cname'=>$data['cname']))) {
					dr_json(0, L('别名已经存在'), array('field' => 'cname'));
				}
				$rt = $this->db->insert(array(
					'pid' => $pid,
					'pids' => '',
					'name' => $data['name'],
					'site' => 1,
					'child' => 0,
					'cname' => $data['cname'],
					'hidden' => 0,
					'childids' => '',
					'displayorder' => 0
				), true);
				if (!$rt) {
					return $rt;
				}
				// 更新pid
				$pid && $this->db->update(array('child' => 1), array('id' => $pid));
				dr_json(1, L('操作成功'));
			}
		} else {
			$pid = (int)$this->input->get('pid');
			$key = (int)$this->input->get('key');
			$link = $this->db->get_one(array('id'=>$key));
			if (!$link) {
				dr_admin_msg(0, L('联动菜单不存在'));
			}
			$select = '';
			if ($pid) {
				$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
				$top = $this->db->get_one(array('id'=>$pid));
				if ($top) {
					$select = '<input type="hidden" name="data[pid]" value="'.$pid.'">';
					$select.= '<p class="form-control-static"> '.$top['name'].' </p>';
				}
			}
			if (!$select) {
				$select = '<input type="hidden" name="data[pid]" value="0">';
				$select.= '<p class="form-control-static"> '.L('顶级').' </p>';
			}
			$show_header = $show_validator = true;
			include $this->admin_tpl('linkage_sub_add');			
		}
	}

	/**
	 * 子菜单修改
	 */
	public function public_list_edit() {		
		if(IS_AJAX_POST) {
			$id = (int)$this->input->post('id');
			$key = (int)$this->input->post('key');
			$post = $this->input->post('data');
			$post['name'] = trim($post['name']);
			$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
			$where = 'id<>'.$id.' and cname=\''.$post['cname'].'\'';
			$info = $this->db->get_one($where);
			if (!$post['name']) {
				dr_json(0, L('名称不能为空'));
			} elseif (!$post['cname']) {
				dr_json(0, L('别名不能为空'));
			} elseif (is_numeric($post['cname'])) {
				dr_json(0, L('别名不能是数字'));
			} else if ($info) {
				dr_json(0, L('别名已经存在'));
			}
			$this->db->update($post,array('id'=>$id));
			dr_json(1, L('操作成功'));
		} else {
			$id = (int)$this->input->get('id');
			$pid = (int)$this->input->get('pid');
			$key = (int)$this->input->get('key');
			$link = $this->db->get_one(array('id'=>$key));
			if (!$link) {
				dr_admin_msg(0, L('联动菜单不存在'));
			}
			$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
			$data = $this->db->get_one(array('id'=>$id));
			if (!$data) {
				dr_admin_msg(0, L('联动菜单数据#'.$id.'不存在'));
			}
			$select = '';
			if ($data['pid']) {
				$top = $this->db->get_one(array('id'=>$data['pid']));
				if ($top) {
					$select = '<input type="hidden" name="data[pid]" value="'.$data['pid'].'">';
					$select.= '<p class="form-control-static"> '.$top['name'].' </p>';
				}
			}
			if (!$select) {
				$select = '<input type="hidden" name="data[pid]" value="0">';
				$select.= '<p class="form-control-static"> '.L('顶级').' </p>';
			}
			$show_header = $show_validator = true;
			include $this->admin_tpl('linkage_sub_edit');			
		}
	}

	public function public_displayorder() {

		// 查询数据
		$id = (int)$this->input->get('id');
		$key = (int)$this->input->get('key');
		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
		$row = $this->db->get_one(array('id'=>$id));
		if (!$row) {
			dr_json(0, L('数据#'.$id.'不存在'));
		}

		$value = (int)$this->input->get('value');
		$this->db->update(array('displayorder'=>$value),array('id'=>$id));
		dr_json(1, L('操作成功'));
	}

	// 禁用或者启用
	public function public_hidden_edit() {

		$id = (int)$this->input->get('id');
		$key = (int)$this->input->get('key');
		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
		$row = $this->db->get_one(array('id'=>$id));
		if (!$row) {
			dr_json(0, L('数据#'.$id.'不存在'));
		}

		$i = intval($this->input->get('id'));
		$v = $row['hidden'] ? 0 : 1;
		$this->db->update(array('hidden'=>$v),array('id'=>$id));
		dr_json(1, L($v ? '此菜单已被禁用' : '此菜单已被启用'), ['value' => $v]);

	}

	// 批量移动分类
	public function edit_pid_all($key, $pid, $ids) {

		$this->db->table_name = $this->db->db_tablepre.'linkage_data_'.$key;
		foreach ($ids as $id) {
			if ($id == $pid) {
				return dr_return_data(0, L('分类上级不能为本身'));
			}
			$childids = $this->db->get_one(array('id'=>$id), 'childids');
			$childids_arr = explode(',',$childids['childids']);
			if(dr_in_array($pid,$childids_arr)){
				return dr_return_data(0, L('分类上级不能为本身'));
			}
		}

		foreach ($ids as $id) {
			$row = $this->db->get_one(array('id'=>intval($id)));
			if (!$row) {
				return dr_return_data(0, L('数据不存在(id:'.$id.')'));
			}

			$this->db->update(array('pid' => $pid),array('id'=>$id));
		}

		$this->repair(array(
			'id' => $key,
			'type' => 0
		));

		return dr_return_data(1, '');
	}
}
?>