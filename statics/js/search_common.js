function setmodel(value, id, siteid, q) {
	$("#myform").find("#typeid").val(value);
	$("#search a").removeClass();
	id.addClass('on');
	if(q!=null && q!='') {
		window.location='?m=search&c=index&a=init&siteid='+siteid+'&typeid='+value+'&q='+q;
	}
}