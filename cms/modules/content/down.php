<?php
defined('IN_CMS') or exit('No permission resources.');
class down {
	private $input,$cache,$db,$category,$category_setting,$category_priv_db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('content_model');
	}

	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$grouplist = getcache('grouplist','member');
		$_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		if(!$grouplist[$_groupid]['allowdownfile']) {
			showmessage(L('您的用户组不允许下载附件')); 
		}
		$a_k = trim($this->input->get('a_k'));
		if(!isset($a_k)) showmessage(L('illegal_parameters'));
		$a_k = sys_auth($a_k, 'DECODE', md5(PC_PATH.'down').SYS_KEY);
		if(empty($a_k)) showmessage(L('illegal_parameters'));
		unset($i,$m,$f);
		$a_k = safe_replace($a_k);
		parse_str($a_k, $a_ks);
		if($a_ks){
			extract($a_ks);
		}
		if(isset($i)) $i = $id = intval($i);
		if(!isset($m)) showmessage(L('illegal_parameters'));
		if(!isset($modelid)||!isset($catid)) showmessage(L('illegal_parameters'));
		if(empty($f)) showmessage(L('url_invalid'));
		$allow_visitor = 1;
		$id = intval($id);
		$modelid = intval($modelid);
		$catid = intval($catid);
		$MODEL = getcache('model','commons');
		$tablename = $this->db->table_name = $this->db->db_tablepre.$MODEL[$modelid]['tablename'];
		$r = $this->db->get_one(array('id'=>$id));	
		$this->db->table_name = $tablename.'_data_'.$r['tableid'];
		$rs = $this->db->get_one(array('id'=>$id));	
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];

		$this->category = dr_cat_value($catid);
		$this->category_setting = string2array($this->category['setting']);
		
		//检查文章会员组权限
		$groupids_view = '';
		if ($rs['groupids_view']) $groupids_view = explode(',', $rs['groupids_view']);
		if($groupids_view && is_array($groupids_view)) {
			if(!$_groupid) {
				$forward = urlencode(dr_now_url());
				showmessage(L('login_website'),APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
			}
			if(!in_array($_groupid,$groupids_view)) showmessage(L('no_priv'));
		} else {
			//根据栏目访问权限判断权限
			$_priv_data = $this->_category_priv($catid);
			if($_priv_data=='-1') {
				$forward = urlencode(dr_now_url());
				showmessage(L('login_website'),APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
			} elseif($_priv_data=='-2') {
				showmessage(L('no_priv'));
			}
		}
		//阅读收费 类型
		$paytype = $rs['paytype'];
		$readpoint = $rs['readpoint'];
		if($readpoint || $this->category_setting['defaultchargepoint']) {
			if(!$readpoint) {
				$readpoint = $this->category_setting['defaultchargepoint'];
				$paytype = $this->category_setting['paytype'];
			}		
			//检查是否支付过
			$allow_visitor = self::_check_payment($catid.'_'.$id,$paytype,$catid);
			if(!$allow_visitor) {
				$http_referer = urlencode(dr_now_url());
				$allow_visitor = sys_auth($catid.'_'.$id.'|'.$readpoint.'|'.$paytype).'&http_referer='.$http_referer;
			} else {
				$allow_visitor = 1;
			}
		}
		if(preg_match('/(php|phtml|php3|php4|jsp|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i',$f) || strpos($f, ":\\")!==FALSE || strpos($f,'..')!==FALSE) showmessage(L('url_error'));
		if(strpos($f, 'http://') !== FALSE || strpos($f, 'ftp://') !== FALSE || strpos($f, '://') === FALSE) {
			$a_k = urlencode(sys_auth("i=$i&d=$d&s=$s&t=".SYS_TIME."&ip=".ip()."&m=".$m."&f=$f&modelid=".$modelid, 'ENCODE', md5(PC_PATH.'down').SYS_KEY));
			$downurl = '?m=content&c=down&a=download&a_k='.$a_k;
		} else {
			$downurl = $f;
		}
		pc_base::load_sys_class('service')->assign($this->category);
		pc_base::load_sys_class('service')->assign([
			'downurl' => $downurl,
			'paytype' => $paytype,
			'readpoint' => $readpoint,
			'allow_visitor' => $allow_visitor,
		]);
		pc_base::load_sys_class('service')->display('content','download');
	}
	
	public function download() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$a_k = trim($this->input->get('a_k'));
		$a_k = sys_auth($a_k, 'DECODE', md5(PC_PATH.'down').SYS_KEY);
		if(empty($a_k)) showmessage(L('illegal_parameters'));
		unset($i,$m,$f,$t,$ip);
		$a_k = safe_replace($a_k);
		parse_str($a_k, $a_ks);
		if($a_ks){
			extract($a_ks);
		}
		if(isset($i)) $downid = intval($i);
		if(!isset($m)) showmessage(L('illegal_parameters'));
		if(!isset($modelid)) showmessage(L('illegal_parameters'));
		if(empty($f)) showmessage(L('url_invalid'));
		if(!$i || $m<0) showmessage(L('illegal_parameters'));
		if(!isset($t)) showmessage(L('illegal_parameters'));
		if(!isset($ip)) showmessage(L('illegal_parameters'));
		$starttime = intval($t);
		if(preg_match('/(php|phtml|php3|php4|jsp|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i',$f) || strpos($f, ":\\")!==FALSE || strpos($f,'..')!==FALSE) showmessage(L('url_error'));
		$fileurl = trim($f);
		if(!$downid || empty($fileurl) || !preg_match("/[0-9]{10}/", $starttime) || !preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $ip) || $ip != ip()) showmessage(L('illegal_parameters'));
		$endtime = SYS_TIME - $starttime;
		if($endtime > 3600) showmessage(L('url_invalid'));
		if($m) $fileurl = trim($s).trim($fileurl);
		//远程文件
		if(strpos($fileurl, ':/') && (strpos($fileurl, SYS_UPLOAD_URL) === false)) { 
			header("Location: $fileurl");
		} else {
			if($d == 0) {
				header("Location: ".$fileurl);
			} else {
				$fileurl = str_replace(array(SYS_UPLOAD_URL,'/'), array(SYS_UPLOAD_PATH,DIRECTORY_SEPARATOR), $fileurl);
				$filename = basename($fileurl);
				//处理中文文件
				if(preg_match("/^([\s\S]*?)([\x81-\xfe][\x40-\xfe])([\s\S]*?)/", $fileurl)) {
					$filename = str_replace(array("%5C", "%2F", "%3A"), array("\\", "/", ":"), urlencode($fileurl));
					$filename = urldecode(basename($filename));
				}
				$ext = fileext($filename);
				$filename = date('Ymd_his').random(3).'.'.$ext;
				$fileurl = str_replace(array('<','>'), '',$fileurl);
				if(preg_match('/(php|phtml|php3|php4|jsp|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i',$fileurl) || strpos($fileurl,'..')!==FALSE) showmessage(L('url_error'));
				if( strpos(str_replace("/",DIRECTORY_SEPARATOR,$fileurl), str_replace("/",DIRECTORY_SEPARATOR,SYS_UPLOAD_PATH)) !== 0){
					showmessage(L('url_error'));
				}
				file_down($fileurl, $filename);
			}
		}
	}
	
	/**
	 * 下载文件
	 */
	public function down() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}

		$_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		$grouplist = getcache('grouplist','member');

		// 判断下载权限
		if (!$grouplist[$_groupid]['allowdownfile']) {
			dr_msg(0, L('您的用户组不允许下载附件'));
		}

		// 读取附件信息
		$id = $this->input->get('id');
		if (is_numeric($id)) {
			$rt = [
				'id' => $id,
				'name' => dr_safe_replace($this->input->get('name')),
			];
		} elseif (strlen((string)$id) == 32) {
			$rt = $this->cache->get_auth_data('down-file-'.$id);
			if (!$rt) {
				dr_msg(0, L('此附件下载链接已经失效'));
			}
		} else {
			$rt = [
				'id' => dr_safe_replace(urldecode($id)),
				'name' => dr_safe_replace($this->input->get('name')),
			];
		}
		
		$id = trim($rt['id']);

		// 下载文件钩子
		pc_base::load_sys_class('hooks')::trigger('down_file', $id);

		// 执行下载
		if (is_numeric($id)) {
			// 表示附件id
			$info = get_attachment($id);
			if (!$info) {
				// 不存在
				dr_msg(0, L('附件['.$id.']不存在'));
			}

			if (is_file($info['file'])) {
				pc_base::load_sys_class('file')->down(
					$info['file'],
					$info['url'],
					(isset($rt['name']) && $rt['name'] ? $rt['name'] : $info['filename']).'.'.$info['fileext']
				);
			} else {
				// 其他附件就转向地址
				$this->_redirect_url($info['url']);
			}
		} else {
			$info = dr_file($id);
			if (!$info) {
				// 不存在
				dr_msg(0, L('附件['.$id.']不存在'));
			}
			$this->_redirect_url($info);
		}

		exit;
	}
	
	/**
	 * 跳转外链提示
	 */
	private function _redirect_url($url) {
		if(intval($this->input->get('siteid'))) {
			$siteid = intval($this->input->get('siteid'));
		} else if(defined('SITE_ID') && SITE_ID!=1) {
			$siteid = SITE_ID;
		} else {
			$siteid = get_siteid();
		}
		define('SITEID', $siteid);
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';

		if (is_file(TPLPATH.$default_style.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'down_file_msg.html')) {
			pc_base::load_sys_class('service')->assign('url', $url);
			pc_base::load_sys_class('service')->display('content','down_file_msg',$default_style);
		}

		dr_msg(1, L('正在为你下载附件'), $url);
	}
	
	/**
	 * 检查支付状态
	 */
	private function _check_payment($flag,$paytype,$catid) {
		$_userid = param::get_cookie('_userid');
		$_username = param::get_cookie('_username');
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->category = dr_cat_value($catid);
		$this->category_setting = string2array($this->category['setting']);
		if(!$_userid) return false;
		pc_base::load_app_class('spend','pay',0);
		$setting = $this->category_setting;
		$repeatchargedays = intval($setting['repeatchargedays']);
		if($repeatchargedays) {
			$fromtime = SYS_TIME - 86400 * $repeatchargedays;
			$r = spend::spend_time($_userid,$fromtime,$flag);
			if($r['id']) return true;
		}
		return false;
	}

	/**
	 * 检查阅读权限
	 *
	 */
	private function _category_priv($catid) {
		$catid = intval($catid);
		if(!$catid) return '-2';
		$_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		$this->category_priv_db = pc_base::load_model('category_priv_model');
		$result = $this->category_priv_db->select(array('catid'=>$catid,'is_admin'=>0,'action'=>'visit'));
		if($result) {
			if(!$_groupid) return '-1';
			foreach($result as $r) {
				if($r['roleid'] == $_groupid) return '1';
			}
			return '-1';
		} else {
			return '1';
		}
	 }
}
?>