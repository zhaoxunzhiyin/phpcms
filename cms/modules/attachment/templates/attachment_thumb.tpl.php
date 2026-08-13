<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<style type="text/css">
.attachment-list{position:relative;margin-bottom:40px; text-align: center;margin: 0px 0px 20px;cursor: pointer; height:120px; background-color:#ddd;border-radius: 5px; border:solid 1px #ddd;}
.attachment-list .name{color: #fff; height: 20px; line-height: 20px;  position:absolute;  left:0;  font-style: normal; width: 100%!important; font-size: 12px;background: #36c6d3; bottom:0px;}
.attachment-list img{max-width: 100%;}
.attachment-list .on {position: absolute;top: 0;right: 0;border: 0;display: block;color: #c72015;padding: 10px 15px;}
</style>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<div class="row">
    <?php foreach($thumbs as $thumb) {?>
<div class="col-md-2 col-sm-2 col-xs-6">
    <div class="attachment-list">
        <img src="<?php echo $thumb['thumb_url']?>">
        <i class="fa fa-close tooltips on" data-original-title="<?php echo L('delete')?>" onclick="thumb_delete('<?php echo urlencode($thumb['thumb_filepath'])?>',this)"></i>
        <span class="name"><?php echo $thumb['width']?> X <?php echo $thumb['height']?></span>
    </div>
</div>
    <?php } ?>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
<!--
function thumb_delete(filepath,obj){
    Dialog.confirm('<?php echo L('del_confirm')?>',function(){
        $.get('?m=attachment&c=manage&a=public_delthumbs&filepath='+filepath+'&pc_hash=<?php echo dr_get_csrf_token()?>',function(data){
            if(data == 1) $(obj).parent().fadeOut("slow");
        })
    });
};
//-->
</script>
</html>