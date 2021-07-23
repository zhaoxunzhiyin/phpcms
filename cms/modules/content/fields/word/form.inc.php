	function word($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		if(!defined('LAYUI_INIT')) {
			$str .= '<script type="text/javascript" src="'.JS_PATH.'layui/layui.js"></script>';
			define('LAYUI_INIT', 1);
		}
		$str .= '<script>';
		$str .= 'layui.use(\'upload\', function () {';
		$str .= '	var upload = layui.upload;';
		$str .= '	upload.render({';
		$str .= '		elem:\'#'.$field.'\',';
		$str .= '		accept:\'file\',';
		$str .= '		field:\'file_upload\',';
		$str .= '		url: \''.WEB_PATH.'api.php?op=get_word&module=content&catid='.intval($this->input->get('catid')).'&watermark='.$watermark.'&attachment='.$attachment.'&image_reduce='.$image_reduce.'\',';
		$str .= '		exts: \'docx\',';
		$str .= '		done: function(data){';
		$str .= '			if(data.code == 1){';
		$str .= '				dr_tips(data.code, data.msg);';
		$str .= '				var arr = data.data;';
		$str .= '				$(\'#'.$field.'_word\').val(arr.file);';
		$str .= '				$(\'#title\').val(arr.title);';
		$str .= '				if ($(\'#keywords\').length > 0) {';
		$str .= '					$(\'#keywords\').val(arr.keyword);';
		$str .= '					$(\'#keywords\').tagsinput(\'add\', arr.keyword);';
		$str .= '				}';
		if (pc_base::load_config('system', 'editor')) {
			$str .= '				CKEDITOR.instances[\'content\'].setData(arr.content);';
		} else {
			$str .= '				UE.getEditor(\'content\').setContent(arr.content);';
		}
		$str .= '			}else{';
		$str .= '				dr_tips(data.code, data.msg);';
		$str .= '			}';
		$str .= '		}';
		$str .= '	});';
		$str .= '});';
		$str .= '</script>';
		if(!$value) $value = $defaultvalue;
		$errortips = $this->fields[$field]['errortips'];
		if($errortips || $minlength) $this->formValidator .= '$("#'.$field.'_word").formValidator({onshow:"",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		//if (defined('IN_ADMIN')) {
			return '<input type="hidden" name="info['.$field.']" id="'.$field.'_word" value="'.$value.'" '.$formattribute.' '.$css.'><button type="button" class="layui-btn" id="'.$field.'"><i class="layui-icon">&#xe67c;</i>'.L('import_word').'</button>'.$str;
		//} else {
			//return L('import_wxurl_publish');
		//}
	}
