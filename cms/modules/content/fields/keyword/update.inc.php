	function keyword ($field, $value) {
		//获取post过来的关键字，关键字用空格或者‘,’分割的
		$siteid = get_siteid();
		$data = array();
		$data = preg_split("/[ ,]+/", $value);
		//加载关键字的数据模型
		$keyword_db = pc_base::load_model('keyword_model');
		$keyword_data_db = pc_base::load_model('keyword_data_model');
		pc_base::load_sys_func('iconv');
		if (is_array($data) && !empty($data)) {
			foreach ($data as $v) {
				$v = defined('IN_ADMIN') ? $v : safe_replace(addslashes($v));
				$v = str_replace(array('//','#','.'),' ',$v);
				if ($v) {
					if (!$r = $keyword_db->get_one(array('keyword'=>$v, 'siteid'=>$siteid))) {
						$letters = gbk_to_pinyin($v);
						$letter = strtolower(implode('', $letters));
						$tagid = $keyword_db->insert(array('keyword'=>$v, 'siteid'=>$siteid, 'pinyin'=>$letter, 'videonum'=>1), true);
					} else {
						$tagid = $r['id'];
					}
					$contentid = $this->id.'-'.$this->modelid;
					if (!$keyword_data_db->get_one(array('tagid'=>$tagid, 'siteid'=>$siteid, 'contentid'=>$contentid))) {
						$keyword_db->update(array('videonum'=>'+=1'), array('id'=>$r['id']));
						$keyword_data_db->insert(array('tagid'=>$tagid, 'siteid'=>$siteid, 'contentid'=>$contentid));
					}
				}
				unset($contentid, $tagid, $letters);
			}
		}
		$keyword_data_arr = $keyword_data_db->select(array('siteid'=>$siteid,'contentid'=>$this->id.'-'.$this->modelid));
		if($keyword_data_arr){
			foreach ($keyword_data_arr as $val){
				$keyword_arr = $keyword_db->get_one(array('siteid'=>$siteid,'id'=>$val['tagid']));
				if (!in_array($keyword_arr['keyword'], $data)) {
					$keyword_db->update(array('videonum'=>'-=1'),array('siteid'=>$siteid,'id'=>$keyword_arr['id']));
					$keyword_data_db->delete(array('siteid'=>$siteid,'tagid'=>$keyword_arr['id'],'contentid'=>$this->id.'-'.$this->modelid));
				}
			}
			$keyword_db->delete(array('videonum'=>'0'));
		}
		return $value;
	}
