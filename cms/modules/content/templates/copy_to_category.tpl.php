<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_header = $show_validator = true;
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
<!--
    $(document).ready(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        <?php if (is_array($html) && $html['validator']){ echo $html['validator']; unset($html['validator']); }?>
    })
//-->
</script>
<div class="pad-10">
<div>
<ul class="tabBut cu-li">
<li<?php if ($this->input->get('order')==3) {?> class="on"<?php }?>><a href="?m=content&c=copy&a=init&module=content&classname=push_api&action=category_list_copy&order=3&tpl=copy_to_category&modelid=<?php echo $this->input->get('modelid')?>&catid=<?php echo $this->input->get('catid')?>&id=<?php echo $this->input->get('id')?>"><?php echo L('copy_to_category');?></a></li>
</ul>

<div class='content' style="height:auto;">
<form action="?m=content&c=copy&a=init&module=<?php echo $this->input->get('module')?>&action=<?php echo $this->input->get('action')?>" method="post" name="myform" id="myform">
<input name="dosubmit" type="hidden" value="1">
<input type="hidden" name="modelid" value="<?php echo $this->input->get('modelid')?>">
<input type="hidden" name="catid" value="<?php echo $this->input->get('catid')?>">
<input type='hidden' name="id" value='<?php echo $this->input->get('id')?>'>
<input type="hidden" value="<?php echo $modelid;?>" name="modelid">
<?php
$sitelist = getcache('sitelist','commons');
$siteid = $this->siteid;
echo '<div class="mt-radio-inline">';
    foreach($sitelist as $_k=>$_v) {
        $checked = $_k==$siteid ? 'checked' : '';
        echo '<label class="mt-radio mt-radio-outline"><input type=\'radio\' name=\'select_siteid\' '.$checked.' onclick=\'change_siteid('.$_k.')\'> '.$_v['name'].' <span></span></label>';

    }
echo '</div>';
?>
<input type="hidden" value="<?php echo $siteid;?>" name="siteid" id="siteid">
</div>
</div>
    <div style="width:500px; float:left; margin-right:10px">
    <div class="table-list"><table width="100%" cellspacing="0">
            <thead>
                <tr>
                <th width="100"><?php echo L('catid');?></th>
                <th ><?php echo L('catname');?></th>
                <th width="150" ><?php echo L('select_model_name');?></th>
                </tr>
            </thead>
        <tbody id="load_catgory">
        <?php echo $categorys;?>
        </tbody>
        </table></div>
    </div>

    <div style="overflow:hidden;_float:left;">
    <fieldset>
        <legend><?php echo L('category_checked');?></legend>
    <ul class='list-dot-othors' id='catname'>
    <input type='hidden' name='ids' value="" id="relation"></ul>
    </fieldset>
    </div>
</div>
<style type="text/css">
.line_ff9966,.line_ff9966:hover td{background-color:#FF9966}
.line_fbffe4,.line_fbffe4:hover td {background-color:#fbffe4}
.list-dot-othors li{float:none; width:auto}
</style>

<div class="bk15"></div>

<input type="hidden" name="return" value="<?php echo $return?>" />
</form>

<SCRIPT LANGUAGE="JavaScript">
<!--
    function select_list(obj,title,id) {
        var relation_ids = $('#relation').val();
        var sid = 'v'+id;
        $(obj).attr('class','line_fbffe4');
        var str = "<li id='"+sid+"'>·<span>"+title+"</span><a href='javascript:;' class='close' onclick=\"remove_id('"+sid+"')\"></a></li>";
        $('#catname').append(str);
        if(relation_ids =='' ) {
            $('#relation').val(id);
        } else {
            relation_ids = relation_ids+'|'+id;
            $('#relation').val(relation_ids);
        }
}

function change_siteid(siteid) {
        $("#load_catgory").load("?m=content&c=content&a=public_getsite_categorys&siteid="+siteid);
        $("#siteid").val(siteid);
}
//移除ID
function remove_id(id) {
    $('#'+id).remove();
}
change_siteid(<?php echo $siteid;?>);
//-->
</SCRIPT>