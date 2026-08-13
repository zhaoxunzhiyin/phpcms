<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p>标签：{dr_var_value('分类名', '变量名', $siteid)}</p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <?php
            $i = 0;
            foreach($root as $r){
            ?>
            <li<?php if ($page==$i) {?> class="active"<?php }?>>
                <a data-toggle="tab_<?php echo $i?>" onclick="$('#dr_page').val('<?php echo $i;?>')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.$r['description'].'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo $r['description'];}?> </a>
            </li>
            <?php $i++;}?>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <?php
            $i = 0;
            $j = 0;
            foreach($root as $k => $r){
            if(is_array($filed_list[$r['id']])){
            ?>
            <div class="tab-pane<?php if ($page==$i) {?> active<?php }?>" id="tab_<?php echo $i;?>">
                <div class="form-body">
                    <?php
                    foreach($filed_list[$r['id']] as $f){
                    if($f['conf']['status'] == 1){
                    ?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo $f['description']?></label>
                        <div class="col-md-9">
                            <input type="hidden" name="postdata[<?php echo $j ?>][id]" value="<?php echo $f['id'] ?>" />
                            <?php if($f['conf']['type'] == 'textarea'){ ?>
                            <textarea name='postdata[<?php echo $j ?>][val]' class="form-control" style="height:120px;"><?php echo $f['val']?></textarea>
                            <?php }elseif($f['conf']['type'] == 'image'){ ?>
							<?php echo form::images('postdata['.$j.'][val]', 'val_'.$j, $f['val'], 'customfield');?>
                            <?php }elseif($f['conf']['type'] == 'editor'){ ?>
                            <textarea class="dr_ueditor dr_ueditor_val_<?php echo $j ?>" name="postdata[<?php echo $j ?>][val]" id="val_<?php echo $j ?>"><?php echo $f['val']?></textarea>
                            <?php echo form::editor('val_'.$j.'', 'full', 'customfield');?>
                            <?php }else{ ?>
                            <input class="form-control" type="text" name="postdata[<?php echo $j ?>][val]" value="<?php echo $f['val']?>" >
                            <?php } ?>
                        </div>
                    </div>
                    <?php }$j++;}}?>
                </div>
            </div>
            <?php $i++;}?>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('cm_save')?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
</body>
</html>