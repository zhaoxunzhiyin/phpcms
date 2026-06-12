<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<form name="searchform" id="searchform" action="?m=admin&c=ipbanned&a=init&menuid=<?php echo $this->input->get('menuid');?>" method="get"  >
<input type="hidden" value="admin" name="m">
<input type="hidden" value="ipbanned" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" name="dosubmit" value="1">
<div class="col-md-12 col-sm-12">
    <label>IP: </label>
    <label><i class="fa fa-caret-right"></i></label>
    <label><input type="text" value="<?php echo $ip;?>" class="input-text" id="ip" name="search[ip]"></label>
</div>
<div class="col-md-12 col-sm-12">
    <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
</div>
</form>
</div>
<form name="myform" id="myform" action="?m=admin&c=ipbanned&a=delete" method="post" onsubmit="checkuid();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
 <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th align="center" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="200">IP</th>
            <th><?php echo L('deblocking_time')?></th> 
            <th><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php
if(is_array($infos)){
    foreach($infos as $info){
?>
    <tr>
    <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="ipbannedid[]" value="<?php echo $info['ipbannedid']?>" />
                        <span></span>
                    </label></td>
        <td align="left"><span  class="<?php echo $info['style']?>"><?php echo $info['ip']?></span> </td>
        <td align="center"><?php echo dr_date($info['expires'], 'Y-m-d', 'red');?></td>
         <td align="center"><a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=ipbanned&a=delete&ipbannedid=<?php echo $info['ipbannedid']?>', '<?php echo L('confirm', array('message' => L('selected')))?>')"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> </td>
    </tr>
<?php
    }
}
?></tbody>
 </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function checkuid() {
    var ids='';
    $("input[name='ipbannedid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert("<?php echo L('before_select_operation')?>");
        return false;
    } else {
        myform.submit();
    }
}

function checkSubmit()
{
    if (searchform.ip.value=="")
    {
        searchform.ip.focus();
        Dialog.alert("<?php echo L('parameters_error')?>");
        return false;
    }
    else
    {
        if(searchform.ip.value.split(".").length!=4)
        {
            searchform.ip.focus();
            Dialog.alert("<?php echo L('ip_type_error')?>");
            return false;
        }
        else
        {
            for(i=0;i<searchform.ip.value.split(".").length;i++)

            {

                var ipPart;

                ipPart=searchform.ip.value.split(".")[i];

                if(isNaN(ipPart) || ipPart=="" || ipPart==null)

                {

                    searchform.ip.focus();

                    Dialog.alert("<?php echo L('ip_type_error')?>");

                    return false;

                }

                else

                {

                    if(ipPart/1>255 || ipPart/1<0)
                    {
                        searchform.ip.focus();
                        Dialog.alert("<?php echo L('ip_type_error')?>");
                        return false;
                    }
                }
            }
        }
    }
}
</script>