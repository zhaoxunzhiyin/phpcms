function setmodel(value, id, siteid, keyword) {
	$("#myform").find("#typeid").val(value);
	$("#search a").removeClass('badge-light-primary');
	id.addClass('badge-light-primary');
	if(keyword!=null && keyword!='') {
		window.location='?m=search&c=index&a=init&siteid='+siteid+'&typeid='+value+'&keyword='+keyword;
	}
}