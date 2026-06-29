<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="col-tab">
<ul class="tabBut cu-li">
<li <?php if(empty($status)) echo 'class="on" '?>id="tab_1"><a href="?m=collection&c=node&a=publist&nodeid=<?php echo $nodeid?>"><?php echo L('all')?></a></li>
<li <?php if($status==1) echo 'class="on" '?>id="tab_1"><a href="?m=collection&c=node&a=publist&nodeid=<?php echo $nodeid?>&status=1"><?php echo L('if_bsnap_then')?></a></li>
<li <?php if($status==2) echo 'class="on" '?> id="tab_2"><a href="?m=collection&c=node&a=publist&nodeid=<?php echo $nodeid?>&status=2"><?php echo L('spidered')?></a></li>
<li <?php if($status==3) echo 'class="on" '?> id="tab_3"><a href="?m=collection&c=node&a=publist&nodeid=<?php echo $nodeid?>&status=3"><?php echo L('imported')?></a></li>
</ul>
<div class="content pad-10" id="show_div_1" style="height:auto">
<form name="myform" id="myform" action="" method="get">
<div id="form_">
<input type="hidden" name="m" value="collection" />
<input type="hidden" name="c" value="node" />
<input type="hidden" name="a" value="content_del" />
</div>
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th  align="left" width="20" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th align="left"><?php echo L('status')?></th>
            <th align="left"><?php echo L('title')?></th>
            <th align="left"><?php echo L('url')?></th>
            <th align="left"><?php echo L('operation')?></th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($data) && !empty($data))foreach($data as $k=>$v) {
?>
    <tr>
        <td align="left" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['id']?>" name="id[]" />
                        <span></span>
                    </label></td>
        <td align="left"><?php if ($v['status'] == '0') {echo L('if_bsnap_then');} elseif ($v['status'] == 1) {echo L('spidered');} elseif ($v['status'] == 2) {echo L('imported');} ?></td>
        <td align="left"><?php echo $v['title']?></td>
        <td align="left"><?php echo $v['url']?></td>
        <td align="left"><a href="javascript:void(0)" onclick="$('#tab_<?php echo $v['id']?>').toggle()"><?php echo L('view')?></a></td>
    </tr>
      <tr id="tab_<?php echo $v['id']?>" style="display:none">
        <td align="left" colspan="5"><textarea style="width:98%;height:300px;"><?php echo new_html_special_chars(print_r(string2array($v['data']),true))?></textarea></td>
    </tr>
<?php
    }

?>
</tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="button" onclick="re_url('m=collection&c=node&a=content_del&nodeid=<?php echo $nodeid?>');return check_checkbox(1);" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
        <label><button type="button" onclick="re_url('m=collection&c=node&a=content_del&nodeid=<?php echo $nodeid?>&history=1');return check_checkbox(1);" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('also_delete_the_historical')?></button></label>
        <label><button type="submit" onclick="re_url('m=collection&c=node&a=import&nodeid=<?php echo $nodeid?>&menuid=<?php echo $this->input->get('menuid');?>');return check_checkbox();" class="btn green btn-sm"> <i class="fa fa-cloud-upload"></i> <?php echo L('import_selected')?></button></label>
        <label><button type="submit" onclick="re_url('m=collection&c=node&a=import&type=all&nodeid=<?php echo $nodeid?>&menuid=<?php echo $this->input->get('menuid');?>')" class="btn blue btn-sm"> <i class="fa fa-cloud-upload"></i> <?php echo L('import_all')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
<!--
function re_url(url) {
    var urls = url.split('&');
    var num = urls.length;
    var str = '';
    for (var i=0;i<num;i++){
        var a = urls[i].split('=');
        str +='<input type="hidden" name="'+a[0]+'" value="'+a[1]+'" />';
    }
    $('#form_').html(str);
}

function check_checkbox(obj) {
    var checked = 0;
    $("input[type='checkbox'][name='id[]']").each(function (i,n){if (this.checked) {
        checked = 1;
    }});
    if (checked != 0) {
        if (obj) {
            Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});
        }
        return true;
    } else {
        Dialog.alert('<?php echo L('select_article')?>');
        return false;
    }
}
//-->
</script>
</body>
</html>