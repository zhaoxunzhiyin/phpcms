<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_app_func('global','import');//导入程序处理函数
class import extends admin {
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->import = pc_base::load_model('import_model');
  	}
 	
	public function init() {
		//默认调用所有导入规则
		$type = $this->input->get('type');
		if($type!=''){
			$where = array("type"=>$type);
		}
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->import->listinfo($where,$order = 'id DESC',$page, $pages = '9');
		$pages = $this->import->pages; 
		include $this->admin_tpl('import_list');
 	}
 	
 	
 	/**
 	 * 
 	 * 验证配置名称是否已存在 ...
 	 */
	public function check_import_name(){
 		$import_name = $this->input->get('import_name') && trim($this->input->get('import_name')) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('import_name'))) : trim($this->input->get('import_name'))) : exit('0');
 		$importid = $this->input->get('importid');
  		if(!$importid){//没有配置ID，说明是新建，此时不能同名
  			$array = $this->import->get_one(array("import_name"=>$import_name),'import_name');
  			if(!empty($array) && $array['import_name']==$import_name){
  				exit('0');
  			}else {
  				exit('1');
  			}
 		}else{
 			exit('1');
 		}
		
 	}
 	
	//添加数据导入规则
 	public function add() {
 		if($this->input->post('dosubmit')) {
  			if($this->input->post('info')['type']=='content'){
 				$modelid = $this->input->post('info')['contentmodelid'];
 			}elseif ($this->input->post('info')['type']=='member'){
 				$modelid = $this->input->post('info')['membermodelid'];
 			}else {
 				$modelid = 'other';
 			}
 			$url = "?m=import&c=import&a=import_setting&type=".$this->input->post('info')['type']."&modelid=".$modelid."&pc_hash=".$_SESSION['pc_hash'];
 			showmessage('进入下一步',$url,'0');
  		}else {
 			$models = getcache('model','commons');
	 		$members = getcache('member_model','commons');
	  		pc_base::load_sys_class('form', '', 1);
	 		include $this->admin_tpl('import_add_setting');
 		}
 		
	}
	
	//正式保存数据导入规则
 	public function import_setting() {
 		if($this->input->post('dosubmit')) {
  			//写入缓存配置文件
 			$forward = "?m=import&c=import&a=init";
 			$setting = $this->input->post('setting');
  			if(empty($setting['import_name'])){
				showmessage('请输入配置名称');
			}
  			setcache($setting['import_name'], $setting, 'import'); 
   			//写入数据库,分添加/修改配置
   			$into_array = array();
  			$into_array['addtime'] = SYS_TIME;
			$into_array['type'] = $this->input->post('type');
 			$into_array['import_name'] = $setting['import_name'];
 			$into_array['desc'] = $setting['desc'];
 			
 			//获取importid，根据是否有此值，来判断是添加还是修改操作
 			$importid = $this->input->get('importid');
   			if($importid){
   				$this->import->update($into_array,array('id'=>$importid));
   			}else {
   				$importid = $this->import->insert($into_array,true);
    		}
  			showmessage('操作成功', $forward);
   		} else {
  			$show_validator = $show_scroll = $show_header = true;
 			pc_base::load_sys_class('form', '', 0);
  			//获取传递的参数，来决定显示那个模型字段  
  			$type = $this->input->get('type');
 			$modelid = $this->input->get('modelid');
 			
   			//根据是否存在importid 来获取配置信息
   			$importid = $this->input->get('importid');
   			if(!empty($importid)){
   				$info = $this->import->get_one(array('id'=>$importid));
	 			$import_name = $info['import_name'];
	 			$setting = getcache($import_name,'import');
   			}
       		if($type == 'other'){
    			/*如果选择other 类型，则从数据库配置中读取*/
    			$database = pc_base::load_config('database');
	    		foreach($database as $name=>$value) {
					$pdos[$name] = $value['database'].'['.$value['hostname'].']';
				}
				
				/*分为传递和读配置二方面*/
	    		if($this->input->get('pdoname') || $setting['pdo_select']) {
	    			$pdo_name = $this->input->get('pdoname') ? trim($this->input->get('pdoname')) : trim($setting['pdo_select']);
	    			
  					$to_tables = array();
					$db = db_factory::get_instance($database)->get_database($pdo_name);
					$tbl_show = $db->query("SHOW TABLES"); 
    				$to_table = '<select id="selecte_to_tables" onchange="to_tables(this.value)">';
 					while(($rs = $db->fetch_next()) != false) {
 			 			$to_table .= "<option value='".$rs['Tables_in_'.$database[$pdo_name]['database']]."'>".$rs['Tables_in_'.$database[$pdo_name]['database']]."</option>";
 						$r[] = $rs;
 					} 
 					$to_table .= "</select>"; 
					$db->free_result($tbl_show);
					//根据传递的所选表名，显示所选表的所有字段
					if($this->input->get('into_tables') || $setting['into_tables']){
 						$into_tables = $this->input->get('into_tables') ? trim($this->input->get('into_tables')) : trim($setting['into_tables']);
						//分拆多表
						$get_tables = array();
						$db_table = explode(',', $into_tables);
				  		foreach ($db_table as $key=>$val){
				  			if(!empty($val)){
				  				$get_tables[$val] = $db->get_fields($val);
				  			}
  						}
  						//组合数据字段数组
						$get_keywords = '';
						foreach ($get_tables as $key=>$val){
				 			foreach ($val as $v=>$true_key){
								$get_keywords[]= $v;
							}
				  		}
 					}
				}
 				
				
    		}else {
    			//other类型，没有modelid，所以把modelid值的判断，移到这里面
    			if (empty($type) || empty($modelid)){
				showmessage('请选择模型！');
				}
 			
    			/*只有指定模型才用得着获取模型对应字段*/
	    		$fields = getcache('model_field_'.$modelid, 'model');
	    		//构建栏目选择框
	   			if($type == 'content'){
	   				$site = $this->get_siteid();
	   				$category = getcache('category_content_'.$site, 'commons');
	    				foreach ($category as $cat){
						if($modelid == $cat['modelid'] && $cat['arrchildid'] == $cat['catid']){//$cat['arrchildid'] == $cat['catid'] 这个用来确定是终级目录
							$arr_cat[$cat['catid']] = $cat['catname'];
						}
					}
	   			}elseif($type == 'member') {
	   				//获取会员模型默认字段，并于自定义字段，合并为完整数组。
	   				$memberfields =  getcache('import_fields', 'commons');//此缓存是手动写进里面的。如果用户修改了默认的会员字段，这个文件也要跟着变化。
	 				$fields = array_merge($memberfields, $fields);
	 				//获取用户组
	   				$group = getcache('grouplist', 'member');
	   				$new_group = array();
	   				foreach ($group as $mem){
	   					$new_group[$mem['groupid']] = $mem['name'];
	   				}
	    		}
    		}
  			include $this->admin_tpl($type.'_add');
  		}

	}

	/*
	 * 测试数据库是否链接正常
	 */
	public function testdb(){
		//调用转换程序类 import.class.php
  		pc_base::load_sys_class('import_test', '', 0);
  		$import_test = new import_test();
 		$dbtype = $this->input->get('dbtype');
 		$dbhost = $this->input->get('dbhost');
 		$dbuser = $this->input->get('dbuser');
 		$dbpw = $this->input->get('dbpassword');
 		$dbname = $this->input->get('dbname');
   		$r = $import_test->testdb($dbtype, $dbhost, $dbuser, $dbpw, $dbname);
    	if ($r=='OK') {
			echo 'OK';
		}
	}
	
	/*
	 * 获取本系统指定数据源的，所有数据表
	 * */
	public function get_into_tables(){
		$pdo_select = $this->input->get('pdo_select');
   		if (empty($pdo_select)){
			exit();
		}
		
		/*如果选择other 类型，则从数据库配置中读取*/
    	$database = pc_base::load_config('database');
 		$db = db_factory::get_instance($database)->get_database($pdo_select);
		$tbl_show = $db->query("SHOW TABLES"); 
    	$to_table = '<select id="selecte_to_tables" onchange="to_tables(this.value)">';
    	$to_table .= "<option value=''>请选择</option>";
 		while(($rs = $db->fetch_next()) != false) {
  			$to_table .= "<option value='".$rs['Tables_in_'.$database[$pdo_select]['database']]."'>".$rs['Tables_in_'.$database[$pdo_select]['database']]."</option>";
 			$r[] = $rs;
 		} 
 		$to_table .= "</select>";
 		echo $to_table;		 
	}
	
	//获取指定数据库的，所有数据表
	public function get_tables(){
		$dbtype = $this->input->get('dbtype');
 		$dbhost = $this->input->get('dbhost');
 		$dbuser = $this->input->get('dbuser');
 		$dbpassword = $this->input->get('dbpassword');
 		$dbname = $this->input->get('dbname');
 		$dbcharset = $this->input->get('dbcharset');
 		if($dbtype == 'mysql'){
 			if (empty($dbhost) || empty($dbuser) || empty($dbtype) || empty($dbpassword) || empty($dbname) || empty($dbcharset)){
			exit();
			}
 		}
  		
   		$database = '<select id="tables" onchange="in_tables(this.value)">';
   		$database .= "<option value=''>请选择</option>";
  		if($dbtype == 'access'){
  				/*读取access.class.php*/
  				pc_base::load_sys_class('access');
  				$access = new access();
  				$access->connect($dbhost,$dbuser,$dbpassword);
   				$array = $access->select("SELECT name from MSysObjects where type = 1 and flags = 0"); 
    			foreach ($array as $arr){
  					$database .= "<option value='".$arr['name']."'>".$arr['name']."</option>";
   				} 
  		}else{//是mysql数据库
  			//临时构建一个数据模型配置表单
			$db_conf = array();
			$db_conf['import_array'] = array();
			$db_conf['import_array']['type']= $dbtype;
			$db_conf['import_array']['hostname']= $dbhost;
			$db_conf['import_array']['username']= $dbuser;
			$db_conf['import_array']['password']= $dbpassword;
			$db_conf['import_array']['database']= $dbname;
			$db_conf['import_array']['charset']= $dbcharset;
	    	//处理没有数据模型而使用系统内置数据库处理程序 
			pc_base::load_sys_class('db_factory');
			//$database = pc_base::load_config('database');
			$db = db_factory::get_instance($db_conf)->get_database('import_array');
			
  			$query =$db->query("SHOW TABLES");
  			while(($rs = $db->fetch_next()) != false) {
 			$database .= "<option value='".$rs['Tables_in_'.$dbname]."'>".$rs['Tables_in_'.$dbname]."</option>";
			$r[] = $rs;
			}
  		}
  		 
		echo $database."</select>"; 
	}
	
	//获取所选数据表的字段列表
	public function get_fields(){
		//调用转换程序类 import.class.php
		//echo '大家好';exit;
 		$dbtype = $this->input->get('dbtype');
 		$dbhost = $this->input->get('dbhost');
 		$dbuser = $this->input->get('dbuser');
 		$dbpassword = $this->input->get('dbpassword');
 		$dbname = $this->input->get('dbname');
 		$tables = $this->input->get('tables');
		if(empty($dbhost) || empty($dbuser) || empty($dbtype) || empty($dbpassword) || empty($dbname) || empty($tables)){
		exit();
		}
		//临时构建一个数据模型配置表单
		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $dbtype;
		$db_conf['import_array']['hostname']= $dbhost;
		$db_conf['import_array']['username']= $dbuser;
		$db_conf['import_array']['password']= $dbpassword;
		$db_conf['import_array']['database']= $dbname;
		$db_conf['import_array']['charset']= $dbcharset;
    	//处理没有数据模型而使用系统内置数据库处理程序 
		pc_base::load_sys_class('db_factory');
		$database = pc_base::load_config('database');
		$db = db_factory::get_instance($db_conf)->get_database('import_array');
		
		//组合数据字段数组
		$r = array();
		$db_table = explode(',', $tables);
  		foreach ($db_table as $key=>$val){
  			if(!empty($val)){
  				$r[$val] = $db->get_fields($val);
  			}
 		}
  		//组成下拉列表
		$database = '<select onchange="if(this.value!=\'\'){put_fields(this.value)}"><option value="">请选择</option>';
		foreach ($r as $key=>$val){
 			foreach ($val as $v=>$true_key){
				$database .= '<option value="'.$v.'">'.$key.'.'.$v.'</option>';
			}
  		}
 		echo $database."</select>";  
   		exit; 
	} 
	
	
	/**
	 * 
	 * ajax 获取本系统所选数据表，所有字段 ...
	 */
	public function get_keywords(){
 		$into_tables = trim($this->input->get('into_tables'),',');
 		$pdo_select = $this->input->get('pdo_select'); 
 		if(empty($pdo_select) || empty($into_tables)){
		exit();
		}
 		
		//根据传递过来，数据配置，来定义数据连接
		$database = pc_base::load_config('database');
		pc_base::load_sys_class('db_factory');
		$db = db_factory::get_instance($database)->get_database($pdo_select);
		
		//组合数据字段数组
		$r = array();
		$db_table = explode(',', $into_tables);
  		foreach ($db_table as $key=>$val){
  			$val = trim($val,',');
  			$r[$val] = $db->get_fields($val);
 			
		}
   		//组成下拉列表
		//$database = '<select onchange="if(this.value!=\'\'){put_fields(this.value)}"><option value="">请选择</option>';
		$return_str = '';
		foreach ($r as $key=>$val){
 			foreach ($val as $v=>$true_key){
				$return_str .= '<tr height="40">
				<th width="80" align="right">'.$v.':</th>
				<th width="200" align="left" class="list_fields">
				<input name="setting['.$v.'][field]" id="field_'.$v.'" class="input_blur" type="text" value="'.$v.'"><span id="test"></span>
				</th>
				<th width="200" align="left"><input name="setting['.$v.'][value]" class="input_blur" type="text" value='.$v.'></th>
				<th width="200" align="left">
				<input require="false" datatype="ajax" url="?m=import&c=import&a=test_func" msg="" name="setting['.$v.'][func]" type="text" value="'.$v.'" /></th></tr>';
			}
  		}
  		echo $return_str;
	} 
	
 	/*
	 * 配置修改 
	 */
	public function choice() {
		if($this->input->post('dosubmit')){
			$type = $this->input->get('type');
			$importid = $this->input->get('importid');
			if($type=='content'){
				$modelid = $this->input->post('info')['contentmodelid'];
			}elseif ($type=='member'){
				$modelid = $this->input->post('info')['membermodelid'];
			}
			$url = "?m=import&c=import&a=import_setting&type=".$type."&importid=".$importid."&modelid=".$modelid."&pc_hash=".$_SESSION['pc_hash'];
 			showmessage('进入下一步',$url,'0');
		}else {
			$importid = intval($this->input->get('importid'));
			if($importid < 1) return false;
			$array = $this->import->get_one(array("id"=>$importid));
			$type = $array['type'];
			$import_name = $array['import_name'];
	 		//获取文件配置信息
	 		$setting = getcache($import_name,'import');
	 		$now_modelid = $setting['modelid'];
	 		
			$models = getcache('model','commons');
	 		$members = getcache('member_model','commons');
	  		pc_base::load_sys_class('form', '', 1);
	  		include $this->admin_tpl('import_choice');
		}
		
 	} 
	 
	
	/*
	 * 编辑修改信息
	 * */
	public function edit() {
		if($this->input->post('dosubmit')){
			$setting = $this->input->post('setting');
  			if(empty($setting['name'])){
				showmessage('请输入配置名称');
			}
  			setcache($setting['name'], $setting, 'import'); 
   			//写入数据库
   			$importid = $this->input->get('importid');
  			$into_array = array();
  			$into_array['addtime'] = SYS_TIME;
			$into_array['type'] = $this->input->post('type');
 			$into_array['import_name'] = $setting['name'];
			$this->import->update($into_array,array('id'=>$importid));
  			showmessage('修改操作成功');
 		}else{
 			//判断参数正确
			$importid = intval($this->input->get('importid'));
			if($importid < 1) {
				return false;
			}
			$type = $this->input->get('type');
			if($type!='content' && $type!='member' && $type!='other'){
				return false;
			}
			
			$modelid = $this->input->get('modelid');
 			if($type == 'content'){
   				$site = $this->get_siteid();
   				$category = getcache('category_content_'.$site, 'commons');
    				foreach ($category as $cat){
					if($modelid == $cat['modelid'] && $cat['arrchildid'] == $cat['catid']){//$cat['arrchildid'] == $cat['catid'] 这个用来确定是终级目录
						$arr_cat[$cat['catid']] = $cat['catname'];
					}
				}
   			}elseif($type == 'member') {
   				$group = getcache('grouplist', 'member');
   				$new_group = array();
   				foreach ($group as $mem){
   					$new_group[$mem['groupid']] = $mem['name'];
   					
   				}
    				$fields = array_merge($memberfields, $fields);//组合会员组
   			}
 			//查询对应文件名
			$info = $this->import->get_one(array('id'=>$importid));
 			$import_name = $info['import_name'];
 			//获取文件配置信息
 			$setting = getcache($import_name,'import');
 			//根据新选择的模型ID，取出系统对应字段
 			
   			$fields = getcache('model_field_'.$modelid, 'model');
  			pc_base::load_sys_class('form', '', 1);
 			include $this->admin_tpl('import_'.$type.'_edit');
		}

	}
	
	/**
	 * 删除导入配置信息  
	 * @param	intval	$sid	记录ID，递归删除
	 */
	public function delete() {
  		if((!$this->input->get('importid') || empty($this->input->get('importid'))) && (!$this->input->post('importid') || empty($this->input->post('importid')))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('importid'))){
				foreach($this->input->post('importid') as $importid_arr) {
					//删除缓存文件
					$array = $this->import->get_one(array("id"=>$importid_arr));
					$import_name = $array['import_name'];
 					delcache($import_name,'import');
 					//删除数据库信息
					$this->import->delete(array('id'=>$importid_arr));
				}
				showmessage(L('operation_success'),'?m=import&c=import');
			}else{
				$importid = intval($this->input->get('importid'));
				if($importid < 1) return false;
				//删除记录
				$result = $this->import->delete(array('id'=>$importid));
				if($result){
					showmessage(L('operation_success'),'?m=import&c=import');
				}else {
					showmessage(L("operation_failure"),'?m=import&c=import');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	} 
	
	/*检测处理函数是否存在*/
	public function test_func(){
		$value = $this->input->get('value');
    	if(!function_exists($value)) {
			echo  $value.'该函数不存在';
		}else{
			echo '正确';
 		}
	}
	
	
	/*
	 * 执行导入操作 
	 */
	public function do_import() {
		//获取importid type 
		$type = $this->input->get('type');
		$importid = $this->input->get('importid');
		//获取对应配置文件名
		$import_array = $this->import->get_one(array("id"=>$importid));
  		$import_name = $import_array['import_name']; 
 		//获取配置缓存
		$import_info = getcache($import_name,'import');
 	 		
 		if($import_info['expire']) set_time_limit($import_info['expire']);//超时时间设置
		$name = $import_info['import_name'];
	    $number = $import_info['number'];
	    
	    $offset = $this->input->get('offset');
	    $offset = isset($offset) ? intval($offset) : 0 ;
	    
	    //信息的导入分：信息、会员、其它三种类型
		if(!$do_import){
			$do_import = pc_base::load_app_class('do_import');
		}
  		if($type == 'content'){
   			$result = $do_import->add_content($import_info, $offset,$importid);
   			 //通过不同的offset来循环取数据并入库。
 		}elseif($type == 'member'){
 			$result = $do_import->add_member($import_info, $offset,$importid); //通过不同的offset来循环取数据并入库。
  		}elseif($type == 'other'){
			$result = $do_import->add_other($import_info, $offset,$importid); //通过不同的offset来循环取数据并入库。
		}
   		if(!$result){
			showmessage('返回数据有问题，请查看!');
		}
 		//从返回的信息中，分解成数组，来判断是否结束 
 		list($finished, $total) = explode('-', $result);
 		$newoffset = $offset + $number;
 		
   		//跳转说明
 		$start = $this->input->get('start') ? $this->input->get('start') : 0;
		$end_start = $start + $number;
   		$forward = $finished ? "?m=import&c=import&a=init" : "?m=import&c=import&a=do_import&type=".$type."&importid=".$importid."&offset=$offset&start=$end_start&total=$total";//结束跳至管理列表，未结束继续进行
   		showmessage('正在进行数据导入<br>'.$start.' - '.$end_start, $forward);
   	}
   
}
?>