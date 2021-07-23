if(typeof jQuery == 'undefined'){
    window.alert("没有引用jquery库");
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
function pc_or_mobile(linkurl,url,siteid,ismobile,ishtml) {
	var loading = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 100000000
	});
	$.ajax({
		type: "POST",
		dataType: "json",
		url: linkurl,
		data:{siteid: siteid, ismobile: ismobile, ishtml: ishtml, url: encodeURIComponent(url)},
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