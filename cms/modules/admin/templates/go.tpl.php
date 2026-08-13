<?php if ($goto_url) {?>
<meta http-equiv="refresh" content="0; url=<?php echo $goto_url;?>" />
<?php } else {?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php echo L('没有获取到转向字段值。');?>
<?php }?>