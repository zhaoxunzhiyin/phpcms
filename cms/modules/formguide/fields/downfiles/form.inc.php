	function downfiles($field, $value, $fieldinfo) {
		extract(string2array($fieldinfo['setting']));
		$list_str = '';
		if($value) {
			$value = dr_get_files($value);
			if(is_array($value)) {
				foreach($value as $_k=>$_v) {
					$id = $_v['id'] ? $_v['id'] : $_v['file'];
					if($grid) {
						$list_str .= '<div class="grid-item files_row"><div class="files_row_preview preview"><a href="javascript:preview(\''.dr_get_file($id).'\');"><img src="'.(dr_is_image(dr_get_file($id)) ? dr_get_file($id) : WEB_PATH.'api.php?op=icon&fileext='.fileext(dr_get_file($id))).'"></a></div><input type="hidden" class="files_row_id" name="'.$field.'[id][]" value="'.$id.'"><div class="op-btn"><label><button onclick="dr_file_remove(this, \''.$field.'\')" type="button" class="btn red file_delete btn-xs"><i class="fa fa-trash"></i></button></label></div><div class="col-md-12 files_show_title_html"><input placeholder="'.L('名称').'" class="form-control files_row_title" type="text" name="'.$field.'[title][]" value="'.htmlspecialchars((string)$_v['title']).'"></div><div class="col-md-12 files_show_description_html"><textarea placeholder="'.L('描述').'" class="form-control files_row_description" name="'.$field.'[description][]">'.htmlspecialchars((string)$_v['description']).'</textarea></div></div>';
					} else {
						$list_str .= '<tr class="template-download files_row"><td style="text-align:center;width: 80px;"><div class="files_row_preview preview"><a href="javascript:preview(\''.dr_get_file($id).'\');"><img src="'.(dr_is_image(dr_get_file($id)) ? dr_get_file($id) : WEB_PATH.'api.php?op=icon&fileext='.fileext(dr_get_file($id))).'"></a></div></td><td class="files_show_info"><div class="row"><div class="col-md-12 files_show_title_html"><input placeholder="'.L('名称').'" class="form-control files_row_title" type="text" name="'.$field.'[title][]" value="'.htmlspecialchars((string)$_v['title']).'"><input type="hidden" class="files_row_id" name="'.$field.'[id][]" value="'.$id.'"></div><div class="col-md-12 files_show_description_html"><textarea placeholder="'.L('描述').'" class="form-control files_row_description" name="'.$field.'[description][]">'.htmlspecialchars((string)$_v['description']).'</textarea></div></div></td><td style="text-align:center;width: 80px;"><label><button onclick="dr_file_remove(this, \''.$field.'\')" type="button" class="btn red file_delete btn-sm"><i class="fa fa-trash"></i></button></label></td></tr>';
					}
				}
			}
		}
		$string = load_css(JS_PATH.'jquery-ui/jquery-ui.min.css');
		$string .= load_js(JS_PATH.'jquery-ui/jquery-ui.min.js');
		$string .= '<fieldset class="blue pad-10">
        <legend>'.L('file_list').'</legend>';
		$string .= '<div class="scroller_'.$field.'_files"><div class="'.($scroller ? 'scroller' : '').'" data-inited="0" data-initialized="1" data-always-visible="1" data-rail-visible="1">';
		if($grid) {
			$string .= '<div id="fileupload_'.$field.'_files" class="files-grid-list scroller_body">'.$list_str.'</div>';
		} else {
			$string .= '<table class="table table-striped table-upload clearfix"><tbody id="fileupload_'.$field.'_files" class="files scroller_body">'.$list_str.'</tbody></table>';
		}
		$string .= '</div></div>
		</fieldset><script type="text/javascript">$("#fileupload_'.$field.'_files").sortable();dr_slimScroll_init(".scroller_'.$field.'_files", 300);</script>
		<div class="bk10"></div>';
		$str = load_js(JS_PATH.'h5upload/h5editor.js');
		$authkey = upload_key($this->input->get('siteid').",$upload_number,$upload_allowext,$upload_maxsize,$isselectimage,,,,$attachment,$image_reduce,$chunk");
		$p = dr_authcode(array(
			'siteid' => $this->input->get('siteid'),
			'file_upload_limit' => $upload_number,
			'file_types_post' => $upload_allowext,
			'size' => $upload_maxsize,
			'allowupload' => $isselectimage,
			'thumb_width' => '',
			'thumb_height' => '',
			'watermark_enable' => '',
			'attachment' => $attachment,
			'image_reduce' => $image_reduce,
			'chunk' => $chunk,
		), 'ENCODE');
		$string .= $str."<input type=\"hidden\" name=\"info[{$field}]\" value=\"\"><label><button type=\"button\" onclick=\"javascript:h5upload('".SELF."', '{$field}_file', '".L('attachment_upload')."','{$field}','".($grid ? 'change_images' : 'change_files')."','{$p}','content','$this->catid','{$authkey}',".SYS_EDITOR.")\" class=\"btn green btn-sm\"> <i class=\"fa fa-plus\"></i> ".L('multiple_file_list')."</button></label>";
		return $string;
	}
