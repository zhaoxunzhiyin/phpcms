<?php
class form {
	/**
	 * 编辑器
	 * @param string $textareaid
	 * @param string $toolbar
	 * @param string $module 模块名称
	 * @param int $catid 栏目id
	 * @param int $color 编辑器颜色
	 * @param boole $allowupload  是否允许上传
	 * @param boole $allowbrowser 是否允许浏览文件
	 * @param string $alowuploadexts 允许上传类型
	 * @param string $height 编辑器高度
	 * @param string $disabled_page 是否禁用分页和子标题
	 * @param string $allowuploadnum
	 * @param string $modelid
	 * @param string $toolvalue
	 * @param string $autofloat
	 * @param string $autoheight
	 * @param string $theme
	 * @param string $language
	 * @param string $watermark
	 * @param string $attachment
	 * @param string $image_reduce
	 * @param string $chunk
	 * @param string $div2p
	 * @param string $anchorduiqi
	 * @param string $imageduiqi
	 * @param string $videoduiqi
	 * @param string $musicduiqi
	 * @param string $enter
	 * @param string $enablesaveimage
	 * @param string $upload_maxsize
	 * @param string $show_bottom_boot
	 * @param string $tool_select_1
	 * @param string $tool_select_2
	 * @param string $tool_select_3
	 * @param string $tool_select_4
	 */
	public static function editor($textareaid = 'content', $toolbar = 'basic', $module = '', $catid = '', $color = '', $allowupload = 0, $allowbrowser = 1,$alowuploadexts = '',$height = 200,$disabled_page = 0, $allowuploadnum = '10', $modelid = '', $toolvalue = '', $autofloat = 0, $autoheight = 0, $theme = '', $language = '', $watermark = 1, $attachment = 0, $image_reduce = '', $chunk = 0, $div2p = 0, $anchorduiqi = 0, $imageduiqi = 0, $videoduiqi = 0, $musicduiqi = 0, $enter = 0, $enablesaveimage = 1, $width = '100%', $upload_maxsize = 0, $show_bottom_boot = 0, $tool_select_1 = 0, $tool_select_2 = 0, $tool_select_3 = 0, $tool_select_4 = 0) {
		$siteid = pc_base::load_sys_class('input')->get('siteid') ? pc_base::load_sys_class('input')->get('siteid') : get_siteid();
		if ($autofloat) {
			$autoFloatEnabled = 'true';
		} else {
			$autoFloatEnabled = 'false';
		}
		if ($autoheight) {
			$autoHeightEnabled = 'true';
		} else {
			$autoHeightEnabled = 'false';
		}
		$show_page = ($module == 'content' && !$disabled_page) ? 'true' : 'false';
		$authkey = upload_key("$siteid,$allowuploadnum,$alowuploadexts,$upload_maxsize,$allowbrowser,,,$watermark,$attachment,$image_reduce,$chunk");
		$p = dr_authcode(array(
			'siteid' => $siteid,
			'file_upload_limit' => $allowuploadnum,
			'file_types_post' => $alowuploadexts,
			'size' => $upload_maxsize,
			'allowupload' => $allowbrowser,
			'thumb_width' => '',
			'thumb_height' => '',
			'watermark_enable' => $watermark,
			'attachment' => $attachment,
			'image_reduce' => $image_reduce,
			'chunk' => $chunk,
		), 'ENCODE');
		$str ='';
		if (SYS_EDITOR) {
			$str .= load_js(JS_PATH.'ckeditor/ckeditor.js');
			if($toolbar == 'basic') {
				$tool = defined('IS_ADMIN') && IS_ADMIN ? "['Source']," : '';
				$tool .= "['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink' ],['Maximize'],\r\n";
			} elseif($toolbar == 'full') {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					$tool = "['Source',";
				} else {
					$tool = '[';
				}
				$tool .= "'-','Templates'],
				['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
				['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['ShowBlocks'],['Image','Capture','Html5video','Iframe'],['Maximize'],
				'/',
				['Bold','Italic','Underline','Strike','-'],
				['Subscript','Superscript','-'],
				['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
				['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				['Link','Unlink','Anchor'],
				['Table','HorizontalRule','EmojiPanel','SpecialChar'";
				if ($show_page=="true") {
					$tool .= ",'PageBreak'";
				}
				$tool .= "],
				'/',
				['Styles','Format','Font','FontSize'],
				['TextColor','BGColor'],\r\n";
			} elseif($toolbar == 'desc') {
				$tool = "['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'Image', '-','Source'],['Maximize'],\r\n";
			} elseif($toolbar == 'standard') {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					$tool = "['Source',";
				} else {
					$tool = '[';
				}
				$tool .= "'Undo', 'Redo', 'Bold', 'Italic', 'Underline', 'Superscript', 'Subscript', 'RemoveFormat','BlockQuote', 'SelectAll', 'Font', 'FontSize', '|', 'Indent', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'Link', 'Unlink', 'Anchor', '|', 'Image'";
				if ($show_page=="true") {
					$tool .= ", 'PageBreak'";
				}
				$tool .= "],['Maximize'],\r\n";
			} else {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					$tool = "['Source',";
				} else {
					$tool = '[';
				}
				$tool .= "" . $toolvalue . "],['Maximize'],\r\n";
			}
			$str .= "<script type=\"text/javascript\">\r\n";
			$str .= "var editor_{$textareaid} = CKEDITOR.replace('$textareaid', {";
			$str .= "on:{";
			$str .= "change:function(evt){";
			$str .= "$('#".$textareaid."').html(evt.editor.getData());";
			$str .= "}";
			$str .= "},";
			$str .= "width:\"".$width."\",";
			$str .= "height:{$height},";
			$str .="textareaid:'".$textareaid."',module:'".$module."',catid:'".$catid."',\r\n";
			if($allowupload) $str .= "filebrowserUploadUrl : '".SELF."?m=attachment&c=attachments&a=upload&module=".$module."&catid=".$catid."&args=".$p."&authkey=".$authkey."&token=".csrf_hash()."',\r\n";
			if($language && file_exists(CMS_PATH.'statics/js/ckeditor/lang/'.$language.'.js')) {
				$str .= "language: '$language',";
			}
			if($color) {
				$str .= "uiColor: '$color',";
			}
			if($theme && file_exists(CMS_PATH.'statics/js/ckeditor/skins/'.$theme.'/') && $theme!='moono-lisa') {
				$str .= "skin: '$theme',";
			}
			$str .= "toolbar :\r\n";
			$str .= "[\r\n";
			$str .= $tool;
			$str .= "]\r\n";
			$str .= "});\r\n";
			$str .= "dr_post_addfunc(function(){\n";
			$str .= "if(editor_{$textareaid}.mode == 'source'){\n";
			$str .= "editor_{$textareaid}.execCommand('source');\n";
			$str .= "$('#".$textareaid."').html(editor_{$textareaid}.getData());\n";
			$str .= "}\n";
			$str .= "})\n";
			if (!intval($enablesaveimage)) {
				$str .= "function dr_editor_down_img_".$textareaid."(){
var index = layer.load(2, {
    shade: [0.3,'#fff'], //0.1透明度的白色背景
    time: 100000000
});
$.ajax({
    type: 'POST',
    url: '".SELF."?m=attachment&c=attachments&a=down_img&is_iframe=1&module=".$module."&catid=".$catid."&args=".$p."&authkey=".$authkey."&token=".csrf_hash()."',
    dataType: 'json',
    data: { value: CKEDITOR.instances.".$textareaid.".getData(), ".SYS_TOKEN_NAME.": $(\"#myform input[name='".SYS_TOKEN_NAME."']\").val() },
    success: function (json) {
        layer.close(index);
        // token 更新
        if (json.token) {
            var token = json.token;
            $(\"#myform input[name='\"+token.name+\"']\").val(token.value);
        }
        if (json.code == 0) {
            dr_tips(0, json.msg, json.data.time);
        } else {
            
            var width = '500px';
            var height = '70%';
        
            if (is_mobile()) {
                width = '95%';
                height = '90%';
            }
        
            layer.open({
                type: 2,
                title: '',
                fix:true,
                scrollbar: false,
                maxmin: false,
                resize: true,
                shadeClose: true,
                shade: 0,
                area: [width, height],
                btn: ['确定', '取消'],
                yes: function(index, layero){
                    // 延迟加载
                    var loading = layer.load(2, {
                        shade: [0.3,'#fff'], //0.1透明度的白色背景
                        time: 100000000
                    });
                    var body = layer.getChildFrame('body', index);
                    $.ajax({type: 'POST',dataType:'json', url: json.msg, data: $(body).find('#myform').serialize(),
                        success: function(json) {
                            layer.close(loading);
                            if (json.code) {
                                layer.close(index);
                                CKEDITOR.instances['".$textareaid."'].setData(json.data);
                                dr_tips(1, json.msg);
                            } else {
                                dr_tips(0, json.msg, json.data.time);
                            }
                            return false;
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError) {
                            dr_ajax_alert_error(HttpRequest, this, thrownError);
                        }
                    });
                    return false;
                },
                success: function(layero, index){
                    // 主要用于后台权限验证
                    var body = layer.getChildFrame('body', index);
                    var json = $(body).html();
                    if (json.indexOf('\"code\":0') > 0 && json.length < 500){
                        var obj = JSON.parse(json);
                        layer.close(index);
                        dr_tips(0, obj.msg);
                    }
                },
                content: json.msg+'&is_iframe=1'
            });
            
            
        }
    },
    error: function(HttpRequest, ajaxOptions, thrownError) {
        dr_ajax_alert_error(HttpRequest, this, thrownError);
    }
});
            }\n";
			}
			$str .= '</script>';
		} else {
			$str .= load_script('var ueditor_baidumap_ak = "'.SYS_BDMAP_API.'";');
			$str .= load_js(JS_PATH.'ueditor/ueditor.config.js');
			$str .= load_js(JS_PATH.'ueditor/ueditor.all.js');
			if($toolbar == 'basic') {
				$tool = defined('IS_ADMIN') && IS_ADMIN ? "['Source'," : '[';
				$tool .= "'Bold', 'Italic', '|', 'InsertOrderedList', 'InsertUnorderedList', '|', 'Link', 'Unlink' ]";
			} elseif($toolbar == 'standard') {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					$tool = "['Source',";
				} else {
					$tool = '[';
				}
				$tool .= "'FullScreen', 'Undo', 'Redo', '|', 'Bold', 'Italic', 'Underline', 'StrikeThrough', 'Superscript', 'Subscript', 'RemoveFormat', 'FormatMatch', 'AutoTypeSet', '|', 'BlockQuote', '|', 'PastePlain', '|', 'ForeColor', 'BackColor', 'InsertOrderedList', 'InsertUnorderedList', 'SelectAll', 'ClearDoc', '|', 'CustomStyle', 'Paragraph', '|', 'RowSpacingTop', 'RowSpacingBottom', 'LineHeight', '|', 'FontFamily', 'FontSize', '|', 'DirectionalityLtr', 'DirectionalityRtl', '|', '', 'Indent', '|', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyJustify', '|', 'Link', 'Unlink', 'Anchor', '|', 'ImageNone', 'ImageLeft', 'ImageRight', 'ImageCenter', '|', 'InsertImage', 'Emotion', 'Scrawl', 'InsertVideo', 'Music', 'Attachment', 'Map', 'InsertFrame'";
				if ($show_page=="true") {
					$tool .= ", 'PageBreak'";
				}
				$tool .= ", 'HighlightCode', '|', 'Horizontal', 'Date', 'Time', 'Spechars', '|', 'InsertTable', 'DeleteTable', 'InsertParagraphBeforeTable', 'InsertRow', 'DeleteRow', 'InsertCol', 'DeleteCol', 'MergeCells', 'MergeRight', 'MergeDown', 'SplittoCells', 'SplittoRows', 'SplittoCols', '|', 'Print', 'Preview', 'SearchReplace', 'Help']";
			} elseif($toolbar == 'desc') {
				$tool = "['Bold', 'Italic', '|', 'InsertOrderedList', 'InsertUnorderedList', '|', 'Link', 'Unlink', '|', 'InsertImage', '|', 'Source']";
			} elseif($toolbar == 'full') {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					$tool = "['Source',";
				} else {
					$tool = '[';
				}
				$tool .= "'Fullscreen', '|', 'Undo', 'Redo', '|',
				'Bold', 'Italic', 'Underline', 'Fontborder', 'Strikethrough', 'Superscript', 'Subscript', 'Removeformat', 'Formatmatch', 'Autotypeset', 'Blockquote', 'Pasteplain', '|', 'Forecolor', 'Backcolor', 'Insertorderedlist', 'Insertunorderedlist', 'Selectall', 'Cleardoc', '|',
				'Rowspacingtop', 'Rowspacingbottom', 'Lineheight', '|',
				'Customstyle', 'Paragraph', 'Fontfamily', 'Fontsize', '|',
				'Directionalityltr', 'Directionalityrtl', 'Indent', '|',
				'Justifyleft', 'Justifycenter', 'Justifyright', 'Justifyjustify', '|', 'Touppercase', 'Tolowercase', '|',
				'Link', 'Unlink', 'Anchor', '|', 'Imagenone', 'Imageleft', 'Imageright', 'Imagecenter', '|',
				'Insertimage', 'ImportWord', 'Emotion', 'Scrawl', 'Insertvideo', 'Music', 'Attachment', 'Map', 'Insertframe', 'Insertcode'";
				if ($show_page=="true") {
					$tool .= ", 'PageBreak', 'Subtitle'";
				}
				$tool .= ", 'Template', 'Background', '|',
				'Horizontal', 'Date', 'Time', 'Spechars', '|',
				'Inserttable', 'Deletetable', 'Insertparagraphbeforetable', 'Insertrow', 'Deleterow', 'Insertcol', 'Deletecol', 'Mergecells', 'Mergeright', 'Mergedown', 'Splittocells', 'Splittorows', 'Splittocols', 'Charts', '|',
				'Print', 'Preview', 'Searchreplace', 'Help']";
			} else {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					$tool = "['Source',";
				} else {
					$tool = '[';
				}
				$tool .= "'Fullscreen', '|', " . $toolvalue . "]";
			}
			$tool = str_ireplace(array('"Simpleupload", ', ', "Simpleupload"',"'Simpleupload', ", ", 'Simpleupload'", '"Simpleupload",', ',"Simpleupload"',"'Simpleupload',", ",'Simpleupload'"), '', $tool);
			$str .= "<script type=\"text/javascript\">\r\n";
			$opt = array();
			if($tool) {$opt[] = "toolbars:[".$tool."]";}
			if($theme && file_exists(CMS_PATH.'statics/js/ueditor/themes/'.$theme.'/') && $theme!='default') {$opt[] = "theme:'".$theme."'";}
			if($language && file_exists(CMS_PATH.'statics/js/ueditor/lang/'.$language.'/'.$language.'.js') && $language!='zh-cn') {$opt[] = "lang:'".$language."'";}
			$opt[] = "topOffset:\"".(IS_ADMIN && $modelid==-2 ? 40 : 0)."\"";
			$opt[] = "initialFrameWidth:\"".$width."\"";
			$opt[] = "initialFrameHeight:".$height;
			$opt[] = "autoHeightEnabled:".$autoHeightEnabled;
			$opt[] = "autoFloatEnabled:".$autoFloatEnabled;
			$opt[] = "allowDivTransToP:".($div2p ? 'true' : 'false');
			$opt[] = "anchorDqEnabled:".($anchorduiqi ? 'true' : 'false');
			$opt[] = "imageDqEnabled:".($imageduiqi ? 'true' : 'false');
			$opt[] = "videoDqEnabled:".($videoduiqi ? 'true' : 'false');
			$opt[] = "musicDqEnabled:".($musicduiqi ? 'true' : 'false');
			$enter ? $opt[] = "enterTag:'br'" : '';
			$str .= "$(function(){\n";
			$str .= "var editor_{$textareaid} = new baidu.editor.ui.Editor({ismobile: ".(is_mobile() ? 1 : 0).",UEDITOR_HOME_URL:'".WEB_PATH."statics/js/ueditor/',serverUrl:'".WEB_PATH.(trim(str_ireplace(CMS_PATH, '', SELF_DIR), DIRECTORY_SEPARATOR) ? trim(str_ireplace(CMS_PATH, '', SELF_DIR), DIRECTORY_SEPARATOR).'/' : '').SELF."?m=404&c=index&a=ueditor&module=".$module."&catid=".$catid."&token=".csrf_hash()."&is_wm=".intval($watermark)."&is_esi=".intval($enablesaveimage)."&attachment=".intval($attachment)."&image_reduce=".intval($image_reduce)."&siteid=".intval($siteid)."',".join(",",$opt)."});editor_{$textareaid}.render('$textareaid');\n";
			$str .= "dr_post_addfunc(function(){\n";
			$str .= "if(UE.getEditor(\"$textareaid\").queryCommandState('source')!=0){\n";
			$str .= "UE.getEditor(\"$textareaid\").execCommand('source');\n";
			$str .= "}\n";
			$str .= "})\n";
			$str .= "})\n";
			if (!intval($enablesaveimage)) {
				$str .= "function dr_editor_down_img_".$textareaid."(){
var index = layer.load(2, {
    shade: [0.3,'#fff'], //0.1透明度的白色背景
    time: 100000000
});
$.ajax({
    type: 'POST',
    url: '".SELF."?m=attachment&c=attachments&a=down_img&is_iframe=1&module=".$module."&catid=".$catid."&args=".$p."&authkey=".$authkey."&token=".csrf_hash()."',
    dataType: 'json',
    data: { value: UE.getEditor('".$textareaid."').getContent(), ".SYS_TOKEN_NAME.": $(\"#myform input[name='".SYS_TOKEN_NAME."']\").val() },
    success: function (json) {
        layer.close(index);
        // token 更新
        if (json.token) {
            var token = json.token;
            $(\"#myform input[name='\"+token.name+\"']\").val(token.value);
        }
        if (json.code == 0) {
            dr_tips(0, json.msg, json.data.time);
        } else {
            
            var width = '500px';
            var height = '70%';
        
            if (is_mobile()) {
                width = '95%';
                height = '90%';
            }
        
            layer.open({
                type: 2,
                title: '',
                fix:true,
                scrollbar: false,
                maxmin: false,
                resize: true,
                shadeClose: true,
                shade: 0,
                area: [width, height],
                btn: ['确定', '取消'],
                yes: function(index, layero){
                    // 延迟加载
                    var loading = layer.load(2, {
                        shade: [0.3,'#fff'], //0.1透明度的白色背景
                        time: 100000000
                    });
                    var body = layer.getChildFrame('body', index);
                    $.ajax({type: 'POST',dataType:'json', url: json.msg, data: $(body).find('#myform').serialize(),
                        success: function(json) {
                            layer.close(loading);
                            if (json.code) {
                                layer.close(index);
                                UE.getEditor('".$textareaid."').setContent(json.data);
                                dr_tips(1, json.msg);
                            } else {
                                dr_tips(0, json.msg, json.data.time);
                            }
                            return false;
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError) {
                            dr_ajax_alert_error(HttpRequest, this, thrownError);
                        }
                    });
                    return false;
                },
                success: function(layero, index){
                    // 主要用于后台权限验证
                    var body = layer.getChildFrame('body', index);
                    var json = $(body).html();
                    if (json.indexOf('\"code\":0') > 0 && json.length < 500){
                        var obj = JSON.parse(json);
                        layer.close(index);
                        dr_tips(0, obj.msg);
                    }
                },
                content: json.msg+'&is_iframe=1'
            });
            
            
        }
    },
    error: function(HttpRequest, ajaxOptions, thrownError) {
        dr_ajax_alert_error(HttpRequest, this, thrownError);
    }
});
            }\n";
			}
			$str .= '</script>';
		}
		$ext_str = '';
		if (isset($show_bottom_boot) && $show_bottom_boot) {
			$ext_str .= '<div class="mt-checkbox-inline" style="margin-top: 10px;">';
			if (isset($modelid) && $modelid) {
				$model_arr = getcache('model', 'commons');
				$MODEL = $model_arr[$modelid];
				unset($model_arr);
				$cache = pc_base::load_sys_class('cache');
				$sitemodel = $cache->get('sitemodel');
				$form_cache = $sitemodel[$MODEL['tablename']];
				$ext_str .= '
					 <label style="margin-bottom: 5px;" class="mt-checkbox mt-checkbox-outline">
					  <input name="is_auto_description_'.$textareaid.'" type="checkbox" '.($tool_select_1 ? 'checked' : '').' value="1"> '.L('提取内容').' <span></span>
					 </label><label style="width: 80px;margin-right: 15px;"><input type="text" name="auto_description_'.$textareaid.'" value="'.(isset($form_cache['setting']['desc_limit']) && intval($form_cache['setting']['desc_limit']) ? intval($form_cache['setting']['desc_limit']) : 200).'" class="form-control" style="width: 80px;"></label><label style="margin-right: 15px;">'.L('作为描述信息').'</label>';
				$ext_str .= '     <label style="margin-bottom: 5px;" class="mt-checkbox mt-checkbox-outline">
					  <input name="is_auto_thumb_'.$textareaid.'" type="checkbox" '.($tool_select_2 ? 'checked' : '').' value="1"> '.L('提取第').' <span></span>
					 </label><label style="width: 80px;margin-right: 15px;"><input type="text" name="auto_thumb_'.$textareaid.'" value="1" class="form-control" style="width: 80px;"></label><label style="margin-right: 15px;">'.L('个图片为缩略图').'</label>';
			}
			if (!intval($enablesaveimage)) {
				$ext_str .= '
				 <label style="margin-bottom: 5px;" class="mt-checkbox mt-checkbox-outline">
				  <input name="is_auto_down_img_'.$textareaid.'" type="checkbox" '.($tool_select_3 ? 'checked' : '').' value="1"> '.L('下载远程图片').' <span></span>
				 </label>';
				$ext_str .= '
					 <label style="margin-bottom: 5px;">
					  <a class="btn blue btn-xs" onclick="dr_editor_down_img_'.$textareaid.'()"> <i class="fa fa-download"></i> '.L('一键下载远程图片').'</a>
					 </label>';
			}
			$ext_str .= '
				 <label style="margin-bottom: 5px;" class="mt-checkbox mt-checkbox-outline">
				  <input name="is_remove_a_'.$textareaid.'" type="checkbox" '.($tool_select_4 ? 'checked' : '').' value="1"> '.L('去除站外链接').' <span></span>
				 </label>';
			$ext_str .= '</div>';
		}
		$ext_str .= "<div class='editor_bottom'>";
		$ext_str .= load_js(JS_PATH.'h5upload/h5editor.js');
		$ext_str .= "<div class='cke_footer'>";
		if ($show_page=="true") {
			$ext_str .= "<a href='javascript:insert_page(\"$textareaid\")' class=\"btn blue btn-sm\"> <i class=\"fa fa-plus\"></i> ".L('pagebreak')."</a><a href='javascript:insert_page_title(\"$textareaid\")' class=\"btn green btn-sm\"> <i class=\"fa fa-indent\"></i> ".L('subtitle')."</a>";
		}
		if($allowupload) {
			$ext_str.="<a onclick=\"h5upload('".SELF."', 'h5upload', '".L('attachmentupload')."','{$textareaid}','','{$p}','{$module}','{$catid}','{$authkey}',".SYS_EDITOR.");return false;\" href=\"javascript:void(0);\" class=\"btn red btn-sm\"> <i class=\"fa fa-plus\"></i> ".L('attachmentupload')."</a>";
		}
		$ext_str .= "</div>";
		if ($show_page=="true") {
			$ext_str .= "<div id='page_title_div' class='page_".$textareaid."_div'><div class='title'>".L('subtitle')."<span id='msg_page_title_value' class='msg_page_".$textareaid."_value'></span><a class='close' href='javascript:;' onclick='javascript:$(\".page_".$textareaid."_div\").hide();'><span>×</span></a></div><div class='page_content'><label><input name='page_title_value' id='page_title_value' class='page_".$textareaid."_value input-text' value=''></label>&nbsp;<label><input type='button' class='button' value='".L('submit')."' onclick=insert_page_title(\"$textareaid\",1)></label></div></div>";
		}
		$ext_str .= "</div>";
		$str .= $ext_str;
		return $str;
	}
	
	/**
	 * 
	 * @param string $name 表单名称
	 * @param int $id 表单id
	 * @param string $value 表单默认值
	 * @param string $moudle 模块名称
	 * @param int $catid 栏目id
	 * @param int $size 表单大小
	 * @param string $class 表单风格
	 * @param string $ext 表单扩展属性 如果 js事件等
	 * @param string $alowexts 允许图片格式
	 * @param array $thumb_setting 
	 * @param int $watermark_setting  0或1
	 * @param int $attachment
	 * @param int $image_reduce
	 * @param int $chunk
	 */
	public static function images($name, $id = '', $value = '', $moudle='', $catid='', $size = 50, $class = '', $ext = '', $alowexts = '',$thumb_setting = array(),$watermark_setting = 0,$attachment = 0, $image_reduce = '', $chunk = 0, $upload_maxsize = 0) {
		$siteid = pc_base::load_sys_class('input')->get('siteid') ? pc_base::load_sys_class('input')->get('siteid') : get_siteid();
		if(!$id) $id = $name;
		if(!$size) $size= 50;
		if(!empty($thumb_setting) && count($thumb_setting)) $thumb_ext = $thumb_setting[0].','.$thumb_setting[1];
		else $thumb_ext = ',';
		if(!$alowexts) $alowexts = 'jpg|jpeg|gif|bmp|png|webp';
		$str = load_js(JS_PATH.'h5upload/h5editor.js');
		$value = new_html_special_chars($value);
		$authkey = upload_key("$siteid,1,$alowexts,$upload_maxsize,1,$thumb_ext,$watermark_setting,$attachment,$image_reduce,$chunk");
		$p = dr_authcode(array(
			'siteid' => $siteid,
			'file_upload_limit' => 1,
			'file_types_post' => $alowexts,
			'size' => $upload_maxsize,
			'allowupload' => 1,
			'thumb_width' => isset($thumb_setting[0]) && $thumb_setting[0] ? $thumb_setting[0] : '',
			'thumb_height' => isset($thumb_setting[1]) && $thumb_setting[1] ? $thumb_setting[1] : '',
			'watermark_enable' => $watermark_setting,
			'attachment' => $attachment,
			'image_reduce' => $image_reduce,
			'chunk' => $chunk,
		), 'ENCODE');
		return $str."<div class=\"row fileupload-buttonbar\" id=\"fileupload_{$id}\"><div class=\"col-lg-12\"><label><input type=\"hidden\" name=\"$name\" id=\"$id\" value=\"$value\"/><button type=\"button\" onclick=\"javascript:h5upload('".SELF."', '{$id}_images', '".L('attachmentupload')."','{$id}','submit_images','{$p}','{$moudle}','{$catid}','{$authkey}',".SYS_EDITOR.")\" class=\"btn btn-sm green\"> <i class=\"fa fa-plus\"></i> ".L('imagesupload')."</button></label> <label><button onclick=\"fileupload_file_remove('{$id}');\" type=\"button\" class=\"btn btn-sm red {$id}-delete\"".($value ? "" : " style=\"display:none\"")."><i class=\"fa fa-trash\"></i><span> ".L('delete')." </span></button></label></div></div><div id='dr_".$id."_files_row' class='file_row_html files_row'>".($value ? "<div class=\"files_row_preview preview\"><a href=\"javascript:preview('".dr_get_file($value)."');\"><img src=\"".(dr_is_image(dr_get_file($value)) ? dr_get_file($value) : WEB_PATH."api.php?op=icon&fileext=".fileext(dr_get_file($value)))."\"></a></div>" : "")."</div>";
	}

	/**
	 * 
	 * @param string $name 表单名称
	 * @param int $id 表单id
	 * @param string $value 表单默认值
	 * @param string $moudle 模块名称
	 * @param int $catid 栏目id
	 * @param int $size 表单大小
	 * @param string $class 表单风格
	 * @param string $ext 表单扩展属性 如果 js事件等
	 * @param string $alowexts 允许上传的文件格式
	 * @param array $file_setting 
	 * @param int $attachment
	 * @param int $image_reduce
	 * @param int $chunk
	 */
	public static function upfiles($name, $id = '', $value = '', $moudle='', $catid='', $size = 50, $class = '', $ext = '', $alowexts = '',$file_setting = array(),$attachment = 0, $image_reduce = '', $chunk = 0, $upload_maxsize = 0) {
		$siteid = pc_base::load_sys_class('input')->get('siteid') ? pc_base::load_sys_class('input')->get('siteid') : get_siteid();
		if(!$id) $id = $name;
		if(!$size) $size= 50;
		if(!empty($file_setting) && count($file_setting)) $file_ext = $file_setting[0].','.$file_setting[1];
		else $file_ext = ',';
		if(!$alowexts) $alowexts = 'jpg|rar|zip';
		$str = load_js(JS_PATH.'h5upload/h5editor.js');
		$authkey = upload_key("$siteid,1,$alowexts,$upload_maxsize,1,$file_ext,,$attachment,$image_reduce,$chunk");
		$p = dr_authcode(array(
			'siteid' => $siteid,
			'file_upload_limit' => 1,
			'file_types_post' => $alowexts,
			'size' => $upload_maxsize,
			'allowupload' => 1,
			'thumb_width' => isset($file_setting[0]) && $file_setting[0] ? $file_setting[0] : '',
			'thumb_height' => isset($file_setting[1]) && $file_setting[1] ? $file_setting[1] : '',
			'watermark_enable' => '',
			'attachment' => $attachment,
			'image_reduce' => $image_reduce,
			'chunk' => $chunk,
		), 'ENCODE');
		return $str."<div class=\"row fileupload-buttonbar\" id=\"fileupload_{$id}\"><div class=\"col-lg-12\"><label><input type=\"hidden\" name=\"$name\" id=\"$id\" value=\"$value\"/><button type=\"button\" onclick=\"javascript:h5upload('".SELF."', '{$id}_files', '".L('attachmentupload')."','{$id}','submit_files','{$p}','{$moudle}','{$catid}','{$authkey}',".SYS_EDITOR.")\" class=\"btn btn-sm green\"> <i class=\"fa fa-plus\"></i> ".L('filesupload')."</button></label> <label><button onclick=\"fileupload_file_remove('{$id}');\" type=\"button\" class=\"btn btn-sm red {$id}-delete\"".($value ? "" : " style=\"display:none\"")."><i class=\"fa fa-trash\"></i><span> ".L('delete')." </span></button></label></div></div><div id='dr_".$id."_files_row' class='file_row_html files_row'>".($value ? "<div class=\"files_row_preview preview\"><a href=\"javascript:preview('".dr_get_file($value)."');\"><img src=\"".(dr_is_image(dr_get_file($value)) ? dr_get_file($value) : WEB_PATH."api.php?op=icon&fileext=".fileext(dr_get_file($value)))."\"></a></div>" : "")."</div>";
	}
	
	/**
	 * 日期时间控件
	 * 
	 * @param $name 控件name，id
	 * @param $value 选中值
	 * @param $isdatetime 是否显示时间
	 * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
	 * @param $showweek 是否显示周，使用，true | false
	 */
	public static function date($name, $value = '', $isdatetime = 0, $loadjs = 0, $showweek = 'true', $timesystem = 1, $modelid = 0, $datepicker = 0, $is_left = 0, $color = '', $width = '') {
		if($value == '0000-00-00 00:00:00') $value = '';
		$id = preg_match("/\[(.*)\]/", $name, $m) ? $m[1] : $name;
		$str = '';
		if($isdatetime==1 || !$isdatetime) {
			// 表单宽度设置
			$width = is_mobile() ? '100%' : ($width ? $width : '100%');
			// 风格
			$style = ' style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
			$str .= load_css(JS_PATH.'bootstrap-datepicker/css/bootstrap-datepicker.min.css');
			$str .= load_css(JS_PATH.'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
			$str .= load_js(JS_PATH.'bootstrap-datepicker/js/bootstrap-datepicker.min.js');
			$str .= load_js(JS_PATH.'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');
			$model_db = pc_base::load_model('sitemodel_model');
			$model = $model_db->get_one(array('modelid'=>$modelid));
			$module_setting = dr_string2array($model['setting']);
			$updatetime_select = isset($module_setting['updatetime_select']) && $module_setting['updatetime_select'] ? $module_setting['updatetime_select'] : '';
			// 字段默认值
			//!$value && $value = SYS_TIME;
			if ($value == 'SYS_TIME' || (defined('ROUTE_M') && ROUTE_M=='content' && $model && $id == 'updatetime')) {
				$value = SYS_TIME;
			} elseif (strpos((string)$value, '-') === 0) {
			} elseif (strpos((string)$value, '-') !== false) {
				$value = strtotime($value);
			}
			$value = $isdatetime ? dr_date($value, 'Y-m-d H:i:s') : dr_date($value, 'Y-m-d');
			$shuru = '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" class="form-control dateright field_date_'.$id.'">';
			$tubiao = '<span class="input-group-btn">
				<button class="btn default date-set"'.($color ? ' style="color: '.$color.';"' : '').' type="button">
					<i class="fa fa-calendar"></i>
				</button>
			</span>';
			if($datepicker) {
				$str .= '<div class="form-date input-group"><div class="input-group date"'.$style.'>';
				$str .= $is_left ? $shuru.$tubiao : $tubiao.$shuru;
				$str .= '</div></div>';
				defined('ROUTE_M') && ROUTE_M=='content' && $model && $id == 'updatetime' && $str .= '<div class="mt-checkbox-inline"><label class="mt-checkbox mt-checkbox-outline"><input name="no_time"'.(isset($updatetime_select) && $updatetime_select ? ' checked' : '').' class="dr_no_time" type="checkbox" value="1" /> '.L('不更新').'<span></span></label></div>';
			} else {
				$str .= '<div class="formdate"><div class="form-date input-group"><div class="input-group date">';
				$str .= $is_left ? $shuru.$tubiao : $tubiao.$shuru;
				$str .= '</div></div></div>';
				defined('ROUTE_M') && ROUTE_M=='content' && $model && $id == 'updatetime' && $str .= '<div class="mt-checkbox-inline"><label class="mt-checkbox mt-checkbox-outline"><input name="no_time"'.(isset($updatetime_select) && $updatetime_select ? ' checked' : '').' class="dr_no_time" type="checkbox" value="1" /> '.L('不更新').'<span></span></label></div>';
			}
			if ($isdatetime) {
				// 日期 + 时间
				$str.= '
				<script type="text/javascript">
				$(function(){
					$(".field_date_'.$id.'").datetimepicker({
						format: "yyyy-mm-dd hh:ii:ss",
						autoclose: true,
						todayBtn: "linked"
					});
				});
				</script>
				';
			} else {
				// 日期
				$str.= '
				<script type="text/javascript">
				$(function(){
					$(".field_date_'.$id.'").datepicker({
						format: "yyyy-mm-dd",
						autoclose: true,
						todayBtn: "linked"
					});
				});
				</script>
				';
			}
		}
		if($isdatetime==2 || $isdatetime==3) {
			// 表单宽度设置
			$width = is_mobile() ? '100%' : ($width ? $width : '100%');
			// 风格
			$style = ' style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
			$format = (int)$isdatetime==2 ? 'H:i:s' : 'H:i';
			// 字段默认值
			if ($value == 'SYS_TIME') {
				$value = dr_date(SYS_TIME, $format);
			}
			$str .= load_css(JS_PATH.'bootstrap-timepicker/css/bootstrap-timepicker.min.css');
			$str .= load_js(JS_PATH.'bootstrap-timepicker/js/bootstrap-timepicker.min.js');
			$shuru = '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" class="form-control timepicker dateright field_time_'.$id.'">';
			$tubiao = '<span class="input-group-btn">
				<button class="btn default"'.($color ? ' style="color: '.$color.';"' : '').' type="button">
					<i class="fa fa-clock-o"></i>
				</button>
			</span>';
			if($datepicker) {
				$str .= '<div class="form-date input-group"><div class="input-group"'.$style.'>';
				$str .= $is_left ? $shuru.$tubiao : $tubiao.$shuru;
				$str .= '</div></div>';
			} else {
				$str .= '<div class="formdate"><div class="form-date input-group"><div class="input-group">';
				$str .= $is_left ? $shuru.$tubiao : $tubiao.$shuru;
				$str .= '</div></div></div>';
			}
			$str.= '
			<script>
			$(function(){
				$(".field_time_'.$id.'").timepicker({
					autoclose: true,
					defaultTime:"'.($value ? $value : dr_date(SYS_TIME, $format)).'",
					minuteStep: 1,
					secondStep: 1,
					showSeconds: '.($format == 'H:i:s' ? 'true' : 'false').',
					showMeridian: false
				});
				$(".timepicker").parent(".input-group").on("click", ".input-group-btn", function(e){
					$(this).parent(".input-group").find(".timepicker").timepicker("showWidget");
				});
				$( document ).scroll(function(){
					$(".field_time_'.$id.'").timepicker("place");
				});
			});
			</script>
			';
		}
		return $str;
	}

	/**
	 * 栏目选择
	 * @param string $dir 栏目缓存目录
	 * @param intval/array $catid 别选中的ID，多选是可以是数组
	 * @param string $str 属性
	 * @param string $default_option 默认选项
	 * @param intval $modelid 按所属模型筛选
	 * @param intval $type 栏目类型
	 * @param intval $onlysub 只可选择子栏目
	 * @param intval $siteid 如果设置了siteid 那么则按照siteid取
	 */
	public static function select_category($dir = '',$catid = 0, $str = '', $default_option = '', $modelid = 0, $type = -1, $onlysub = 0,$siteid = 0,$is_push = 0) {
		$tree = pc_base::load_sys_class('tree');
		if(!$siteid) $siteid = get_siteid();
		if (!$dir) {
			$dir = 'module/category-'.$siteid.'-data';
		}
		$result = getcache('cache', $dir);
		//加载权限表模型 ,获取会员组ID值,以备下面投入判断用
		if($is_push=='1'){
			$priv = pc_base::load_model('category_priv_model');
			$user_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		}
		if (is_array($result)) {
			foreach($result as $r) {
 				//检查当前会员组，在该栏目处是否允许投稿？
				if($is_push=='1' and $r['child']=='0'){
					if(IS_ADMIN){
						$sql = array('catid'=>$r['catid'],'roleid'=>$user_groupid,'action'=>'add','is_admin'=>1);
					}else{
						$sql = array('catid'=>$r['catid'],'roleid'=>$user_groupid,'action'=>'add','is_admin'=>0);
					}
					$array = $priv->get_one($sql);
					if(!$array){
						continue;	
					}
				}
				if($siteid != $r['siteid'] || ($type >= 0 && $r['type'] != $type)) continue;
				$r['selected'] = '';
				if(is_array($catid)) {
					$r['selected'] = in_array($r['catid'], $catid) ? 'selected' : '';
				} elseif(is_numeric($catid)) {
					$r['selected'] = $catid==$r['catid'] ? 'selected' : '';
				}
				$r['html_disabled'] = "0";
				if (!empty($onlysub) && $r['child'] != 0) {
					$r['html_disabled'] = "1";
				}
				$categorys[$r['catid']] = $r;
				if($modelid && $r['modelid']!= $modelid ) unset($categorys[$r['catid']]);
			}
		}

		$string = '<label><select '.$str.(!strpos($str,'class') ? ' class="form-control"' : '').(dr_count($categorys) > 30 && strpos($str,'bs-select') ? ' data-live-search="true"' : '').'>';
		if($default_option) $string .= "<option value='0'>$default_option</option>";

		$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
		$str2 = "<optgroup label='\$spacer \$catname'></optgroup>";

		$tree->init($categorys);
		$string .= $tree->get_tree_category(0, $str, $str2);
			
		$string .= '</select></label>';
		return $string;
	}

	public static function select_linkage($keyid = 0, $name = 'parentid', $id ='') {
		$linkage_db = pc_base::load_model('linkage_model');
		$result = $linkage_db->get_one(array('id'=>$keyid));
		$id = $id ? $id : $name;
		return menu_linkage($result['code'],$id,0);
	}
	/**
	 * 下拉选择框
	 */
	public static function select($array = array(), $id = 0, $str = '', $default_option = '') {
		$string = '<label><select '.$str.(!strpos($str,'class') ? ' class="form-control"' : '').'>';
		$default_selected = (empty($id) && $default_option) ? 'selected' : '';
		if($default_option) $string .= "<option value='' $default_selected>$default_option</option>";
		if(!is_array($array) || count($array)== 0) return false;
		if(is_array($id)) {
			foreach($array as $key=>$value) {
				$selected = in_array($key, $id) ? 'selected' : '';
				$string .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
			$string .= '</select></label>';
		} else {
			$ids = array();
			if(isset($id)) $ids = explode(',', $id);
			foreach($array as $key=>$value) {
				$selected = in_array($key, $ids) ? 'selected' : '';
				$string .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
			$string .= '</select></label>';
		}
		return $string;
	}
	
	/**
	 * 复选框
	 * 
	 * @param $array 选项 二维数组
	 * @param $id 默认选中值，多个用 '逗号'分割
	 * @param $str 属性
	 * @param $defaultvalue 是否增加默认值 默认值为 -99
	 * @param $width 宽度
	 */
	public static function checkbox($array = array(), $id = '', $str = '', $defaultvalue = '', $width = 0, $field = '') {
		$string = '';
		if ($id != '') {
			if (!is_array($id)) {
				$id = strpos($id, ',') !== false ? explode(',', $id) : array($id);
			}
		}
		if($defaultvalue) $string .= '<input type="hidden" '.$str.' value="-99">';
		$i = 1;
		$string .= '<div class="mt-checkbox-inline">';
		foreach($array as $key=>$value) {
			$key = trim($key);
			$checked = ($id && in_array($key, $id)) ? 'checked' : '';
			$string .= '<label class="mt-checkbox mt-checkbox-outline"';
			if($width) $string .= ' style="width:'.$width.'px"';
			$string .= '>';
			$string .= '<input type="checkbox" '.$str.' id="'.$field.'_'.$i.'" '.$checked.' value="'.new_html_special_chars($key).'"> '.new_html_special_chars($value);
			$string .= '<span></span></label>';
			$i++;
		}
		$string .= '</div>';
		return $string;
	}

	/**
	 * 单选框
	 * 
	 * @param $array 选项 二维数组
	 * @param $id 默认选中值
	 * @param $str 属性
	 */
	public static function radio($array = array(), $id = 0, $str = '', $width = 0, $field = '') {
		$string = '';
		$string .= '<div class="mt-radio-inline">';
		foreach($array as $key=>$value) {
			$checked = trim((string)$id)==trim((string)$key) ? 'checked' : '';
			$string .= '<label class="mt-radio mt-radio-outline"';
			if($width) $string .= ' style="width:'.$width.'px"';
			$string .= '>';
			$string .= '<input type="radio" '.$str.' id="'.$field.'_'.new_html_special_chars($key).'" '.$checked.' value="'.$key.'"> '.$value;
			$string .= '<span></span></label>';
		}
		$string .= '</div>';
		return $string;
	}
	/**
	 * 模板选择
	 * 
	 * @param $style  风格
	 * @param $module 模块
	 * @param $id 默认选中值
	 * @param $str 属性
	 * @param $pre 模板前缀
	 */
	public static function select_template($style, $module, $id = '', $str = '', $pre = '') {
		$templatedir = TPLPATH.$style.DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR;
		$confing_path = TPLPATH.$style.DIRECTORY_SEPARATOR.'config.php';
		$localdir = str_replace(array('/', '\\'), '', SYS_TPL_ROOT).'|'.$style.'|pc|'.$module;
		$templates = glob($templatedir.$pre.'*.html');
		if(empty($templates)) {
			$style = 'default';
			$templatedir = TPLPATH.$style.DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR;
			$confing_path = TPLPATH.$style.DIRECTORY_SEPARATOR.'config.php';
			$localdir = str_replace(array('/', '\\'), '', SYS_TPL_ROOT).'|'.$style.'|pc|'.$module;
			$templates = glob($templatedir.$pre.'*.html');
		}
		if(empty($templates)) return false;
		$files = @array_map('basename', $templates);
		$names = array();
		if(file_exists($confing_path)) {
			$names = include $confing_path;
		}
		$templates = array();
		if(is_array($files)) {
			foreach($files as $file) {
				$key = substr($file, 0, -5);
				$templates[$key] = isset($names['file_explan'][$localdir][$file]) && !empty($names['file_explan'][$localdir][$file]) ? $names['file_explan'][$localdir][$file].'('.$file.')' : $file;
			}
		}
		ksort($templates);
		return self::select($templates, $id, $str,L('please_select'));
	}
	
	/**
	 * 验证码
	 * @param string $id            生成的验证码ID
	 * @param integer $code_len     生成多少位验证码
	 * @param integer $font_size    验证码字体大小
	 * @param integer $width        验证图片的宽
	 * @param integer $height       验证码图片的高
	 * @param string $font          使用什么字体，设置字体的URL
	 * @param string $font_color    字体使用什么颜色
	 * @param string $background    背景使用什么颜色
	 */
	public static function checkcode($id = 'checkcode',$code_len = 4, $font_size = 20, $width = 130, $height = 50, $font = '', $font_color = '', $background = '') {
		$js = '<div id="voices" style="width: 0px; height: 0px; overflow:hidden; text-indent:-99999px;"></div><script type="text/javascript">function voice() {$(\'#voices\').html(\'<audio id="audio" controls="controls" autoplay="autoplay"><source src="'.trim(FC_NOW_HOST, '/').WEB_PATH.'api.php?op=voice&\'+Math.random()+\'" type="audio/mpeg"></audio>\');$(\'#audio\').get(0).play();$(\'#captcha\').val(\'\');$(\'#captcha\').focus();}</script>';
		return "<img id='$id' onclick='this.src=this.src+Math.random();voice();' src='".trim(FC_NOW_HOST, '/').WEB_PATH."api.php?op=checkcode&code_len=$code_len&font_size=$font_size&width=$width&height=$height&font_color=".urlencode($font_color)."&background=".urlencode($background)."&n=".SYS_TIME."' style=\"vertical-align: middle;\">".$js;
	}
	/**
	 * url  规则调用
	 * 
	 * @param $module 模块
	 * @param $file 文件名
	 * @param $ishtml 是否为静态规则
	 * @param $id 选中值
	 * @param $str 表单属性
	 * @param $default_option 默认选项
	 */
	public static function urlrule($module, $file, $ishtml, $id, $str = '', $default_option = '') {
		if(!$module) $module = 'content';
		$urlrules = getcache('urlrules_detail','commons');
		$array = array();
		foreach($urlrules as $roleid=>$rules) {
			if($rules['module'] == $module && $rules['file']==$file && $rules['ishtml']==$ishtml) $array[$roleid] = $rules['example'];
		}
		
		return form::select($array, $id,$str,$default_option);
	}
}
?>