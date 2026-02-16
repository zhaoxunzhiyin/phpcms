<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class attachment_model extends model {
	private $att_index_db;
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'attachment';
		parent::__construct();
	}
	
	public function api_add($uploadedfile) {
		$uploadfield = array();
		$uploadfield = $uploadedfile;
		unset($uploadfield['fn']);
		$this->insert($uploadfield);
		$aid = $this->insert_id();
		$uploadedfile['aid'] = $aid;
		return $aid;
	}
	/**
	 * 附件更新接口.
	 * @param string $content 可传入空，html，数组形式url，url地址，传入空时，以cookie方式记录。
	 * @param string 传入附件关系表中的组装id
	 * @isurl intval 为本地地址时设为1,以cookie形式管理时设置为2
	 */
	public function api_update($content, $keyid, $isurl = 0) {
		if(!SYS_ATTACHMENT_STAT) return false;
		$keyid = trim($keyid);
		$isurl = intval($isurl);
		if($isurl==2 || empty($content)) {
			$this->api_update_cookie($keyid);
		} else {
			$att_index_db = pc_base::load_model('attachment_index_model');
			if(strpos(SYS_UPLOAD_URL,'://')!==false) {
				$pos = strpos(SYS_UPLOAD_URL,"/",8);
				$domain = substr(SYS_UPLOAD_URL,0,$pos).'/';
				$dir_name = substr(SYS_UPLOAD_URL,$pos+1);
			}
			if($isurl == 0) {
				$pattern = '/(href|src)=\"(.*)\"/isU';
				preg_match_all($pattern,$content,$matches);
				if(is_array($matches) && !empty($matches)) {
					$att_arr = array_unique($matches[2]);
					foreach ($att_arr as $_k=>$_v) $att_arrs[$_k] = md5(str_replace(array($domain,$dir_name), '', $_v));
				}
			} elseif ($isurl == 1) {
				if(is_array($content)) {
					$att_arr = array_unique($content);
					foreach ($att_arr as $_k=>$_v) {
						if (is_numeric($_v)) {
							$_v = dr_get_file($_v);
						}
						$att_arrs[$_k] = md5(str_replace(array($domain,$dir_name), '', $_v));
					}
				} else {
					if (is_numeric($content)) {
						$content = dr_get_file($content);
					}
					$att_arrs[] = md5(str_replace(array($domain,$dir_name), '', $content));
				}
			}
			$att_index_db->delete(array('keyid'=>$keyid));	
			if(is_array($att_arrs) && !empty($att_arrs)) {
				foreach ($att_arrs as $r) {
					$infos = $this->get_one(array('authcode'=>$r),'aid');
					if($infos){
						$this->update(array('status'=>1),array('aid'=>$infos['aid']));
						$att_index_db->insert(array('keyid'=>$keyid,'aid'=>$infos['aid']));
					}
				}
			}
		}
		$cache = pc_base::load_sys_class('cache');
		$cache->clear('att_json');
		return true;
	}
	/*
	 * cookie 方式关联附件
	 */
	private function api_update_cookie($keyid) {
		if(!SYS_ATTACHMENT_STAT) return false;
		$cache = pc_base::load_sys_class('cache');
		$att_index_db = pc_base::load_model('attachment_index_model');
		$att_json = $cache->get_data('att_json');
		if($att_json) {
			$att_cookie_arr = explode('||', $att_json);
			$att_cookie_arr = array_unique($att_cookie_arr);
		} else {
			return false;
		}
		foreach ($att_cookie_arr as $_att_c) $att[] = json_decode($_att_c,true);
		foreach ($att as $_v) {
			$this->update(array('status'=>1),array('aid'=>$_v['aid']));
			$att_index_db->insert(array('keyid'=>$keyid,'aid'=>$_v['aid']));
		}		
	}
	/*
	 * 附件删除接口
	 * @param string 传入附件关系表中的组装id
	 */
	public function api_delete($keyid) {
		if(!SYS_ATTACHMENT_STAT || !SYS_ATTACHMENT_DEL) return false;
		pc_base::load_sys_class('upload');
		$upload = new upload();
		$keyid = trim($keyid);
		if($keyid=='') return false;
		$att_index_db = pc_base::load_model('attachment_index_model');
		$info = $att_index_db->select(array('keyid'=>$keyid),'aid');
		if($info) {
			foreach ($info as $_v) {
				$upload->_delete_file($_v);
			}
			return true;
		} else {
			return false;
		}		
	}
}
?>