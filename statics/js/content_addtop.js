var userAgent = navigator.userAgent.toLowerCase();
jQuery.browser = {
	version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
	safari: /webkit/.test( userAgent ),
	opera: /opera/.test( userAgent ),
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
};

function add_multifile(returnid) {
	var ids = parseInt(Math.random() * 10000); 
	var str = "<li id='multifile"+ids+"'><input type='text' name='"+returnid+"_fileurl[]' value='' class='input-text'><input type='text' name='"+returnid+"_filename[]' value='附件说明' placeholder='附件说明...' onfocus=\"if(this.value == this.defaultValue) this.value = ''\" onblur=\"if(this.value.replace(' ','') == '') this.value = this.defaultValue;\" class='input-textarea'> <a href='javascript:;' class='img-left'><i class='am-icon-angle-double-left am-icon-fw'></i>上移</a><a href='javascript:;' class='img-right'><i class='am-icon-angle-double-right am-icon-fw'></i>下移</a><a href=\"javascript:remove_div('multifile"+ids+"')\">移除</a> </li>";
	$('#'+returnid).append(str);
}

function set_title_color(color) {
	$('#title').css('color',color);
	$('#style_color').val(color);
}
//-----------------------
function check_content(obj) {
	//if($.browser.msie) {
		//CKEDITOR.instances[obj].insertHtml('');
		//CKEDITOR.instances[obj].focusManager.hasFocus;
	//}
	ownerDialog.close();
	return true;
}

function image_priview(img) {
	var diag = new Dialog({
		id:'image_priview',
		title:'图片查看',
		html:'<img src="'+img+'" />',
		modal:true,
		autoClose:5
	});
	diag.show();
}

function remove_div(id) {
	$('#'+id).remove();
}

$(document).on("click",".picList .img-left",function(){
	var $li=$(this).parent().parent();
	var $pre=$li.prev("li");
	$pre.insertAfter($li)
})
$(document).on("click",".picList .img-right",function(){
	var $li=$(this).parent().parent();
	var $next=$li.next("li");
	$next.insertBefore($li);
});
$(document).on("click",".picList .img-del",function(){
	$(this).parent().parent().remove();
});

$(document).on("click",".txtList .img-left",function(){
	var $li=$(this).parent();
	var $pre=$li.prev("li");
	$pre.insertAfter($li)
})
$(document).on("click",".txtList .img-right",function(){
	var $li=$(this).parent();
	var $next=$li.next("li");
	$next.insertBefore($li);
});
$(document).on("click",".txtList .img-del",function(){
	$(this).parent().remove();
});

function input_font_bold() {
	if($('#title').css('font-weight') == '700' || $('#title').css('font-weight')=='bold') {
		$('#title').css('font-weight','normal');
		$('#style_font_weight').val('');
	} else {
		$('#title').css('font-weight','bold');
		$('#style_font_weight').val('bold');
	}
}
function ruselinkurl() {
        if($('#islink').attr('checked')=='checked') {
                $('#linkurl').attr('disabled',false);
                //UE.getEditor('content').execCommand('insertHtml', '　');
                return false;
        } else {
                $('#linkurl').attr('disabled','true');
        }
}

function ChangeInput (objSelect,objInput) {
	if (!objInput) return;
	var str = objInput.value;
	var arr = str.split(",");
	for (var i=0; i<arr.length; i++){
	  if(objSelect.value==arr[i])return;
	}
	if(objInput.value=='' || objInput.value==0 || objSelect.value==0){
	   objInput.value=objSelect.value
	}else{
	   objInput.value+=','+objSelect.value
	}
}

//移除相关文章
function remove_relation(sid,id) {
	var relation_ids = $('#relation').val();
	if(relation_ids !='' ) {
		$('#'+sid).remove();
		var r_arr = relation_ids.split('|');
		var newrelation_ids = '';
		$.each(r_arr, function(i, n){
			if(n!=id) {
				if(i==0) {
					newrelation_ids = n;
				} else {
				 newrelation_ids = newrelation_ids+'|'+n;
				}
			}
		});
		$('#relation').val(newrelation_ids);
	}
}
//显示相关文章
function show_relation(modelid,id) {
$.getJSON("?m=content&c=content&a=public_getjson_ids&modelid="+modelid+"&id="+id, function(json){
	var newrelation_ids = '';
	if(json==null) {
		Dialog.alert('没有添加相关文章');
		return false;
	}
	$.each(json, function(i, n){
		newrelation_ids += "<li id='"+n.sid+"'>·<span>"+n.title+"</span><a href='javascript:;' class='close' onclick=\"remove_relation('"+n.sid+"',"+n.id+")\"></a></li>";
	});

	$('#relation_text').html(newrelation_ids);
}); 
}
//移除ID
function remove_id(id) {
	$('#'+id).remove();
}

function strlen_verify(obj, checklen, maxlen) {
	var v = obj.value, charlen = 0, maxlen = !maxlen ? 200 : maxlen, curlen = maxlen, len = strlen(v);
	for(var i = 0; i < v.length; i++) {
		if(v.charCodeAt(i) < 0 || v.charCodeAt(i) > 255) {
			curlen -= charset == 'utf-8' ? 2 : 1;
		}
	}
	if(curlen >= len) {
		$('#'+checklen).html(curlen - len);
	} else {
		obj.value = mb_cutstr(v, maxlen, true);
	}
}
function mb_cutstr(str, maxlen, dot) {
	var len = 0;
	var ret = '';
	var dot = !dot ? '...' : '';
	maxlen = maxlen - dot.length;
	for(var i = 0; i < str.length; i++) {
		len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == 'utf-8' ? 3 : 2) : 1;
		if(len > maxlen) {
			ret += dot;
			break;
		}
		ret += str.substr(i, 1);
	}
	return ret;
}
function strlen(str) {
	return ($.browser.msie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}

/*文本组字段添加上移、下移排序、删除本行功能*/
function moveUp(obj){
	var current=$(obj).parent().parent();
	var prev=current.prev();
	if(prev){
		current.insertBefore(prev);
	}
}
function moveDown(obj){
	var current=$(obj).parent().parent();
	var next=current.next();
	if(next){
		current.insertAfter(next);
	}
}
function delThisAttr(self){
	Dialog.confirm('确认要删除么？',function() {
		$(self).parent().parent().remove();
	});
}