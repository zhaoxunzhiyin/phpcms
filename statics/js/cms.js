if(typeof jQuery == 'undefined'){
	window.alert("没有引用jquery库");
}
var cms_post_addfunc = new Array();
// 获取URL路径名
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
// 提交时追加执行函数
function dr_post_addfunc(func) {
	cms_post_addfunc.push(func);
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
// 电脑版和手机版切换
function pc_or_mobile(linkurl,url,siteid,ismobile) {
	var loading = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 100000000
	});
	$.ajax({
		type: "POST",
		dataType: "json",
		url: linkurl,
		data:{siteid: siteid, ismobile: ismobile, url: encodeURIComponent(url)},
		success: function(json) {
			layer.close(loading);
			if (json.code) {
				dr_tips(1, json.msg);
				if (json.data.url) {
					location.href = json.data.url;
				}
			} else {
				dr_tips(0, json.msg, json.data.time);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
		}
	});
}
// 弹出对话框
function omnipotent(id,url,title,rt,w,h) {
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
	if(!rt) {
		diag.onOk = function(){
			$DW.$('.form-group').removeClass('has-error');
			$.ajax({type: "POST",dataType:"json", url: url, data: $DW.$('#myform').serialize(),
				success: function(json) {
					// token 更新
					if (json.token) {
						var token = json.token;
						$DW.$("#myform input[name='"+token.name+"']").val(token.value);
					}
					if (json.code) {
						if (json.data.jscode) {
							eval(json.data.jscode);
							return;
						} else if (json.data.tourl) {
							setTimeout("window.location.href = '"+json.data.tourl+"'", 2000);
						} else {
							if (rt == 'nogo') {

							} else {
								setTimeout("window.location.reload(true)", 2000);
							}
						}
						dr_tips(1, json.msg);
						diag.close();
					} else {
						if (json.data.field) {
							$DW.$('#dr_row_'+json.data.field).addClass('has-error');
							Dialog.warn(json.msg, function(){if(json.data.jscode){$DW.eval(json.data.jscode);}else{if(json.data.batch){$DW.$('#'+json.data.batch).focus();}else{if($DW.$('#'+json.data.field).attr('class') == 'dr_ueditor dr_ueditor_'+json.data.field+' edui-default'){$DW.UE.getEditor(json.data.field).focus();}else{if($DW.$('#'+json.data.field).length > 0){$DW.$('#'+json.data.field).focus();}else{$DW.$('#dr_'+json.data.field).focus();}}}}});
						} else {
							Dialog.warn(json.msg);
						}
					}
					return false;
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {
					dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
				}
			});
			return false;
		};
	} else {
		diag.cancelText = '关闭(X)';
	}
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
// 初始化滚动区域
function dr_slimScroll_init(a, b) {
	if ($().slimScroll) {
		var c = a + " .scroller",
			e = a + " .scroller_body";
		if ("1" === $(c).attr("data-inited")) {
			$(c).removeAttr("data-inited");
			$(c).removeAttr("style");
			var d = {};
			$(c).attr("data-handle-color") &&
				(d["data-handle-color"] = $(c).attr("data-handle-color"));
			$(c).attr("data-wrapper-class") &&
				(d["data-wrapper-class"] = $(c).attr("data-wrapper-class"));
			$(c).attr("data-rail-color") &&
				(d["data-rail-color"] = $(c).attr("data-rail-color"));
			$(c).attr("data-always-visible") &&
				(d["data-always-visible"] = $(c).attr("data-always-visible"));
			$(c).attr("data-rail-visible") &&
				(d["data-rail-visible"] = $(c).attr("data-rail-visible"));
			$(c).slimScroll({
				wrapperClass: $(c).attr("data-wrapper-class")
					? $(c).attr("data-wrapper-class")
					: "slimScrollDiv",
				destroy: !0
			});
			var f = $(c);
			$.each(d, function (a, b) {
				f.attr(a, b);
			});
		}
		e = $(e).height() > b ? b : "auto";
		$(c).slimScroll({
			allowPageScroll: !1,
			size: "7px",
			color: $(c).attr("data-handle-color")
				? $(c).attr("data-handle-color")
				: "#bbb",
			wrapperClass: $(c).attr("data-wrapper-class")
				? $(c).attr("data-wrapper-class")
				: "slimScrollDiv",
			railColor: $(c).attr("data-rail-color")
				? $(c).attr("data-rail-color")
				: "#eaeaea",
			position: "right",
			height: e,
			alwaysVisible: "1" == $(c).attr("data-always-visible") ? !0 : !1,
			railVisible: "1" == $(c).attr("data-rail-visible") ? !0 : !1,
			disableFadeOut: !0
		});
		$(c).attr("data-inited", "1");
	}
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
function dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError) {
	layer.closeAll('loading');
	dr_tips(0, '系统错误');
}