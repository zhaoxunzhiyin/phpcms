<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L("peed_your_server")?></p>
</div>
<div class="right-card-box">
<link href="<?php echo CSS_PATH?>progress_bar.css" rel="stylesheet" type="text/css" />
<?php 
$i = 0;
foreach ($this->point as $v) :
$r = $this->db->get_one(array('id'=>$v), 'name');
echo '<b>'.$r['name'].'</b><span class="progress_status" id="status_'.$i.'"><img src="'.IMG_PATH.'msg_img/loading.gif"> '.L("are_release_ing").' </span>';
?>
<div class="progress_bar"><div id="progress_bar_<?php echo $i?>" class="p_bar"></div></div>
<iframe id="iframe_<?php echo $i?>" src="" width="0" height="0"></iframe>
<script type="text/javascript">$(function(){setTimeout("iframe(<?php echo $i?>, '?m=release&c=index&a=public_sync&id=<?php echo $i?>&ids=<?php echo $ids?>&statuses=<?php echo $statuses?>')", 1000)})</script>
<br>
<?php $i++;endforeach;?>
<h5><?php echo L("remind")?></h5>
<ul>
<li><?php echo L("remind_message")?></li>
</ul>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function progress(id, val) {
    var width = $('#progress_bar_'+id).parent('div').width();
    var block = width/100*val;
    $('#progress_bar_'+id).width(block);
}
function iframe(id, url) {
    $('#iframe_'+id).attr('src', url);
}
</script>
</body>
</html>