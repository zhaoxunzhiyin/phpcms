<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<link href="<?php echo JS_PATH;?>bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            orientation: "left",
            autoclose: true
        });
    }
});
</script>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
    <div class="right-card-box">
        <div class="row table-search-tool">
            <form name="searchform" action="" method="get" >
            <input type="hidden" value="attachment" name="m">
            <input type="hidden" value="manage" name="c">
            <input type="hidden" value="init" name="a">
            <div class="col-md-12 col-sm-12">
                <label><div class="btn-group dropdown-btn-group">
                    <a class="btn blue btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false" href="javascript:;"><i class="fa fa-th-large"></i> <?php echo L('moudle')?> <i class="fa fa-angle-down"></i></a>
                    <ul class="dropdown-menu">
                        <?php $i = 0;
                        foreach ($modules as $module) {
                        if(in_array($module['module'], array('404','bdts','pay','digg','search','scan','attachment','block','dbsource','template','release','cnzz','comment','mood'))) continue;
                        if (isset($i) && $i) echo '<div class="dropdown-line"></div>';
                        echo '<li><a href='.url_par('module='.$module['module']).' class="dropdown-item"><i class="fa fa-chain"></i> '.$module['name'].'</a></li>';
                        $i++;
                        }?>
                    </ul>
                </div></label>
            </div>
            <?php if ($remote) {?>
            <div class="col-md-12 col-sm-12">
                <label><select name="remote" id="remote" class="form-control">
                    <option value=""> - </option>
                    <?php 
                    if (is_array($remote)) {
                    foreach ($remote as $t) {
                    ?>
                    <option value="<?php echo $t['id'];?>"<?php if ($param['remote']==$t['id']) {?> selected<?php }?>><?php echo $t['name'];?></option>
                    <?php }} ?>
                </select></label>
            </div>
            <?php }?>
            <div class="col-md-12 col-sm-12">
                <label>
                    <select name="field" class="form-control">
                        <option value="aid"> Id </option>
                        <?php 
                        if (is_array($field)) {
                        foreach ($field as $t) {
                        if (dr_is_admin_search_field($t)) {
                        ?>
                        <option value="<?php echo $t['field'];?>"<?php if ($param['field']==$t['field']) {?> selected<?php }?>><?php echo $t['name'];?></option>
                        <?php }}} ?>
                    </select>
                </label>
                <label><i class="fa fa-caret-right"></i></label>
                <label><input class="form-control" name="keyword" id="keyword" value="<?php if(isset($param['keyword'])) echo $param['keyword'];?>"></label>
            </div>
            <div class="col-md-12 col-sm-12">
                <label>
                    <div class="input-group input-medium date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control" value="<?php echo $param['start_uploadtime'];?>" name="start_uploadtime" id="start_uploadtime">
                        <span class="input-group-addon"> <?php echo L('to')?> </span>
                        <input type="text" class="form-control" value="<?php echo $param['end_uploadtime'];?>" name="end_uploadtime" id="end_uploadtime">
                    </div>
                </label>
            </div>
            <div class="col-md-12 col-sm-12">
                <label><button type="submit" class="btn blue btn-sm onloading"><i class="fa fa-search"></i> <?php echo L('search');?></button></label>
            </div>
            </form>
        </div>
        <form class="form-horizontal" role="form" id="myform">
            <div class="table-list">
                <table width="100%" cellspacing="0">
                    <thead>
                    <tr class="heading">
                        <th class="myselect table-checkable">
                            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                                <span></span>
                            </label>
                        </th>
                        <th style="text-align:center" width="90" class="<?php echo dr_sorting('aid');?>" name="aid"><?php echo L('number');?></th>
                        <th style="text-align:center" width="100" class="<?php echo dr_sorting('remote');?>" name="remote"><?php echo L('储存策略');?></th>
                        <th width="100" class="<?php echo dr_sorting('module');?>" name="module"><?php echo L('moudle');?></th>
                        <th width="100" class="<?php echo dr_sorting('catid');?>" name="catid"><?php echo L('catname');?></th>
                        <th class="<?php echo dr_sorting('filename');?>" name="filename"><?php echo L('filename');?></th>
                        <th style="text-align:center" width="120" class="<?php echo dr_sorting('fileext');?>" name="fileext"><?php echo L('fileext');?></th>
                        <th width="100" class="<?php echo dr_sorting('filesize');?>" name="filesize"><?php echo L('filesize');?></th>
                        <th width="160" class="<?php echo dr_sorting('uploadtime');?>" name="uploadtime"><?php echo L('uploadtime');?></th>
                        <th><?php echo L('附件归属');?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($array as $t) {?>
                    <tr class="odd gradeX" id="dr_row_<?php echo $t['aid'];?>">
                        <td class="myselect">
                            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $t['aid'];?>" />
                                <span></span>
                            </label>
                        </td>
                        <td style="text-align:center">
                            <?php echo $t['aid'];?>
                        </td>
                        <td style="text-align:center">
                            <?php echo $t['type'];?>
                        </td>
                        <td><?php echo $t['module'];?></td>
                        <td><?php echo $t['catname'];?></td>
                        <td>
                            <a href="javascript:preview('<?php echo $t['filepath'];?>')"><?php echo $t['filename'];?></a>
                            <a class="btn blue btn-xs" href="javascript:iframe('<?php echo L('改名');?>', '?m=attachment&c=manage&a=public_name_edit&aid=<?php echo $t['aid'];?>', '350px', '220px');"><?php echo L('改名');?></a>
                            <a class="btn green btn-xs" href="javascript:iframe_show('<?php echo L('重新上传');?>', '?m=attachment&c=manage&a=public_file_edit&aid=<?php echo $t['aid'];?>', '350px', '230px');"><?php echo L('重传');?></a>
                            <?php if (dr_is_image($t['filepath'])) {?><a class="btn red btn-xs" href="javascript:iframe('<?php echo L('改图');?>', '?m=attachment&c=manage&a=public_image_edit&aid=<?php echo $t['aid'];?>', '80%');"><?php echo L('改图');?></a><?php }?>
                        </td>
                        <td style="text-align:center"><?php echo $t['fileext'];?></td>
                        <td><?php echo $t['filesize'];?></td>
                        <td><?php echo $t['uploadtime'];?></td>
                        <td><?php echo $t['related'];?></td>
                    </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>

            <div class="row list-footer table-checkable">
                <div class="col-md-5 list-select">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label>
                    <label><button type="button" onclick="ajax_option('?m=attachment&c=manage&a=delete', '<?php echo L('del_confirm')?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button></label>
                    <label>
                        <select name="remote" class="form-control">
                            <option value="-1"> -- </option>
                            <option value="0"> <?php echo L('默认');?> </option>
                            <?php 
                            if (is_array($remote)) {
                            foreach ($remote as $t) {
                            ?>
                            <option value="<?php echo $t['id'];?>"<?php if ($param['remote']==$t['id']) {?> selected<?php }?>><?php echo $t['name'];?></option>
                            <?php }} ?>
                        </select>
                    </label>
                    <label><button type="button" onclick="ajax_option('?m=attachment&c=manage&a=public_type_edit', '<?php echo L('需要手动将这些附件复制到储存策略的目录中，你确定要变更吗？');?>', 1)" class="btn green btn-sm"> <i class="fa fa-cloud"></i> <?php echo L('变更储存策略');?></button></label>
                    <label>
                        <div class="btn-group dropup">
                            <a class="btn blue btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false" href="javascript:;"><i class="fa fa-files-o"></i> <?php echo L('附件状态')?> <i class="fa fa-angle-up"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo url_par('status=0')?>" class="dropdown-item"><i class="fa fa-chain"></i> <?php echo L('not_used');?></a></li>
                                <div class="dropdown-line"></div>
                                <li><a href="<?php echo url_par('status=1')?>" class="dropdown-item"><i class="fa fa-chain"></i> <?php echo L('used');?></a></li>
                            </ul>
                        </div>
                    </label>
                </div>
                <div class="col-md-7 list-page">
                    <?php echo $pages;?>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
<script>
function showthumb(id, name) {
    var width = 500;
    var height = 400;
    if (is_mobile()) {
        width = height = '90%';
    }
    var diag = new Dialog({
        id:'edit',
        title:'<?php echo L('att_thumb_manage')?>--'+name,
        url:'<?php echo SELF;?>?m=attachment&c=manage&a=public_showthumbs&aid='+id+'&is_iframe=1&pc_hash='+pc_hash,
        width:width,
        height:height,
        modal:true
    });
    diag.show();
}
function hoverUse(target){
    if($("#"+target).css("display") == "none"){
        $("#"+target).show();
    }else{
        $("#"+target).hide();
    }
}
</script>
</body>
</html>