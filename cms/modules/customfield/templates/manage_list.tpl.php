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
    <a href="?m=customfield&c=customfield&a=manage_list&siteid=<?php echo $site['siteid'];?>&menuid=<?php echo intval($this->input->get('menuid')) ?>"><?php echo $site['name'];?></a>&nbsp;
    <?php }}?></p>
</div>
<?php if(dr_count($root) > 0){ ?>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input type="hidden" value="<?php echo $siteid ?>" name="siteid" />
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
            ?>
            <div class="tab-pane<?php if ($page==$i) {?> active<?php }?>" id="tab_<?php echo $i;?>">
<div class="table-list">
<table width="100%" cellspacing="0" id="listtable<?php echo $r['id'] ?>">
    <thead>
        <tr>
            <th width="70" style="text-align:center"><?php echo L('listorder')?></th>
            <th width="200" style="text-align:center"><?php echo L('cm_field') ?></th>
            <th width="200" style="text-align:center"><?php echo L('cm_value') ?></th>
            <th width="200" style="text-align:center"><?php echo L('cm_description') ?></th>
            <th width="80" style="text-align:center"><?php echo L('cm_status') ?></th>
            <th width="50" style="text-align:center"><?php echo L('cm_textarea') ?></th>
            <th><?php echo L('operations_manage')?></th>
        </tr>
    </thead>
<tbody>
<?php if(is_array($filed_list[$r['id']])){ ?>
<?php foreach($filed_list[$r['id']] as $f){ ?>
    <tr id="tr<?php echo $j ?>">
        <td align="center">
        <input type="hidden" value="<?php echo $f['pid'] ?>" name="postdata[<?php echo $j ?>][pid]" />
        <input type="hidden" value="<?php echo $f['id'] ?>" name="postdata[<?php echo $j ?>][id]" />
        <input type="hidden" value="1" name="postdata[<?php echo $j ?>][options]" class="dataoptions" />
        <input name="postdata[<?php echo $j ?>][listorder]" type='text' value='<?php echo $f['listorder']?>' class="displayorder form-control input-sm input-inline input-mini" />
        </td>
        <td><label><input name="postdata[<?php echo $j ?>][name]" type='text' value='<?php echo $f['name']?>' class="form-control" /></label></td>
        <td><label><input name="postdata[<?php echo $j ?>][val]" type='text' value='<?php echo $f['val']?>' class="form-control" /></label></td>
        <td><label><input name="postdata[<?php echo $j ?>][description]" type='text' value='<?php echo $f['description']?>' class="form-control" /></label></td>
        <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j ?>][conf][status]" value="1" <?php if($f['conf']['status'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
        <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j ?>][conf][textarea]" value="1" <?php if($f['conf']['textarea'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
        <td><a class="btn btn-xs red" href="javascript:;" onclick="delTr('tr<?php echo $j ?>')"><?php echo L('delete');?></a></td>
    </tr>
<?php $j++;}} ?>
<tr><td colspan="7"><input type="button" value=" <?php echo L('cm_add')?> " class="button" onclick="addTr(<?php echo $r['id'] ?>)" /></td></tr>
</tbody>
</table>
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
<?php }else{echo "<div style='text-align:center;padding-top:30px;color:#999'>". L('cm_no_data') ."</div>";} ?>
</div>
</div>
</div>
<script type="text/javascript">
var addnum = <?php echo $j+1 ?>;
//添加行
function addTr(pid){
    var trHtml   =    "<tr id='ntr" + addnum + "'>";
    trHtml  +=    "<td align='center'>";
    trHtml  +=    "<input type='hidden' value='"+ pid +"' name='postdata[" + addnum + "][pid]' />";
    trHtml  +=    "<input type='hidden' value='2' name='postdata[" + addnum + "][options]' class='dataoptions' />";
    trHtml  +=    "<input name='postdata[" + addnum + "][listorder]' type='text' value='0' class='displayorder form-control input-sm input-inline input-mini' />";
    trHtml  +=    "</td>";
    trHtml  +=    "<td><label><input name='postdata[" + addnum + "][name]' type='text' value='' class='form-control' /></label></td>";
    trHtml  +=    "<td><label><input name='postdata[" + addnum + "][val]' type='text' value='' class='form-control' /></label></td>";
    trHtml  +=    "<td><label><input name='postdata[" + addnum + "][description]' type='text' value='' class='form-control' /></label></td>";
    trHtml  +=    "<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][status]' value='1' checked='checked' /><span></span></label></td>";
    trHtml  +=    "<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][textarea]' value='1' /><span></span></label></td>";
    trHtml  +=    "<td><a class=\"btn btn-xs red\" href='javascript:;' onclick=\"delTr('ntr"+ addnum +"')\"><?php echo L('delete');?></a></td>";
    trHtml  +=    "</tr>";
    addnum++;
    var $tr=$("#listtable"+ pid +" tr").eq(-2);
    if($tr.size() <= 1){
        $tr=$("#listtable"+ pid +" tr").eq(-1);
        $tr.before(trHtml);
    }else{$tr.after(trHtml);}
}
//删除行
function delTr(trid){
    $("#"+trid).hide();
    $("#"+trid+" .dataoptions").val(3);
}
</script>
</body>
</html>