	function tabletexts($field, $value, $fieldinfo) {
		extract(string2array($fieldinfo['setting']));
		$columns = explode("\n",$this->fields[$field]['column']);
		$grouplist = getcache('grouplist','member');
		$_groupid = param::get_cookie('_groupid');
		$grouplist = $grouplist[$_groupid];
		$allowupload = defined('IN_ADMIN') ? 1 : ($grouplist['allowattachment'] ? 1: 0);
		$toolbar = 'full';
		$list_str = '';
		if($value) {
			$value = string2array(html_entity_decode($value,ENT_QUOTES));
			if(is_array($value)) {
				foreach($value as $_k=>$_v) {
					$list_str .= "<tr>";
					for ($x=1; $x<=count($columns); $x++) {
						$list_str .="<td><input type='text' name='".$field."_".$x."[]' value='".$_v[$field."_".$x]."' class='input-text' style='width:100%; padding:6px 0;'></td>";
					}
					$list_str .= "<td><input type='button' class='button' value='删除' onclick='delThisAttr(this)'> <input type='button' class='button' value='↑上移' onclick='moveUp(this)'> <input type='button' class='button' value='↓下移' onclick='moveDown(this)'></td></tr>";
				}
			}
		}
		$string ='<script type=text/javascript>
		function add'.$field.'(id){
			var html = "<tr>';
			for($cols=1; $cols<=count($columns); $cols++){
				$string .='<td><input type=\'text\' name=\''.$field.'_'.$cols.'[]\' value=\'\' class=\'input-text\' style=\'width:100%; padding:6px 0;\'></td>';
			}
			$string .='<td><input type=\'button\' class=\'button\' value=\'删除\' onclick=\'delThisAttr(this)\'> <input type=\'button\' class=\'button\' value=\'↑上移\' onclick=\'moveUp(this)\'> <input type=\'button\' class=\'button\' value=\'↓下移\' onclick=\'moveDown(this)\'></td></tr>";
			var temp_id = Math.random().toString(36).substr(2);
			var html_new = html.replace(/thisisid/g,temp_id);
			$("#"+id).before(html_new);
		}
		</script>';
		$string .= '<input name="info['.$field.']" type="hidden" value="1">
		<fieldset class="blue pad-10">
		<legend>列表</legend><div class="table-list"><table width="100%" cellspacing="0"><thead><tr align="left"> ';
		foreach($columns as $column){
			$string .="<th align='left' style='border-bottom: 1px solid #d5dfe8;'>".$column."</th>";
		}
		$string .="<th align='left' style='border-bottom: 1px solid #d5dfe8; width:185px;'>操作</th></tr></thead><tbody>";
		$string .= $list_str;
		$string .= "<tr id='".$field."'></tr></tbody>
		</table></div>
		</fieldset>
		<div class='bk10'></div>";
		$string .= "<input type=\"button\" class=\"button\" value=\"添加一行\" onclick=\"add".$field."('".$field."')\">";
		return $string;
	}