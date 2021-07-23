<?php
defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('global');
class search {
	private $db;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
	}
	/**
	 * 按照模型搜索
	 */
	public function init() {
		$grouplist = getcache('grouplist','member');
		$_groupid = param::get_cookie('_groupid');
		if(!$_groupid) $_groupid = 8;
		if(!$grouplist[$_groupid]['allowsearch']) {
			if ($_groupid==8) showmessage(L('guest_not_allowsearch')); 
			else showmessage(L('group_not_allowsearch'));
		}

		if(!$this->input->get('catid')) showmessage(L('missing_part_parameters'));
		$catid = intval($this->input->get('catid'));
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->categorys = getcache('category_content_'.$siteid,'commons');
		if(!isset($this->categorys[$catid])) showmessage(L('missing_part_parameters'));
		$info = $this->input->get('info');
		if(isset($info['catid']) && $info['catid']) {
			$catid = intval($info['catid']);
		} else {
			$info['catid'] = 0;
		}
		$modelid = $this->categorys[$catid]['modelid'];
		$modelid = intval($modelid);
		if(!$modelid) showmessage(L('illegal_parameters'));
		//搜索间隔
		$minrefreshtime = getcache('common','commons');
		$minrefreshtime = intval($minrefreshtime['minrefreshtime']);
		$minrefreshtime = $minrefreshtime ? $minrefreshtime : 5;
		if(param::get_cookie('search_cookie') && param::get_cookie('search_cookie')>SYS_TIME-2) {
			showmessage(L('search_minrefreshtime',array('min'=>$minrefreshtime)),'index.php?m=content&c=search&catid='.$catid,$minrefreshtime*1280);
		} else {
			param::set_cookie('search_cookie',SYS_TIME+2);
		}
		//搜索间隔
		
		$CATEGORYS = $this->categorys;
		//产生表单
		pc_base::load_sys_class('form','',0);
		$fields = getcache('model_field_'.$modelid,'model');
		$forminfos = array();
		foreach ($fields as $field=>$r) {
			if($r['issearch']) {
				if($r['formtype']=='catid') {
					$r['form'] = form::select_category('',$catid,'name="info[catid]"',L('please_select_category'),$modelid,0,1);
				} elseif($r['formtype']=='number') {
					$r['form'] = "<input type='text' name='{$field}_start' id='{$field}_start' value='' size=5 class='input-text'/> - <input type='text' name='{$field}_end' id='{$field}_start' value='' size=5 class='input-text'/>";
				} elseif($r['formtype']=='datetime') {
					$r['form'] = form::date("info[$field]");
				} elseif($r['formtype']=='box') {
					$options = explode("\n",$r['options']);
					$option = array();
					foreach($options as $_k) {
						$v = explode("|",trim($_k));
						$option[$v[1]] = $v[0];
					}
					switch($r['boxtype']) {
						case 'radio':
							$string = form::radio($option,$value,"name='info[$field]' id='$field'");
						break;
			
						case 'checkbox':
							$string = form::radio($option,$value,"name='info[$field]' id='$field'");
						break;
			
						case 'select':
							$string = form::select($option,$value,"name='info[$field]' id='$field'");
						break;
			
						case 'multiple':
							$string = form::select($option,$value,"name='info[$field]' id='$field'");
						break;
					}
					$r['form'] = $string;
				} elseif($r['formtype']=='typeid') {
					$types = getcache('type_content','commons');
					$types_array = array(L('no_limit'));
					foreach ($types as $_k=>$_v) {
						if($modelid == $_v['modelid']) $types_array[$_k] = $_v['name'];
					}
					$r['form'] = form::select($types_array,0,"name='info[$field]' id='$field'");
				} elseif($r['formtype']=='linkage') {
					$setting = string2array($r['setting']);
					if(isset($info[$field]) && $info[$field]) {
						$value = $info[$field];
					}
					$r['form'] = menu_linkage($setting['linkageid'],$field,$value);
				} elseif(in_array($r['formtype'], array('text','keyword','textarea','editor','title','author','omnipotent'))) {
					if(isset($info[$field]) && $info[$field]) {
						$value = safe_replace($info[$field]);
					}
					$r['form'] = "<input type='text' name='info[$field]' id='$field' value='".$value."' class='input-text search-text'/>";
				} else {
					continue;
				}
				$forminfos[$field] = $r;
			}
		}
		//-----------
		if($this->input->get('dosubmit')) {
			$siteid = $this->categorys[$catid]['siteid'];
			$siteurl = siteurl($siteid);
			$this->db->set_model($modelid);
			$tablename = $this->db->table_name;
			
			$page = max(intval($this->input->get('page')), 1);
			$sql  = "SELECT * FROM `{$tablename}` a,`{$tablename}_data` b WHERE a.id=b.id AND a.status=99";
			$sql_count  = "SELECT COUNT(*) AS num FROM `{$tablename}` a,`{$tablename}_data` b WHERE a.id=b.id AND a.status=99";
			//构造搜索SQL
			$where = '';
			foreach ($fields as $field=>$r) {
				if($r['issearch']) {
					$table_nickname = $r['issystem'] ? 'a' : 'b';
					if($r['formtype']=='catid') {
						if($info['catid']) $where .= " AND {$table_nickname}.catid='$catid'";
					} elseif($r['formtype']=='number') {
						$start = "{$field}_start";
						$end = "{$field}_end";
						if($this->input->get($start)) {
							$start = intval($this->input->get($start));
							$where .= " AND {$table_nickname}.{$field}>'$start'";
						}
						if($this->input->get($end)) {
							$end = intval($this->input->get($end));
							$where .= " AND {$table_nickname}.{$field}<'$end'";
						}
					} elseif($r['formtype']=='datetime') {
						if($info[$field]) {
							$start = strtotime($info[$field]);
							if($start) $where .= " AND {$table_nickname}.{$field}>'$start'";
						}
					} elseif($r['formtype']=='box') {
						if($info[$field]) {
							$field_value = safe_replace($info[$field]);
							switch($r['boxtype']) {
								case 'radio':
									$where .= " AND {$table_nickname}.`$field`='$field_value'";
								break;
					
								case 'checkbox':
									$where .= " AND {$table_nickname}.`$field` LIKE '%,$field_value,%'";
								break;
					
								case 'select':
									$where .= " AND {$table_nickname}.`$field`='$field_value'";
								break;
					
								case 'multiple':
									$where .= " AND {$table_nickname}.`$field` LIKE '%,$field_value,%'";
								break;
							}
						}
					} elseif($r['formtype']=='typeid') {
						if($info[$field]) {
							$typeid = intval($info[$field]);
							$where .= " AND {$table_nickname}.`$field`='$typeid'";
						}
					} elseif($r['formtype']=='linkage') {
						if($info[$field]) {
							$linkage = intval($info[$field]);
							$where .= " AND {$table_nickname}.`$field`='$linkage'";
						}
					} elseif(in_array($r['formtype'], array('text','keyword','textarea','editor','title','author','omnipotent'))) {
						if($info[$field]) {
							$keywords = safe_replace($info[$field]);
							$where .= " AND {$table_nickname}.`$field` LIKE '%$keywords%'";
						}
					} else {
						continue;
					}
				}
			}
			//-----------
			if($where=='') showmessage(L('please_enter_content_to_search'));
			$pagesize = 20;
			$offset = intval($pagesize*($page-1));
			$sql_count .= $where;
			$this->db->query($sql_count);
			$total = $this->db->fetch_array();
			$total = $total[0]['num'];
			if($total!=0) {
				$sql .= $where;
				$order = '';
				$order = $this->input->get('orderby')=='a.id DESC' ? 'a.id DESC' : 'a.id ASC';
				$sql .= ' ORDER BY '.$order;
				$sql .= " LIMIT $offset,$pagesize";
				$this->db->query($sql);
				$datas = $this->db->fetch_array();
				$pages = pages($total, $page, $pagesize);
			} else {
				$datas = array();
				$pages = '';
			}
		}
		define('SITEID', $siteid);
		$SEO = seo($siteid, $catid, $keywords);
		$sitelist  = getcache('sitelist','commons');
		$default_style = $sitelist[$siteid]['default_style'];
		if(!$default_style) $default_style = 'default';
		define('ISMOBILE', 1);
		define('IS_HTML', 0);
		include template('mobile','search',$default_style);
	}
}
?>