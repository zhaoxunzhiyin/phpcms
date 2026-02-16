<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_header = $show_validator = $show_scroll = true; 
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript" src="<?php echo JS_PATH?>clipboard.min.js"></script>
<div class="pad-10">
<h2 class="title-1 f14 lh28">(<?php echo $r['name'];?>)<?php echo L('get_code_space')?></h2>
<div class="bk10"></div>
<div class="explain-col">
<strong><?php echo L('explain')?>：</strong><br />
<?php echo L('notice')?>
</div>
<div class="bk10"></div>
<?php if($r['type']=='code') {?>
<fieldset>
    <legend><?php echo L('one_way')?></legend>
    <?php echo L('js_code')?><font color='red'><?php echo L('this_way_stat_show')?></font><br />
<input name="jscode1" id="jscode1" value='<?php echo $r['path']?>' style="width:400px"><button class="btn green" data-clipboard-action="copy" data-clipboard-target="#jscode1"><?php echo L('copy_code')?></button>
</fieldset>
<?php } else {?>
<fieldset>
    <legend><?php echo L('one_way')?></legend>
    <?php echo L('js_code')?><font color='red'><?php echo L('this_way_stat_show')?></font><br />
<input name="jscode1" id="jscode1" value='<script language="javascript" src="{APP_PATH}{SELF}?m=poster&c=index&a=show_poster&id=<?php echo $r['spaceid']?>"></script>' style="width:400px"><button class="btn green" data-clipboard-action="copy" data-clipboard-target="#jscode1"><?php echo L('copy_code')?></button>
</fieldset>
<div class="bk10"></div>
<fieldset>
    <legend><?php echo L('second_code')?></legend>
    <?php echo L('js_code_html')?><br />
<input name="jscode2" id="jscode2" value='<script language="javascript" src="{APP_PATH}caches/<?php echo $r['path']?>"></script>' style="width:400px">
<button class="btn green" data-clipboard-action="copy" data-clipboard-target="#jscode2"><?php echo L('copy_code')?></button>
</fieldset>
<?php } ?>
</div>
<script type="text/javascript">
//点击复制
var clipboard = new ClipboardJS('.btn');
</script>
</body>
</html>