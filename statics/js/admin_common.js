if(typeof jQuery == 'undefined'){
	window.alert("没有引用jquery库");
}
var cms_post_addfunc = new Array();
// 主目录相对路径
function dr_get_web_dir() {
	if (typeof web_dir != "undefined" && web_dir) {
		return web_dir;
	}
	return '/';
}
// 是否有隐藏区域
function dr_isEllipsis(dom) {
	var checkDom = dom.cloneNode(),parent, flag;
	checkDom.style.width = dom.offsetWidth + 'px';
	checkDom.style.height = dom.offsetHeight + 'px';
	checkDom.style.overflow = 'auto';
	checkDom.style.position = 'absolute';
	checkDom.style.zIndex = -1;
	checkDom.style.opacity = 0;
	checkDom.style.whiteSpace = "nowrap";
	checkDom.innerHTML = dom.innerHTML;
	parent = dom.parentNode;
	parent.appendChild(checkDom);
	flag = checkDom.scrollWidth > checkDom.offsetWidth;
	parent.removeChild(checkDom);
	return flag;
};
jQuery(document).ready(function() {
	handleGoTop();
	handleBootstrapSwitch();
	handleTabs();
	handleTooltips();
	initSlimScroll('.scroller');
	$('.onloading').click(function(){
		var index = layer.load(2,{time:5E3})
	});
	/*if ($(document).width() < 600) {
		$('.table-list table').attr('style', 'table-layout: inherit!important;');
	}*/
	// 排序操作
	$('.table-list table .heading th').click(function(e) {
		var _class = $(this).attr("class");
		if (_class == '' || _class == undefined) {
			return;
		}
		var _name = $(this).attr("name");
		if (_name == '' || _name == undefined) {
			return;
		}
		var _order = '';
		if (_class == "order_sorting") {
			_order = 'desc';
		} else if (_class == "order_sorting_desc") {
			_order = 'asc';
		} else {
			_order = 'desc';
		}
		var url = decodeURI(window.location.href);
		url = url.replace("&order=", "&");
		url+= "&order="+_name+"+"+_order;
		window.location.href=url;
	});
	// tabl
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
	// 当存在隐藏时单击显示区域
	$(".table-list table td,.table-list table th").click(function() {
		var e = $(this);
		if (1 == dr_isEllipsis(e[0])) {
			var t = e.html();
			if (t.indexOf("checkbox") != -1) return;
			if (t.indexOf("<input") != -1) return;
			if (t.indexOf('class="btn') != -1);
			else if (t.indexOf('href="') != -1) return;
			layer.tips(t, e, {
				tips: [1, "#fff"],
				time: 5e3
			})
		}
	});
	// 宽度小时
	if ($(document).width() < 900) {
		// 缩小table
		/*
		$('.page-breadcrumb a').each(function () {
			var name = $(this).html();
			re = new RegExp(/<i class=\"(.+)\"(.+)/i);
			if (re.test(name)) {
				var result = name.match(re);
				$(this).html('<i class="'+result[1]+'"></i>');
				$(this).attr('title', result[2].replace('></i> ', ''));
			}
		});*/
		// 缩小table下方按钮
		$('.list-select button').each(function () {
			var name = $(this).html();
			re = new RegExp(/<i class=\"(.+)\"(.+)/i);
			if (re.test(name)) {
				var result = name.match(re);
				$(this).html('<i class="'+result[1]+'"></i>');
				$(this).attr('title', result[2].replace('></i> ', ''));
			}
		});
		// 缩小后台导航面包屑
		$('a[data-toggle="tab"]').each(function () {
			var name = $(this).html();
			re = new RegExp(/<i class=\"(.+)\"(.+)/i);
			if (re.test(name)) {
				var result = name.match(re);
				$(this).html('<i class="'+result[1]+'"></i>');
				$(this).attr('title', result[2].replace('></i> ', ''));
			}
		});
	}
	//离开提示失效
	if (typeof is_cms != "undefined" && is_cms == 1) {
		var d, f = false;
		window.onunloadcancel = function(){
			clearTimeout(d);
		}
		window.onbeforeunload = function(){
			if (f) {
				return (
					setTimeout(function(){
						d = setTimeout(onunloadcancel, 0);
					}, 0),
					'数据未保存，你确定要离开吗？'
				);
			}
		}
		$("[type='submit'], [type='button']").click(function(){
			f = false;
		});
		$("select").change(function(){
			f = true;
		});
		$(document).keydown(function(a){
			if (40 <= a.keyCode || 0 == a.keyCode) f = true;
			if (16 == a.keyCode || 82 == a.keyCode || 91 == a.keyCode) f = false;
		});
	}
	/*复选框全选(支持多个，纵横双控全选)。
	 *实例：版块编辑-权限相关（双控），验证机制-验证策略（单控）
	 *说明：
	 *	"J_check"的"data-xid"对应其左侧"J_check_all"的"data-checklist"；
	 *	"J_check"的"data-yid"对应其上方"J_check_all"的"data-checklist"；
	 *	全选框的"data-direction"代表其控制的全选方向(x或y)；
	 *	"J_check_wrap"同一块全选操作区域的父标签class，多个调用考虑
	 */
	if ($('.J_check_wrap').length) {
		var total_check_all = $('input.J_check_all');
		//遍历所有全选框
		$.each(total_check_all, function () {
			var check_all = $(this), check_items;
			//分组各纵横项
			var check_all_direction = check_all.data('direction');
			check_items = $('input.J_check[data-' + check_all_direction + 'id="' + check_all.data('checklist') + '"]');
			//点击全选框
			check_all.change(function (e) {
				var check_wrap = check_all.parents('.J_check_wrap'); //当前操作区域所有复选框的父标签（重用考虑）
				if ($(this).is(":checked")) {
					//全选状态
					check_items.prop('checked', true);
					//所有项都被选中
					if (check_wrap.find('input.J_check').length === check_wrap.find('input.J_check:checked').length) {
						check_wrap.find(total_check_all).prop('checked', true);
					}
				} else {
					//非全选状态
					check_items.removeAttr('checked');
					//另一方向的全选框取消全选状态
					var direction_invert = check_all_direction === 'x' ? 'y' : 'x';
					check_wrap.find($('input.J_check_all[data-direction="' + direction_invert + '"]')).removeAttr('checked');
				}
			});
			//点击非全选时判断是否全部勾选
			check_items.change(function () {
				if ($(this).is(":checked")) {
					if (check_items.filter(':checked').length === check_items.length) {
						//已选择和未选择的复选框数相等
						check_all.prop('checked', true);
					}
				} else {
					check_all.removeAttr('checked');
				}
			});
		});
	}
});
function handleGoTop() {
	navigator.userAgent.match(/iPhone|iPad|iPod/i)
		? $(window).bind("touchend touchcancel touchleave", function (a) {
			100 < $(this).scrollTop()
				? $(".scroll-to-top").fadeIn(500)
				: $(".scroll-to-top").fadeOut(500);
		})
		: $(window).scroll(function () {
			100 < $(this).scrollTop()
				? $(".scroll-to-top").fadeIn(500)
				: $(".scroll-to-top").fadeOut(500);
		});
	$(".scroll-to-top").click(function (a) {
		a.preventDefault();
		$("html, body").animate({ scrollTop: 0 }, 500);
		return !1;
	});
}
// 滑动选择组件
function handleBootstrapSwitch() {
	if (!$().bootstrapSwitch) {
		return;
	}
	$('.make-switch').bootstrapSwitch();
}
// Tab切换
function handleTabs() {
	$('.nav-tabs a').click(function (e) {
		$('.nav-tabs').find('li').removeClass('active');
		$('.tab-pane').removeClass('active');
		$(this).parent().addClass('active');
		$('#'+$(this).attr("data-toggle")).addClass('active');
	})
	//activate tab if tab id provided in the URL
	if (encodeURI(location.hash)) {
		var tabid = encodeURI(location.hash.substr(1));
		$('a[href="#' + tabid + '"]').parents('.tab-pane:hidden').each(function() {
			var tabid = $(this).attr("id");
			$('a[href="#' + tabid + '"]').click();
		});
		$('a[href="#' + tabid + '"]').click();
	}
	if ($().tabdrop) {
		$('.tabbable-tabdrop .nav-pills, .tabbable-tabdrop .nav-tabs').tabdrop({
			text: '<i class="fa fa-ellipsis-v"></i>&nbsp;<i class="fa fa-angle-down"></i>'
		});
	}
}
// 提示信息显示
function handleTooltips() {
	// global tooltips
	$('.tooltips').tooltip();
	// portlet tooltips
	$('.portlet > .portlet-title .fullscreen').tooltip({
		trigger: 'hover',
		container: 'body',
		title: 'Fullscreen'
	});
	$('.portlet > .portlet-title > .tools > .reload').tooltip({
		trigger: 'hover',
		container: 'body',
		title: 'Reload'
	});
	$('.portlet > .portlet-title > .tools > .remove').tooltip({
		trigger: 'hover',
		container: 'body',
		title: 'Remove'
	});
	$('.portlet > .portlet-title > .tools > .config').tooltip({
		trigger: 'hover',
		container: 'body',
		title: 'Settings'
	});
	$('.portlet > .portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip({
		trigger: 'hover',
		container: 'body',
		title: 'Collapse/Expand'
	});
};
function initSlimScroll(a) {
	$().slimScroll &&
	$(a).each(function () {
		if (!$(this).attr("data-initialized")) {
		var a;
		a = $(this).attr("data-height")
			? $(this).attr("data-height")
			: $(this).css("height");
		$(this).slimScroll({
			allowPageScroll: !0,
			size: "7px",
			color: $(this).attr("data-handle-color")
				? $(this).attr("data-handle-color")
				: "#bbb",
			wrapperClass: $(this).attr("data-wrapper-class")
				? $(this).attr("data-wrapper-class")
				: "slimScrollDiv",
			railColor: $(this).attr("data-rail-color")
				? $(this).attr("data-rail-color")
				: "#eaeaea",
			position: "right",
			height: a,
			alwaysVisible:
				"1" == $(this).attr("data-always-visible") ? !0 : !1,
			railVisible: "1" == $(this).attr("data-rail-visible") ? !0 : !1,
			disableFadeOut: !0
		});
		$(this).attr("data-initialized", "1");
		}
	});
}
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
function confirmiframe(url,title,message,width,height) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	Dialog.confirm(message,function() {
		iframe_show(title, url, width, height);
	});
}
function confirmdriframe(url,title,message,width,height) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	Dialog.confirm(message,function() {
		dr_iframe_show(title, url, width, height);
	});
}
function redirect(url) {
	location.href = url;
}
function dr_content_go(url) {
	window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location = url;
}
function topinyin(url, value, name, length) {
	if(!length) length=12;
	var val = $("#" + value).val();
	if ($("#" + name).val()) {
		return false
	}
	$.get(url+'&name='+val+'&length='+length+'&rand='+Math.random(), function(data){
		if ($('#'+name).length > 0) {
			$('#'+name).val(data);
		}
	});
}
//text
$(function(){
	$(":text").addClass('input-text');
})
/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($("#check_box").is(":checked")) {
		$("input[name='"+name+"']").each(function() {
			$(this).prop("checked", true);
			$(this).parents('tr').addClass("active");
		});
	} else {
		$("input[name='"+name+"']").each(function() {
			$(this).prop("checked", false);
			$(this).parents('tr').removeClass("active");
		});
	}
}
// 删除文件
function dr_file_delete(url, obj, id) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	Dialog.confirm('确定要删除本文件吗？', function() {
		$.post(url, {ids:[id]}, function(data) {
			dr_tips(data.code, data.msg);
			if (data.code) {
				$(obj).parents('.files_row').parent().remove();
				setTimeout("window.location.reload(true)", 2000);
			}
		}, 'json');
	});
}
// 显示ip信息
function dr_show_ip(url, value) {
	$.get(url+'&value='+value, function(html){
		layer.alert(html, {
			shade: 0,
			title: "",
			icon: 1
		})
	}, 'text');
}
// 显示ip信息
function show_ip(name) {
	$.get(dr_get_web_dir()+'api.php?op=ip_address&value='+$('#dr_'+name).val(), function(html){
		layer.alert(html, {
			shade: 0,
			title: "",
			icon: 1
		})
	}, 'text');
}
function dr_diy_func(name) {
	dr_tips(1, '这是一个自定义函数');
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
// 显示音频
function dr_preview_audio(file) {
	var width = '400px';
	var height = '300px';
	if (is_mobile()) {
		width = height = '90%';
	}
	layer.alert('<p style="text-align: center"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center"> <audio src="'+file+'" controls="controls"></audio>\n</p>', {
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
// 预览
function preview(file) {
	if(IsImg(file)) {
		var width = 400;
		var height = 300;
		var att = 'width: 350px;height: 260px;';
		if (is_mobile()) {
			width = height = '90%';
			var att = 'height: 90%;';
		}
		var diag = new Dialog({
			title:'预览',
			html:'<style type="text/css">a{text-shadow: none; color: #337ab7; text-decoration:none;}a:hover{cursor: pointer; color: #23527c; text-decoration: underline;}</style><div style="'+att+'line-height: 24px;word-break: break-all;overflow: hidden auto;"><p style="word-break: break-all;text-align: center;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"><a href="'+file+'" target="_blank"><img style="max-width:100%" src="'+file+'"></a></p></div>',
			width:width,
			height:height,
			modal:true
		});
		diag.show();
	} else if(IsMp4(file)) {
		var width = 500;
		var height = 320;
		var att = 'width="420" height="238"';
		if (is_mobile()) {
			width = height = '90%';
			var att = 'width="90%" height="200"';
		}
		var diag = new Dialog({
			title:'预览',
			html:'<style type="text/css">a{text-shadow: none; color: #337ab7; text-decoration:none;}a:hover{cursor: pointer; color: #23527c; text-decoration: underline;}</style><p style="word-break: break-all;text-align: center;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"> <video class="video-js vjs-default-skin" controls="true" preload="auto" '+att+'><source src="'+file+'" type="video/mp4"/></video>\n</p>',
			width:width,
			height:height,
			modal:true
		});
		diag.show();
	} else if(IsMp3(file)) {
		var diag = new Dialog({
			title:'预览',
			html:'<style type="text/css">a{text-shadow: none; color: #337ab7; text-decoration:none;}a:hover{cursor: pointer; color: #23527c; text-decoration: underline;}</style><p style="text-align: center;word-break: break-all;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"><audio src="'+file+'" controls="controls"></audio></p>',
			modal:true
		});
		diag.show();
	} else {
		var diag = new Dialog({
			title:'预览',
			html:'<style type="text/css">a{text-shadow: none; color: #337ab7; text-decoration:none;}a:hover{cursor: pointer; color: #23527c; text-decoration: underline;}</style><p style="text-align: center;word-break: break-all;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"><a href="'+file+'" target="_blank"><i class="fa fa-download"></i> 单击打开</a></p>',
			modal:true
		});
		diag.show();
	}
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
// 内容窗口提交
function dr_content_submit(url,type,w,h) {
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
	var title = '';
	if (type == 'add') {
		title = '<i class="fa fa-plus"></i> 添加';
	} else if (type == 'edit') {
		title = '<i class="fa fa-edit"></i> 修改';
	} else if (type == 'send') {
		title = '<i class="fa fa-send"></i> 推送';
	} else if (type == 'save') {
		title = '<i class="fa fa-save"></i> 保存';
	} else {
		title = type;
	}
	var diag = new Dialog({
		id:'content_id',
		title:title,
		url:url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.addButton('dosubmit','保存后自动关闭',function(){
		$DW.$('.form-group').removeClass('has-error');
		$.ajax({type: "POST",dataType:"json", url: url, data: $DW.$('#myform').serialize(),
			success: function(json) {
				// token 更新
				if (json.token) {
					var token = json.token;
					$DW.$("#myform input[name='"+token.name+"']").val(token.value);
				}
				if (json.code) {
					if (json.data.tourl) {
						setTimeout("window.location.href = '"+json.data.tourl+"'", 2000);
					} else {
						setTimeout("window.location.reload(true)", 2000);
					}
					dr_tips(1, json.msg);
					diag.close();
				} else {
					if (json.data.field) {
						$DW.$('#dr_row_'+json.data.field).addClass('has-error');
						Dialog.warn(json.msg, function(){if(json.data.jscode){$DW.eval(json.data.jscode);}else{if($DW.$('#'+json.data.field).attr('class') == 'dr_ueditor dr_ueditor_'+json.data.field+' edui-default'){$DW.UE.getEditor(json.data.field).focus();}else{if($DW.$('#'+json.data.field).length > 0){$DW.$('#'+json.data.field).focus();}else{$DW.$('#dr_'+json.data.field).focus();}}}});
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
	},0,1);
	if (type == 'edit') {
		diag.okText = '保存并继续修改';
	} else {
		diag.okText = '保存并继续发表';
	}
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
					$DW.location.reload(true);
					Dialog.tips(json.msg);
				} else {
					if (json.data.field) {
						$DW.$('#dr_row_'+json.data.field).addClass('has-error');
						Dialog.warn(json.msg, function(){if(json.data.jscode){$DW.eval(json.data.jscode);}else{if($DW.$('#'+json.data.field).attr('class') == 'dr_ueditor dr_ueditor_'+json.data.field+' edui-default'){$DW.UE.getEditor(json.data.field).focus();}else{if($DW.$('#'+json.data.field).length > 0){$DW.$('#'+json.data.field).focus();}else{$DW.$('#dr_'+json.data.field).focus();}}}});
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
//选择图标
function menuicon(id,url,title,w,h) {
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
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
// 弹出对话框
function artdialog(id,url,title,w,h) {
	omnipotent(id,url,title,0,w,h);
}
// 弹出对话框
function openwinx(id,url,name,w,h) {
	if(!w) w='100%';
	if(!h) h='100%';
	if (is_mobile()) {
		w = h = '100%';
	}
	omnipotent(id,url,name,1,w,h);
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
							dr_safe_execute(json.data.jscode);
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
// 选择地图
function map(id,url,title,tcstr,w,h) {
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
		$S(tcstr).value = $DW.$V('#'+tcstr);
		diag.close();
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
// 窗口提交
function dr_iframe(type, url, width, height, rt) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var title = '';
	if (type == 'add') {
		title = '<i class="fa fa-plus"></i> 添加';
	} else if (type == 'edit') {
		title = '<i class="fa fa-edit"></i> 修改';
	} else if (type == 'send') {
		title = '<i class="fa fa-send"></i> 推送';
	} else if (type == 'save') {
		title = '<i class="fa fa-save"></i> 保存';
	} else {
		title = type;
	}
	if (!width) {
		width = '500px';
	}
	if (!height) {
		height = '70%';
	}
	if (is_mobile()) {
		width = '100%';
		height = '100%';
	}
	if (width=='100%' && height=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:'iframe',
		title:title,
		url:url+'&is_iframe=1',
		width:width,
		height:height,
		modal:true,
		draggable:drag
	});
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
						dr_safe_execute(json.data.jscode);
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
	diag.onCancel=function(){
		$DW.close();
	};
	diag.show();
}
// 窗口提交
function iframe(type, url, width, height, rt) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var title = '';
	if (type == 'add') {
		title = '<i class="fa fa-plus"></i> 添加';
	} else if (type == 'edit') {
		title = '<i class="fa fa-edit"></i> 修改';
	} else if (type == 'send') {
		title = '<i class="fa fa-send"></i> 推送';
	} else if (type == 'save') {
		title = '<i class="fa fa-save"></i> 保存';
	} else {
		title = type;
	}
	if (!width) {
		width = '500px';
	}
	if (!height) {
		height = '70%';
	}
	if (is_mobile()) {
		width = '95%';
		height = '90%';
	}
	layer.open({
		type: 2,
		title: title,
		fix:true,
		scrollbar: false,
		maxmin: false,
		resize: true,
		shadeClose: true,
		shade: 0,
		area: [width, height],
		btn: ['确定', '取消'],
		yes: function(index, layero){
			var body = layer.getChildFrame('body', index);
			$(body).find('.form-group').removeClass('has-error');
			// 延迟加载
			var loading = layer.load(2, {
				shade: [0.3,'#fff'], //0.1透明度的白色背景
				time: 100000000
			});
			$.ajax({type: "POST",dataType:"json", url: url, data: $(body).find('#myform').serialize(),
				success: function(json) {
					layer.close(loading);
					// token 更新
					if (json.token) {
						var token = json.token;
						$(body).find("#myform input[name='"+token.name+"']").val(token.value);
					}
					if (json.code) {
						layer.close(index);
						if (json.data.jscode) {
							dr_safe_execute(json.data.jscode);
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
					} else {
						$(body).find('#dr_row_'+json.data.field).addClass('has-error');
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
			json = json.replace(/<.*?>/g,"");
			if (json.indexOf('"code":0') > 0 && json.length < 500){
				var obj = JSON.parse(json);
				layer.close(index);
				dr_tips(0, obj.msg);
			}
		},
		content: url+'&is_iframe=1'
	});
}
// 退出登录
function dr_logout(msg, url, tourl) {
	Dialog.confirm(msg, function(){
		$.ajax({
			type: "GET",
			dataType: "json",
			url: url,
			success: function(json) {
				if (json.code == 1) {
					setTimeout("window.location.href='" + tourl + "'", 1000);
				}
				dr_tips(json.code, json.msg);
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
				dr_ajax_alert_error(HttpRequest, this, thrownError);
			}
		});
	});
}
// ajax 显示内容
function dr_iframe_show(type, url, width, height, rt) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var title = '';
	if (type == 'show') {
		title = '<i class="fa fa-search"></i> 查看';
	} else if (type == 'edit') {
		title = '<i class="fa fa-edit"></i> 修改';
	} else if (type == 'code') {
		title = '<i class="fa fa-code"></i> 代码';
	} else if (type == 'cart') {
		title = '<i class="fa fa-shopping-cart"></i> 交易记录';
	} else {
		title = type;
	}
	if (!width) {
		width = '60%';
	}
	if (!height) {
		height = '70%';
	}
	if (is_mobile()) {
		width = '95%';
		height = '90%';
	}
	var diag = new Dialog({
		id:'iframe_show',
		title:title,
		url:url+'&is_iframe=1',
		width:width,
		height:height,
		modal:true,
		draggable:true
	});
	diag.cancelText = '关闭(X)';
	diag.onCancel=function(){
		if (rt == "load") {
			window.location.reload(true);
		}
		$DW.close();
	};
	if (rt == "load") {
		diag.onClose=function(){
			window.location.reload(true);
			$DW.close();
		};
	}
	diag.show();
}
// ajax 显示内容
function iframe_show(type, url, width, height, rt) {
	var title = '';
	if (type == 'show') {
		title = '<i class="fa fa-search"></i> 查看';
	} else if (type == 'edit') {
		title = '<i class="fa fa-edit"></i> 修改';
	} else if (type == 'code') {
		title = '<i class="fa fa-code"></i> 代码';
	} else if (type == 'cart') {
		title = '<i class="fa fa-shopping-cart"></i> 交易记录';
	} else {
		title = type;
	}
	if (!width) {
		width = '60%';
	}
	if (!height) {
		height = '75%';
	}
	if (is_mobile()) {
		width = '95%';
		height = '90%';
	}
	layer.open({
		type: 2,
		title: title,
		fix:true,
		scrollbar: false,
		shadeClose: true,
		shade: 0,
		area: [width, height],
		success: function(layero, index){
			// 主要用于后台权限验证
			var body = layer.getChildFrame('body', index);
			var json = $(body).html();
			json = json.replace(/<.*?>/g,"");
			if (json.indexOf('"code":0') > 0 && json.length < 500){
				var obj = JSON.parse(json);
				layer.close(index);
				dr_tips(0, obj.msg);
			}
		},end: function(){
			if (rt == "load") {
				window.location.reload(true);
			}
		},
		content: url+'&is_iframe=1'
	});
}
// ajax保存数据
function dr_ajax_save(value, url, name) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 5000
	});
	$.ajax({
		type: "GET",
		url: url+'&name='+name+'&value='+value,
		dataType: "json",
		success: function (json) {
			layer.close(index);
			dr_tips(json.code, json.msg, json.data.time);
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}
// ajax关闭或启用
function dr_ajax_open_close(e, url, fan) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 10000
	});
	$.ajax({
		type: "GET",
		cache: false,
		url: url,
		dataType: "json",
		success: function (json) {
			layer.close(index);
			if (json.code == 1) {
				if (json.data.value == fan) {
					$(e).attr('class', 'badge badge-no');
					$(e).html('<i class="fa fa-times"></i>');
				} else {
					$(e).attr('class', 'badge badge-yes');
					$(e).html('<i class="fa fa-check"></i>');
				}
				dr_tips(1, json.msg);
			} else {
				dr_tips(0, json.msg);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}
// 批量模块数据 ajax
function dr_module_send_ajax(url) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	url+= '&'+$("#myform").serialize();
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 10000
	});
	$.ajax({type: "GET",dataType:"json", url: url,
		success: function(json) {
			layer.close(index);
			dr_tips(json.code, json.msg);
			if (json.code == 1) {
				setTimeout("window.location.reload(true)", 2000);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, this, thrownError);
		}
	});
}
// 批量退稿
function dr_module_tuigao(url, tourl) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	if (typeof pc_hash == 'string') tourl += (tourl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (tourl.toLowerCase().indexOf("http://") != -1 || tourl.toLowerCase().indexOf("https://") != -1) {
	} else {
		tourl = geturlpathname()+tourl;
	}
	var width = '50%';
	var height = '60%';
	if (is_mobile()) {
		width = height = '90%';
	}
	url+= '&'+$("#myform").serialize();
	layer.open({
		type: 2,
		title: '批量退稿确认',
		shadeClose: true,
		shade: 0,
		area: [width, height],
		btn: ['确定'],
		yes: function(index, layero){
			var body = layer.getChildFrame('body', index);
			$(body).find('.form-group').removeClass('has-error');
			// 延迟加载
			var loading = layer.load(2, {
				shade: [0.3,'#fff'], //0.1透明度的白色背景
				time: 5000
			});
			$.ajax({type: "POST",dataType:"json", url: tourl, data: $(body).find('#myform').serialize(),
				success: function(json) {
					layer.close(loading);
					// token 更新
					if (json.token) {
						var token = json.token;
						$(body).find("#myform input[name='"+token.name+"']").val(token.value);
					}
					if (json.code) {
						layer.close(index);
						setTimeout("window.location.reload(true)", 2000);
					}
					dr_tips(json.code, json.msg);
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {
					dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError, this);
				}
			});
			return false;
		},
		success: function(layero, index){
			// 主要用于后台权限验证
			var body = layer.getChildFrame('body', index);
			var json = $(body).html();
			json = json.replace(/<.*?>/g,"");
			if (json.indexOf('"code":0') > 0 && json.length < 150){
				var obj = JSON.parse(json);
				layer.close(index);
				dr_tips(0, obj.msg);
			}
			if (json.indexOf('"code":1') > 0 && json.length < 150){
				var obj = JSON.parse(json);
				layer.close(index);
				dr_tips(1, obj.msg);
			}
		},
		content: url+'&is_iframe=1'
	});
}
// 实时存储选择值
function dr_ajax_list_open_close(e, url) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var obj = $(e);
	var val = 0;
	if (obj.attr("value") == 1) {
		val = 0;
	} else {
		val = 1;
	}
	url+="&value="+val;
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (json) {
			if (json.code == 1) {
				if (val == 0) {
					obj.attr('class', 'badge badge-no');
					obj.html('<i class="fa fa-times"></i>');
				} else {
					obj.attr('class', 'badge badge-yes');
					obj.html('<i class="fa fa-check"></i>');
				}
				obj.attr("value", val);
			}
			dr_tips(json.code, json.msg);
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}
// ajax 操作确认 并跳转
function ajax_confirm_url(url, msg, tourl) {
	Dialog.confirm(msg, function(){
		var loading = layer.load(2, {
			shade: [0.3,'#fff'], //0.1透明度的白色背景
			time: 100000000
		});
		$.ajax({
			type: "GET",
			dataType: "json",
			url: url,
			success: function(json) {
				layer.close(loading);
				if (json.code) {
					if (json.data.jscode) {
						dr_safe_execute(json.data.jscode);
						return;
					}
					if (json.data.url) {
						setTimeout("window.location.href = '"+json.data.url+"'", 2000);
					} else if (tourl) {
						setTimeout("window.location.href = '"+tourl+"'", 2000);
					}
				}
				dr_tips(json.code, json.msg);
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
				dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
			}
		});
	});
}
// ajax 操作确认 并跳转
function dr_ajax_confirm_url(url, msg, tourl) {
	layer.confirm(msg, {
		icon: 3,
		shade: 0,
		title: '提示',
		btn: ['确定', '取消']
	}, function(index){
		layer.close(index);
		var loading = layer.load(2, {
			shade: [0.3,'#fff'], //0.1透明度的白色背景
			time: 100000000
		});
		$.ajax({
			type: "GET",
			dataType: "json",
			url: url,
			success: function(json) {
				layer.close(loading);
				if (json.code) {
					if (json.data.jscode) {
						dr_safe_execute(json.data.jscode);
						return;
					}
					if (json.data.url) {
						setTimeout("window.location.href = '"+json.data.url+"'", 2000);
					} else if (tourl) {
						setTimeout("window.location.href = '"+tourl+"'", 2000);
					}
				}
				dr_tips(json.code, json.msg);
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
				dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
			}
		});
	});
}
// ajax 批量操作确认
function ajax_option(url, msg, remove) {
	Dialog.confirm(msg, function(){
		var loading = layer.load(2, {
			shade: [0.3,'#fff'], //0.1透明度的白色背景
			time: 100000000
		});
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: $("#myform").serialize(),
			success: function(json) {
				layer.close(loading);
				// token 更新
				if (json.token) {
					var token = json.token;
					$("#myform input[name='"+token.name+"']").val(token.value);
				}
				if (json.code) {
					if (remove) {
						// 批量移出去
						var ids = json.data.ids;
						if (typeof ids != "undefined" ) {
							console.log(ids);
							for ( var i = 0; i < ids.length; i++){
								$("#dr_row_"+ids[i]).remove();
							}
						}
					}
					if (json.data.url) {
						setTimeout("window.location.href = '"+json.data.url+"'", 2000);
					} else {
						setTimeout("window.location.reload(true)", 3000)
					}
				}
				dr_tips(json.code, json.msg, json.data.time);
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
				dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
			}
		});
	});
}
// ajax 批量操作确认
function dr_ajax_option(url, msg, remove) {
	layer.confirm(msg, {
		icon: 3,
		shade: 0,
		title: '提示',
		btn: ['确定', '取消']
	}, function(index){
		layer.close(index);
		var loading = layer.load(2, {
			shade: [0.3,'#fff'], //0.1透明度的白色背景
			time: 100000000
		});
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: $("#myform").serialize(),
			success: function(json) {
				layer.close(loading);
				// token 更新
				if (json.token) {
					var token = json.token;
					$("#myform input[name='"+token.name+"']").val(token.value);
				}
				if (json.code) {
					if (remove) {
						// 批量移出去
						var ids = json.data.ids;
						if (typeof ids != "undefined" ) {
							console.log(ids);
							for ( var i = 0; i < ids.length; i++){
								$("#dr_row_"+ids[i]).remove();
							}
						}
					}
					if (json.data.url) {
						setTimeout("window.location.href = '"+json.data.url+"'", 2000);
					} else {
						setTimeout("window.location.reload(true)", 3000)
					}
				}
				dr_tips(json.code, json.msg, json.data.time);
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
				dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
			}
		});
	});
}
// ajax提交
function dr_ajax_submit(url, form, time, go) {
	var flen = $('[id='+form+']').length;
	// 验证id是否存在
	if (flen == 0) {
		dr_tips(0, '表单id属性不存在' + ' ('+form+')');
		return;
	}
	// 验证重复
	if (flen > 1) {
		dr_tips(0, '表单id属性已重复定义' + ' ('+form+')');
		return;
	}

	// 验证必填项管理员
	var tips_obj = $('#'+form).find('[name=is_tips]');
	if (tips_obj.val() == 'required') {
		tips_obj.val('');
	}
	if ($('#'+form).find('[name=is_admin]').val() == 1) {
		$('#'+form).find('.dr_required').each(function () {
			if (!$(this).val()) {
				tips_obj.val('required');
			}
		});
	}

	var tips = tips_obj.val();
	if (tips) {
		if (tips == 'required') {
			tips = '有必填字段未填写，确认提交吗？';
		}
		layer.confirm(
		tips,
		{
			icon: 3,
			shade: 0,
			title: '提示',
			btn: ['确定', '取消']
		}, function(index){
			dr_post_submit(url, form, time, go);
		});
	} else {
		dr_post_submit(url, form, time, go);
	}
}
// 提交时追加执行函数
function dr_post_addfunc(func) {
	cms_post_addfunc.push(func);
}
// 处理post提交
function dr_post_submit(url, form, time, go) {
	var p = url.split('/');
	if ((p[0] == 'http:' || p[0] == 'https:') && document.location.protocol != p[0]) {
		alert('当前提交的URL是'+p[0]+'模式，请使用'+document.location.protocol+'模式访问再提交');
		return;
	}

	url = url.replace(/&page=\d+&page/g, '&page');

	var loading = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 100000000
	});

	$("#"+form+' .form-group').removeClass('has-error');

	var cms_post_dofunc = "";
	for(var i = 0; i < cms_post_addfunc.length; i++) {
		var cms_post_dofunc = cms_post_addfunc[i];
		var rst = cms_post_dofunc();
		if (rst) {
			dr_tips(0, rst);
			return;
		}
	}

	$.ajax({
		type: "POST",
		dataType: "json",
		url: url,
		data: $("#"+form).serialize(),
		success: function(json) {
			layer.close(loading);
			// token 更新
			if (json.token) {
				var token = json.token;
				$("#"+form+" input[name='"+token.name+"']").val(token.value);
			}
			if (json.code) {
				dr_tips(1, json.msg, json.data.time);
				if (time) {
					var gourl = url;
					if (go != '' && go != undefined && go != 'undefined') {
						gourl = go;
					} else if (json.data.url) {
						gourl = json.data.url;
					}
					setTimeout("window.location.href = '"+gourl+"'", time);
				}
			} else {
				dr_tips(0, json.msg, json.data.time);
				$('.captcha img').click();
				if (json.data.field) {
					$('#dr_row_'+json.data.field).addClass('has-error');
					if(json.data.jscode){
						dr_safe_execute(json.data.jscode);
					} else {
						if ($('#'+json.data.field).attr('class') == 'dr_ueditor dr_ueditor_'+json.data.field+' edui-default') {
							UE.getEditor(json.data.field).focus();
						} else {
							if($('#'+json.data.field).length > 0){
								$('#'+json.data.field).focus();
							} else {
								$('#dr_'+json.data.field).focus();
							}
						}
					}
				}
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
		}
	});
}
// 动态执行菜单链接
function dr_admin_menu_ajax(url, not_sx) {
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 10000
	});
	$.ajax({type: "GET",dataType:"json", url: url,
		success: function(json) {
			layer.close(index);
			dr_tips(json.code, json.msg);
			if (json.code == 1) {
				if (not_sx) {
					return;
				} else {
					setTimeout("window.location.reload(true)", 2000);
				}
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, this, thrownError);
		}
	});
}
// 提交到执行sql页面 post
function dr_submit_sql_todo(myform, url) {
	var loading = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 1000
	});
	$("#sql_result").html(' ... ');
	$.ajax({type: "POST",dataType:"json", url: url, data: $('#'+myform).serialize(),
		success: function(json) {
			layer.close(loading);
			// token 更新
			if (json.token) {
				var token = json.token;
				$("#"+myform+" input[name='"+token.name+"']").val(token.value);
			}
			if (json.code == 1) {
				$("#sql_result").html('<pre>'+json.msg+'</pre>');
			} else {
				$("#sql_result").html('<div class="alert alert-danger">'+json.msg+'</div>');
			}
			return false;
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, this, thrownError);
		}
	});
}
// 提交到执行页面 post
function dr_submit_sql_todo2(myform, url) {
	var loading = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 1000
	});
	$("#"+myform+" .dr_sql_row").hide();
	$.ajax({type: "POST",dataType:"json", url: url, data: $('#'+myform).serialize(),
		success: function(json) {
			layer.close(loading);
			// token 更新
			if (json.token) {
				var token = json.token;
				$("#"+myform+" input[name='"+token.name+"']").val(token.value);
			}
			if (json.code == 1) {
				dr_tips(1, json.msg);
				if (json.data) {
					$("#"+myform+" .dr_sql_row").show();
					$("#"+myform+" .dr_sql").val(json.data);
				}
			} else {
				dr_tips(0, json.msg, 90000);
			}
			return false;
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError, this);
		}
	});
}
// 安装模块提示
function dr_install_uninstall(msg,url,module,w,h) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	if(!w) w=500;
	if(!h) h=260;
	if (is_mobile()) {
		w = h = '100%';
	}
	Dialog.confirm(msg, function() {
		var t = layer.load(2, {
			shade: [.3, "#fff"],
			time: 5e3
		});
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: $("#myform").serialize()+"&module="+module,
			success: function(e) {
				layer.close(t);
				// token 更新
				if (e.token) {
					var token = e.token;
					$("#myform input[name='"+token.name+"']").val(token.value);
				}
				dr_tips(e.code, e.msg), 1 == e.code && setTimeout("dr_install_confirm()", 2e3)
			},
			error: function(e, t, a) {
				dr_ajax_alert_error(e, t, a)
			}
		})
	}, function() {})
}
// 弹出提示
function dr_install_confirm() {
	Dialog.confirm("确定要刷新整个后台吗？", function() {
		parent.location.reload(!0);
	}, function() {
		window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(!0);
	})
}
// 动态执行链接
function dr_load_ajax(msg, url, go) {
	layer.confirm(msg,{
		icon: 3,
		shade: 0,
		title: '提示',
		btn: ['确定', '取消']
	}, function(index){
		layer.close(index);
		var index = layer.load(2, {
			shade: [0.3,'#fff'], //0.1透明度的白色背景
			time: 5000
		});

		$.ajax({type: "GET",dataType:"json", url: url,
			success: function(json) {
				layer.close(index);
				dr_tips(json.code, json.msg);
				if (go == 1 && json.code > 0) {
					setTimeout("window.location.reload(true)", 2000)
				}
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
				dr_ajax_alert_error(HttpRequest, this, thrownError);
			}
		});
	});
}
// 百分百进度控制
function dr_bfb(title, myform, url) {
	layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 1000
	});
	layer.open({
		type: 2,
		title: title,
		scrollbar: false,
		resize: true,
		maxmin: true,
		shade: 0,
		area: ['80%', '80%'],
		success: function(layero, index){
			// 主要用于后台权限验证
			var body = layer.getChildFrame('body', index);
			var json = $(body).html();
			json = json.replace(/<.*?>/g,"");
			if (json.indexOf('"code":0') > 0 && json.length < 150){
				var obj = JSON.parse(json);
				layer.close(index);
				dr_tips(0, obj.msg);
			}
		},
		content: url+'&'+$('#'+myform).serialize(),
		cancel: function(index, layero){
			var body = layer.getChildFrame('body', index);
			if ($(body).find('#dr_check_status').val() == "1") {
				layer.confirm('关闭后将中断操作，是否确认关闭呢？', {
					icon: 3,
					shade: 0,
					title: '提示',
					btn: ['确定', '取消']
				}, function(index){
					layer.closeAll();
				});
				return false;
			}
		}
	});
}
// 百分百提交再进度控制
function dr_bfb_submit(title, myform, url) {
	var loading = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 1000
	});
	$.ajax({type: "POST",dataType:"json", url: url, data: $('#'+myform).serialize(),
		success: function(json) {
			layer.close(loading);
			// token 更新
			if (json.token) {
				var token = json.token;
				$("#"+myform+" input[name='"+token.name+"']").val(token.value);
			}
			if (json.code == 1) {
				layer.open({
					type: 2,
					title: title,
					scrollbar: false,
					resize: true,
					maxmin: true,
					shade: 0,
					area: ['80%', '80%'],
					success: function(layero, index){
						// 主要用于后台权限验证
						var body = layer.getChildFrame('body', index);
						var json = $(body).html();
						json = json.replace(/<.*?>/g,"");
						if (json.indexOf('"code":0') > 0 && json.length < 150){
							var obj = JSON.parse(json);
							layer.close(index);
							dr_tips(0, obj.msg);
						}
					},
					content: json.data.url,
					cancel: function(index, layero){
						var body = layer.getChildFrame('body', index);
						if ($(body).find('#dr_check_status').val() == "1") {
							layer.confirm('关闭后将中断操作，是否确认关闭呢？', {
								icon: 3,
								shade: 0,
								title: '提示',
								btn: ['确定', '取消']
							}, function (index) {
								layer.closeAll();
							});
							return false;
						}
					}
				});

			} else {
				dr_tips(0, json.msg, 90000);
			}
			return false;
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, this, thrownError);
		}
	});
}
// 提交到执行页面
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
// 提交到执行页面 post
function dr_submit_post_todo(myform, url) {
	var loading = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 1000
	});
	$.ajax({type: "POST",dataType:"json", url: url, data: $('#'+myform).serialize(),
		success: function(json) {
			layer.close(loading);
			// token 更新
			if (json.token) {
				var token = json.token;
				$("#"+myform+" input[name='"+token.name+"']").val(token.value);
			}
			if (json.code == 1) {
				dr_tips(1, json.msg);
			} else {
				dr_tips(0, json.msg, 90000);
			}
			return false;
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, this, thrownError);
		}
	});
}
// 一键更新栏目缓存
function dr_link_ajax_url(url, index) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	if (index == undefined) {
		var index = layer.msg("正在自动更新栏目缓存，请等待...", {time: 999999999});
	}
	$.ajax({
		type: "GET",
		cache: false,
		url: url+"&is_ajax=1",
		dataType: "json",
		success: function (json) {
			if (json.code) {
				if (json.data) {
					dr_link_ajax_url(json.data, index);
				} else {
					layer.close(index);
					setTimeout("window.location.reload(true)", 2000);
					dr_tips(1, json.msg, json.data.time);
				}
			} else {
				dr_tips(0, json.msg);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			layer.close(index);
		}
	});
}
// 打开预览文件
function dr_show_file_code(title, url) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var diag = new Dialog({
		id:'show_file_code',
		title:title,
		url:url,
		width:'80%',
		height:'80%',
		modal:true,
		draggable:true
	});
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
	dr_ajax_alert_error(HttpRequest, this, thrownError);
}
function dr_ajax_alert_error(HttpRequest, ajax, thrownError) {
	layer.closeAll('loading');
	if (typeof is_admin != "undefined" && is_admin) {
		var msg = HttpRequest.responseText;
		var html = '请求状态：'+HttpRequest.status+'<br>';
		html+= '请求方式：'+ajax.type+'<br>';
		html+= '请求地址：'+ajax.url+'<br>';
		if (!msg) {
			msg = thrownError;
		}
		if (is_admin == 1) {
			layer.open({
				type: 1,
				title: '系统崩溃，请检查错误日志',
				fix:true,
				shadeClose: true,
				shade: 0,
				area: ['50%', '50%'],
				btn: ['查看日志'],
				yes: function(index, layero) {
					layer.close(index);
					dr_iframe_show('错误日志', '?m=admin&c=index&a=public_error_log');
				},
				content: '<div style="padding:10px;border-bottom: 1px solid #eee;">'+html+'</div><div style="padding:10px;">'+msg+'</div>'
			});
		} else {
			layer.open({
				type: 1,
				title: '系统崩溃，请检查错误日志',
				fix:true,
				shadeClose: true,
				shade: 0,
				area: ['50%', '50%'],
				content: '<div style="padding:10px;border-bottom: 1px solid #eee;">'+html+'</div><div style="padding:10px;">'+msg+'</div>'
			});
		}
	} else {
		dr_tips(0, '系统错误');
	}
}
function help(url) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	layer.open({
		type: 2,
		title: '<i class="fa fa-question-circle"></i> 在线帮助',
		shadeClose: true,
		scrollbar: false,
		shade: 0,
		area: ['80%', '90%'],
		content: url
	});
}
function dr_help(url) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var diag = new Dialog({
		id:'content_help',
		title:'<i class="fa fa-question-circle"></i> 在线帮助',
		url:url,
		width:'80%',
		height:'90%',
		modal:true,
		draggable:true
	});
	diag.show();
}
// 初始化滚动区域
function dr_slimScroll_init(a, b) {
	if ($().slimScroll) {
		var c = a + ' .scroller',
			e = a + ' .scroller_body';
		if ('1' === $(c).attr('data-inited')) {
			$(c).removeAttr('data-inited');
			$(c).removeAttr('style');
			var d = {};
			$(c).attr('data-handle-color') &&
				(d['data-handle-color'] = $(c).attr('data-handle-color'));
			$(c).attr('data-wrapper-class') &&
				(d['data-wrapper-class'] = $(c).attr('data-wrapper-class'));
			$(c).attr('data-rail-color') &&
				(d['data-rail-color'] = $(c).attr('data-rail-color'));
			$(c).attr('data-always-visible') &&
				(d['data-always-visible'] = $(c).attr('data-always-visible'));
			$(c).attr('data-rail-visible') &&
				(d['data-rail-visible'] = $(c).attr('data-rail-visible'));
			$(c).slimScroll({
				wrapperClass: $(c).attr('data-wrapper-class')
					? $(c).attr('data-wrapper-class')
					: 'slimScrollDiv',
				destroy: !0
			});
			var f = $(c);
			$.each(d, function (a, b) {
				f.attr(a, b);
			});
		}
		e = $(e).height() > b ? b : 'auto';
		$(c).slimScroll({
			allowPageScroll: !1,
			size: '7px',
			color: $(c).attr('data-handle-color')
				? $(c).attr('data-handle-color')
				: '#bbb',
			wrapperClass: $(c).attr('data-wrapper-class')
				? $(c).attr('data-wrapper-class')
				: 'slimScrollDiv',
			railColor: $(c).attr('data-rail-color')
				? $(c).attr('data-rail-color')
				: '#eaeaea',
			position: 'right',
			height: e,
			alwaysVisible: '1' == $(c).attr('data-always-visible') ? !0 : !1,
			railVisible: '1' == $(c).attr('data-rail-visible') ? !0 : !1,
			disableFadeOut: !0
		});
		$(c).attr('data-inited', '1');
	}
}
function check_title(url,title) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var val = $('#'+title).val();
	$.get(url+"&data=" + val + "&is_ajax=1",
	function(data) {
		if (data) {
			dr_tips(0, data);
		}
	});
}
function get_wxurl(syseditor, field, url, formname, titlename, keywordname, contentname) {
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 5000
	});
	$.ajax({type: "POST",dataType:"json", url: url+'&field='+field, data: $('#'+formname).serialize(),
		success: function(json) {
			layer.close(index);
			// token 更新
			if (json.token) {
				var token = json.token;
				$("#"+formname+" input[name='"+token.name+"']").val(token.value);
			}
			dr_tips(json.code, json.msg);
			if (json.code > 0) {
				var arr = json.data;
				$('#'+titlename).val(arr.title);
				if ($('#'+keywordname).length > 0) {
					$('#'+keywordname).val(arr.keyword);
					$('#'+keywordname).tagsinput('add', arr.keyword);
				}
				if (syseditor==1) {
					CKEDITOR.instances[contentname].setData(arr.content);
				} else {
					UE.getEditor(contentname).setContent(arr.content);
				}
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}
/**
 * 安全执行代码函数 - 替代危险的 eval()
 * 
 * @param {string} code - 要执行的函数调用字符串，例如: "dr_iframe_show('show', 'url')"
 * @returns {boolean} 是否成功执行
 */
function dr_safe_execute(code) {
	if (!code || typeof code !== 'string') {
		return false;
	}
	try {
		var sanitized = code.trim();
		// 解析函数调用，例如: dr_iframe_show('show', 'url', '60%', '70%')
		var functionCallMatch = sanitized.match(/^(\w+)\((.*)\)$/);
		if (!functionCallMatch) {
			console.warn('安全警告：无法解析的函数调用格式，已阻止：', sanitized);
			return false;
		}
		var funcName = functionCallMatch[1];
		var paramsStr = functionCallMatch[2];
		// 安全解析参数
		var params = dr_parse_function_params(paramsStr);
		// 调用函数
		if (typeof window[funcName] !== 'function') {
			console.warn('函数不存在：', funcName);
			return false;
		}
		try {
			window[funcName].apply(window, params);
			return true;
		} catch (callError) {
			console.error('调用函数时发生错误：', funcName, callError);
			return false;
		}
	} catch (e) {
		console.error('执行代码时发生错误：', e);
		return false;
	}
}
/**
 * 安全解析函数参数字符串
 * 支持字符串、数字、布尔值、null、undefined
 * 
 * @param {string} paramsStr - 参数字符串，例如: "'show', 'url', 60, true"
 * @returns {array} 解析后的参数数组
 */
function dr_parse_function_params(paramsStr) {
	if (!paramsStr || paramsStr.trim() === '') {
		return [];
	}
	var params = [];
	var current = '';
	var inString = false;
	var stringChar = '';
	var depth = 0; // 括号深度，用于处理嵌套
	for (var i = 0; i < paramsStr.length; i++) {
		var char = paramsStr[i];
		// 处理字符串
		if ((char === '"' || char === "'") && (i === 0 || paramsStr[i-1] !== '\\')) {
			if (!inString) {
				inString = true;
				stringChar = char;
				current += char;
			} else if (char === stringChar) {
				inString = false;
				stringChar = '';
				current += char;
			} else {
				current += char;
			}
			continue;
		}
		// 处理括号（用于数组、对象等）
		if (!inString) {
			if (char === '(' || char === '[' || char === '{') {
				depth++;
				current += char;
				continue;
			} else if (char === ')' || char === ']' || char === '}') {
				depth--;
				current += char;
				continue;
			}
		}
		// 处理参数分隔符
		if (!inString && depth === 0 && char === ',') {
			params.push(dr_parse_single_param(current.trim()));
			current = '';
			continue;
		}
		current += char;
	}
	// 添加最后一个参数
	if (current.trim() !== '') {
		params.push(dr_parse_single_param(current.trim()));
	}
	return params;
}
/**
 * 解析单个参数值
 * 
 * @param {string} paramStr - 参数字符串
 * @returns {*} 解析后的值
 */
function dr_parse_single_param(paramStr) {
	if (!paramStr || paramStr.trim() === '') {
		return undefined;
	}
	paramStr = paramStr.trim();
	// 字符串
	if ((paramStr[0] === '"' && paramStr[paramStr.length - 1] === '"') ||
		(paramStr[0] === "'" && paramStr[paramStr.length - 1] === "'")) {
		// 移除引号并处理转义
		var str = paramStr.slice(1, -1);
		return str.replace(/\\(.)/g, function(match, char) {
			if (char === 'n') return '\n';
			if (char === 't') return '\t';
			if (char === 'r') return '\r';
			return char;
		});
	}
	// 数字
	if (/^-?\d+(\.\d+)?$/.test(paramStr)) {
		return parseFloat(paramStr);
	}
	// 布尔值
	if (paramStr === 'true') {
		return true;
	}
	if (paramStr === 'false') {
		return false;
	}
	// null
	if (paramStr === 'null') {
		return null;
	}
	// undefined
	if (paramStr === 'undefined') {
		return undefined;
	}
	// 其他情况，作为字符串返回（已去除引号的情况）
	return paramStr;
}