function video_store_select(uploadid, name, textareaid, funcName, pc_hash) {
	var diag = new Dialog({
		id:uploadid,
		title:name,
		url:'index.php?m=video&c=video&a=video2content&pc_hash='+pc_hash,
		width:'565',
		height:'420',
		modal:true
	});
	diag.onOk = function(){
		if(funcName){funcName.apply(this,[uploadid,textareaid]);}else{submit_ckeditor(uploadid,textareaid);}}
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}