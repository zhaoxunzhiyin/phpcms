function h5upload(sysfilename, uploadid, name, textareaid, funcName, args, module, catid, authkey, syseditor) {
	var ct = $('#fileupload_' + textareaid + '_files .files_row').length;
	var w = '76%';
	var h = '68%';
	if (is_mobile()) {
		w = h = '90%';
	}
	var args = args ? '&args='+args : '';
	var setting = '&module='+module+'&catid='+catid+'&ct='+ct+'&authkey='+authkey;
	var url = sysfilename+'?m=attachment&c=attachments&a=h5upload'+args+setting;
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	var diag = new Dialog({
		id:uploadid,
		title:'<i class="fa fa-folder-open"></i> '+name,
		url:url,
		width:w,
		height:h,
		modal:true
	});
	diag.onOk = function(){
		//var body = diag.innerFrame.contentWindow.document;
		//var in_content = $(body).find("#att-status").html().substring(1);
		var in_content = $DW.$("#att-status").html().substring(1);
		var in_filename = $DW.$("#att-name").html().substring(1);
		var in_id = $DW.$("#att-id").html().substring(1);
		if(in_content == '') {diag.close();return false;}
		var contents = in_content.split('|');
		if(contents == '') return true;
		var filenames = in_filename.split('|');
		var ids = in_id.split('|');
		var str = '';
		if (funcName=='thumb_images') {
			if(!IsImg(contents[0])) {
				Dialog.alert('选择的类型必须为图片类型');
				return false;
			}
			if($('#'+textareaid+'_preview').attr('src')) {
				$('#'+textareaid+'_preview').attr('src', (IsImg(contents[0]) ? contents[0] : get_web_dir()+'api.php?op=icon&fileext='+contents[0].substring(contents[0].lastIndexOf('.')+1)));
			}
			$('#'+textareaid).val(ids[0]);
			$('#fileupload_'+textareaid).find('.mpreview').html((/^\d+$/.test(ids[0]) && $('#fileupload_'+textareaid).find('#crop_'+textareaid).attr('class') ? '<a href="javascript:crop_cut_'+textareaid+'('+ids[0]+');"><i class="fa fa-cut"></i></a>' : ''));
			$('#fileupload_'+textareaid).find('.'+textareaid+'-delete').show();
		} else if (funcName=='change_images') {
			$.each(contents, function(i, n) {
				var id = ids[i];
				var filename = filenames[i];
				str += '<div class="grid-item files_row"><div class="files_row_preview preview"><a href="javascript:preview(\''+n+'\');"><img src="'+(IsImg(n) ? n : get_web_dir()+'api.php?op=icon&fileext='+n.substring(n.lastIndexOf('.')+1))+'"></a></div><input type="hidden" class="files_row_id" name="'+textareaid+'[id][]" value="'+(id ? id : n)+'"><div class="op-btn"><label><button onclick="dr_file_remove(this, \''+textareaid+'\')" type="button" class="btn red file_delete btn-xs"><i class="fa fa-trash"></i></button></label></div><div class="col-md-12 files_show_title_html"><input placeholder="名称" class="form-control files_row_title" type="text" name="'+textareaid+'[title][]" value="'+filename+'"></div><div class="col-md-12 files_show_description_html"><textarea placeholder="描述" class="form-control files_row_description" name="'+textareaid+'[description][]"></textarea></div></div>';
			});
			$('#fileupload_'+textareaid+'_files').append(str);
			dr_slimScroll_init('.scroller_'+textareaid+'_files', 300);
		} else if (funcName=='change_files') {
			$.each(contents, function(i, n) {
				var id = ids[i];
				var filename = filenames[i];
				str += '<tr class="template-download files_row"><td style="text-align:center;width: 80px;"><div class="files_row_preview preview"><a href="javascript:preview(\''+n+'\');"><img src="'+(IsImg(n) ? n : get_web_dir()+'api.php?op=icon&fileext='+n.substring(n.lastIndexOf('.')+1))+'"></a></div></td><td class="files_show_info"><div class="row"><div class="col-md-12 files_show_title_html"><input placeholder="名称" class="form-control files_row_title" type="text" name="'+textareaid+'[title][]" value="'+filename+'"><input type="hidden" class="files_row_id" name="'+textareaid+'[id][]" value="'+(id ? id : n)+'"></div><div class="col-md-12 files_show_description_html"><textarea placeholder="描述" class="form-control files_row_description" name="'+textareaid+'[description][]"></textarea></div></div></td><td style="text-align:center;width: 80px;"><label><button onclick="dr_file_remove(this, \''+textareaid+'\')" type="button" class="btn red file_delete btn-sm"><i class="fa fa-trash"></i></button></label></td></tr>';
			});
			$('#fileupload_'+textareaid+'_files').append(str);
			dr_slimScroll_init('.scroller_'+textareaid+'_files', 300);
		} else if (funcName=='submit_images') {
			if(!IsImg(contents[0])) {
				Dialog.alert('选择的类型必须为图片类型');
				return false;
			}
			$('#'+textareaid).val(ids[0]);
			$('#dr_'+textareaid+'_files_row').html('<div class="files_row_preview preview"><a href="javascript:preview(\''+contents[0]+'\');"><img src="'+(IsImg(contents[0]) ? contents[0] : get_web_dir()+'api.php?op=icon&fileext='+contents[0].substring(contents[0].lastIndexOf('.')+1))+'"></a></div>'+(/^\d+$/.test(ids[0]) && $('#fileupload_'+textareaid).find('#crop_'+textareaid).attr('class') ? '<div class="mpreview"><a href="javascript:crop_cut_'+textareaid+'('+ids[0]+');"><i class="fa fa-cut"></i></a></div>' : ''));
			$('#fileupload_'+textareaid).find('.'+textareaid+'-delete').show();
		} else if (funcName=='submit_files') {
			$('#'+textareaid).val(ids[0]);
			$('#dr_'+textareaid+'_files_row').html('<div class="files_row_preview preview"><a href="javascript:preview(\''+contents[0]+'\');"><img src="'+(IsImg(contents[0]) ? contents[0] : get_web_dir()+'api.php?op=icon&fileext='+contents[0].substring(contents[0].lastIndexOf('.')+1))+'"></a></div>');
			$('#fileupload_'+textareaid).find('.'+textareaid+'-delete').show();
		} else if (funcName=='preview') {
			$('#'+textareaid).val(ids[0]);
			$('#'+textareaid+'_s').attr('src', (IsImg(contents[0]) ? contents[0] : get_web_dir()+'api.php?op=icon&fileext='+contents[0].substring(contents[0].lastIndexOf('.')+1)));
		} else {
			for (var n=0;n<contents.length;n++){
				str += IsImg(contents[n]) ? '<p><img src="'+contents[n]+'" alt="'+filenames[n]+'" /></p>' : (IsMp4(contents[n]) ? '<p><video class="edui-faked-video video-js" controls="" preload="none" width="420" height="280" src="'+contents[n]+'"><source src="'+contents[n]+'" type="video/mp4"/></video></p>' : (IsMp3(contents[n]) ? '<p><audio src="'+contents[n]+'" controls="controls"></audio></p>' : '<p><img style="vertical-align: middle; margin-right: 2px;" width="16" src="'+get_web_dir()+'api.php?op=icon&fileext='+contents[n].substring(contents[n].lastIndexOf('.')+1)+'" _src="'+get_web_dir()+'api.php?op=icon&fileext='+contents[n].substring(contents[n].lastIndexOf('.')+1)+'"/><a href="'+contents[n]+'" title="'+filenames[n]+'" />'+filenames[n]+'</a></p>'));
			}
			if (syseditor==1) {
				CKEDITOR.instances[textareaid].insertHtml(str);
			} else {
				UE.getEditor(textareaid).execCommand('insertHtml', str);
			}
		}
		diag.close();
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}

function fileupload_file_remove(name) {
	$('#'+name).attr('value','');
	$('#dr_'+name+'_files_row').html('');
	$('#fileupload_'+name).find('.'+name+'-delete').hide();
}

// 多文件上传删除元素
function dr_file_remove(e, name) {
	$(e).parents(".files_row").remove();
	dr_slimScroll_init('.scroller_'+name+'_files', 300);
}

// 主目录相对路径
function get_web_dir() {
	if (typeof web_dir != "undefined" && web_dir) {
		return web_dir;
	}
	return '/';
}

// 判断图片
function IsImg(url){
	var sTemp;
	var b=false;
	var opt="jpg|gif|png|bmp|jpeg|webp";
	var s=opt.toUpperCase().split("|");
	for (var i=0;i<s.length ;i++ ){
		sTemp=url.substr(url.length-s[i].length-1);
		sTemp=sTemp.toUpperCase();
		s[i]="."+s[i];
		if (s[i]==sTemp){
			b=true;
			break;
		}
	}
	return b;
}

// 判断视频
function IsMp4(url){
	var sTemp;
	var b=false;
	var opt="mp4";
	var s=opt.toUpperCase().split("|");
	for (var i=0;i<s.length ;i++ ){
		sTemp=url.substr(url.length-s[i].length-1);
		sTemp=sTemp.toUpperCase();
		s[i]="."+s[i];
		if (s[i]==sTemp){
			b=true;
			break;
		}
	}
	return b;
}

// 判断音频
function IsMp3(url){
	var sTemp;
	var b=false;
	var opt="mp3";
	var s=opt.toUpperCase().split("|");
	for (var i=0;i<s.length ;i++ ){
		sTemp=url.substr(url.length-s[i].length-1);
		sTemp=sTemp.toUpperCase();
		s[i]="."+s[i];
		if (s[i]==sTemp){
			b=true;
			break;
		}
	}
	return b;
}