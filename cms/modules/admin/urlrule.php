<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);

class urlrule extends admin {
	private $input,$db,$module_db,$cache_api;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('urlrule_model');
		$this->module_db = pc_base::load_model('module_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->siteid = $this->get_siteid();
	}
	
	function init () {
		$page = intval($this->input->get('page'));
		$infos = $this->db->listinfo('','',$page,SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$this->public_cache_urlrule();
		include $this->admin_tpl('urlrule_list');
	}
	function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$info['urlrule'] = rtrim(trim($info['urlrule']),'.php');
			$info['urlrule'] = $this->url_replace($info['urlrule']);
			if($this->url_ifok($info['urlrule'], $info['ishtml'])==false){
				dr_admin_msg(0,'url规则里含有非法php字符');
			}
			$this->db->insert($info);
			$this->public_cache_urlrule();
			dr_admin_msg(1,L('add_success'),'','','add');
		} else {
			$show_validator = $show_header = true;
			$modules_arr = $this->module_db->select('','module,name');
			
			$modules = array();
			foreach ($modules_arr as $r) {
				$modules[$r['module']] = $r['name'];
			}
		
			include $this->admin_tpl('urlrule_add');
		}
	}
	function delete() {
		$urlruleid = intval($this->input->get('urlruleid'));
		$this->db->delete(array('urlruleid'=>$urlruleid));
		$this->public_cache_urlrule();
		dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
	}
	
	function edit() {
		if(IS_POST) {
			$urlruleid = intval($this->input->post('urlruleid'));
			$info = $this->input->post('info');
			$info['urlrule'] = rtrim(trim($info['urlrule']),'.php');
			$info['urlrule'] = $this->url_replace($info['urlrule']);
			if($this->url_ifok($info['urlrule'], $info['ishtml'])==false){
				dr_admin_msg(0,'url规则里含有非法php字符');
			}
			$this->db->update($info,array('urlruleid'=>$urlruleid));
			$this->public_cache_urlrule();
			dr_admin_msg(1,L('update_success'),'','','edit');
		} else {
			$show_validator = $show_header = true;
			$urlruleid = $this->input->get('urlruleid');
			$r = $this->db->get_one(array('urlruleid'=>$urlruleid));
			extract($r);
			$modules_arr = $this->module_db->select('','module,name');
			
			$modules = array();
			foreach ($modules_arr as $r) {
				$modules[$r['module']] = $r['name'];
			}
			include $this->admin_tpl('urlrule_edit');
		}
	}
	// 伪静态
	public function rewrite() {
		
		$show_header = true;
		$domain = array();
		$domain[siteurl($this->siteid)] = L('本站电脑域名');
		sitemobileurl($this->siteid) && $domain[sitemobileurl($this->siteid)] = L('本站手机域名');

		$root = WEB_PATH;
		$server = strtolower($_SERVER['SERVER_SOFTWARE']);
		if (strpos($server, 'apache') !== FALSE) {
			$name = 'Apache';
			$note = '<font color=red><b>将以下内容保存为.htaccess文件，放到每个域名所绑定的根目录</b></font>';
			$code = '';

			// 子目录
			$code.= '###当存在多个子目录格式的域名时，需要多写几组RewriteBase标签：RewriteBase /目录/ '.PHP_EOL;
			if (!dr_site_info('mobilemode', $this->siteid)) {
				$code.= 'RewriteEngine On'.PHP_EOL.PHP_EOL;
				$code.= 'RewriteBase '.$root.'mobile/'.PHP_EOL
					.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
					.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
					.'RewriteRule !.(js|ico|gif|jpe?g|bmp|png|css)$ '.$root.'mobile/index.php [NC,L]'.PHP_EOL.PHP_EOL;
				$code.= '####以上目录需要单独保持到'.$root.'mobile/.htaccess文件中';
			}
			// 主目录
			$code.= 'RewriteEngine On'.PHP_EOL.PHP_EOL;
			$code.= 'RewriteBase '.$root.PHP_EOL
				.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
				.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
				.'RewriteRule !.(js|ico|gif|jpe?g|bmp|png|css)$ '.$root.'index.php [NC,L]'.PHP_EOL.PHP_EOL;
		} elseif (strpos($server, 'nginx') !== FALSE) {
			$name = $server;
			$note = '<font color=red><b>将以下代码放到Nginx配置文件中去（如果是绑定了域名，所绑定目录也要配置下面的代码）</b></font>';
			// 子目录
			$code = '###当存在多个子目录格式的域名时，需要多写几组location标签：location /目录/ '.PHP_EOL;
			if (!dr_site_info('mobilemode', $this->siteid)) {
				$code.= 'location '.$root.'mobile/ { '.PHP_EOL
					.'    if (-f $request_filename) {'.PHP_EOL
					.'           break;'.PHP_EOL
					.'    }'.PHP_EOL
					.'    if ($request_filename ~* "\.(js|ico|gif|jpe?g|bmp|png|css)$") {'.PHP_EOL
					.'        break;'.PHP_EOL
					.'    }'.PHP_EOL
					.'    if (!-e $request_filename) {'.PHP_EOL
					.'        rewrite . '.$root.'mobile/index.php last;'.PHP_EOL
					.'    }'.PHP_EOL
					.'}'.PHP_EOL.PHP_EOL;
			}
			// 主目录
			$code.= 'location '.$root.' { '.PHP_EOL
				.'    if (-f $request_filename) {'.PHP_EOL
				.'           break;'.PHP_EOL
				.'    }'.PHP_EOL
				.'    if ($request_filename ~* "\.(js|ico|gif|jpe?g|bmp|png|css)$") {'.PHP_EOL
				.'        break;'.PHP_EOL
				.'    }'.PHP_EOL
				.'    if (!-e $request_filename) {'.PHP_EOL
				.'        rewrite . '.$root.'index.php last;'.PHP_EOL
				.'    }'.PHP_EOL
				.'}'.PHP_EOL;
		} else {
			$name = $server;
			$note = '<font color=red><b>无法为此服务器提供伪静态规则，建议让运营商帮你把下面的Apache规则做转换</b></font>';
			$code = 'RewriteEngine On'.PHP_EOL
				.'RewriteBase /'.PHP_EOL
				.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
				.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
				.'RewriteRule !.(js|ico|gif|jpe?g|bmp|png|css)$ /index.php [NC,L]';
		}
		
		$count = $code ? dr_count(explode(PHP_EOL, $code)) : 0;

		include $this->admin_tpl('urlrule_rewrite');
	}
	// 生成伪静态解析文件规则
	public function public_rewrite_add() {
		$rt = $this->get_rewrite_code();
		dr_json($rt['code'], $rt['msg'], $rt['data']);
	}
	/**
	 * 更新URL规则
	 */
	public function public_cache_urlrule() {
		$this->cache_api->cache('urlrule');
	}
	/*
	*url规则替换
	**/
	public function url_replace($url){
		$urldb = explode("|",$url);
		foreach($urldb as $key=>$value){
			if(strpos($value, "index.php") === 0){
				$value = str_replace('index.php','',$value);
				$value = str_replace('.php','',$value);
				$value = "index.php".$value;
			}else{
				$value = str_replace('.php','',$value);
			}
			$urldb[$key]=$value;
		}
		return implode("|",$urldb);
	}
	/*
	*url规则 判断。
	**/
	public function url_ifok($url, $ishtml){
		$urldb = explode("|",$url);
		foreach($urldb as $key=>$value){
			if(!intval($ishtml) && strpos($value, "index.php") === 0){
				$value = substr($value,'9');
			}
			if( stripos($value, "php") !== false){
				return false;
			}
		}
		return true;
	}
	// 生成伪静态解析代码
	protected function get_rewrite_code() {

		$data = $this->db->select(array('ishtml'=>0));
		if (!$data) {
			return dr_return_data(0, L('你没有设置URL规则'));
		}

		$code = '';
		$error = '';
		$write = array(); // 防止重复
		foreach ($data as $r) {
			$urlrule = str_replace('$', '', $r['urlrule']);
			if (strstr($urlrule, '.php')) {
				continue;
			}
			if (strstr($urlrule, '|')) {
				$urlrule = explode('|', $urlrule);
				if ($r['file']=='category' && $urlrule[1]) {
					$rule = $urlrule[1];
					$cname = "栏目列表(分页)（{$rule}）";
					list($preg, $rname) = $this->_rule_preg_value($rule);
					if (!$preg || !$rname) {
						$error.= "<p>".$cname."格式不正确</p>";
					} elseif (!isset($rname['{page}'])) {
						$error.= "<p>".$cname."缺少{page}标签</p>";
					} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}'])) {
						$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}标签</p>";
					} else {
						if (isset($rname['{catdir}'])) {
							// 目录格式
							$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{catdir}'].'&page=$'.$rname['{page}'];
						} elseif (isset($rname['{categorydir}'])) {
							// 层次目录格式
							$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{categorydir}'].'&page=$'.$rname['{page}'];
						} elseif (isset($rname['{parentdir}'])) {
							// 层次目录格式
							$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{parentdir}'].'&page=$'.$rname['{page}'];
						} else {
							// catid模式
							$rule = 'index.php?m=content&c=index&a=lists&catid=$'.$rname['{catid}'].'&page=$'.$rname['{page}'];
						}
						if (isset($write[$preg])) {
							$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
						} else {
							$write[$preg] = $cname;
							$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
						}
					}
				}
				if ($r['file']=='category' && $urlrule[0]) {
					$rule = $urlrule[0];
					$cname = "栏目列表（{$rule}）";
					list($preg, $rname) = $this->_rule_preg_value($rule);
					if (!$preg || !$rname) {
						$error.= "<p>".$cname."格式不正确</p>";
					} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}'])) {
						$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}标签</p>";
					} else {
						if (isset($rname['{catdir}'])) {
							// 目录格式
							$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{catdir}'];
						} elseif (isset($rname['{categorydir}'])) {
							// 层次目录格式
							$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{categorydir}'];
						} elseif (isset($rname['{parentdir}'])) {
							// 层次目录格式
							$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{parentdir}'];
						} else {
							// catid模式
							$rule = 'index.php?m=content&c=index&a=lists&catid=$'.$rname['{catid}'];
						}
						if (isset($write[$preg])) {
							$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
						} else {
							$write[$preg] = $cname;
							$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
						}
					}
				}
				if ($r['file']=='show' && $urlrule[1]) {
					$rule = $urlrule[1];
					$cname = "内容页(分页)（{$rule}）";
					list($preg, $rname) = $this->_rule_preg_value($rule);
					if (!$preg || !$rname) {
						$error.= "<p>".$cname."格式不正确</p>";
					} elseif (!isset($rname['{page}'])) {
						$error.= "<p>".$cname."缺少{page}标签</p>";
					} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}']) && !isset($rname['{id}'])) {
						$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}或{id}标签</p>";
					} else {
						if (isset($rname['{catdir}'])) {
							$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{catdir}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
						} elseif (isset($rname['{categorydir}'])) {
							$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{categorydir}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
						} elseif (isset($rname['{parentdir}'])) {
							$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{parentdir}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
						} else {
							$rule = 'index.php?m=content&c=index&a=show&catid=$'.$rname['{catid}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
						}
						if (isset($write[$preg])) {
							$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
						} else {
							$write[$preg] = $cname;
							$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
						}
					}
				}
				if ($r['file']=='show' && $urlrule[0]) {
					$rule = $urlrule[0];
					$cname = "内容页（{$rule}）";
					list($preg, $rname) = $this->_rule_preg_value($rule);
					if (!$preg || !$rname) {
						$error.= "<p>".$cname."格式不正确</p>";
					} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}']) && !isset($rname['{id}'])) {
						$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}或{id}标签</p>";
					} else {
						if (isset($rname['{catdir}'])) {
							$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{catdir}'].'&id=$'.$rname['{id}'];
						} elseif (isset($rname['{categorydir}'])) {
							$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{categorydir}'].'&id=$'.$rname['{id}'];
						} elseif (isset($rname['{parentdir}'])) {
							$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{parentdir}'].'&id=$'.$rname['{id}'];
						} else {
							$rule = 'index.php?m=content&c=index&a=show&catid=$'.$rname['{catid}'].'&id=$'.$rname['{id}'];
						}
						if (isset($write[$preg])) {
							$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
						} else {
							$write[$preg] = $cname;
							$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
						}
					}
				}
			} else {
				if ($r['file']=='category') {
					if (strstr($urlrule, '{page}')) {
						$rule = $urlrule;
						$cname = "栏目列表(分页)（{$rule}）";
						list($preg, $rname) = $this->_rule_preg_value($rule);
						if (!$preg || !$rname) {
							$error.= "<p>".$cname."格式不正确</p>";
						} elseif (!isset($rname['{page}'])) {
							$error.= "<p>".$cname."缺少{page}标签</p>";
						} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}'])) {
							$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}标签</p>";
						} else {
							if (isset($rname['{catdir}'])) {
								// 目录格式
								$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{catdir}'].'&page=$'.$rname['{page}'];
							} elseif (isset($rname['{categorydir}'])) {
								// 层次目录格式
								$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{categorydir}'].'&page=$'.$rname['{page}'];
							} elseif (isset($rname['{parentdir}'])) {
								// 层次目录格式
								$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{parentdir}'].'&page=$'.$rname['{page}'];
							} else {
								// catid模式
								$rule = 'index.php?m=content&c=index&a=lists&catid=$'.$rname['{catid}'].'&page=$'.$rname['{page}'];
							}
							if (isset($write[$preg])) {
								$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
							} else {
								$write[$preg] = $cname;
								$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
							}
						}
					} else {
						$rule = $urlrule;
						$cname = "栏目列表（{$rule}）";
						list($preg, $rname) = $this->_rule_preg_value($rule);
						if (!$preg || !$rname) {
							$error.= "<p>".$cname."格式不正确</p>";
						} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}'])) {
							$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}标签</p>";
						} else {
							if (isset($rname['{catdir}'])) {
								// 目录格式
								$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{catdir}'];
							} elseif (isset($rname['{categorydir}'])) {
								// 层次目录格式
								$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{categorydir}'];
							} elseif (isset($rname['{parentdir}'])) {
								// 层次目录格式
								$rule = 'index.php?m=content&c=index&a=lists&catdir=$'.$rname['{parentdir}'];
							} else {
								// catid模式
								$rule = 'index.php?m=content&c=index&a=lists&catid=$'.$rname['{catid}'];
							}
							if (isset($write[$preg])) {
								$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
							} else {
								$write[$preg] = $cname;
								$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
							}
						}
					}
				}
				if ($r['file']=='show') {
					if (strstr($urlrule, '{page}')) {
						$rule = $urlrule;
						$cname = "内容页(分页)（{$rule}）";
						list($preg, $rname) = $this->_rule_preg_value($rule);
						if (!$preg || !$rname) {
							$error.= "<p>".$cname."格式不正确</p>";
						} elseif (!isset($rname['{page}'])) {
							$error.= "<p>".$cname."缺少{page}标签</p>";
						} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}']) && !isset($rname['{id}'])) {
							$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}或{id}标签</p>";
						} else {
							if (isset($rname['{catdir}'])) {
								$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{catdir}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
							} elseif (isset($rname['{categorydir}'])) {
								$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{categorydir}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
							} elseif (isset($rname['{parentdir}'])) {
								$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{parentdir}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
							} else {
								$rule = 'index.php?m=content&c=index&a=show&catid=$'.$rname['{catid}'].'&id=$'.$rname['{id}'].'&page=$'.$rname['{page}'];
							}
							if (isset($write[$preg])) {
								$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
							} else {
								$write[$preg] = $cname;
								$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
							}
						}
					} else {
						$rule = $urlrule;
						$cname = "内容页（{$rule}）";
						list($preg, $rname) = $this->_rule_preg_value($rule);
						if (!$preg || !$rname) {
							$error.= "<p>".$cname."格式不正确</p>";
						} elseif (!isset($rname['{catdir}']) && !isset($rname['{catid}']) && !isset($rname['{categorydir}']) && !isset($rname['{parentdir}']) && !isset($rname['{id}'])) {
							$error.= "<p>".$cname."缺少{catdir}或{catid}或{categorydir}或{parentdir}或{id}标签</p>";
						} else {
							if (isset($rname['{catdir}'])) {
								$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{catdir}'].'&id=$'.$rname['{id}'];
							} elseif (isset($rname['{categorydir}'])) {
								$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{categorydir}'].'&id=$'.$rname['{id}'];
							} elseif (isset($rname['{parentdir}'])) {
								$rule = 'index.php?m=content&c=index&a=show&catdir=$'.$rname['{parentdir}'].'&id=$'.$rname['{id}'];
							} else {
								$rule = 'index.php?m=content&c=index&a=show&catid=$'.$rname['{catid}'].'&id=$'.$rname['{id}'];
							}
							if (isset($write[$preg])) {
								$error.= "<p>".$cname."与".$write[$preg]."规则存在冲突</p>";
							} else {
								$write[$preg] = $cname;
								$code.= '<textarea class="form-control" rows="1">    "'.$preg.'" => "'.$rule.'",  //'.$cname."</textarea>";
							}
						}
					}
				}
			}
		}

		return dr_return_data(1, L('生成成功'), array(
			'code' => nl2br($code),
			'error' => $error,
		));
	}
	// 正则解析
	protected function _rule_preg_value($rule) {

		$rule = trim(trim($rule, '/'));

		if (preg_match_all('/\{(.*)\}/U', $rule, $match)) {

			$value = [];
			foreach ($match[0] as $k => $v) {
				$value[$v] = ($k + 1);
			}

			$preg = preg_replace(
				[
					'#\{id\}#U',
					'#\{catid\}#U',
					'#\{page\}#U',

					'#\{parentdir\}#Ui',
					'#\{categorydir\}#Ui',
					'#\{catdir\}#Ui',

					'#\{tag\}#U',
					'#\{param\}#U',

					'#\{year\}#U',
					'#\{month\}#U',
					'#\{day\}#U',

					'#\{.+}#U',
					'#/#'
				],
				[
					'([0-9]+)',
					'([0-9]+)',
					'([0-9]+)',

					'([\w\/]+)',
					'([A-za-z0-9 \-\_]+)',
					'([A-za-z0-9 \-\_]+)',

					'(.+)',
					'(.+)',

					'([0-9]{4})',
					'([0-9]{2})',
					'([0-9]{2})',

					'(.+)',
					'\/'
				],
				$rule
			);

			// 替换特殊的结果
			$preg = str_replace(
				['(.+))}-', '.html'],
				['(.+)-', '\.html'],
				$preg
			);

			return [$preg, $value];
		}

		return [$rule, []];
	}
}
?>