<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box table-checkable">
<div class="comment_button"><a href="?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid=<?php echo $commentid?>&hot=0"<?php if (empty($hot)) {?> class="on"<?php }?>>最新</a> <a href="?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid=<?php echo $commentid?>&hot=1"<?php if ($hot==1) {?> class="on"<?php }?>>最热</a></div>     
<label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>
 <form id="myform" name="myform" action="?" method="get">
 <input type="hidden" name="m" value="comment">
  <input type="hidden" name="c" value="check">
   <input type="hidden" name="a" value="ajax_checks">
    <input type="hidden" name="type" value="-1">
    <input type="hidden" name="form" value="1">
    <input type="hidden" name="commentid" value="<?php echo $commentid?>">
<div class="comment">
<?php if(is_array($list)) foreach($list as $v) :
?>
<div  id="tbody_<?php echo $v['id']?>">
<h5 class="mt-checkbox-inline" ><span class="rt"><input class="button" type="button" value="<?php echo L('delete')?>" onclick="check(<?php echo $v['id']?>, -1, '<?php echo $v['commentid']?>')" />
</span><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $v['id']?>"><?php echo direction($v['direction'])?> <?php echo $v['username']?> (<?php echo $v['ip']?>) <?php echo L('chez')?> <?php echo format::date($v['creat_at'], 1)?> <?php echo L('release')?> <?php echo L('support')?>：<?php echo $v['support']?><span></span></label></h5>
    <div class="content">
        <pre><?php echo $v['content']?></pre>
    </div>
    <div class="bk20 hr mb8"></div>
</div>
<?php endforeach;?>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('are_you_sure_you_want_to_delete')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
 </form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function check(id, type, commentid) {
    Dialog.confirm('<?php echo L('are_you_sure_you_want_to_delete')?>',function(){$.get('?m=comment&c=check&a=ajax_checks&id='+id+'&type='+type+'&commentid='+commentid+'&pc_hash='+pc_hash+'&'+Math.random(), function(data){if(data!=1){if(data==0){alert('<?php echo L('illegal_parameters')?>')}else{alert(data)}}else{$('#tbody_'+id).remove();}});});
}
</script>
</body>
</html>