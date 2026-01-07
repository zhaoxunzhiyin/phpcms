<?php
/**
 * 
 * 更新缓存类
 *
 */

defined('IN_CMS') or exit('No permission resources.');
class cache_api {
	
	private $input,$config,$cache,$db,$siteid;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->config = pc_base::load_sys_class('config');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = '';
		$this->siteid = get_siteid();
	}
	
	/**
	 * 更新缓存
	 * @param string $model 方法名
	 * @param string $param 参数
	 */
	public function cache($model = '', $param = '') {
		if (file_exists(PC_PATH.'model'.DIRECTORY_SEPARATOR.$model.'_model.class.php')) {
			$this->db = pc_base::load_model($model.'_model');
			if ($param) {
				$this->$model($param);
			} else {
				$this->$model();
			}
		} else {
			$this->$model();
		}
	}
	
	
	/**
	 * 更新站点缓存方法
	 */
	public function cache_site() {
		$site = pc_base::load_app_class('sites', 'admin');
		$site->set_cache();
	}
	
	/**
	 * 更新栏目缓存方法
	 */
	public function category() {
		$categorys = array();
		$models = getcache('model','commons');
		if (is_array($models)) {
			foreach ($models as $modelid=>$model) {
				$datas = $this->db->select(array('modelid'=>$modelid),'catid,type,items');
				$array = array();
				foreach ($datas as $r) {
					if($r['type']==0) $array[$r['catid']] = $r['items'];
				}
				setcache('category_items_'.$modelid, $array,'commons');
			}
		}
		$array = array();
		$categorys = $this->db->select(array('module'=>'content'),'catid,siteid','','listorder ASC,catid ASC');
		foreach ($categorys as $r) {
			$array[$r['catid']] = $r['siteid'];
		}
		setcache('category_content',$array,'commons');
		return true;
	}
	
	/**
	 * 更新单网页缓存方法
	 */
	public function page() {
		$data = $this->db->select();
		$cache = array();
		if ($data) {
			foreach ($data as $t) {
				$cache['data'][$t['catid']] = $t;
			}
		}
		$this->cache->set_file('page', $cache);
		return true;
	}
	
	// 清理缩略图
	public function update_thumb() {
		if (strpos(CMS_PATH, SYS_THUMB_PATH) !== false || is_file(SYS_THUMB_PATH.'index.php')) {
			// 防止误删除
			dr_json(0, L('缩略图目录异常，请手动清理：'.SYS_THUMB_PATH));
		}
		dr_file_delete(SYS_THUMB_PATH, true, true);
		dr_json(1, L('清理完成'), 1);
	}
	
	// 远程附件缓存
	public function attachment_remote() {
		$data = $this->db->select();
		$cache = array();
		if ($data) {
			foreach ($data as $t) {
				$t['url'] = trim($t['url'], '/').'/';
				$t['value'] = dr_string2array($t['value']);
				$t['value'] = $t['value'][intval($t['type'])];
				$cache[$t['id']] = $t;
			}
		}
		$this->cache->set_file('attachment', $cache);
		return true;
	}
	
	// 更新附件缓存
	public function attachment() {
		$page = intval($this->input->get('page'));
		if (!$page) {
			dr_file_delete(CACHE_PATH.'caches_attach/caches_data', true, true);
			/*不清理缩略图文件是因为静态页面会导致缩略图404的悲剧
			dr_dir_delete(SYS_THUMB_PATH);
			dr_mkdirs(SYS_THUMB_PATH);*/
			dr_json(1, L('正在检查附件'), 1);
		}

		$total = $this->db->count();
		if (!$total) {
			dr_json(1, L('无可用附件更新'), 0);
		}

		$psize = 300;
		$tpage = ceil($total/$psize);
		$result = $this->db->listinfo('','aid ASC',$page,$psize);
		if ($result) {
			foreach ($result as $t) {
				get_attachment($t['aid'], 1);
			}
		}

		if ($page > $tpage) {
			dr_json(1, L('已更新'.$total.'个附件'), 0);
		}

		dr_json(1, L('正在更新中（'.$page.'/'.$tpage.'）'), $page + 1);
	}
	
	/**
	 * 更新下载服务器缓存方法
	 */
	public function downservers() {
		$infos = $this->db->select('','*','','listorder DESC');
		$servers = array();
		foreach ($infos as $info){
			$servers[$info['id']] = $info;
		}
		setcache('downservers', $servers,'commons');
		return $infos;
	}
	
	/**
	 * 更新敏感词缓存方法
	 */
	public function badword() {
		$infos = $this->db->select('','badid,badword,replaceword,level','','badid ASC');
		setcache('badword', $infos, 'commons');
		return true;
	}
	
	/**
	 * 更新ip禁止缓存方法
	 */
	public function ipbanned() {
		$infos = $this->db->select('', '`ip`,`expires`', '', 'ipbannedid desc');
		setcache('ipbanned', $infos, 'commons');
		return true;
	}
	
	/**
	 * 更新关联链接缓存方法
	 */
	public function keylink() {
		$infos = $this->db->select('','word,url','','keylinkid ASC');
		$datas = $rs = array();
		foreach($infos as $r) {
			$rs[0] = $r['word'];
			$rs[1] = $r['url'];
			$datas[] = $rs;
		}
		setcache('keylink', $datas, 'commons');
		return true;
	}
	
	/**
	 * 更新推荐位缓存方法
	 */
	public function position() {
		$infos = $this->db->select('','*','','listorder DESC');
		$positions = array();
		foreach ($infos as $info){
			$positions[$info['posid']] = $info;
		}
		setcache('position', $positions,'commons');
		return $infos;
	}
	
	/**
	 * 更新投票配置
	 */
	public function vote_setting() {
		$m_db = pc_base::load_model('module_model');
		$data = $m_db->select(array('module'=>'vote'));
		if ($data) {
			$setting = string2array($data[0]['setting']);
		} else {
			$setting = array();
		}
		setcache('vote', $setting, 'commons');
	}
	
	/**
	 * 更新友情链接配置
	 */
	public function link_setting() {
		$m_db = pc_base::load_model('module_model');
		$data = $m_db->select(array('module'=>'link'));
		if ($data) {
			$setting = string2array($data[0]['setting']);
		} else {
			$setting = array();
		}
		setcache('link', $setting, 'commons');
	}
	
	/**
	 * 更新管理员角色缓存方法
	 */
	public function admin_role() {
		$infos = $this->db->select(array('disabled'=>'0'), $data = '`roleid`,`rolename`', '', 'roleid ASC');
		$role = array();
		foreach ($infos as $info){
			$role[$info['roleid']] = $info['rolename'];
		}
		$this->cache_siteid($role);
		setcache('role', $role,'commons');
		return $infos;
	}
	
	/**
	 * 更新管理员角色缓存方法
	 */
	public function cache_siteid($role) {
		$priv_db = pc_base::load_model('admin_role_priv_model');
		$sitelist = array();
		foreach($role as $n=>$r) {
			$sitelists = $priv_db->select(array('roleid'=>$n),'siteid', '', 'siteid');
			foreach($sitelists as $site) {
				foreach($site as $v){
					$sitelist[$n][] = intval($v);
				}
			}
		}
		$sitelist = @array_map("array_unique", $sitelist);
		setcache('role_siteid', $sitelist,'commons');
		return $sitelist;
	}
	
	/**
	 * 更新url规则缓存方法
	 */
	public function urlrule() {
		$datas = $this->db->select('','*','','','','urlruleid');
		$basic_data = array();
		foreach($datas as $roleid=>$r) {
			$basic_data[$roleid] = $r['urlrule'];
		}
		setcache('urlrules_detail',$datas,'commons');
		setcache('urlrules',$basic_data,'commons');
	}
	
	/**
	 * 更新模块缓存方法
	 */
	public function module() {
		$modules = array();
		$modules = $this->db->select(array('disabled'=>0), '*', '', '', '', 'module');
		setcache('modules', $modules, 'commons');
		return true;
	}
	
	/**
	 * 更新模型缓存方法
	 */
	public function sitemodel() {
		define('MODEL_PATH', PC_PATH.'modules'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
		define('CACHE_MODEL_PATH', CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
		require MODEL_PATH.'fields.inc.php';
		//更新内容模型类：表单生成、入库、更新、输出
		$classtypes = array('form','input','update','output');
		foreach($classtypes as $classtype) {
			$cache_data = file_get_contents(MODEL_PATH.'content_'.$classtype.'.class.php');
			$cache_data = str_replace('}?>','',$cache_data);
			foreach($fields as $field=>$fieldvalue) {
				if(file_exists(MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php')) {
					$cache_data .= file_get_contents(MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php');
				}
			}
			$cache_data .= "\r\n } \r\n?>";
			file_put_contents(CACHE_MODEL_PATH.'content_'.$classtype.'.class.php',$cache_data);
			chmod(CACHE_MODEL_PATH.'content_'.$classtype.'.class.php',0777);
		}
		$this->sitemodel_field(0);
		$this->sitemodel_field(-1);
		$this->sitemodel_field(-2);
		//更新模型数据缓存
		$this->sitemodels();
		$data = $this->db->select('' , "*", '', 'sort,modelid');
		if ($data) {
			foreach ($data as $t) {
				$t['field'] = array();
				$t['setting'] = dr_string2array($t['setting']);
				// 排列table字段顺序
				$t['setting']['list_field'] = isset($t['setting']['list_field']) ? dr_list_field_order($t['setting']['list_field']) : '';

				// 当前表单的自定义字段
				$sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
				$field = $sitemodel_field_db->select(array('modelid'=>intval($t['modelid'])),'*','','listorder ASC,fieldid ASC');
				if ($field) {
					foreach ($field as $fv) {
						$fv['setting'] = dr_string2array($fv['setting']);
						$t['field'][$fv['field']] = $fv;
					}
				}
				$cache[$t['tablename']] = $t;
			}
		}
		$this->cache->set_file('sitemodel', $cache);
		return true;
	}
	
	/**
	 * 更新模型缓存方法
	 */
	public function sitemodels() {
		$sitemodel_db = pc_base::load_model('sitemodel_model');
		$sitemodel_datas = $sitemodel_db->select(array('type'=>0,'disabled'=>0), "*", '', 'sort,modelid');
		$model_array = array();
		foreach ($sitemodel_datas as $r) {
			$r['setting'] = dr_string2array($r['setting']);
			$model_array[$r['modelid']] = $r;
			$this->sitemodel_field($r['modelid']);
		}
		setcache('model', $model_array, 'commons');
	}
	
	/**
	 * 更新模型字段缓存方法
	 */
	public function sitemodel_field($modelid) {
		$field_array = array();
		$db = pc_base::load_model('sitemodel_field_model');
		$fields = $db->select(array('modelid'=>$modelid,'disabled'=>0),'*','','listorder ASC,fieldid ASC');
		foreach($fields as $_value) {
			if (is_array(string2array($_value['setting']))) {
				$setting = string2array($_value['setting']);
			} else {
				$setting = $_value['setting'];
			}
			$_value = array_merge($_value,$setting);
			$field_array[$_value['field']] = $_value;
		}
		setcache('model_field_'.$modelid,$field_array,'model');
		return true;
	}
	
	/**
	 * 更新类别缓存方法
	 */
	public function type($param = '') {
		$datas = array();
		$result_datas = $this->db->select(array('siteid'=>get_siteid(),'module'=>$param),'*','','listorder ASC,typeid ASC');
		foreach($result_datas as $_key=>$_value) {
			$datas[$_value['typeid']] = $_value;
		}
		if ($param=='search') {
			$this->search_type();
		} else {
			if ($param) {
				setcache('type_'.$param, $datas, 'commons');
			}
		}
		return true;
	}
	
	/**
	 * 更新工作流缓存方法
	 */
	public function workflow() {
		$datas = array();
		$workflow_datas = $this->db->select(array('siteid'=>get_siteid()));
		foreach($workflow_datas as $_k=>$_v) {
			$datas[$_v['workflowid']] = $_v;
		}
		setcache('workflow_'.get_siteid(),$datas,'commons');
		return true;
	}
	
	/**
	 * 更新数据源缓存方法
	 */
	public function dbsource() {
		$db = pc_base::load_model('dbsource_model');
		$list = $db->select();
		$data = array();
		if ($list) {
			foreach ($list as $val) {
				$data[$val['name']] = array('hostname'=>$val['host'].':'.$val['port'], 'database' =>$val['dbname'] , 'db_tablepre'=>$val['dbtablepre'], 'username' =>$val['username'],'password' => $val['password'],'charset'=>$val['charset'],'debug'=>0,'pconnect'=>0,'autoconnect'=>0);
			}
		} else {
			return false;
		}
		return setcache('dbsource', $data, 'commons');
	}
	
	/**
	 * 更新会员组缓存方法
	 */
	public function member_group() {
		$grouplist = $this->db->select('', '*', '', 'sort ASC', '', 'groupid');
		setcache('grouplist', $grouplist,'member');
		return true;
	}
	
	/**
	 * 更新会员配置缓存方法
	 */
	public function member_setting() {
		$this->db = pc_base::load_model('module_model');
		$member_setting = $this->db->get_one(array('module'=>'member'), 'setting');
		$member_setting = dr_string2array($member_setting['setting']);
		// 排列table字段顺序
		$member_setting['list_field'] = isset($member_setting['list_field']) ? dr_list_field_order($member_setting['list_field']) : '';
		setcache('member_setting', $member_setting, 'member');
		return true;
	}
	
	/**
	 * 更新会员模型缓存方法
	 */
	public function membermodel() {
		define('MEMBER_MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'member'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
		//模型缓存路径
		define('MEMBER_CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
		
		$sitemodel_db = pc_base::load_model('sitemodel_model');
		$data = $sitemodel_db->select(array('type'=>2,'disabled'=>0), "*", '', 'sort,modelid', '', 'modelid');
		if ($data) {
			foreach ($data as $t) {
				$t['setting'] = dr_string2array($t['setting']);
				$cache[$t['modelid']] = $t;
				$this->sitemodel_field($t['modelid']);
			}
		}
		setcache('member_model', $cache, 'commons');
		
		require MEMBER_MODEL_PATH.'fields.inc.php';
		//更新内容模型类：表单生成、入库、更新、输出
		$classtypes = array('form','input','update','output');
		foreach($classtypes as $classtype) {
			$cache_data = file_get_contents(MEMBER_MODEL_PATH.'member_'.$classtype.'.class.php');
			$cache_data = str_replace('}?>','',$cache_data);
			foreach($fields as $field=>$fieldvalue) {
				if(file_exists(MEMBER_MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php')) {
					$cache_data .= file_get_contents(MEMBER_MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php');
				}
			}
			$cache_data .= "\r\n } \r\n?>";
			file_put_contents(MEMBER_CACHE_MODEL_PATH.'member_'.$classtype.'.class.php',$cache_data);
			chmod(MEMBER_CACHE_MODEL_PATH.'member_'.$classtype.'.class.php',0777);
		}
		
		return true;
	}
	
	/**
	 * 更新会员模型字段缓存方法
	 */
	public function member_model_field() {
		$member_model = getcache('member_model', 'commons');
		$this->db = pc_base::load_model('sitemodel_field_model');
		foreach ($member_model as $modelid => $m) {
			$field_array = array();
			$fields = $this->db->select(array('modelid'=>$modelid,'disabled'=>0),'*','','listorder ASC');
			foreach($fields as $_value) {
				if (is_array(string2array($_value['setting']))) {
					$setting = string2array($_value['setting']);
				} else {
					$setting = $_value['setting'];
				}
				$_value = array_merge($_value,$setting);
				$field_array[$_value['field']] = $_value;
			}
			setcache('model_field_'.$modelid,$field_array,'model');
		}
		return true;
	}
	
	/**
	 * 更新表单向导模型缓存方法
	 */
	public function formguidemodel() {
		define('FORMGUIDE_MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'formguide'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
		//模型缓存路径
		define('FORMGUIDE_CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
		
		$sitemodel_db = pc_base::load_model('sitemodel_model');
		$data = $sitemodel_db->select(array('type'=>3,'disabled'=>0), "*", '', 'sort,modelid', '', 'modelid');
		if ($data) {
			foreach ($data as $t) {
				$t['setting'] = dr_string2array($t['setting']);
				$cache[$t['modelid']] = $t;
				$this->sitemodel_field($t['modelid']);
			}
		}
		setcache('formguide_model', $cache, 'commons');
		
		require FORMGUIDE_MODEL_PATH.'fields.inc.php';
		//更新内容模型类：表单生成、入库、更新、输出
		$classtypes = array('form','input','update','output');
		foreach($classtypes as $classtype) {
			$cache_data = file_get_contents(FORMGUIDE_MODEL_PATH.'formguide_'.$classtype.'.class.php');
			$cache_data = str_replace('}?>','',$cache_data);
			foreach($fields as $field=>$fieldvalue) {
				if(file_exists(FORMGUIDE_MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php')) {
					$cache_data .= file_get_contents(FORMGUIDE_MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php');
				}
			}
			$cache_data .= "\r\n } \r\n?>";
			file_put_contents(FORMGUIDE_CACHE_MODEL_PATH.'formguide_'.$classtype.'.class.php',$cache_data);
			@chmod(FORMGUIDE_CACHE_MODEL_PATH.'formguide_'.$classtype.'.class.php',0777);
		}
		
		return true;
	}
	
	/**
	 * 更新搜索配置缓存方法
	 */
	public function search_setting() {	
		$this->db = pc_base::load_model('module_model');
		$setting = $this->db->get_one(array('module'=>'search'), 'setting');
		$setting = string2array($setting['setting']);
		setcache('search', $setting, 'search');
		return true;
	}
	
	/**
	 * 更新搜索类型缓存方法
	 */
	public function search_type() {
		$sitelist = getcache('sitelist','commons');
		foreach ($sitelist as $siteid=>$_v) {
			$datas = $search_model = array();
			$result_datas = $result_datas2 = $this->db->select(array('siteid'=>$siteid,'module'=>'search'),'*','','listorder ASC');
			foreach($result_datas as $_key=>$_value) {
				if(!$_value['modelid']) continue;
				$datas[$_value['modelid']] = $_value['typeid'];
				$search_model[$_value['modelid']]['typeid'] = $_value['typeid'];
				$search_model[$_value['modelid']]['name'] = $_value['name'];
				$search_model[$_value['modelid']]['sort'] = $_value['listorder'];
			}
			setcache('type_model_'.$siteid,$datas,'search');
			$datas = array();	
			foreach($result_datas2 as $_key=>$_value) {
				if($_value['modelid']) continue;
				$datas[$_value['typedir']] = $_value['typeid'];
				$search_model[$_value['typedir']]['typeid'] = $_value['typeid'];
				$search_model[$_value['typedir']]['name'] = $_value['name'];
			}
			setcache('type_module_'.$siteid,$datas,'search');
			//搜索header头中使用类型缓存
			setcache('search_model_'.$siteid,$search_model,'search');
		}
		return true;
	}
	
	/**
	 * 更新专题缓存方法
	 */
	public function special() {
		$specials = array();
		$result = $this->db->select(array('disabled'=>0), '`id`, `siteid`, `title`, `url`, `thumb`, `banner`, `ishtml`', '', '`listorder` DESC, `id` DESC');
		foreach($result as $r) {
			$specials[$r['id']] = $r;
		}
		setcache('special', $specials, 'commons');
		return true;
	}
	
	/**
	 * 更新网站配置方法
	 */
	public function setting() {
		$this->db = pc_base::load_model('module_model');
		$result = $this->db->get_one(array('module'=>'admin'));
		$setting = string2array($result['setting']);
		setcache('common', $setting,'commons');
		return true;
	}
	
	/**
	 * 更新数据源模块缓存方法
	 */
	public function database() {
		$module = $M = array();
		$M = getcache('modules', 'commons');
		if (is_array($M)) {
			foreach ($M as $key => $m) {
				if (file_exists(PC_PATH.'modules'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$key.'_tag.class.php') && !in_array($key, array('message', 'block'))) {
					$module[$key] = $m['name'];
				}
			}
		}
		return $this->config->file(CONFIGPATH.'modules.php', '模块缓存', 32)->to_require($module);
	}
	
	/**
	 * 更新删除缓存文件方法
	 */
	public function del_file() {
		$path = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR;
		$files = glob($path.'*');
		if (is_array($files)) {
			foreach ($files as $f) {
				$dir = basename($f);
				if (!in_array($dir, array('block', 'dbsource'))) {
					dr_dir_delete($path.$dir, TRUE);
				}
			}
		}
		dr_dir_delete(CACHE_PATH.'caches_page_tmp'.DIRECTORY_SEPARATOR, TRUE);
		if (!SYS_CACHE_CLEAR) {
			// 清空系统缓存
			$this->cache->init()->clean();
			// 清空文件缓存
			pc_base::load_sys_class('cache_file')->clean();
		}
		// 删除缓存保留24小时内的文件
		$path = [
			CACHE_PATH.'caches_authcode/caches_data/',
			CACHE_PATH.'sessions',
			CACHE_PATH.'temp',
		];
		foreach ($path as $p) {
			if ($fp = opendir($p)) {
				while (FALSE !== ($file = readdir($fp))) {
					if ($file === '.' OR $file === '..'
						OR $file === 'index.html'
						OR $file === '.htaccess'
						OR $file[0] === '.'
						OR !is_file($p.'/'.$file)
						OR SYS_TIME - filemtime($p.'/'.$file) < 3600 * 24 // 保留24小时内的文件
					) {
						continue;
					}
					unlink($p.'/'.$file);
				}
			}
		}
		if ($fp) {
			flock ( $fp ,LOCK_UN);
			fclose( $fp );
		}
		return true;
	}

	// 清理日志文件
	public function update_log() {
		$db = pc_base::load_model('log_model');
		$db->query('TRUNCATE `'.$db->table_name.'`');
		foreach ([
			CACHE_PATH.'debuglog/',
			CACHE_PATH.'/caches_error/caches_data/',
		] as $t) {
			if (is_dir($t)) {
				dr_file_delete($t, true, true);
			}
		}
		foreach ([
			CACHE_PATH.'email_log.php',
		] as $t) {
			if (is_file($t)) {
				unlink($t);
			}
		}
		return true;
	}

	/**
	 * 更新来源缓存方法
	 */
	public function copyfrom() {
		$infos = $this->db->select('','*','','listorder DESC','','id');
		setcache('copyfrom', $infos, 'admin');
		return true;
	}
}