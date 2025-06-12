var userAgent = navigator.userAgent.toLowerCase();
jQuery.browser = {
	version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
	safari: /webkit/.test( userAgent ),
	opera: /opera/.test( userAgent ),
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
};

function set_title_color(color) {
	$('#title').css('color',color);
	$('#style_color').val(color);
}

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
	if($('#islink').is(":checked")) {
		$('#linkurl').prop('disabled',false);
		return false;
	} else {
		$('#linkurl').prop('disabled',true);
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
		len += str.charCodeAt(i) < 0 || 1;
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