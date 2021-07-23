<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<style type="text/css">
	html{_overflow:hidden}
	.frmaa{float:left;width:80%; min-width: 870px; _width:870px;}
	.rraa{float: right; width:230px;}
	.pt{margin-top: 4px;}
	
</style>
<body style="overflow:hidden">
<div class="pad-10" style="padding-bottom:0px">
<div class="col-right">
<h3 class="f14"><?php echo L('common_variables')?></h3>
<input type="button" class="button pt" onClick="javascript:insertText('{CSS_PATH}')" value="{CSS_PATH}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{JS_PATH}')" value="{JS_PATH}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{IMG_PATH}')" value="{IMG_PATH}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{APP_PATH}')" value="{APP_PATH}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{get_siteid()}')" value="{get_siteid()}" title="获取站点ID"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{loop $data $n $r}')" value="{loop $data $n $r}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{$r[\'url\']}')" value="{$r['url']}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{$r[\'title\']}')" value="{$r['title']}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{$r[\'thumb\']}')" value="{$r['thumb']}" title="<?php echo L('click_into')?>"/><br />
<input type="button" class="button pt" onClick="javascript:insertText('{strip_tags($r[description])}')" value="{strip_tags($r[description])}" title="<?php echo L('click_into')?>"/><br />
<?php if (is_array($file_t_v[$file_t])) { foreach($file_t_v[$file_t] as $k => $v) {?>
 <input type="button" class="button pt" onClick="javascript:insertText('<?php echo $k?>')" value="<?php echo $k?>" title="<?php echo $v ? $v :L('click_into')?>"/><br />
 <?php } }?>
</div>
<div class="col-auto">
<form action="?m=template&c=file&a=edit_file&style=<?php echo $this->style?>&dir=<?php echo $dir?>&file=<?php echo $file?>" method="post" name="myform" id="myform" onsubmit="return check_form();">
<textarea name="code" id="code" style="height: 280px;width:96%; visibility:inherit"><?php echo $data?></textarea>
<div class="bk10"></div>
时间格式：<input type="button" class="button pt" onClick="javascript:insertText('{wordtime($r[\'inputtime\'])}')" value="{wordtime($r['inputtime'])}" title="<?php echo L('click_into')?>"/>显示：刚刚、几秒前、几分钟前、几小时前、几天前、几星期前、几个月前、几年前<br />
时间格式：<input type="button" class="button pt" onClick="javascript:insertText('{formatdate($r[\'inputtime\'])}')" value="{formatdate($r['inputtime'])}" title="<?php echo L('click_into')?>"/>显示：刚刚、几秒前、几分钟前、几小时前、几天前、几星期前、几个月前、几年前<br />
时间格式：<input type="button" class="button pt" onClick="javascript:insertText('{mtime($r[\'inputtime\'])}')" value="{mtime($r['inputtime'])}" title="<?php echo L('click_into')?>"/>显示：今天08:00、昨天08:00、前天08:00<br />
时间格式：<input type="button" class="button pt" onClick="javascript:insertText('{mdate($r[\'inputtime\'])}')" value="{mdate($r['inputtime'])}" title="<?php echo L('click_into')?>"/>显示：刚刚、几分钟前、几小时前<br />
时间格式：<input type="button" class="button pt" onClick="javascript:insertText('{timediff(date(\'Y-m-d H:i:s\',$r[\'inputtime\']),date(\'Y-m-d H:i:s\'))}')" value="{timediff(date('Y-m-d H:i:s',$r['inputtime']),date('Y-m-d H:i:s'))}" title="<?php echo L('click_into')?>"/>显示：1天1小时1分钟1秒<br />
友好的时间：<input type="button" class="button pt" onClick="javascript:insertText('{dr_fdate($r[\'inputtime\'])}')" value="{dr_fdate($r['inputtime'])}" title="<?php echo L('友好的时间')?>"/><br />
<div class="bk10"></div>
<input type="text" id="text" value="" /><input type="button" class="button" onClick="fnSearch()" value="<?php echo L('find_code')?>" /> <?php if ($is_write==0){echo '<font color="red">'.L("file_does_not_writable").'</font>';}?> <?php if (module_exists('tag')) {?><input type="button" class="button" onClick="create_tag()" value="<?php echo L('create_tag')?>" /> <input type="button" class="button" onClick="select_tag()" value="<?php echo L('select_tag')?>" /> <?php }?>
<BR><input type="submit" id="dosubmit" class="button pt" name="dosubmit" value="<?php echo L('submit')?>" />
</form>
</div>
</div>
<script type="text/javascript">
var oRange; 
var intCount = 0;  
var intTotalCount = 100;

function fnSearch() { 
	var strBeReplaced; 
	var strReplace; 
	strBeReplaced = $('#text').val(); 
	findInPage(strBeReplaced);
} 

function create_tag() {
	artdialog('add','?m=tag&c=tag&a=add&ac=js',"<?php echo L('create_tag')?>",700,500);
}

function insertText(text)
{
	$('#code').focus();
	InsetSymbol('code',text)
}

function InsetSymbol(elementId,values) {
	if (document.selection) {
		document.getElementById(elementId).focus();
		var rang = document.selection.createRange();
		rang.select(); rang.text=values;
	} else if (typeof document.getElementById(elementId).selectionStart === 'number' && typeof document.getElementById(elementId).selectionEnd === 'number') {
		var startPos = document.getElementById(elementId).selectionStart,endPos = document.getElementById(elementId).selectionEnd,cursorPos = startPos,tmpStr = document.getElementById(elementId).value;
		document.getElementById(elementId).value = tmpStr.substring(0, startPos) + values + tmpStr.substring(endPos, tmpStr.length);
		cursorPos += values.length;
		document.getElementById(elementId).selectionStart = document.getElementById(elementId).selectionEnd = cursorPos;
	} else {
		$('#'+elementId).value += values;
	}
}

function call(text) {
	$('#code').focus();
    var str = document.selection.createRange();
	var text_lenght = parseInt($('#text_lenght').val());
	str.moveStart("character", text_lenght);
	str.select();
	str.text = text;
}

function GetPoint() {
	if ($.browser.msie) {
		rng = event.srcElement.createTextRange();
		rng.moveToPoint(event.x,event.y);
		rng.moveStart("character",-event.srcElement.value.length);
		var text_lenght = rng.text.length;
	} else {
		//Dialog.alert($('#code').selectionStart);
	}
	$('#text_lenght').val(text_lenght);
}

function select_tag() {
	artdialog('list','?m=tag&c=tag&a=lists',"<?php echo L('tag_list')?>",700,500);
}

function fnNext(){ 
	if (intCount > 0 && intCount < intTotalCount){ 
		intCount = intCount + 1; 
	} else { 
		intCount = 1 ; 
	} 
} 

function check_form() {
	if(findInPage("{php")) {
		Dialog.alert("在线模板编辑禁止提交含有{php 的标签。");
		return false;
	} else if(findInPage("<\?php")) {
		Dialog.alert('在线模板编辑禁止提交含有<\?php 的标签。');
		return false;
	} else {
		myform.submit();
	}
}
var isie = false;
if(navigator.userAgent.indexOf("MSIE")>0) { 
   isie = true;
}
   
var win = window;    // window to search. 
var n   = 0; 

function findInPage(str) { 
  var txt, i, found;
  if (str == "") 
    return false; 
  if (isie) { 
	 txt = win.document.body.createTextRange();
		for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) { 
		  txt.moveStart("character", 1); 
		  txt.moveEnd("textedit"); 
		} 
		if (found) {
		  txt.moveStart("character", -1); 
		  txt.findText(str); 
		  txt.select(); 
		  txt.scrollIntoView();
		  n++;
		  return true;
		} else {
		  if (n > 0) {
			n = 0; 
			findInPage(str);
			return true;
		  }
	  }
	  return false; 
  } else {
		 if (!win.find(str)) 
      while(win.find(str, true)) 
        n++; 
    else
      n++;
    if (n == 0) {
      return false
	 } else {
		 return true;
	 }
  }
}
//--> 
</script>

</body>
</html>