	function linkfield($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);

		$get_db = pc_base::load_model("get_model");

		$sel_tit=$setting['select_title']?$setting['select_title']:'*';
		$sel_where = $setting['select_where'] ? safe_replace($setting['select_where']) : '';
		$sql = "SELECT ".$sel_tit." FROM `".$setting['table_name']."`" . ($sel_where ? ' WHERE '.$sel_where : '');
		$r= $get_db->query($sql);
		while(($s = $get_db->fetch_next()) != false) {
			$dataArr[] = $s;
		}

		if($fieldinfo['link_type']){
			$value = str_replace('&amp;','&',$value);
			if(!defined('BOOTSTRAP_SELECT')) {
				$data = '<script type="text/javascript">var bs_selectAllText = \'全选\';var bs_deselectAllText = \'全删\';var bs_noneSelectedText = \'没有选择\'; var bs_noneResultsText = \'没有找到 {0}\';</script>
				<script type="text/javascript">jQuery(document).ready(function(){$(\'.bs-select\').selectpicker();});</script>';
				define('BOOTSTRAP_SELECT', 1);
			} else {
				$data = '';
			}
			$data .= load_css(JS_PATH.'bootstrap-select/css/bootstrap-select.min.css');
			$data .= load_js(JS_PATH.'bootstrap-select/js/bootstrap-select.min.js');
			if($fieldinfo['insert_type'] == "multiple_id"){
				
				$data1 = $data2 = '';
				$data .= '<input type="hidden" name="info['.$fieldinfo['field'].'][]" value="-99"><label><select name="info['.$fieldinfo['field'].'][]" id="'.$fieldinfo['field'].'" multiple="multiple" class="form-control bs-select" data-title="'.L('请选择').'" data-live-search="true" data-actions-box="true">';
			}else{
				$data .= '<label><select name="info['.$fieldinfo['field'].']" id="'.$fieldinfo['field'].'" class="form-control bs-select" data-title="'.L('请选择').'" data-live-search="true" data-actions-box="true">';
			}

			foreach($dataArr as $v) {
				if($fieldinfo['insert_type']=="id"){
					$output_type = $v[$fieldinfo['set_id']];
				}elseif($fieldinfo['insert_type']=="title"){
					$output_type = $v[$fieldinfo['set_title']];
				}elseif($fieldinfo['insert_type']=="title_id"){
					$output_type = $v[$fieldinfo['set_title']].'_'.$v[$fieldinfo['set_id']];
				}else{
					$output_type = $v[$fieldinfo['set_id']];
				}

				if($fieldinfo['insert_type'] == "multiple_id" && $value){
					$value = trim($value,'|');
					$arr_value = explode('|',$value);
					if(in_array($output_type,$arr_value))
						$data1 .= "<option value='".$output_type."' selected>".$v[$fieldinfo['set_title']]."</option>\n";
					else
						$data2 .= "<option value='".$output_type."'>".$v[$fieldinfo['set_title']]."</option>\n";
				}else{
					if($output_type == $value) $select = 'selected';
					else $select = '';
					$data .= "<option value='".$output_type."' ".$select.">".$v[$fieldinfo['set_title']]."</option>\n";
				}
			}
			if($fieldinfo['insert_type'] == "multiple_id" && $value)
				$data .= $data1.$data2;
			$data .= '</select></label>';

		}else{
			$multiple_field_value = '';
			$multiple_field_value_escape = '';
			$cat_field_value = '';
			$data_json = json_encode($dataArr);

			if($fieldinfo['insert_type'] == "multiple_id"){
				if(strpos($value, '_') !== false)
					$value = preg_match('/(\d+)/',$value,$a) ? $a[1] : '';

				$value = trim($value,'|');
				$value_escape = str_replace('|',',',$value);

				if($value && preg_match('/^[0-9,]+$/i',$value_escape)){
					$value_arr = explode(',', $value_escape);
					foreach($dataArr as $v) {
						if(in_array($v[$fieldinfo['set_id']],$value_arr))
							$multiple_field_value .= '<li class="search_view" id="search_view_' . $v[$fieldinfo['set_id']] . '"><span>' . $v[$fieldinfo['set_title']] . '</span><a href="javascript:;" class="close" onclick="remove_serach' . $field . '(\'' . $v[$fieldinfo['set_title']] . '\',\'' . $v[$fieldinfo['set_id']] . '\')"></a></li>';
					}

					$multiple_field_value_escape = str_replace("'","\'",$multiple_field_value);
				}else{
					$value = '';
				}
			}elseif($fieldinfo['insert_type'] == "title_id" && $value){
				$exp_value = explode('_',$value);
				$cat_field_value = $exp_value[0];
			}elseif($fieldinfo['insert_type'] == "title" && $value){
				$cat_field_value = $value;
			}
			$data = <<<EOT
			<style type="text/css">
			.content_div{ margin-top:0px; font-size:14px; position:relative}
			#search_div{$field}{z-index: 2; position:absolute; top:23px; border:1px solid #dfdfdf; text-align:left; padding:1px; left:0px;*left:0px; width:263px;*width:260px; background-color:#FFF; display:none; font-size:12px;}
			#search_div{$field} li{ line-height:24px;cursor:pointer}
			#search_div{$field} li a{  padding-left:6px;display:block}
			#search_div{$field} li a:hover, #search_div{$field} li:hover{ background-color:#e2eaff}
			#search_view{$field}{padding-bottom:0;}
			#search_view{$field} li.search_view{float: left;margin: 3px 10px 3px 0;padding-left: 5px;padding-bottom: 2px;}
			#search_view{$field} li.search_view span{margin-right: 30px;}
			</style>
			<div class="content_div">
				<label><input type="text" id="cat_search{$field}" value="{$cat_field_value}" onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;" class='form-control'><input name="info[{$fieldinfo['field']}]" id="{$fieldinfo['field']}" type="hidden" class='form-control' value="{$value}"/></label>
				<ul id="search_div{$field}"></ul>
				<ul id="search_view{$field}" class="list-dot">{$multiple_field_value}</ul>
			</div>
			<script type="text/javascript" language="javascript">
				var str_title{$field} = '{$multiple_field_value_escape}';
				var data_json{$field} = eval({$data_json});
				function remove_serach{$field}(title,id){
					$('#search_view_'+id).remove();
					str_title{$field} = $('#search_view{$field}').html();
					var ele_value = $("#{$fieldinfo['field']}").val();
					ele_value = ele_value.replace('|' + id,'');
					ele_value = ele_value.replace(id,'');
					$("#{$fieldinfo['field']}").val(ele_value);
				}
				function setvalue{$field}(title,id){
					var title = title;
					var id = id;
					var type = "{$fieldinfo['insert_type']}";
					if(type == "id"){
						$("#{$fieldinfo['field']}").val(id);
					}else if(type == "title"){
						$("#{$fieldinfo['field']}").val(title);
					}else if(type == "title_id"){
						$("#{$fieldinfo['field']}").val(title+'_'+id);
					}
					else if(type == "multiple_id"){
						var elem_value = $("#{$fieldinfo['field']}").val();
						if(title && elem_value.indexOf(id) < 0){
							str_title{$field} = str_title{$field} + '<li class="search_view" id="search_view_' + id + '"><span>' + title + '</span><a href="javascript:;" class="close" onclick="remove_serach{$field}(\'' + title + '\',\'' + id + '\')"></a></li>';
							elem_value = (elem_value)?elem_value + '|' + id:id;
							$("#{$fieldinfo['field']}").val(elem_value);
							$('#search_view{$field}').html(str_title{$field});
						}
					}
					$("#cat_search{$field}").val('');
					$('#search_div{$field}').hide();
				}
				
			$(document).ready(function(){
				$('#cat_search{$field}').keyup(function(){
					var value = $("#cat_search{$field}").val();
					if (value.length > 0){
						var str = '';
						var ele_value = $("#{$fieldinfo['field']}").val();
						var ele_value_arr = ele_value.split('|');
						value = value.replace(/^\s+|\s+$/gm,'');
						$.each(data_json{$field}, function(i,n){
							if($.inArray(n.{$fieldinfo['set_id']}, ele_value_arr) === -1){
								if(n.{$fieldinfo['like_title']}.indexOf(value) !== -1 || value == '')
									str += '<li onclick=\'setvalue{$field}("'+n.{$fieldinfo['set_title']}+'","'+n.{$fieldinfo['set_id']}+'");\'>'+n.{$fieldinfo['set_title']}+'</li>';
							}
						});
						if (str != null && str!= '') {
							$('#search_div{$field}').html(str);
							$('#search_div{$field}').show();
						} else {
							$('#search_div{$field}').hide();
						}
					} else {
						$('#search_div{$field}').hide();
					}
				});	
			})
			</script>
EOT;
		}
		return $data;
	}
