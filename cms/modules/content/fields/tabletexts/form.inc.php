	function tabletexts($field, $value, $fieldinfo) {
		extract(string2array($fieldinfo['setting']));
		$columns = explode("\n",$this->fields[$field]['column']);
		$list_str = '';
		if($value) {
			$value = string2array(html_entity_decode($value,ENT_QUOTES));
			if(is_array($value)) {
				foreach($value as $_k=>$_v) {
					$list_str .= "<tr>";
					for ($x=1; $x<=dr_count($columns); $x++) {
						$list_str .="<td><input type='text' name='".$field."_".$x."[]' value='".$_v[$field."_".$x]."' class='form-control' style='width:100%; padding:6px 0;'></td>";
					}
					$list_str .= "<td style='text-align: center;'><button type='button' class='btn red btn-xs' onclick='delThisAttr(this)'> <i class='fa fa-trash'></i> </button></td></tr>";
				}
			}
		}
		$string = load_css(JS_PATH.'jquery-ui/jquery-ui.min.css');
		$string .= load_js(JS_PATH.'jquery-ui/jquery-ui.min.js');
		$string .= '<script type=text/javascript>
		function add'.$field.'(id){
			var html = "<tr>';
			for($cols=1; $cols<=dr_count($columns); $cols++){
				$string .='<td><input type=\'text\' name=\''.$field.'_'.$cols.'[]\' value=\'\' class=\'form-control\' style=\'width:100%; padding:6px 0;\'></td>';
			}
			$string .='<td style=\'text-align: center;\'><button type=\'button\' class=\'btn red btn-xs\' onclick=\'delThisAttr(this)\'> <i class=\'fa fa-trash\'></i> </button></td></tr>";
			var temp_id = Math.random().toString(36).substr(2);
			var html_new = html.replace(/thisisid/g,temp_id);
			$("#"+id).append(html_new);
		}
		</script>';
		$string .= '<input name="info['.$field.']" type="hidden" value="1">
		<fieldset class="blue pad-10">
		<legend>列表</legend><div class="table-scrollable"><table class="table table-nomargin table-striped table-bordered table-advance"><thead><tr align="left"> ';
		foreach($columns as $column){
			$string .="<th align='left'>".$column."</th>";
		}
		$string .="<th width='50' style='text-align: center;'><button type=\"button\" class=\"btn blue btn-xs\" onClick=\"add".$field."('".$field."')\"> <i class=\"fa fa-plus\">
		</i> </button></th></tr></thead><tbody id='".$field."' class=\"".$field."-sortable\">";
		$string .= $list_str;
		$string .= "</tbody>
		</table></div>
		</fieldset><script type=\"text/javascript\">$(\".".$field."-sortable\").sortable();</script>";
		return $string;
	}