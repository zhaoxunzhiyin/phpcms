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
<div class="note note-danger">
    <p><?php echo L('cm_site')?>：<?php if(is_array($sitelist)){
    foreach($sitelist as $site){?>
    <a href="?m=customfield&c=customfield&a=category_list&siteid=<?php echo $site['siteid'];?>&menuid=<?php echo intval($this->input->get('menuid')) ?>"><?php echo $site['name'];?></a>&nbsp;
    <?php }}?></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input type="hidden" value="<?php echo $siteid ?>" name="siteid" />
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-body form">
        <div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th width="70" style="text-align:center"><?php echo L('listorder')?></th>
            <th width="200" style="text-align:center"><?php echo L('cm_cate_name')?></th>
            <th width="80" style="text-align:center"><?php echo L('cm_status')?></th>
            <th><?php echo L('operations_manage')?></th>
        </tr>
    </thead>
<tbody id="listtable">
<?php
    $j = 1;
    foreach($cate_list as $f){
?>
    <tr id="tr<?php echo $j;?>">
        <td align="center">
            <input type="hidden" value="<?php echo $f['id'] ?>" name="postdata[<?php echo $j;?>][id]" />
            <input type="hidden" value="1" name="postdata[<?php echo $j;?>][options]" class="dataoptions" />
            <input name="postdata[<?php echo $j;?>][listorder]" type='text' value='<?php echo $f['listorder']?>' class="displayorder form-control input-sm input-inline input-mini" />
        </td>
        <td align="center"><label><input name="postdata[<?php echo $j;?>][description]" type='text' value='<?php echo $f['description']?>' class="form-control" /></label></td>
        <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j;?>][conf][status]" value="1" <?php if($f['conf']['status'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
        <td><a class="btn btn-xs red" href="javascript:;" onclick="delTr('tr<?php echo $j;?>')"> <i class="fa fa-trash"></i> <?php echo L('delete');?></a></td>
    </tr>
<?php $j++;} ?>
</tbody>
</table>
</div>
            <div class="row list-footer table-checkable">
                <div class="col-md-12 list-select">
                    <label><button type="button" onclick="addTr()" class="btn green btn-sm"> <i class="fa fa-plus"></i> <?php echo L('cm_add');?></button></label>
                </div>
            </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000', '<?php echo dr_now_url();?>')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('cm_save')?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
<script type="text/javascript">
//添加行
var addnum = <?php echo $j+1 ?>;
function addTr(){
    var trHtml   =    "<tr id='ntr" + addnum + "'>";
    trHtml  +=    "<td align='center'>";
    trHtml  +=    "<input type='hidden' value='2' name='postdata[" + addnum + "][options]' class='dataoptions' />";
    trHtml  +=    "<input name='postdata[" + addnum + "][listorder]' type='text' value='0' class='displayorder form-control input-sm input-inline input-mini' />";
    trHtml  +=    "</td>";
    trHtml  +=    "<td><label><input name='postdata[" + addnum + "][description]' type='text' value='' class='form-control' /></label></td>";
    trHtml  +=    "<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][status]' value='1' checked='checked' /><span></span></label></td>";
    trHtml  +=    "<td><a class=\"btn btn-xs red\" href='javascript:;' onclick=\"$('#ntr"+ addnum +"').remove()\"> <i class='fa fa-trash'></i> <?php echo L('delete');?></a></td>";
    trHtml  +=    "</tr>";
    addnum++;
    $('#listtable').append(trHtml);
}
//删除行
function delTr(trid){
    $("#"+trid).hide();
    $("#"+trid+" .dataoptions").val(3);
}
</script>
</body>
</html>