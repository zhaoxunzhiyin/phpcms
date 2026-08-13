<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="bk10"></div>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jquery.treeview.css" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.treeview.js"></script>
<?php if($ajax_show) {?>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.treeview.async.js"></script>
<?php }?>
<SCRIPT LANGUAGE="JavaScript">
<!--
<?php if($ajax_show) {?>
$(document).ready(function(){
    $("#category_tree").treeview({
            control: "#treecontrol",
            url: "<?php echo SELF;?>?m=content&c=content&a=public_sub_categorys&menuid=<?php echo $this->input->get('menuid');?>",
            ajax: {
                data: {
                    "additional": function() {
                        return "time: " + new Date;
                    },
                    "modelid": function() {
                        return "<?php echo $modelid?>";
                    }
                },
                type: "post"
            }
    });
});
<?php } else {?>
$(document).ready(function(){
    $("#category_tree").treeview({
        control: "#treecontrol",
        persist: "cookie",
        cookieId: "treeview-black"
    });
});
<?php }?>
function open_list(obj) {
    window.top.$("#current_pos_attr").html($(obj).html());
}

//-->
</SCRIPT>
 <style type="text/css">
 .treelistmain {margin-left: 10px;}
.filetree *{white-space:nowrap;}
.filetree span.folder, .filetree span.file{display:auto;padding:1px 0 1px 16px;}
 </style>
<div class="treelistmain">
  <div id="treecontrol">
  <span style="display:none">
    <a href="javascript:void(0);"></a>
    <a href="javascript:void(0);"></a>
    </span>
    <a href="javascript:void(0);"><img src="<?php echo IMG_PATH;?>minus.gif" /> <img src="<?php echo IMG_PATH;?>application_side_expand.png" /> 展开/收缩</a>
</div>
<?php
 if($this->input->get('from')=='block') {
?>
<ul class="filetree treeview"><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><img src="<?php echo IMG_PATH.'icon/home.png';?>" width="15" height="14">&nbsp;<a href='?m=block&c=block_admin&a=public_visualization&type=index' target='<?php echo $this->input->get('from');?>_right'><?php echo L('block_site_index');?></a></span></li></ul>
<?php } else { ?>
<ul class="filetree treeview"><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><img src="<?php echo IMG_PATH.'icon/home.png';?>" width="15" height="14">&nbsp;<a href='?m=content&c=content&a=initall&menuid=<?php echo $this->input->get('menuid');?>' target='right'><?php echo L('allcontent');?></a></span></li></ul>
<ul class="filetree treeview"><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><img src="<?php echo IMG_PATH.'box-exclaim.png';?>" width="15" height="14">&nbsp;<a href='?m=content&c=content&a=public_checkall&menuid=<?php echo $this->input->get('menuid');?>' target='right'><?php echo L('checkall_content');?></a></span></li></ul>
<?php } echo $categorys; ?>
</div>