<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
<li class="on" id="tab_1"><?php echo L('url_list')?></li>
</ul>
<div class="content pad-10" id="show_div_1" style="height:auto">
<?php foreach ($url as $key=>$val):?>
<?php echo $val['title']?><br>
<div style="float:right"><a href="javascript:void(0)" onclick="show_content('<?php echo urlencode($val['url'])?>')"><span><?php echo L('view')?></span></a></div><?php echo $val['url']?>
<hr size="1" />
<?php endforeach;?>
</div>
</div>
</div>
<script type="text/javascript"> 
<!--
function show_content(url) {
    Dialog.tips('<?php echo L('loading')?>',1);
    var w = 700;
    var h = 550;
    if (is_mobile()) {w = h = '100%';}
    $.get("?m=collection&c=node&a=public_test_content&nodeid=<?php echo $nodeid?>&url="+url+'&pc_hash=<?php echo dr_get_csrf_token()?>', function(data){
    var diag = new Dialog({id:'test_view',title:'<?php echo L('content_view')?>',html:'<textarea rows="26" cols="90">'+data+'</textarea>',width:w,height:h,modal:true});diag.show();});
}
//-->
</script>

</body>
</html>