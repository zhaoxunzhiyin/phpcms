function h5upload(uploadid, name, textareaid, funcName, args, module, catid, authkey) {
	var w = '76%';
	var h = '68%';
	if (is_mobile()) {
		w = h = '90%';
	}
	var args = args ? '&args='+args : '';
	var setting = '&module='+module+'&catid='+catid+'&authkey='+authkey;
	var url = 'index.php?m=attachment&c=attachments&a=h5upload'+args+setting;
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
		if (funcName=='thumb_images') {
			//var in_content = $(body).find("#att-status").html().substring(1);
			var in_content = $DW.$("#att-status").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			if(!IsImg(in_content)) {
				Dialog.alert('选择的类型必须为图片类型');
				return false;
			}
			if($('#'+textareaid+'_preview').attr('src')) {
				$('#'+textareaid+'_preview').attr('src',in_content);
			}
			$('#'+textareaid).val(in_content);
		} else if (funcName=='change_images') {
			var in_content = $DW.$("#att-status").html().substring(1);
			var in_filename = $DW.$("#att-name").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			var str = $('#'+textareaid).html();
			var contents = in_content.split('|');
			var filenames = in_filename.split('|');
			$('#'+textareaid+'_tips').css('display','none');
			if(contents=='') return true;
			$.each( contents, function(i, n) {
				var ids = parseInt(Math.random() * 10000 + 10*i); 
				var filename = filenames[i];
				str += "<li id='image"+ids+"'><input type='text' name='"+textareaid+"_url[]' value='"+n+"' ondblclick='image_priview(this.value);' class='input-text'><input type='text' name='"+textareaid+"_alt[]' value='"+filename+"' class='input-textarea' placeholder='图片描述...' onfocus=\"if(this.value == this.defaultValue) this.value = ''\" onblur=\"if(this.value.replace(' ','') == '') this.value = this.defaultValue;\"> <a href='javascript:;' class='img-left'><i class='am-icon-angle-double-left am-icon-fw'></i>上移</a><a href='javascript:;' class='img-right'><i class='am-icon-angle-double-right am-icon-fw'></i>下移</a><a href=\"javascript:remove_div('image"+ids+"')\" class='img-del'>删除</a></li>";
				});
			
			$('#'+textareaid).html(str);
		} else if (funcName=='change_thumbs') {
			var in_content = $DW.$("#att-status").html().substring(1);
			var in_filename = $DW.$("#att-name").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			var str = $('#'+textareaid).html();
			var contents = in_content.split('|');
			var filenames = in_filename.split('|');
			$('#'+textareaid+'_tips').css('display','none');
			if(contents=='') return true;
			$.each( contents, function(i, n) {
				var ids = parseInt(Math.random() * 10000 + 10*i); 
				var filename = filenames[i];
				str += "<li id='image"+ids+"'><div class='preview'><input type='hidden' name='"+textareaid+"_url[]' value='"+n+"'><img src='"+n+"' id='thumb_preview'></div><div class='intro'><textarea name='"+textareaid+"_alt[]' placeholder='图片描述...' onfocus=\"if(this.value == this.defaultValue) this.value = ''\" onblur=\"if(this.value.replace(' ','') == '') this.value = this.defaultValue;\">"+filename+"</textarea></div><div class='action'><a href='javascript:;' class='img-left'><i class='am-icon-angle-double-left am-icon-fw'></i>左移</a><a href='javascript:;' class='img-right'><i class='am-icon-angle-double-right am-icon-fw'></i>右移</a><a href=\"javascript:remove_div('image"+ids+"')\" class='img-del'>删除</a></div></li>";
				});
			
			$('#'+textareaid).html(str);
		} else if (funcName=='change_multifile') {
			var in_content = $DW.$("#att-status").html().substring(1);
			var in_filename = $DW.$("#att-name").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			var str = '';
			var contents = in_content.split('|');
			var filenames = in_filename.split('|');
			$('#'+textareaid+'_tips').css('display','none');
			if(contents=='') return true;
			$.each( contents, function(i, n) {
				var ids = parseInt(Math.random() * 10000 + 10*i); 
				var filename = filenames[i];
				str += "<li id='multifile"+ids+"'><input type='text' name='"+textareaid+"_fileurl[]' value='"+n+"' class='input-text'><input type='text' name='"+textareaid+"_filename[]' value='"+filename+"' class='input-textarea' placeholder='附件说明...' onfocus=\"if(this.value == this.defaultValue) this.value = ''\" onblur=\"if(this.value.replace(' ','') == '') this.value = this.defaultValue;\"> <a href='javascript:;' class='img-left'><i class='am-icon-angle-double-left am-icon-fw'></i>上移</a><a href='javascript:;' class='img-right'><i class='am-icon-angle-double-right am-icon-fw'></i>下移</a><a href=\"javascript:remove_div('multifile"+ids+"')\">移除</a> </li>";
				});
			$('#'+textareaid).append(str);
		} else if (funcName=='submit_images') {
			var in_content = $DW.$("#att-status").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			var in_content = in_content.split('|');
			if(!IsImg(in_content[0])) {
				Dialog.alert('选择的类型必须为图片类型');
				return false;
			}
			$('#'+textareaid).attr("value",in_content[0]);
		} else if (funcName=='submit_attachment') {
			var in_content = $DW.$("#att-status").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			var in_content = in_content.split('|');
			$('#'+textareaid).attr("value",in_content[0]);
		} else if (funcName=='submit_files') {
			var in_content = $DW.$("#att-status").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			var in_content = in_content.split('|');
			$('#'+textareaid).attr("value",in_content[0]);
		} else if (funcName=='change_videoes') {
			var in_content = $DW.$("#video-paths").html().substring(1);
			var in_filename = $DW.$("#video-name").html().substring(1);
			var in_vid = $DW.$("#video-ids").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			var video_num = parseInt($("#key").val());
			var str = $('#'+textareaid).html();
			var contents = in_content.split('|');
			var fields = uploadid.split('_');
			var field = fields[0];
			var filenames = in_filename.split('|');
			var vids = in_vid.split('|');
			$('#'+textareaid+'_tips').css('display','none');
			if(contents=='') return true;
			$.each( contents, function(i, n) {
				if ($("#thumb").val()==''){
					$('#thumb').val(contents[i]);
					$('#thumb_preview').attr('src', contents[i]);
				}
				var ids = parseInt(Math.random() * 10000 + 10*i); 
				video_num = video_num + 1;
				var filename = filenames[i];
				str += "<li id=\"video_"+field+"_"+video_num+"\"><div class=\"r1\"><img src=\""+contents[i]+"\" width=\"132\" height=\"75\"><input type=\"text\" name=\""+field+"_video["+video_num+"][title]\" value=\""+filename+"\" class=\"input-text\"><input type='hidden' name='"+field+"_video["+video_num+"][videoid]' value='"+vids[i]+"'><div class=\"r2\"><span class=\"l\"><label>排序</label><input type='text' name='"+field+"_video["+video_num+"][listorder]' value='"+video_num+"' class=\"input-text\"></span><span class=\"r\"> <a href=\"javascript:remove_div('video_"+field+"_"+video_num+"')\">移除</a></span></li>";
				});
			$('#key').val(video_num);
			$('#'+textareaid).html(str);
		} else if (funcName=='preview') {
			var in_content = $DW.$("#att-status").html().substring(1);
			if(in_content == '') {diag.close();return false;}
			$('#'+textareaid).val(in_content);
			$('#'+textareaid+'_s').attr('src', in_content);
		} else {
			var in_filename = $DW.$("#att-name").html();
			var in_content = $DW.$("#att-status").html();
			var del_content = $DW.$("#att-status-del").html();
			if(in_content == '') {diag.close();return false;}
			var data = in_content.substring(1).split('|');
			var filenames = in_filename.substring(1).split('|');
			var img = '';
			for (var n=0;n<data.length;n++){
				var filename = filenames[n];
				img += IsImg(data[n]) ? '<img src="'+data[n]+'" alt="'+filename+'" /><br />' : (IsSwf(data[n]) ? '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"><param name="quality" value="high" /><param name="movie" value="'+data[n]+'" /><embed pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="'+data[n]+'" type="application/x-shockwave-flash" width="460"></embed></object>' :'<a href="'+data[n]+'" title="'+filename+'" />'+data[n]+'</a><br />') ;
			}
			$.get("index.php?m=attachment&c=attachments&a=h5delete",{data: del_content},function(data){});
			CKEDITOR.instances[textareaid].insertHtml(img);
		}
		diag.close();
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}

function IsImg(url){
  var sTemp;
  var b=false;
  var opt="jpg|gif|png|bmp|jpeg";
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

function IsSwf(url){
	  var sTemp;
	  var b=false;
	  var opt="swf";
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