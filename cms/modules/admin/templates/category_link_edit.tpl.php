<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<?php echo load_js(JS_PATH.'content_addtop.js');?>
<?php echo load_js(JS_PATH.'cookie.js');?>
<script type="text/javascript">var catid=<?php echo intval($catid);?></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="?m=admin&c=category&a=<?php echo ROUTE_A;?>" class="form-horizontal" method="post" name="myform" id="myform">
<input name="catid" type="hidden" value="<?php echo intval($catid);?>">
<input name="type" type="hidden" value="<?php echo $type;?>">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="portlet light bordered">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('catgory_basic').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('catgory_basic');}?> </a>
            </li>
            <?php if($forminfos && is_array($forminfos['base'])) {?>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('extention_field').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-code"></i> <?php if (is_pc()) {echo L('extention_field');}?> </a>
            </li>
            <?php }?>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('parent_category')?></label>
                        <div class="col-md-9">
                            <?php echo form::select_category('module/category-'.$this->siteid.'-data',$parentid,'name="info[parentid]" id="parentid"',L('please_select_parent_category'),0,-1);?>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_catname">
                        <label class="col-md-2 control-label"><?php echo L('catname')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" name="info[catname]" id="catname" value="<?php echo $catname;?>" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','catname','catdir',12);"></label>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_catdir">
                        <label class="col-md-2 control-label"><?php echo L('catdir')?></label>
                        <div class="col-md-9">
                            <?php if ($parentdir) {?>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $parentdir;?></span>
                                <input class="form-control input-medium" type="text" name="info[catdir]" id="catdir" value="<?php echo htmlspecialchars($catdir);?>">
                            </div>
                            <?php } else {?>
                            <input class="form-control input-large" type="text" name="info[catdir]" id="catdir" value="<?php echo htmlspecialchars($catdir);?>">
                            <?php }?>
                            <span class="help-block" id="dr_catdir_tips"><?php echo L('栏目目录确保唯一，用于url填充或者生成目录')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('catgory_img')?></label>
                        <div class="col-md-9">
                            <?php echo form::images('info[image]', 'image', $image, 'content', $catid);?>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_url">
                        <label class="col-md-2 control-label"><?php echo L('link_url')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="url" name="info[url]" value="<?php echo $url;?>"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ismenu')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='1' <?php if($ismenu) echo 'checked';?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='0' <?php if(!$ismenu) echo 'checked';?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('可用')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[disabled]' value='0' <?php if(!$disabled) echo 'checked';?>> <?php echo L('可用');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[disabled]' value='1' <?php if($disabled) echo 'checked';?>> <?php echo L('禁用');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('禁用状态下此栏目不能正常访问')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('您现在的位置')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='1' <?php if($setting['iscatpos']) echo 'checked';?>> <?php echo L('display');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='0' <?php if(!$setting['iscatpos']) echo 'checked';?>> <?php echo L('hidden');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('前端栏目面包屑导航调用不会显示，但可以正常访问，您现在的位置不显示')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('左侧')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='1' <?php if($setting['isleft']) echo 'checked';?>> <?php echo L('display');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='0' <?php if(!$setting['isleft']) echo 'checked';?>> <?php echo L('hidden');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('前端栏目调用左侧不会显示，但可以正常访问')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <?php if($forminfos && is_array($forminfos['base'])) {?>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

<?php foreach($forminfos['base'] as $field=>$info) {
     if($info['isomnipotent']) continue;
     if($info['formtype']=='omnipotent') {
        foreach($forminfos['base'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
        foreach($forminfos['senior'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
    }
?>
                    <div class="form-group" id="dr_row_<?php echo $field?>">
                        <label class="col-md-2 control-label"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?></label>
                        <div class="col-md-9">
                            <?php echo $info['form']?>
                            <span class="help-block" id="dr_<?php echo $field?>_tips"><?php echo $info['tips']?></span>
                        </div>
                    </div>
<?php }?>

                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>
</form>
</div>
<script type="text/javascript">
function load_file_list(id) {
    if(id=='') return false;
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&catid=<?php echo $catid?>&type=1', function(data){$('#page_template').html(data.page_template);});
}
<?php if(isset($setting['template_list']) && !empty($setting['template_list'])) echo "load_file_list('".$setting['template_list']."')"?>
</script>
</div>
</div>
</body>
</html>