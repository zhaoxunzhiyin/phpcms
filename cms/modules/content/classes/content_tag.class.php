<?php
class content_tag {
	private $input,$db,$position,$keyword_db,$category,$modelid,$hits_db;
	public $tablename;
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		$this->position = pc_base::load_model('position_data_model');
	}
	/**
	 * 初始化模型
	 * @param $catid
	 */
	public function set_modelid($catid) {
		static $CATS;
		$siteids = getcache('category_content','commons');
		if(!$siteids[$catid]) return false;
		$siteid = $siteids[$catid];
		if ($CATS[$siteid]) {
			$this->category = $CATS[$siteid];
		} else {
			$CATS[$siteid] = $this->category = get_category($siteid);
		}
		if($this->category[$catid]['type']!=0) return false;
		$this->modelid = $this->category[$catid]['modelid'];
		$this->db->set_model($this->modelid);
		$this->tablename = $this->db->table_name;
		if(empty($this->category)) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 分页统计
	 * @param $data
	 */
	public function count($data) {
		if($data['action'] == 'lists') {
			$catid = intval($data['catid']);
			$modelid = intval($data['modelid']);
			if(!$this->set_modelid($catid) && $modelid) {
				$this->db->set_model($modelid);
				$this->tablename = $this->db->table_name;
			} elseif(!$this->set_modelid($catid)) {
				return false;
			}
			if(isset($data['where'])) {
				$sql = $data['where'];
			} else {
				$thumb = intval($data['thumb']) ? " AND thumb != ''" : '';
				if($catid && $this->category[$catid]['child']) {
					$catids_str = $this->category[$catid]['arrchildid'];
					$pos = strpos($catids_str,',')+1;
					$catids_str = substr($catids_str, $pos);
					$sql = "status=99 AND catid IN ($catids_str)".$thumb;
				} else {
					$catid && $sql = "status=99 AND catid='$catid'".$thumb;
				}
				$modelid && $sql = "status=99".$thumb;
			}
			if(isset($data['maxsize']) && $data['maxsize'] && (int)$data['maxsize'] > 0) {
				list($start, $pagesize) = explode(",", $data['limit']);
				return min($pagesize * $data['maxsize'], $this->db->count($sql));
			}
			return $this->db->count($sql);
		}
	}
	
	/**
	 * 列表页标签
	 * @param $data
	 */
	public function lists($data) {
		$catid = intval($data['catid']);
		$modelid = intval($data['modelid']);
		if(!$this->set_modelid($catid) && $modelid) {
			$this->db->set_model($modelid);
		} elseif(!$this->set_modelid($catid)) {
			return false;
		}
		if(isset($data['where'])) {
			$sql = $data['where'];
		} else {
			$thumb = intval($data['thumb']) ? " AND thumb != ''" : '';
			if($catid && $this->category[$catid]['child']) {
				$catids_str = $this->category[$catid]['arrchildid'];
				$pos = strpos($catids_str,',')+1;
				$catids_str = substr($catids_str, $pos);
				$sql = "status=99 AND catid IN ($catids_str)".$thumb;
			} else {
				$catid && $sql = "status=99 AND catid='$catid'".$thumb;
			}
			$modelid && $sql = "status=99".$thumb;
		}
		$order = $data['order'];
		
		if (defined('IS_HTML') && IS_HTML) {
			$total = $this->db->count($sql);
			if (isset($data['maxlimit']) && intval($data['maxlimit']) && $total > $data['maxlimit']) {
				$total = $data['maxlimit']; // 最大限制
				list($page, $pagesize) = explode(",", $data['limit']);
				if ($page + $pagesize > $total) {
					log_message('debug', 'maxlimit设置最大显示'.$data['maxlimit'].'条，当前（'.$total.'）已超出');
					return;
				}
			}
		}

		$return = $this->db->select($sql, '*', $data['limit'], $order, '', 'id');

		//调用副表的数据
		if (isset($data['moreinfo']) && intval($data['moreinfo']) == 1) {
			foreach ($return as $k=>$v) {
				if (isset($v['id']) && !empty($v['id'])) {
					$this->db->table_name = $this->db->table_name.'_data_'.$v['tableid'];
					$data_rs = $this->db->get_one(array('id'=>$v['id']));
					if (isset($data_rs)) $return[$k] = array_merge($return[$k], $data_rs);
					if ($catid) {
						$this->set_modelid($catid);
					} else {
						if ($modelid) {
							$this->db->set_model($modelid);
						}
					}
				} else {
					continue;
				}
			}
		}
		return $return;
	}
	
	/**
	 * TAG标签(所有tag列表)
	 * @param $data
	 */
	public function tag($data) {
		$order = isset($data['order']) ? $data['order'] : 'id DESC';
		$limit = isset($data['limit']) ? $data['limit'] : '20';
		$where = isset($data['where']) ? $data['where'] : '';
		$field = isset($data['field']) ? $data['field'] : 'id,siteid,keyword,pinyin,videonum,searchnums';
		$this->keyword_db = pc_base::load_model('keyword_model');
		return $this->keyword_db->select($where, $field, $limit, $order,'','id');
	}	
	
	/**
	 * 内容页TAG标签
	 * @param $data
	 */
	public function centent_tag($data) {
		$id = isset($data['id']) ? intval($data['id']) : 0;
		$modelid = isset($data['modelid']) ? intval($data['modelid']) : 1;
		$siteid = isset($data['siteid']) ? intval($data['siteid']) : 1;
		$limit = isset($data['limit']) ? $data['limit'] : '20';
		$this->keyword_db = pc_base::load_model('keyword_model');
		$keyword_data_db = pc_base::load_model('keyword_data_model');
		
		//获取标签id
		$r = $keyword_data_db->select(array('contentid'=>$id.'-'.$modelid, 'siteid'=>$siteid), 'tagid');
		if ($r) {
			foreach ($r as $v) {
				$tagid .= intval($v['tagid']).',';
			}
		}
		if ($tagid) {
			$tagid = rtrim($tagid, ',');
			$order = $data['order'];
			$sql = " `id` IN ($tagid)";
			return $this->keyword_db->select($sql, '*', $limit, $order,'','id');
		}
	}
	
	/**
	 * 相关文章标签
	 * @param $data
	 */
	public function relation($data) {
		$catid = intval($data['catid']);
		$modelid = intval($data['modelid']);
		if(!$this->set_modelid($catid) && $modelid) {
			$this->db->set_model($modelid);
			$this->tablename = $this->db->table_name;
		} elseif(!$this->set_modelid($catid)) {
			return false;
		}
		$order = $data['order'];
		$sql = "`status`=99";
		$limit = $data['id'] ? $data['limit']+1 : $data['limit'];
		if($data['relation']) {
			$relations = explode('|',trim($data['relation'],'|'));
			$relations = array_diff($relations, array(null));
			$relations = implode(',',$relations);
			$sql = " `id` IN ($relations)";
			$key_array = $this->db->select($sql, '*', $limit, $order,'','id');
		} elseif($data['keywords']) {
			$keywords = str_replace(array('%', "'"), '', $data['keywords']);
			$keywords_arr = explode(',', $keywords);
			$key_array = array();
			$number = 0;
			$i =1;
			$sql .= " AND catid='$catid'";
			foreach ($keywords_arr as $_k) {
				$sql2 = $sql." AND `keywords` LIKE '%$_k%'".(isset($data['id']) && intval($data['id']) ? " AND `id` != '".abs(intval($data['id']))."'" : '');
				$r = $this->db->select($sql2, '*', $limit, '','','id');
				$number += dr_count($r);
				foreach ($r as $id=>$v) {
					if($i<= $data['limit'] && !in_array($id, $key_array)) $key_array[$id] = $v;
					$i++;
				}
				if($data['limit']<$number) break;
			}
		}
		if($data['id']) unset($key_array[$data['id']]);
		return $key_array;
	}
	
	/**
	 * 排行榜标签
	 * @param $data
	 */
	public function hits($data) {
		$catid = intval($data['catid']);
		$modelid = intval($data['modelid']);
		if(!$this->set_modelid($catid) && $modelid) {
			$this->db->set_model($modelid);
			$this->tablename = $this->db->table_name;
		} elseif(!$this->set_modelid($catid)) {
			return false;
		}

		$this->hits_db = pc_base::load_model('hits_model');
		$sql = $desc = $ids = '';
		$array = $ids_array = array();
		$order = $data['order'];
		$hitsid = 'c-'.($modelid ? $modelid : $this->modelid).'-%';
		$sql = "hitsid LIKE '$hitsid'";
		if(isset($data['day'])) {
			$updatetime = SYS_TIME-intval($data['day'])*86400;
			$sql .= " AND updatetime>'$updatetime'";
		}
		if($catid && $this->category[$catid]['child']) {
			$catids_str = $this->category[$catid]['arrchildid'];
			$pos = strpos($catids_str,',')+1;
			$catids_str = substr($catids_str, $pos);
			$sql .= " AND catid IN ($catids_str)";
		} else {
			$catid && $sql .= " AND catid='$catid'";
		}
		$hits = array();
		$result = $this->hits_db->select($sql, '*', $data['limit'], $order);
		foreach ($result as $r) {
			$pos = strpos($r['hitsid'],'-',2) + 1;
			$ids_array[] = $id = substr($r['hitsid'],$pos);
			$hits[$id] = $r;
		}
		$ids = implode(',', $ids_array);
		if($ids) {
			$sql = "status=99 AND id IN ($ids)";
		} else {
			$sql = '';
		}
		$this->db->table_name = $this->tablename;
		$result = $this->db->select($sql, '*', $data['limit'],'','','id');
		foreach ($ids_array as $id) {
			if($result[$id]['title']!='') {
				$array[$id] = $result[$id];
				$array[$id] = array_merge($array[$id], $hits[$id]);
			}
		}
		return $array;
	}
	/**
	 * 栏目标签
	 * @param $data
	 */
	public function category($data) {
		$data['catid'] = intval($data['catid']);
		$array = array();
		$siteid = $data['siteid'] && intval($data['siteid']) ? intval($data['siteid']) : ($data['catid'] ? dr_cat_value($data['catid'], 'siteid') : get_siteid());
		$categorys = get_category($siteid);
		$i = 1;
		if(!is_array($categorys)) $categorys= array();
		foreach ($categorys as $catid=>$cat) {
			if($i>$data['limit']) break;
			if((!dr_cat_value($catid, 'ismenu')) || ($siteid && $cat['siteid']!=$siteid)) continue;
			if (strpos($cat['url'], '://') === false) {
				$cat['url'] = substr(dr_site_info('domain', $siteid),0,-1).$cat['url'];
			}
			if($cat['parentid']==$data['catid']) {
				$array[$catid] = $cat;
				$i++;
			}
		}
		if ($data['order']) {
			$arr = explode(',', $data['order']);
			foreach ($arr as $t) {
				$a = explode(' ', $t);
				$b = strtolower(end($a));
				if (in_array($b, ['desc', 'asc', 'instr'])) {
					$a = str_ireplace(' '.$b, '', $t);
				} else {
					$a = $t;
					$b = 'desc';
				}
				if ($b == 'instr') {

				} else {
					$array = dr_array_sort($array, $a, $b);
				}
			}
		}
		if(isset($data['maxlimit'])){
			list($a, $b) = explode(',', $data['maxlimit']);
			$array = array_slice($array, $a, $b);
		}
		return $array;
	}
	
	/**
	 * 推荐位
	 * @param $data
	 */
	public function position($data) {
		$sql = '';
		$array = array();
		$posid = intval($data['posid']);
		$order = $data['order'];
		$thumb = (empty($data['thumb']) || intval($data['thumb']) == 0) ? 0 : 1;
		$siteid = isset($data['siteid']) ? intval($data['siteid']) : ($GLOBALS['siteid'] ? intval($GLOBALS['siteid']) : 0);
		$catid = (empty($data['catid']) || $data['catid'] == 0) ? '' : intval($data['catid']);
		if($catid) {
			$siteids = getcache('category_content','commons');
			if(!$siteids[$catid]) return false;
			$siteid = $siteids[$catid];
			$this->category = get_category($siteid);
		}
		if($catid && $this->category[$catid]['child']) {
			$catids_str = $this->category[$catid]['arrchildid'];
			$pos = strpos($catids_str,',')+1;
			$catids_str = substr($catids_str, $pos);
			$sql = "`catid` IN ($catids_str) AND ";
		} elseif($catid && !$this->category[$catid]['child']) {
				$sql = "`catid` = '$catid' AND ";
		}
		if($thumb) $sql .= "`thumb` = '1' AND ";
		if($siteid) $sql .= "`siteid` = '".$siteid."' AND ";
		if(isset($data['where'])) $sql .= $data['where'].' AND ';
		if(isset($data['expiration']) && $data['expiration']==1) $sql .= '(`expiration` >= \''.SYS_TIME.'\' OR `expiration` = \'0\' ) AND ';
		$sql .= "`posid` = '$posid'";
		$pos_arr = $this->position->select($sql, '*', $data['limit'],$order);
		if(!empty($pos_arr)) {
			foreach ($pos_arr as $info) {
				$key = $info['catid'].'-'.$info['id'];
				$array[$key] = string2array($info['data']);
				$array[$key]['url'] = dr_go($info['catid'],$info['id']);
				$array[$key]['id'] = $info['id'];
				$array[$key]['catid'] = $info['catid'];
				$array[$key]['listorder'] = $info['listorder'];
			}
		}
		return $array;
	}
	/**
	 * 可视化标签
	 */
	public function pc_tag() {
		$positionlist = getcache('position','commons');
		$sites = pc_base::load_app_class('sites','admin');
		$sitelist = $sites->pc_tag_list();
		
		foreach ($positionlist as $_v) if($_v['siteid'] == get_siteid() || $_v['siteid'] == 0) $poslist[$_v['posid']] = $_v['name'];
		return array(
			'action'=>array('lists'=>L('list','', 'content'),'tag'=>L('标签','', 'content'),'centent_tag'=>L('内容标签','', 'content'),'position'=>L('position','', 'content'), 'category'=>L('subcat', '', 'content'), 'relation'=>L('related_articles', '', 'content'), 'hits'=>L('top', '', 'content')),
			'lists'=>array(
				'catid'=>array('name'=>L('catid', '', 'content'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>1)),
				'order'=>array('name'=>L('sort', '', 'content'), 'htmltype'=>'select','data'=>array('id DESC'=>L('id_desc', '', 'content'), 'updatetime DESC'=>L('updatetime_desc', '', 'content'), 'listorder ASC'=>L('listorder_asc', '', 'content'))),
				'thumb'=>array('name'=>L('thumb', '', 'content'), 'htmltype'=>'radio','data'=>array('0'=>L('all_list', '', 'content'), '1'=>L('thumb_list', '', 'content'))),
				'moreinfo'=>array('name'=>L('moreinfo', '', 'content'), 'htmltype'=>'radio', 'data'=>array('1'=>L('yes'), '0'=>L('no')))
			),
			'tag'=>array(
				'field'=>array('name'=>L('字段', '', 'content'),'htmltype'=>'input_select','validator'=>array('min'=>1)),
				'order'=>array('name'=>L('sort', '', 'content'), 'htmltype'=>'select','data'=>array('videonum DESC'=>L('listorder_desc', '', 'content'),'videonum ASC'=>L('listorder_asc', '', 'content'),'id DESC'=>L('id_desc', '', 'content'))),
			),
			'centent_tag'=>array(
				'modelid'=>array('name'=>L('模型id', '', 'content'),'htmltype'=>'input_select','validator'=>array('min'=>1)),
				'siteid'=>array('name'=>L('站点id', '', 'content'),'htmltype'=>'input_select','validator'=>array('min'=>1)),
				'id'=>array('name'=>L('内容id', '', 'content'),'htmltype'=>'input_select','validator'=>array('min'=>1)),		
				'order'=>array('name'=>L('sort', '', 'content'), 'htmltype'=>'select','data'=>array('videonum DESC'=>L('listorder_desc', '', 'content'),'videonum ASC'=>L('listorder_asc', '', 'content'),'id DESC'=>L('id_desc', '', 'content'))),
			),
			'position'=>array(
				'posid'=>array('name'=>L('posid', '', 'content'),'htmltype'=>'input_select','data'=>$poslist,'validator'=>array('min'=>1)),
				'catid'=>array('name'=>L('catid', '', 'content'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>0)),
				'thumb'=>array('name'=>L('thumb', '', 'content'), 'htmltype'=>'radio','data'=>array('0'=>L('all_list', '', 'content'), '1'=>L('thumb_list', '', 'content'))),			
				'order'=>array('name'=>L('sort', '', 'content'), 'htmltype'=>'select','data'=>array('listorder DESC'=>L('listorder_desc', '', 'content'),'listorder ASC'=>L('listorder_asc', '', 'content'),'id DESC'=>L('id_desc', '', 'content'))),
			),
			'category'=>array(
				'siteid'=>array('name'=>L('siteid'), 'htmltype'=>'input_select', 'data'=>$sitelist),
				'catid'=>array('name'=>L('catid', '', 'content'), 'htmltype'=>'input_select_category', 'data'=>array('type'=>0))
			),
			'relation'=>array(
				'catid'=>array('name'=>L('catid', '', 'content'), 'htmltype'=>'input_select_category', 'data'=>array('type'=>0), 'validator'=>array('min'=>1)),
				'order'=>array('name'=>L('sort', '', 'content'), 'htmltype'=>'select','data'=>array('id DESC'=>L('id_desc', '', 'content'), 'updatetime DESC'=>L('updatetime_desc', '', 'content'), 'listorder ASC'=>L('listorder_asc', '', 'content'))),
				'relation'=>array('name'=>L('relevant_articles_id', '', 'content'), 'htmltype'=>'input'),
				'keywords'=>array('name'=>L('key_word', '', 'content'), 'htmltype'=>'input')
			),
			'hits'=>array(
				'catid'=>array('name'=>L('catid', '', 'content'), 'htmltype'=>'input_select_category', 'data'=>array('type'=>0), 'validator'=>array('min'=>1)),
				'day'=>array('name'=>L('day_select', '', 'content'), 'htmltype'=>'input', 'data'=>array('type'=>0)),
			),
				
		);
	}
}