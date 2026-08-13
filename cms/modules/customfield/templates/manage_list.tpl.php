<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript" src="<?php echo JS_PATH?>clipboard.min.js"></script>
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
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th width="70" style="text-align:center"><?php echo L('listorder')?></th>
            <th width="200" style="text-align:center"><?php echo L('cm_description') ?></th>
            <th width="150" style="text-align:center"><?php echo L('cm_type') ?></th>
            <th width="200" style="text-align:center"><?php echo L('cm_field') ?></th>
            <th width="80" style="text-align:center"><?php echo L('cm_status') ?></th>
            <th width="100" style="text-align:center"><?php echo L('cm_lable') ?></th>
            <th><?php echo L('operations_manage')?></th>
        </tr>
    </thead>
<tbody id="listtable<?php echo $r['id'];?>">
<?php if(is_array($filed_list[$r['id']])){ ?>
<?php foreach($filed_list[$r['id']] as $f){ ?>
    <tr id="tr<?php echo $j?>">
        <td align="center">
        <input type="hidden" value="<?php echo $f['pid'] ?>" name="postdata[<?php echo $j;?>][pid]" />
        <input type="hidden" value="<?php echo $f['id'] ?>" name="postdata[<?php echo $j;?>][id]" />
        <input type="hidden" value="1" name="postdata[<?php echo $j;?>][options]" class="dataoptions" />
        <input name="postdata[<?php echo $j;?>][listorder]" type='text' value='<?php echo $f['listorder']?>' class="displayorder form-control input-sm input-inline input-mini" />
        </td>
        <td><label><input name="postdata[<?php echo $j;?>][description]" id="description_<?php echo $j;?>" type='text' value='<?php echo $f['description']?>' class="form-control" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','description_<?php echo $j;?>','name_<?php echo $j;?>',12);" /></label></td>
        <td align="center"><label><select name="postdata[<?php echo $j;?>][conf][type]" class="form-control"><option value='text'<?php if ($f['conf']['type']=='text') {echo ' selected';}?>><?php echo L('cm_text') ?></option><option value='textarea'<?php if ($f['conf']['type']=='textarea') {echo ' selected';}?>><?php echo L('cm_textarea');?></option><option value='image'<?php if ($f['conf']['type']=='image') {echo ' selected';}?>><?php echo L('cm_image') ?></option><option value='editor'<?php if ($f['conf']['type']=='editor') {echo ' selected';}?>><?php echo L('cm_editor') ?></option></select></label></td>
        <td><label><input name="postdata[<?php echo $j;?>][name]" id="name_<?php echo $j;?>" type='text' value='<?php echo $f['name']?>' class="form-control" /></label></td>
        <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j;?>][conf][status]" value="1" <?php if($f['conf']['status'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
        <td align="center"><a class="btn btn-xs yellow copy" data-clipboard-action="copy" data-clipboard-text="<?php if ($f['conf']['type']=='image') {?>{dr_get_file(dr_var_value('<?php echo $r['description'];?>', '<?php echo $f['name'];?>', $siteid))}<?php }else{?>{dr_var_value('<?php echo $r['description'];?>', '<?php echo $f['name'];?>', $siteid)}<?php }?>"> <i class="fa fa-copy"></i> <?php echo L('cm_copy');?></a></td>
        <td><a class="btn btn-xs red" href="javascript:;" onclick="delTr('tr<?php echo $j;?>')"> <i class="fa fa-trash"></i> <?php echo L('delete');?></a></td>
    </tr>
<?php $j++;}} ?>
</tbody>
</table>
</div>
            <div class="row list-footer table-checkable">
                <div class="col-md-12 list-select">
                    <label><button type="button" onclick="addTr(<?php echo $r['id'];?>, '<?php echo $r['description'];?>')" class="btn green btn-sm"> <i class="fa fa-plus"></i> <?php echo L('cm_add');?></button></label>
                </div>
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
function addTr(pid, description){
    var trHtml   =    "<tr id='ntr" + addnum + "'>";
    trHtml  +=    "<td align='center'>";
    trHtml  +=    "<input type='hidden' value='"+ pid +"' name='postdata[" + addnum + "][pid]' />";
    trHtml  +=    "<input type='hidden' value='2' name='postdata[" + addnum + "][options]' class='dataoptions' />";
    trHtml  +=    "<input name='postdata[" + addnum + "][listorder]' type='text' value='0' class='displayorder form-control input-sm input-inline input-mini' />";
    trHtml  +=    "</td>";
    trHtml  +=    "<td><label><input name='postdata[" + addnum + "][description]' id='description_" + addnum + "' type='text' value='' class='form-control' onblur=\"topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','description_" + addnum + "','name_" + addnum + "',12);\" /></label></td>";
    trHtml  +=    "<td align='center'><label><select name='postdata[" + addnum + "][conf][type]' class='form-control'><option value='text' selected><?php echo L('cm_text') ?></option><option value='textarea'><?php echo L('cm_textarea') ?></option><option value='image'><?php echo L('cm_image') ?></option><option value='editor'><?php echo L('cm_editor') ?></option></select></label></td>";
    trHtml  +=    "<td><label><input name='postdata[" + addnum + "][name]' id='name_" + addnum + "' type='text' value='' class='form-control' /></label></td>";
    trHtml  +=    "<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][status]' value='1' checked='checked' /><span></span></label></td>";
    trHtml  +=    "<td align='center'><a class='btn btn-xs yellow copy' data-clipboard-action='copy' data-clipboard-text=\"{dr_var_value('" + description + "', '变量名', $siteid)}\"> <i class='fa fa-copy'></i> <?php echo L('cm_copy');?></a></td>";
    trHtml  +=    "<td><a class=\"btn btn-xs red\" href='javascript:;' onclick=\"$('#ntr"+ addnum +"').remove()\"> <i class='fa fa-trash'></i> <?php echo L('delete');?></a></td>";
    trHtml  +=    "</tr>";
    addnum++;
    $('#listtable'+pid).append(trHtml);
}
//删除行
function delTr(trid){
    $("#"+trid).hide();
    $("#"+trid+" .dataoptions").val(3);
}
var clipboard = new ClipboardJS('.copy');
clipboard.on('success', function (e) {
    dr_tips(1, '已复制');
});
clipboard.on('error', function (e) {
    dr_tips(0, '复制失败');
});
</script>
</body>
</html>