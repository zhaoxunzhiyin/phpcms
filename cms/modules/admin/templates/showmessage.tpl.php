<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<title><?php echo L('message_tips');?></title>
<meta name="author" content="zhaoxunzhiyin" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="<?php echo CSS_PATH?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>admin/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javaScript" src="<?php echo JS_PATH?>jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>Dialog/main.js"></script>
<script language="JavaScript" src="<?php echo JS_PATH?>admin_common.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>layer/layer.js"></script>
</head>
<body>
<div class="showMsg" style="text-align:center">
	<h5><?php echo L('message_tips');?></h5>
    <div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline;max-width:330px"><?php echo $msg?></div>
    <div class="bottom">
    <?php if($url_forward=='goback' || $url_forward=='') {?>
	<a href="javascript:history.back();" >[<?php echo L('return_previous');?>]</a>
	<?php } elseif($url_forward=="close") {?>
	<input type="button" name="close" value="<?php echo L('close');?> " onClick="window.close();">
	<?php } elseif($url_forward=="blank") {?>
	
	<?php } elseif($url_forward) { 
		if(strpos($url_forward,'&pc_hash')===false) $url_forward .= '&pc_hash='.$_SESSION['pc_hash'];
	?>
	<a href="<?php echo $url_forward?>"><?php echo L('click_here');?></a>
	<script language="javascript">setTimeout("redirect('<?php echo $url_forward?>');",<?php echo $ms?>);</script> 
	<?php }?>
	<?php if($returnjs) { ?> <script style="text/javascript"><?php echo $returnjs;?></script><?php } ?>
	<?php if ($dialog):?><script style="text/javascript">$(function(){if (window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right) {window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right.location.reload(true);}else{window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);}if(window.top.art.dialog({id:"<?php echo $dialog?>"})){window.top.art.dialog({id:"<?php echo $dialog?>"}).close();}else{ownerDialog.close();}})</script><?php endif;?>
        </div>
</div>
<script style="text/javascript">
	function close_dialog() {
		if (window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right) {window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right.location.reload(true);}else{window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);}
		if(window.top.art.dialog({id:"<?php echo $dialog?>"})){window.top.art.dialog({id:"<?php echo $dialog?>"}).close();}else{ownerDialog.close();}
	}
</script>

</body>
</html>