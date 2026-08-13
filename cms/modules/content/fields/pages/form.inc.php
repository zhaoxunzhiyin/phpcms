	function pages($field, $value, $fieldinfo) {
		extract($fieldinfo);
		if($value) {
			$v = explode('|', $value);
			$data = "<label><select name=\"info[paginationtype]\" id=\"paginationtype\" onchange=\"if(this.value==1)\$('#paginationtype1').css('display','');else \$('#paginationtype1').css('display','none');\">";
			$type = array(L('page_type1'), L('page_type2'), L('page_type3'));
			if($v[0]==1) $con = 'style="display:"';
			else $con = 'style="display:none"';
			foreach($type as $i => $val) {
				if($i==$v[0]) $tag = 'selected';
				else $tag = '';
				$data .= "<option value=\"$i\" $tag>$val</option>";
			}
			$data .= "</select></label><label><span id=\"paginationtype1\" $con><label><input name=\"info[maxcharperpage]\" type=\"text\" id=\"maxcharperpage\" value=\"$v[1]\" maxlength=\"8\"></label><label>".L('page_maxlength')."</label></span></label>";
			return $data;
		} else {
			return "<label><select name=\"info[paginationtype]\" id=\"paginationtype\" onchange=\"if(this.value==1)\$('#paginationtype1').css('display','');else \$('#paginationtype1').css('display','none');\">
                <option value=\"0\">".L('page_type1')."</option>
                <option value=\"1\">".L('page_type2')."</option>
                <option value=\"2\">".L('page_type3')."</option>
            </select></label>
			<label><span id=\"paginationtype1\" style=\"display:none\"><label><input name=\"info[maxcharperpage]\" type=\"text\" id=\"maxcharperpage\" value=\"10000\" maxlength=\"8\"></label><label>".L('page_maxlength')."</label></span></label>";
		}
	}
