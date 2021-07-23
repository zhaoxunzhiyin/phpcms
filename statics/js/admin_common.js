if(typeof jQuery == 'undefined'){
	window.alert("没有引用jquery库");
}
$(function(){
	if ($('.table-checkable')) {
		var table = $('.table-checkable');
		table.find('.group-checkable').change(function () {
			var set = jQuery(this).attr("data-set");
			var checked = jQuery(this).is(":checked");
			jQuery(set).each(function () {
				if (checked) {
					$(this).prop("checked", true);
					$(this).parents('tr').addClass("active");
				} else {
					$(this).prop("checked", false);
					$(this).parents('tr').removeClass("active");
				}
			});
		});
	}
});
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
function confirmurl(url,message) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	Dialog.confirm(message,function() {
		redirect(url);
	});
}
function redirect(url) {
	location.href = url;
}
function topinyin(name, from, url) {
	var val = $("#" + from).val();
	if ($("#" + name).val()) {
		return false
	}
	$.get(url+'&name='+val+'&rand='+Math.random(), function(data){
		$('#'+name).val(data);
	});
}
//滚动条
$(function(){
	$(":text").addClass('input-text');
})

/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($("#check_box").attr("checked")=='checked') {
		$("input[name='"+name+"']").each(function() {
  			$(this).attr("checked","checked");
			$(this).parents('tr').addClass("active");
		});
	} else {
		$("input[name='"+name+"']").each(function() {
  			$(this).removeAttr("checked");
			$(this).parents('tr').removeClass("active");
		});
	}
}
// 显示视频
function dr_preview_video(file) {
	var width = '450px';
	var height = '330px';
	var att = 'width="350" height="280"';
	if (is_mobile()) {
		width = height = '90%';
		var att = 'width="90%" height="200"';
	}
	layer.alert('<p style="text-align: center"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center"> <video class="video-js vjs-default-skin" controls="" preload="auto" '+att+'><source src="'+file+'" type="video/mp4"/></video>\n</p>', {
		shade: 0,
		//scrollbar: false,
		shadeClose: true,
		title: '',
		area: [width, width],
		btn: []
	});
}

