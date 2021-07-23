<?php
/**
 * 
 * @param 通用数据导入类
 */

defined('IN_CMS') or exit('No permission resources.');

class do_import {
	private $import_db, $s_db, $queue;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->import_db = pc_base::load_model('import_model');
		
	}
	
	/**
	 * 
	 * 通用数据表对应导入...
	 * @param 配置 $import_info
	 * @param 开始值 $offset
	 * @param 配置importid值 $importid
	 */
	function add_other($import_info, $offset,$importid){
		$data = array();
		$number = $import_info['number'];
		//获取指定的主键字段
		$keyid = $import_info['keyid'];
		if(!$keyid){	
			echo L('no_keyid');
			return false;
		}
		
		//获取要导入到的数据表（通过指定的数据源，和所选的表的所有字段）
		$pdo_select = $import_info['pdo_select'];
		$into_tables = $import_info['into_tables'];
		
		$database = pc_base::load_config('database');
		pc_base::load_sys_class('db_factory');
		$db = db_factory::get_instance($database)->get_database($pdo_select);
		
		//查出来所有表的字段
		$r = array();
		$db_table = explode(',', $into_tables);
  		foreach ($db_table as $key=>$val){
 			$r[$val] = $db->get_fields($val);
 		}
 		
    	//取出有对应关系的字段，以备下面使用
    	foreach ($r as $table){//$r为二维数组，二次foreach 循环
     		foreach ($table as $field=>$val_field){
	 			if($field == $keyid) continue;//如果是主键则继续
				$oldfield = trim($import_info[$field]['field']);
				$func = trim($import_info[$field]['func']);
				$value = trim($import_info[$field]['value']);
				if($value){
					$data[$field] = $value;
				}else{
					if($oldfield && $func){
						$oldfields[$oldfield] = $field;
						$oldfuncs[$oldfield] = $func;
					}elseif($oldfield){
						$oldfields[$oldfield] = $field;
					}
				}	
			}
    	}
   		
 		//处理没有数据模型而使用系统内置数据库处理程序 
 		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $import_info['dbtype'];
		$db_conf['import_array']['hostname']= $import_info[dbhost];
		$db_conf['import_array']['username']= $import_info[dbuser];
		$db_conf['import_array']['password']= $import_info[dbpassword];
		$db_conf['import_array']['database']= $import_info[dbname];
		$db_conf['import_array']['charset']= $import_info[dbcharset];
		//返回一个当前配置所需要的数据库连接，分MYSQL / ACCESS  
		if($import_info['dbtype'] == 'mysql'){
			pc_base::load_sys_class('db_factory');
			$this->thisdb = db_factory::get_instance($db_conf)->get_database('import_array');
			$data_charset = pc_base::load_config('database');
      	   	$this->thisdb->query('SET NAMES '.$data_charset['default']['charset']); 
			$result = $this->filter_fields_new($import_info, $offset, $keyid);//需要返回SQL语句，供下面使用。因为可能涉及多表联查，使用left join来生成SQL
	  		@extract($result); 
	  		$ddata = $data;//暂不知
 	 		$sql = $result['return_sql'];
 	 		
 	 		
	    	$query = $this->thisdb->query($sql);  
			$importnum = $this->thisdb->num_rows($sql);
			/***
			 * 从返回的数据集，重新组合成多维数组，然后再下面用for循环，一条条插入目标数据库。
			 */ 
	   		while ($array = $this->thisdb->fetch_next()){
	 			$data = $ddata;
	     		foreach ($array as $k=>$v){	//对数据集，循环显示
	 				if(isset($oldfields[$k]) && $v) {
						if($oldfuncs[$k]) {//如果有处理函数，则直接用处理函数处理
							$data[$oldfields[$k]] = $oldfuncs[$k]($v);
							if(!$data[$k]) continue;//如果有默认值，
						}else{
							$data[$oldfields[$k]] = $v;//没有处理函数，并且也没有
						}
					}
				}
 	  			//$maxid = max($maxid, $array[$keyid]);
	  			$maxid = max($maxid, $array['max_userid']);
	 			$s[] = $data;
	 		}
 		
		}elseif($import_info['dbtype'] == 'access') {//access处理就不用db_factory，因为可能会出现操作函数覆盖的问题
			pc_base::load_sys_class('access');
  			$this->thisdb = new access();
  			$this->thisdb->connect($import_info[dbhost],$import_info[dbuser],$import_info[dbpassword]);
  			$result = $this->filter_fields_new($import_info, $offset, $keyid);//需要返回SQL语句，供下面使用。因为可能涉及多表联查，使用left join来生成SQL
	  		@extract($result); 
	  		$ddata = $data;//暂不知

	  		if($result['total']==0){
	  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//不需要导入
 				showmessage(L('no_data_needimport'), $forward);
	  		}else {
	  			$sql = $result['return_sql'];
 	 	    	$query = $this->thisdb->query($sql);  
				$importnum = $this->thisdb->num_rows($query);
	 			/*组合本次查询所形成的SQL 相关参数 为数组*/ 
 	 			
				foreach ($query as $array){
						 $data = $ddata;
		 				 foreach ($array as $k=>$v){
						 	if(isset($oldfields[$k]) && $v) {
								if($oldfuncs[$k]) {//如果有处理函数，则直接用处理函数处理
									$data[$oldfields[$k]] = $oldfuncs[$k](iconv('GBK','UTF-8',$v));
									if(!$data[$k]) continue;//如果有默认值，
								}else{
									$data[$oldfields[$k]] = iconv('GBK','UTF-8',$v);//没有处理函数，并且也没有
								}
							}
						 }
   		   				$maxid = max($maxid, $array['max_userid']);
			 			$s[] = $data;
				}
	  		}
	 		
			 
 			
  		}
    	/***
		 * 循环添加到目标数据库
		 */
 		
		foreach ($s as $val){ 
   			/*加载数据模型，把数据插入对应数据表里面*/
			$into_model = $import_info['into_tables'];
  			$tablepre_strlen = strlen($database[$pdo_select]['tablepre']);//取出数据表前缀
  			$table_model = substr($into_model,$tablepre_strlen);//取真正的数据模型名称
   			$this->into_db = pc_base::load_model($table_model.'_model'); 
    		$returnid = $this->into_db->insert($val); 
  		}
   		$finished = 0;
		if($number && ($importnum < $number)){//如果有每次执行多少条，而且当前要插入的条数已经小于设定值，则说明已是最后的执行
			$finished = 1;			
		}
		$import_info['maxid'] = $maxid;
		$import_info['importtime'] = SYS_TIME;
		//更新最近的插入ID，防止重复插入数据
 		$this->setting($import_info);
 		//更新数据库，存入本次执行时间
   		$this->import_db->update(array("lastinputtime"=>SYS_TIME,"last_keyid"=>$maxid),array('id'=>$importid));
 		return $finished.'-'.$total; //$total：为filter_fields()返回的结果解开
 		
	}
	
	
	/**
	 * 
	 * 会员模型数据导入...
	 * @param 配置 $import_info
	 * @param 开始值 $offset
	 * @param 配置importid值 $importid
	 */
	function add_member($import_info, $offset,$importid){
		$data = array();
 		$keyid = $import_info['keyid'];
  		if(!$keyid){	
			echo L('no_keyid');
			return false;
		}
		$import_info['defaultgroupid'] = intval($import_info['defaultgroupid']);
		if(!$import_info['defaultgroupid']){
			echo L('no_default_groupid');
			return false;
		}
		$number = $import_info['number'];
		$data['defaultgroupid'] = $import_info['defaultgroupid'];
		
		//获取选择模型对应的字段
		$fields = getcache('model_field_'.$import_info['modelid'], 'model');
		$memberfields =  getcache('import_fields', 'commons');//此缓存是手动写进里面的。如果用户修改了默认的会员字段，这个文件也要跟着变化。
		$fields = array_merge($memberfields, $fields);
 		
 		foreach ($fields as $field=>$val_field){
			if($field == 'userid') continue;
			$oldfield = trim($import_info[$field]['field']);
			$func = trim($import_info[$field]['func']);
			$value = trim($import_info[$field]['value']);
			if($value){
				$data[$field] = $value;
			}else{
				if($oldfield && $func){
					$oldfields[$oldfield] = $field;
					$oldfuncs[$oldfield] = $func;
				}elseif($oldfield ){
					$oldfields[$oldfield] = $field;
				}
			}	
		}
		
		//处理没有数据模型而使用系统内置数据库处理程序 
 		//临时构建  数据模型配置表单
		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $import_info['dbtype'];
		$db_conf['import_array']['hostname']= $import_info[dbhost];
		$db_conf['import_array']['username']= $import_info[dbuser];
		$db_conf['import_array']['password']= $import_info[dbpassword];
		$db_conf['import_array']['database']= $import_info[dbname];
		$db_conf['import_array']['charset']= $import_info[dbcharset];
		

		//返回一个当前配置所需要的数据库连接  
		pc_base::load_sys_class('db_factory');
		$this->thisdb = db_factory::get_instance($db_conf)->get_database('import_array');
		
		/*组合本次查询所形成的SQL 相关参数 为数组*/
      	$result = $this->filter_fields_new($import_info, $offset, $keyid);//需要返回SQL语句，供下面使用。因为可能涉及多表联查，使用left join来生成SQL
  		@extract($result);
  		
		if($result['total']==0){
  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//不需要导入
 			showmessage(L('no_data_needimport'), $forward);
	  	} 
	  	
  		$sql = $result['return_sql']; 
       	$data_charset = pc_base::load_config('database');
      	$this->thisdb->query('SET NAMES '.$data_charset['default']['charset']); 
		
 		$query = $this->thisdb->query($sql); 
		$importnum = $this->thisdb->num_rows($sql); 
         /***
		 * 从返回的数据集，重新组合成多维数组，然后再下面用for循环，一条条插入目标数据库。
		 */ 
       	while ($r = $this->thisdb->fetch_next()){
  			$data = $ddata;
        		foreach ($r as $k=>$v){	
  				if(isset($oldfields[$k]) && $v) {
					if($oldfuncs[$k]) {
						$data[$oldfields[$k]] = $oldfuncs[$k]($v);
						if(!$data[$k]) continue;
					}else{
						$data[$oldfields[$k]] = $v;
					}
				}
			}
     		$maxid = max($maxid, $r['max_userid']);
 			$s[] = $data;
 		}
     	/***
		 * 循环添加到目标数据库
		 */
		foreach ($s as $val){ 
     		/*在这里对默认用户组进行替换*/
 			//读取配置里关于  组别 替换的设置 
  			$default_groupid = $import_info['defaultgroupid'];
 			$replace_groupids = $import_info['groupids'];
 			if(in_array($val['groupid'], $replace_groupids)){
  				$val['groupid'] = array_search($val['groupid'], $replace_groupids);
 			}else {
 				$val['groupid'] = $default_groupid;
 			}
  			//会员所属模型ID，默认直接是取配置时选择的模型ID值
 			$val['modelid']	= $import_info['modelid'];
  			/*添加用户操作*/
			if(!$member_import){
				$member_import = pc_base::load_app_class('member_import');
			}
 			$memberid = $member_import->add($val,$import_info['membercheck']);//第二个参数为是否要对EMAIL检测。 
  		}
 		
 		$finished = 0;
 		
		if($number && ($importnum < $number)){//如果有每次执行多少条，而且当前要插入的条数已经小于设定值，则说明已是最后的执行
			$finished = 1;			
		}
 		$import_info['maxid'] = $maxid;
		$import_info['importtime'] = SYS_TIME;
		//更新最近的插入ID，防止重复插入数据
 		$this->setting($import_info);
 		//更新数据库，存入本次执行时间
   		$this->import_db->update(array("lastinputtime"=>SYS_TIME,"last_keyid"=>$maxid),array('id'=>$importid));
 		return $finished.'-'.$total; //$total：为filter_fields()返回的结果解开
 		
	}
	
	/**
	 * 
	 * 新闻模型数据导入...
	 * @param 配置 $import_info
	 * @param 开始值 $offset
	 * @param 配置importid值 $importid
	 */
	function add_content($import_info, $offset,$importid){
   		$data = array();
 		$keyid = $import_info['keyid'];
  		if(!$keyid){
  			showmessage(L('no_keyid'),HTTP_REFERER);
 		}
		$import_info['defaultcatid'] = intval($import_info['defaultcatid']);
		if(!$import_info['defaultcatid']){
			echo L('no_default_catid');
			return false;
		}
		$number = $import_info['number'];//每次执行条数
		$data['catid'] = $import_info['defaultcatid'];
 		//获取选择的对应字段
		$fields = getcache('model_field_'.$import_info['modelid'], 'model');
 		foreach ($fields as $field=>$val_field){
			if($field == 'contentid') continue;
			$oldfield = trim($import_info[$field]['field']);
			$func = trim($import_info[$field]['func']);
			$value = trim($import_info[$field]['value']);
			if($value){
				$data[$field] = $value;
			}else{
				if($oldfield && $func){
					$oldfields[$oldfield] = $field;//oldfields为被选中，向里面导入数据的字段
					$oldfuncs[$oldfield] = $func;
				}elseif($oldfield ){
					$oldfields[$oldfield] = $field;
				}
			}	
		}
 		//处理没有数据模型而使用系统内置数据库处理程序 
 		//临时构建  数据模型配置表单
		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $import_info['dbtype'];
		$db_conf['import_array']['hostname']= $import_info[dbhost];
		$db_conf['import_array']['username']= $import_info[dbuser];
		$db_conf['import_array']['password']= $import_info[dbpassword];
		$db_conf['import_array']['database']= $import_info[dbname];
		$db_conf['import_array']['charset']= $import_info[dbcharset];
		
		if($import_info['dbtype'] == 'mysql'){
			
			//返回一个当前配置所需要的数据库连接  
			pc_base::load_sys_class('db_factory');
			$this->thisdb = db_factory::get_instance($db_conf)->get_database('import_array');
			
			/*组合本次查询所形成的SQL 相关参数 为数组*/
	      	$result = $this->filter_fields($import_info, $offset, $keyid);
	  		@extract($result);
	  		
	  		if($result['total']==0){//没有新数据不再往下执行
	  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//不需要导入
	 			showmessage(L('no_data_needimport'), $forward);
	  		} 
	  	
	  		$ddata = $data;//暂不知
	   		$sql = "SELECT $selectfield FROM ".$result['dbtables']." ".$result['condition']." $limit";//此$limit 为$result 解开的变量
   	   		$data_charset = pc_base::load_config('database');
      	   	$this->thisdb->query('SET NAMES '.$data_charset['default']['charset']); 
	 		$query = $this->thisdb->query($sql); 
	 		$importnum = $this->thisdb->num_rows($sql); 
	 		
			/***
			 * 从返回的数据集，重新组合成多维数组，然后再下面用for循环，一条条插入目标数据库。
			 */ 
	   		while ($r = $this->thisdb->fetch_next()){
	 			$data = $ddata;
	     		foreach ($r as $k=>$v){	//对数据集，循环显示
	 				if(isset($oldfields[$k]) && $v) { 
						if($oldfuncs[$k]) {//如果有处理函数，则直接用处理函数处理
							//如果配置是GBK编码,需要转码 
							$data[$oldfields[$k]] = $oldfuncs[$k]($v); 
 							if(!$data[$k]) continue;//如果有默认值，
						}else{
							$data[$oldfields[$k]] = $v;//没有处理函数，并且也没有
						}
					}
				}
	  			$maxid = max($maxid, $r[$keyid]);
	 			$s[] = $data;
	 		}
		}elseif($import_info['dbtype'] == 'access'){
			
			pc_base::load_sys_class('access');
  			$this->thisdb = new access();
  			$this->thisdb->connect($import_info[dbhost],$import_info[dbuser],$import_info[dbpassword]);
  			$result = $this->filter_fields_new($import_info, $offset, $keyid);//需要返回SQL语句，供下面使用。因为可能涉及多表联查，使用left join来生成SQL
	  		@extract($result); 
	  		
	  		if($result['total']==0){//没有新数据不再向下执行
	  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//不需要导入
	 			showmessage(L('no_data_needimport'), $forward);
	  		} 
	  	
	  		$ddata = $data;//暂不知
 	 		$sql = $result['return_sql'];
	    	$query = $this->thisdb->query($sql);  
			$importnum = $this->thisdb->num_rows($query);
			/*组合本次查询所形成的SQL 相关参数 为数组*/ 
 			foreach ($query as $array){
				 $data = $ddata;
 				 foreach ($array as $k=>$v){
				 	if(isset($oldfields[$k]) && $v) {
						if($oldfuncs[$k]) {//如果有处理函数，则直接用处理函数处理
							$data[$oldfields[$k]] = $oldfuncs[$k](iconv('GBK','UTF-8',$v));
							if(!$data[$k]) continue;//如果有默认值，
						}else{
							$data[$oldfields[$k]] = iconv('GBK','UTF-8',$v);//没有处理函数，并且也没有
						}
					}
				 }
  				//$maxid = max($maxid, $array[$keyid]);
				$maxid = max($maxid, $array['max_userid']);
	 			$s[] = $data;
			}
			
		}
 		
    	//循环添加到目标数据库
 		
		foreach ($s as $val){
   			/*在这里对CATID进行替换*/
 			//读取配置里关于catid的设置 
 			$default_catid = $import_info['defaultcatid'];
 			$replace_catids = $import_info['catids'];
  			if(in_array($val['catid'], $replace_catids)){
  				$val['catid'] = array_search($val['catid'], $replace_catids);
 			}else {
  				$val['catid'] = $default_catid;
 			}
 			//echo $val['catid'];
 			/**数据插入目标表中**/
   			$content = pc_base::load_model('content_model');
 			$content->set_model($import_info['modelid']);//设置要导入的模型id
			$contentid = $content->add_content($val, 1);
			
 		}
 		$finished = 0;
		if($number && ($importnum < $number)){//如果有每次执行多少条，而且当前要插入的条数已经小于设定值，则说明已是最后的执行
			$finished = 1;			
		}
		$import_info['maxid'] = $maxid;
		$import_info['importtime'] = SYS_TIME;
		//更新最近的插入ID，防止重复插入数据
 		$this->setting($import_info);
 		//更新数据库，存入本次执行时间
   		$this->import_db->update(array("lastinputtime"=>SYS_TIME,"last_keyid"=>$maxid),array('id'=>$importid));
 		return $finished.'-'.$total; //$total：为filter_fields()返回的结果解开
 		
	}

	/**
	 * 更新用户模型配置文件
	 *
	 * @param array $setting
	 * @param strong $type
	 * @return true
	 */
	function setting($setting){
		if(empty($setting) || !is_array($setting)) return false;
		$setting['edittime'] = TIME;
 		setcache($setting['import_name'], $setting, 'import'); 
 		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $info 配置
	 * @param $offset 开始值
	 * @param $keyid 关键字段
	 * @param $result 要返回的结果  
	 */
	public function filter_fields($info, $offset, $keyid){
		$result = array();
		if(empty($keyid)){
			$keyid = '';
		}
		$result['dbtables'] = trim($info['dbtables']);
   		$firstdot = strpos($result['dbtables'], ',');//返回第一个,号出现的位置值
  		if($firstdot){//如果是多表联查，则会有firstdot 
			$startpos = intval(strpos($result['dbtables'], ' '));
 			$firsttable = trim(substr($result['dbtables'], $startpos, $firstdot-$startpos));
		}
 		$result['maxid'] = intval($info['maxid']);//上次导入结束值
		$result['condition'] = " WHERE ".$info['condition'];//条件
		/*根据配置中最大ID的设置和主键字段，来生成数据获取的条件*/
		if($info['maxid']>0){
			$result['condition'] = $result['condition'].' and '.$firsttable.'.'.$info['keyid'].'>'.$info['maxid'];
		}
 		/*根据每次导入设置，来生成limit语句*/
		$number = $info['number'];//每次导入数据量
		if($number){
			$result['limit'] = " LIMIT $offset,$number";
		}
		//统计要查询的数据总数
 		$sql = "SELECT count(*) AS total FROM ".$result['dbtables']." ".$result['condition'];
 		$result['total'] = $this->thisdb->result($sql);
 		$result['orderby'] = $firsttable ? $firsttable.'.'.$keyid : $keyid;//如多表查询，则取第一表的keyid为order by 主体。
 		$result['selectfield'] = $info['selectfield'] ? $info['selectfield'] : '*';
  		return $result;
 	}
 	
	/**
	 * 
	 * Enter description here ...
	 * @param $info 配置
	 * @param $offset 开始值
	 * @param $keyid 关键字段
	 * @param $result 要返回的结果  
	 */
	public function filter_fields_new($info, $offset, $keyid){
		$result = array();
		if(empty($keyid)){
			$keyid = '';
		}
		$result['dbtables'] = trim($info['dbtables']);
   		$firstdot = strpos($result['dbtables'], ',');//返回第一个,号出现的位置值
  		if($firstdot){//如果是多表联查，则会有firstdot 
			$startpos = intval(strpos($result['dbtables'], ' '));
 			$firsttable = trim(substr($result['dbtables'], $startpos, $firstdot-$startpos));
 		}
 		/*对字符串进行,号分隔为数组，在下面的left join 用到*/
 		$table_array = explode(',',$result['dbtables']);

 		$result['maxid'] = intval($info['maxid']);//上次导入结束值
 		
 		if($result['condition']){
 			$result['condition'] = " WHERE ".$info['condition'];//条件
 		}
 		/*根据配置中最大ID的设置和主键字段，来生成数据获取的条件*/
		if($info['maxid']>0){
			if($result['condition']){
				$result['condition'] = $result['condition'].' and '.$table_array[0].'.'.$info['keyid'].'>'.$info['maxid'];
			}else {
				$result['condition'] = " WHERE ".$table_array[0].'.'.$info['keyid'].'>'.$info['maxid'];
			}
		}
		
 		/*根据每次导入设置，来生成limit语句*/
		$number = $info['number'];//每次导入数据量
		if($number){ 
 				$result['limit'] = " LIMIT $offset,$number"; 
 		}
		$result['orderby'] = $firsttable ? $firsttable.'.'.$keyid : $keyid;//如多表查询，则取第一表的keyid为order by 主体。
 		$result['selectfield'] = $info['selectfield'] ? $info['selectfield'] : '*, '.$table_array[0].'.'.$info['keyid'].' as max_userid';
 		
  		
		//统计要查询的数据总数
 		if(count($table_array)>1){
 			//如多表，用left join 来生成查询语句
 			$left_join ='';
 			foreach ($table_array as $k=>$table){
 				if($k>0){
 					$left_join .=" left join ".$table." on ".$table_array[0].".".$info['keyid']."=".$table.".".$info['keyid']." ";
 				}
  			}
  			$sql = "SELECT count(*) AS total FROM ".$table_array[0].$left_join." ".$result['condition'];
  			$return_sql = "SELECT ".$result['selectfield']." FROM ".$table_array[0].$left_join." ".$result['condition']." order by ".$result['orderby']." asc".$result['limit'];
 		}else{
 			$sql = "SELECT count(*) AS total FROM ".$result['dbtables']." ".$result['condition'];
  		 	$return_sql = "SELECT ".$result['selectfield']." FROM ".$result['dbtables']." ".$result['condition']." order by ".$result['orderby']." asc".$result['limit'];

 		}
 		$result['return_sql'] = $return_sql;
    		if($info['dbtype']=='access'){
   			$total = $this->thisdb->get_one($sql);
 			$result['total'] = $total['total'];
   		}else {
   			$result['total'] = $this->thisdb->result($sql);
   		}
    	return $result;
 	}
}
?>