function att_cancel(obj,id,source){
	var src = $(obj).children("img").attr("path");
	var filename = $(obj).children("img").attr("filename");
	if($(obj).hasClass('on')){
		$(obj).removeClass("on");
		$('#attachment_'+id).removeClass('on').find('input[type="checkbox"]').prop('checked', false);
		var imgstr = $("#att-status").html();
		var length = $("a[class='on']").children("img").length;
		var strs = filenames = '';
		for(var i=0;i<length;i++){
			strs += '|'+$("a[class='on']").children("img").eq(i).attr('path');
			filenames += '|'+$("a[class='on']").children("img").eq(i).attr('filename');
		}
		$('#att-status').html(strs);
		$('#att-name').html(filenames);
		if(source=='upload') $('#att-status-del').append('|'+id);
	} else {
		$(obj).addClass("on");
		$('#attachment_'+id).addClass('on').find('input[type="checkbox"]').prop('checked', true);
		$('#att-status').append('|'+src);
		$('#att-name').append('|'+filename);
		var imgstr_del = $("#att-status-del").html();
		var imgstr_del_obj = $("a[class!='on']").children("img")
		var length_del = imgstr_del_obj.length;
		var strs_del='';
		for(var i=0;i<length_del;i++){strs_del += '|'+imgstr_del_obj.eq(i).attr('id');}
		if(source=='upload') $('#att-status-del').html(strs_del);
	}
}