<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
$menu_data = $this->menu_db->get_one(array('name' => 'category_manage', 'm' => 'admin', 'c' => 'category', 'a' => 'init'));?>
<?php echo load_js(JS_PATH.'content_addtop.js');?>
<?php echo load_css(JS_PATH.'jquery-minicolors/jquery.minicolors.css');?>
<?php echo load_js(JS_PATH.'jquery-minicolors/jquery.minicolors.min.js');?>
<script type="text/javascript">var catid=<?php echo intval($catid);?></script>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="page-bar">
    <ul class="page-breadcrumb">
        <?php if(is_mobile()) {?>
        <li class="dropdown"> <a href="javascript:location.reload(true);" class="on"> <i class="fa fa-list"></i> <?php echo L('page_manage');?></a> <a class="dropdown-toggle on" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"><i class="fa fa-angle-double-down"></i></a>
            <ul class="dropdown-menu">
                <li><a href="<?php if(strpos($category['url'],'http://')===false && strpos($category['url'],'https://') ===false) echo siteurl($this->siteid);echo $category['url'];?>" target="_blank"> <i class="fa fa-home"></i> <?php echo L('click_vistor');?> </a></li>
                <li><a href="javascript:dr_iframe_show('<?php echo L('page_field_manage');?>','?m=content&c=sitemodel_field&a=init&modelid=-2&menuid=<?php echo $menu_data['id'];?>&is_menu=1', '80%', '90%');"> <i class="fa fa-code"></i> <?php echo L('page_field_manage');?> </a></li>
                <li class="divider"></li>
                <li><a href="?m=block&c=block_admin&a=public_visualization&catid=<?php echo intval($catid);?>&type=page"> <i class="fa fa-table"></i> <?php echo L('visualization_edit');?> </a></li>
            </ul>
        </li>
        <?php } else {?>
        <li> <a href="javascript:location.reload(true);" class="on"> <i class="fa fa-list"></i> <?php echo L('page_manage');?></a> <i class="fa fa-circle"></i> </li>
        <li> <a href="<?php if(strpos($category['url'],'http://')===false && strpos($category['url'],'https://') ===false) echo siteurl($this->siteid);echo $category['url'];?>" target="_blank"> <i class="fa fa-home"></i> <?php echo L('click_vistor');?></a> <i class="fa fa-circle"></i> </li>
        <li> <a href="javascript:dr_iframe_show('<?php echo L('page_field_manage');?>','?m=content&c=sitemodel_field&a=init&modelid=-2&menuid=<?php echo $menu_data['id'];?>&is_menu=1', '80%', '90%');"> <i class="fa fa-code"></i> <?php echo L('page_field_manage');?></a> <i class="fa fa-circle"></i> </li>
        <li> <a href="?m=block&c=block_admin&a=public_visualization&catid=<?php echo intval($catid);?>&type=page"> <i class="fa fa-table"></i> <?php echo L('visualization_edit');?></a> </li>
        <?php }?>
    </ul>
</div>
<form name="myform" id="myform" action="?m=content&c=content&a=add" class="form-horizontal" method="post">
<input type="hidden" name="dosubmit" value="1" />
<input type="hidden" name="info[catid]" value="<?php echo intval($catid);?>" />
<input type="hidden" name="edit" value="<?php echo $title ? 1 : 0;?>" />
<input type="hidden" name="info[updatetime]" value="<?php echo dr_date($updatetime, 'Y-m-d H:i:s');?>" />
<div class="myfbody" style="margin-top: 20px;padding-top:15px;">
        <div class="row ">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-blue sbold"></span>
                        </div>
                        <div class="actions">
                            <div class="btn-group">
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
<?php
if(is_array($forminfos['base'])) {
 foreach($forminfos['base'] as $field=>$info) {
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
    <label class="control-label col-md-2"><?php if($info['star']){ ?><span class="required" aria-required="true"> * </span><?php } ?><?php echo $info['name']?></label>
    <div class="col-md-10">
        <?php echo $info['form']?>
        <span class="help-block" id="dr_<?php echo $field?>_tips"><?php echo $info['tips']?></span>
    </div>
</div>
<?php
} }
?>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="portlet-body form myfooter">
        <div class="form-actions text-center">
            <label><button type="button" onclick="dr_ajax_submit('?m=content&c=content&a=add', 'myform', '2000', '<?php echo dr_now_url();?>')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit');?></button></label>
        </div>
    </div>
</form>
</div>
</div>
</div>
</body>
</html>