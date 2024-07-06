<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<style type="text/css">
.attachment-list{width:480px;}
.attachment-list .cu{dispaly:block;float:right; background:url(<?php echo IMG_PATH;?>admin_img/cross.png) no-repeat 0px 100%;width:20px; height:16px; overflow:hidden;}
.attachment-list li{width:120px; margin:10px 20px; float:left;}
@media (max-width: 400px) {
.attachment-list{width:300px;}
.attachment-list li{margin:10px 15px;}
}
</style>
<div class="pad-10">
<ul class="attachment-list">
	<?php foreach($thumbs as $thumb) {
    ?>
    <li>
            <img src="<?php echo $thumb['thumb_url']?>" alt="<?php echo $thumb['width']?> X <?php echo $thumb['height']?>" width="120" />
            <span class="tooltips cu" data-original-title="<?php echo L('delete')?>" onclick="thumb_delete('<?php echo urlencode($thumb['thumb_filepath'])?>',this)"></span>
            <?php echo $thumb['width']?> X <?php echo $thumb['height']?>
    </li>
    <?php } ?>
</ul>
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