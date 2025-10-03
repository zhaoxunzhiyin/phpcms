	function word($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		$str = load_js(JS_PATH.'jquery-ui/jquery-ui.js');
		$str .= load_css(JS_PATH.'jquery-fileupload/css/jquery.fileupload.css');
		$str .= load_js(JS_PATH.'jquery-fileupload/js/jquery.fileupload.min.js');
		$str .= '<script type="text/javascript">'.PHP_EOL;
		$str .= '$(document).ready(function(){'.PHP_EOL;
		$str .= '	// 初始化上传组件'.PHP_EOL;
		$str .= '	$(\'.'.$field.'\').fileupload({'.PHP_EOL;
		$str .= '		disableImageResize: false,'.PHP_EOL;
		$str .= '		autoUpload: true,'.PHP_EOL;
		$str .= '		maxFileSize: 2,'.PHP_EOL;
		$str .= '		url: \''.WEB_PATH.'api.php?op=get_word&module=content&catid='.intval($this->input->get('catid')).'&isadmin='.$this->isadmin.'&userid='.$this->userid.'&groupid='.$this->groupid.'&siteid='.$this->siteid.'&watermark='.$watermark.'&attachment='.$attachment.'&image_reduce='.$image_reduce.'&token='.csrf_hash().'\','.PHP_EOL;
		$str .= '		dataType: \'json\','.PHP_EOL;
		$str .= '		formData : {'.PHP_EOL;
		$str .= '			\''.SYS_TOKEN_NAME.'\': \''.csrf_hash().'\','.PHP_EOL;
		$str .= '		},'.PHP_EOL;
		$str .= '		progressall: function (e, data) {'.PHP_EOL;
		$str .= '			// 上传进度条 all'.PHP_EOL;
		$str .= '			var progress = parseInt(data.loaded / data.total * 100, 10);'.PHP_EOL;
		$str .= '			layer.msg(progress+\'%\');'.PHP_EOL;
		$str .= '		},'.PHP_EOL;
		$str .= '		add: function (e, data) {'.PHP_EOL;
		$str .= '			data.submit();'.PHP_EOL;
		$str .= '		},'.PHP_EOL;
		$str .= '		done: function (e, data) {'.PHP_EOL;
		$str .= '			//console.log($(this).html());'.PHP_EOL;
		$str .= '			dr_tips(data.result.code, data.result.msg);'.PHP_EOL;
		$str .= '			if (data.result.code) {'.PHP_EOL;
		$str .= '				var arr = data.result.data;'.PHP_EOL;
		$str .= '				$(\'#'.$field.'_word\').val(arr.file);'.PHP_EOL;
		$str .= '				$(\'#title\').val(arr.title);'.PHP_EOL;
		$str .= '				if ($(\'#keywords\').length > 0) {'.PHP_EOL;
		$str .= '					$(\'#keywords\').val(arr.keyword);'.PHP_EOL;
		$str .= '				$(\'#keywords\').tagsinput(\'add\', arr.keyword);'.PHP_EOL;
		$str .= '				}'.PHP_EOL;
		if (SYS_EDITOR) {
			$str .= '				CKEDITOR.instances[\'content\'].setData(arr.content);'.PHP_EOL;
		} else {
			$str .= '				UE.getEditor(\'content\').setContent(arr.content);'.PHP_EOL;
		}
		$str .= '			}'.PHP_EOL;
		$str .= '		},'.PHP_EOL;
		$str .= '	});'.PHP_EOL;
		$str .= '})'.PHP_EOL;
		$str .= '</script>'.PHP_EOL;
		if (dr_is_empty($value)) $value = '';
		$errortips = $this->fields[$field]['errortips'];
		if($errortips || $minlength) $this->formValidator .= '$("#'.$field.'_word").formValidator({onshow:"",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		//if (defined('IS_ADMIN') && IS_ADMIN) {
			return '<input type="hidden" name="info['.$field.']" id="'.$field.'_word" value="'.$value.'"><label class="'.$field.'"><span class="btn green btn-sm fileinput-button"><i class="fa fa-cloud-upload"></i> <span>'.L('import_word').'</span> <input type="file" name="file_data" title=""> </span> </label>'.$str;
		//} else {
			//return L('import_wxurl_publish');
		//}
	}
