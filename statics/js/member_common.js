if(typeof jQuery == 'undefined'){
	window.alert("没有引用jquery库");
}
/**
 * 会员中心公用js
 *
 */
function geturlpathname() {
	var url = document.location.toString();
	var arrUrl = url.split("//");
	var start = arrUrl[1].indexOf("/");
	var relUrl = arrUrl[1].substring(start);
	if(relUrl.indexOf("?") != -1){
		relUrl = relUrl.split("?")[0];
	}
	return relUrl;
}
// 时间戳转换
function dr_strtotime(datetime) {
	if (datetime.indexOf(" ") == -1) {
		datetime+= ' 00:00:00';
	}
	var tmp_datetime = datetime.replace(/:/g,'-');
	tmp_datetime = tmp_datetime.replace(/ /g,'-');
	var arr = tmp_datetime.split("-");
	var now = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]));
	return parseInt(now.getTime()/1000);
}
// 判断当前终端是否是移动设备
function is_mobile() {
	var ua = navigator.userAgent,
	 isWindowsPhone = /(?:Windows Phone)/.test(ua),
	 isSymbian = /(?:SymbianOS)/.test(ua) || isWindowsPhone, 
	 isAndroid = /(?:Android)/.test(ua), 
	 isFireFox = /(?:Firefox)/.test(ua), 
	 isChrome = /(?:Chrome|CriOS)/.test(ua),
	 isTablet = /(?:iPad|PlayBook)/.test(ua) || (isAndroid && !/(?:Mobile)/.test(ua)) || (isFireFox && /(?:Tablet)/.test(ua)),
	 isPhone = /(?:iPhone)/.test(ua) && !isTablet,
	 isPc = !isPhone && !isAndroid && !isSymbian;
	 if (isPc) {
		// pc
		return false;
	 } else {
		return true;
	 }
}
/**
 * 隐藏html element
 */
function hide_element(name) {
	$('#'+name+'').fadeOut("slow");
}

/**
 * 显示html element
 */
function show_element(name) {
	$('#'+name+'').fadeIn("slow");
}

/*$(document).ready(function(){
　　$("input.input-text").blur(function () { this.className='input-text'; } );
　　$(":text").focus(function(){this.className='input-focus';});
});*/

/**
 * url跳转
 */
function redirect(url) {
	location.href = url;
}
/*$(function(){
	$(":text").addClass('input-text');
})*/
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
	$('#'+id).html(' ');
}

function select_catids() {
	$('#addbutton').attr('disabled',false);

}

//商业用户会添加 num，普通用户默认为5
function transact(update,fromfiled,tofiled, num) {
	if(update=='delete') {
		var fieldvalue = $('#'+tofiled).val();

		$("#"+tofiled+" option").each(function() {
		   if($(this).val() == fieldvalue){
			$(this).remove();
		   }
		});
	} else {
		var fieldvalue = $('#'+fromfiled).val();
		var have_exists = 0;
		var len = $("#"+tofiled+" option").size();
		if(len>=num) {
			alert('最多添加 '+num+' 项');
			return false;
		}
		$("#"+tofiled+" option").each(function() {
		   if($(this).val() == fieldvalue){
			have_exists = 1;
			alert('已经添加到列表中');
			return false;
		   }
		});
		if(have_exists==0) {
			obj = $('#'+fromfiled+' option:selected');
			text = obj.text();
			text = text.replace('│', '');
			text = text.replace('├ ', '');
			text = text.replace('└ ', '');
			text = text.trim();
			fieldvalue = "<option value='"+fieldvalue+"'>"+text+"</option>"
			$('#'+tofiled).append(fieldvalue);
			$('#deletebutton').attr('disabled','');
		}
	}
}
function omnipotent(id,linkurl,title,close_type,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	if(!w) w=700;
	if(!h) h=500;
	if (is_mobile()) {
		w = h = '100%';
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:linkurl,
		width:w,
		height:h,
		modal:true
	});
	diag.onOk = function(){
		if(close_type==1) {
			diag.close();
		} else {
			var form = $DW.$('#dosubmit');
			form.click();
		}
		return false;
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
function map(id,linkurl,title,tcstr,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	if(!w) w=700;
	if(!h) h=500;
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:linkurl,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.onOk = function(){
		$S(tcstr).value = $DW.$V('#'+tcstr);
		diag.close();
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
function dr_tips(code, msg, time) {
	if (!time || time == "undefined") {
		time = 3000;
	} else {
		time = time * 1000;
	}
	var is_tip = 0;
	if (time < 0) {
		is_tip = 1;
	} else if (code == 0 && msg.length > 15) {
		is_tip = 1;
	}

	if (is_tip) {
		if (code == 0) {
			layer.alert(msg, {
				shade: 0,
				title: "",
				icon: 2
			})
		} else {
			layer.alert(msg, {
				shade: 0,
				title: "",
				icon: 1
			})
		}
	} else {
		var tip = '<i class="fa fa-info-circle"></i>';
		//var theme = 'teal';
		if (code >= 1) {
			tip = '<i class="fa fa-check-circle"></i>';
			//theme = 'lime';
		} else if (code == 0) {
			tip = '<i class="fa fa-times-circle"></i>';
			//theme = 'ruby';
		}
		layer.msg(tip+'&nbsp;&nbsp;'+msg, {time: time});
	}
}

function dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError) {
	layer.closeAll("loading");
	var msg = HttpRequest.responseText;
	if (!msg) {
		dr_tips(0, "系统错误");
	} else {
		layer.open({
			type:1,
			title:"系统错误",
			fix:true,
			shadeClose:true,
			shade:0,
			area:[ "50%", "50%" ],
			content:'<div style="padding:10px;">' + msg + "</div>"
		});
	}
}

function get_wxurl(field,linkurl,titlename,keywordname,contentname) {
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 5000
	});
	$.ajax({type: "GET",dataType:"json", url: linkurl+'&url='+encodeURIComponent($('#'+field).val()),
		success: function(json) {
			layer.close(index);
			dr_tips(json.code, json.msg);
			if (json.code > 0) {
				var arr = json.data;
				$('#'+titlename).val(arr.title);
				if ($('#'+keywordname).length > 0) {
					$('#'+keywordname).val(arr.keyword);
					$('#'+keywordname).tagsinput('add', arr.keyword);
				}
				UE.getEditor(contentname).setContent(arr.content);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}

function get_wxurlckeditor(field,linkurl,titlename,keywordname,contentname) {
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 5000
	});
	$.ajax({type: "GET",dataType:"json", url: linkurl+'&url='+encodeURIComponent($('#'+field).val()),
		success: function(json) {
			layer.close(index);
			dr_tips(json.code, json.msg);
			if (json.code > 0) {
				var arr = json.data;
				$('#'+titlename).val(arr.title);
				if ($('#'+keywordname).length > 0) {
					$('#'+keywordname).val(arr.keyword);
					$('#'+keywordname).tagsinput('add', arr.keyword);
				}
				CKEDITOR.instances[contentname].setData(arr.content);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}

String.prototype.trim = function() {
	var str = this,
	whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
	for (var i = 0,len = str.length; i < len; i++) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(i);
			break;
		}
	}
	for (i = str.length - 1; i >= 0; i--) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}