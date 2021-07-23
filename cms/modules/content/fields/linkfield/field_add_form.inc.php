<?php
pc_base::load_sys_class('db_factory');
$database = pc_base::load_config('database');
$pdo_name = 'default';
$db = db_factory::get_instance($database)->get_database($pdo_name);
$tbl_show = $db->query("SHOW TABLE STATUS FROM `".$database[$pdo_name]['database']."`");
$web_tables = array();
$web_tables_info = array();
$pre = $database[$pdo_name]['tablepre'];
//排除系统关键信息数据表
$sys_tables = array($pre.'admin', $pre.'admin_panel', $pre.'admin_role', $pre.'admin_role_priv', $pre.'block', $pre.'block_history', $pre.'cache', $pre.'dbsource', $pre.'log', $pre.'urlrule', $pre.'session', $pre.'sso_admin', $pre.'sso_applications', $pre.'sso_members', $pre.'sso_messagequeue', $pre.'sso_session', $pre.'sso_settings', $pre.'site', $pre.'queue', $pre.'release_point');
while(($rs = $db->fetch_next()) != false) {
	if(strpos($rs['Name'], $pre) === 0 && !in_array($rs['Name'], $sys_tables) && !preg_match('/'.$pre.'poster_(\d){6}/i',$rs['Name'])) {
		$web_tables[] = $rs['Name'];
	}
}
foreach($web_tables as $key => $table) {
	$data =$db->get_fields($table);
	$web_tables_info[$table] = array_keys($data);
}
$json_web_tables_info = json_encode($web_tables_info);
$db->free_result($tbl_show);
?>
<table cellpadding="2" cellspacing="1" onclick="javascript:$('#minlength').val(0);$('#maxlength').val(255);">
	<tr>
		<td>搜索类型</td>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[link_type]" type="radio" value="1" checked /> 下拉选择框 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[link_type]" type="radio" value="0" /> 搜索选择 <span></span></label>
        </div></td>
	</tr>
	<tr>
		<td>关联表名</td>
		<td>
			<?php if(is_array($web_tables)){?>
				<select name="setting[table_name]" id="st_name">
					<?php
					foreach($web_tables as $key => $v){
					?>
						<option value='<?php echo $v?>'><?php echo $v?></option>
					<?php } ?>
				</select>
			<?php }?>

			<script type="text/javascript">
				<!--
				var json_web_tables_info =eval(<?php echo $json_web_tables_info?>);
				$(document).ready(function() {

					updatemenu($('#st_name').val());
					$("#st_name").change(function() {
						updatemenu($(this).trigger("select").val());
						$('#select_title').val('');
					});

					function updatemenu(table_name) {
						var data = '';
						$.each(json_web_tables_info, function(i,n){
							if(i == table_name){
								data = n;
								return false;
							}else{
								return true;
							}
						});
						if (data != '') {
							var str = '';
							$.each(data, function(i,n){
							    str += '<option>'+n+'</option>';
							});
							$('#like_title').html(str);
							$('#set_title').html(str);
							$('#set_id').html(str);
						}else{
							alert('数据查询错误！');
						}
					}

					var linkage_arr = ['like_title','set_title','set_id'];
					var linkage_value_arr = [];
					$("#like_title").change(function() {
						linkagechange('like_title','select_title',$(this).trigger("select").val());
					});
					$("#set_title").change(function() {
						$("#like_title").val($(this).trigger("select").val());
						linkagechange('set_title','select_title',$(this).trigger("select").val());
					});
					$("#set_id").change(function() {
						linkagechange('set_id','select_title',$(this).trigger("select").val());
					});

					function linkagechange(ele_from,ele_to,value){
						var toVal = $('#'+ele_to).val();
						var toValArr = [];
						if(toVal){
							toValArr = toVal.split(',');
							if($.inArray(value,toValArr) === -1)
								toValArr.push(value);
							var source_value = linkage_value_arr[ele_from + '_value'];
							var other_value = true;
							$.each(linkage_arr, function(i,n){
								if(n != ele_from){
									if($('#' + n).val() == source_value){
										other_value = false;
										return false;
									}
									if($.inArray($('#' + n).val(),toValArr) === -1)
										toValArr.push($('#' + n).val());
								}
							});
							if(other_value){
								var i = $.inArray(source_value,toValArr);
								if(i !== -1 && source_value !== value)
									toValArr.splice(i,1);
							}
						}else{
							toValArr.push(value);
							$.each(linkage_arr, function(i,n){
								if(n != ele_from){
									if($.inArray($('#' + n).val(),toValArr) === -1)
										toValArr.push($('#' + n).val());
									linkage_value_arr[n + '_value'] = $('#' + n).val();
								}
							});
						}
						$('#'+ele_to).val(toValArr.join(','));
						linkage_value_arr[ele_from + '_value'] = value;
					}
				});
				//-->
			</script>
		</td>
	</tr>
	<tr>
		<td>主键</td>
		<td><select name="setting[set_id]" id="set_id"></select> 用于返回值赋值给管理字段作为存入ID。(表里面唯一标示，比如主键)</td>
	</tr>
	<tr>
		<td>赋值字段</td>
		<td><select name="setting[set_title]" id="set_title"></select> 用于返回值赋值给管理字段作为存入标题。</td>
	</tr>
	<tr>
		<td>like字段</td>
		<td><select name="setting[like_title]" id="like_title"></select> 选择like字段。(解读为where '【字段名】' like '%张三%')</td>
	</tr>
	<tr>
		<td>查询字段</td>
		<td><input type="text" name="setting[select_title]" id="select_title" value="" size="40" class="input-text"> 请填写字段名如：id,title (为空则表示全部查询。非空时必须包含like字段、赋值字段、主键)</td>
	</tr>
	<tr>
		<td>存入数据方式</td>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[insert_type]" value="id" checked="checked"/> ID存入 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[insert_type]" value="title"/> 标题存入 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[insert_type]" type="radio" value="title_id"/> 标题+ID存入 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[insert_type]" type="radio" value="multiple_id" id="multiple_id"/> 多选ID存入 <span></span></label>
        </div></td>
	</tr>
</table>