// 显示图片
function dr_preview_image(file) {
	var width = '400px';
	var height = '300px';
	if (is_mobile()) {
		width = height = '90%';
	}
	layer.alert('<p style="text-align: center"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center"><a href="'+file+'" target="_blank"><img style="max-width:100%" src="'+file+'"></a></p>', {
		shade: 0,
		//scrollbar: false,
		shadeClose: true,
		title: '',
		area: [width, width],
		btn: []
	});
}
// 显示url
function dr_preview_url(url) {
	var width = '400px';
	var height = '200px';
	if (is_mobile()) {
		width = height = '90%';
	}
	layer.alert('<div style="text-align: center;"><a href="'+url+'" target="_blank">'+url+'</a></div>', {
		shade: 0,
		title: '',
		area: [width, width],
		btn: []
	});
}
function openwinx(url,name,w,h) {
	if(!w) w='100%';
	if(!h) h='100%';
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var diag = new Dialog({
		id:'content_id',
		title:name,
		url:url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.cancelText = '关闭(X)';
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
function contentopen(url,name,w,h) {
	if(!w) w='100%';
	if(!h) h='100%';
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var diag = new Dialog({
		id:'content_id',
		title:name,
		url:url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.addButton('dosubmit','保存后自动关闭',function(){
		//var body = diag.innerFrame.contentWindow.document;
		//$(body).find('#myform').serialize()
		var form = $DW.$('#dosubmit');
		if(form.length > 0) {
			form.click();
		} else {
			if (parent.right) {
				parent.right.location.reload(true);
			} else {
				window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
			}
			diag.close();
		}
		return false;
	},0,1);
	diag.okText = '保存并继续发表';
	diag.onOk = function(){
		var form = $DW.$('#dosubmit_continue');
		if(form.length > 0) {
			form.click();
		} else {
			if (parent.right) {
				parent.right.location.reload(true);
			} else {
				window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
			}
			diag.close();
		}
		return false;
	};
	diag.cancelText = '关闭(X)';
	diag.onCancel=function(){
		if($DW.$V('#title') !='') {
			Dialog.confirm('内容已经录入，确定离开将不保存数据？', function(){
				if (parent.right) {
					parent.right.location.reload(true);
				} else {
					window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
				}
				diag.close();
			}, function(){});
		} else {
			if (parent.right) {
				parent.right.location.reload(true);
			} else {
				window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
			}
			diag.close();
		}
		return false;
	};
	diag.onClose=function(){
		if (parent.right) {
			parent.right.location.reload(true);
		} else {
			window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
		}
		$DW.close();
	};
	diag.show();
}
//弹出对话框
function artdialog(id,url,title,w,h) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
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
		url:url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.onOk = function(){
		var form = $DW.$('#dosubmit');
		form.click();
		return false;
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
//选择图标
function menuicon(id,linkurl,title,w,h) {
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
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
//弹出对话框
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
function dr_admin_menu_ajax(e, t) {
	var a = layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e4
	});
	$.ajax({
		type: "GET",
		dataType: "json",
		url: e,
		success: function(e) {
			if (layer.close(a), dr_tips(e.code, e.msg), 1 == e.code) {
				if (t) return;
				setTimeout("location.reload(true)", 2e3)
			}
		},
		error: function(e, t, a) {
			dr_ajax_admin_alert_error(e, t, a)
		}
	})
}
function dr_submit_sql_todo(e, t) {
	var a = layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e3
	});
	$("#sql_result").html(" ... "), $.ajax({
		type: "POST",
		dataType: "json",
		url: t,
		data: $("#" + e).serialize(),
		success: function(e) {
			return layer.close(a), 1 == e.code ? $("#sql_result").html("<pre>" + e.msg + "</pre>") : $("#sql_result").html('<div class="alert alert-danger">' + e.msg + "</div>"), !1
		},
		error: function(e, t, a) {
			dr_ajax_admin_alert_error(e, t, a)
		}
	})
}
function dr_install_uninstall(id,msg,title,linkurl,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	if(!w) w=500;
	if(!h) h=260;
	if (is_mobile()) {
		w = h = '100%';
	}
	Dialog.confirm(msg, function() {
		artdialog(id,linkurl,title,w,h);
	}, function() {})
}
function dr_install_confirm() {
	Dialog.confirm("确定要刷新整个后台吗？", function() {
		parent.location.reload(!0);
	}, function() {
		window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(!0);
	})
}
function dr_bfb(e, t, a) {
	layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e3
	}), layer.open({
		type: 2,
		title: e,
		scrollbar: !1,
		resize: !0,
		maxmin: !0,
		shade: 0,
		area: ["80%", "80%"],
		success: function(e, t) {
			var a = layer.getChildFrame("body", t),
				r = $(a).html();
			if (r.indexOf('"code":0') > 0 && r.length < 150) {
				var i = JSON.parse(r);
				layer.closeAll(), dr_tips(0, i.msg)
			}
		},
		content: a + "&" + $("#" + t).serialize(),
		cancel: function(e, t) {
			return layer.confirm("关闭后将中断操作，是否确认关闭呢？", {
				icon: 3,
				shade: 0,
				title: "提示",
				btn: ["确定", "取消"]
			}, function(e) {
				layer.closeAll()
			}), !1
		}
	})
}
function dr_bfb_submit(e, t, a) {
	layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e3
	}), $.ajax({
		type: "POST",
		dataType: "json",
		url: a,
		data: $("#" + t).serialize(),
		success: function(t) {
			return layer.closeAll("loading"), 1 == t.code ? layer.open({
				type: 2,
				title: e,
				scrollbar: !1,
				resize: !0,
				maxmin: !0,
				shade: 0,
				area: ["80%", "80%"],
				success: function(e, t) {
					var a = layer.getChildFrame("body", t),
						r = $(a).html();
					if (r.indexOf('"code":0') > 0 && r.length < 150) {
						var i = JSON.parse(r);
						layer.closeAll("loading"), dr_tips(0, i.msg)
					}
				},
				content: t.data.url,
				cancel: function(e, t) {
					return layer.confirm('关闭后将中断操作，是否确认关闭呢？', {
						icon: 3,
						shade: 0,
						title: "提示",
						btn: ["确定", "取消"]
					}, function(e) {
						layer.closeAll()
					}), !1
				}
			}) : dr_tips(0, t.msg, 9e4), !1
		},
		error: function(e, t, a) {
			dr_ajax_admin_alert_error(e, t, a)
		}
	})
}
function dr_submit_todo(e, t) {
	var w = '30%';
	var h = '30%';
	if (is_mobile()) {
		w = '95%';
		h = '50%';
	}
	layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e3
	}), layer.open({
		type: 2,
		title: '执行结果',
		shadeClose: !0,
		shade: 0,
		area: [w, h],
		success: function(e, t) {
			var a = layer.getChildFrame("body", t),
				r = $(a).html();
			if (r.indexOf('"code":0') > 0 && r.length < 150) {
				var i = JSON.parse(r);
				layer.closeAll(t), dr_tips(0, i.msg)
			}
		},
		content: t + "&" + $("#" + e).serialize()
	})
}
function dr_submit_post_todo(e, t) {
	var a = layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e3
	});
	$.ajax({
		type: "POST",
		dataType: "json",
		url: t,
		data: $("#" + e).serialize(),
		success: function(e) {
			return layer.close(a), 1 == e.code ? dr_tips(1, e.msg) : dr_tips(0, e.msg, 9e4), !1
		},
		error: function(e, t, a) {
			dr_ajax_admin_alert_error(e, t, a)
		}
	})
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
			content:'<div style="padding:10px;">' + msg + '</div>'
		});
	}
}
function check_title(linkurl,title) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	var val = $('#'+title).val();
	$.get(linkurl+"&data=" + val + "&is_ajax=1",
	function(data) {
		if (data) {
			dr_tips(0, data);
		}
	});
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