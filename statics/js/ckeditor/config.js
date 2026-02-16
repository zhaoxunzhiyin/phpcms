/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	// config.skin = 'moono';
	// config.uiColor = '#f7f5f4';
	//config.removePlugins = 'elementspath,scayt';
	config.disableNativeSpellChecker = false;
	config.resize_dir = 'vertical';
	config.keystrokes =[[ CKEDITOR.CTRL + 13 /*Enter*/, 'maximize' ]];
	//config.extraPlugins = 'capture,videoforpc,flashplayer';
	config.extraPlugins = 'html5video';
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	config.font_names='宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;微软雅黑/微软雅黑;'+ config.font_names;
};
CKEDITOR.on( 'instanceReady', function( ev ){ with (ev.editor.dataProcessor.writer) { setRules("p", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("h1", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("h2", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("h3", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("h4", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("h5", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("div", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("table", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("tr", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("td", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("iframe", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("li", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("ul", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); setRules("ol", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); } });
//CKEDITOR.plugins.load('pgrfilemanager');
function insert_page(editorid) {
	var editor = CKEDITOR.instances[editorid];
	editor.insertHtml('<p><br/></p>[page]');
	if($('#paginationtype').val()) {
		$('#paginationtype').val(2);
		$('#paginationtype').css("color","red");
	}
}

function insert_page_title(editorid,insertdata) {
	if(insertdata){
		var editor = CKEDITOR.instances[editorid];
		var data = editor.getData();
		var page_title_value = $(".page_"+editorid+"_value").val();
		if(page_title_value==''){
			$(".msg_page_"+editorid+"_value").html("<font color='red'>请输入子标题</font>");
			return false;
		}
		page_title_value = '<p><br/></p>[page]'+page_title_value+'[/page]';
		editor.insertHtml(page_title_value);
		$(".page_"+editorid+"_value").val('');
		$(".msg_page_"+editorid+"_value").html('');
		if($('#paginationtype').val()) {
			$('#paginationtype').val(2);
			$('#paginationtype').css("color","red");
		}
	}else{
		$(".page_"+editorid+"_div").slideDown("fast");
	}
}