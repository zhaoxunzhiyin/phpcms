<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=collection&c=node&a=del" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="80">ID</th>
            <th><?php echo L('nodename')?></th>
            <th width="180"><?php echo L('lastdate')?></th>
            <th width="300"><?php echo L('content').L('operation')?></th>
            <th><?php echo L('operation')?></th>
        </tr>
    </thead>
<tbody>
<?php
    foreach($nodelist as $k=>$v) {
?>
    <tr>
        <td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['nodeid']?>" name="nodeid[]" />
                        <span></span>
                    </label></td>
        <td><?php echo $v['nodeid']?></td>
        <td><?php echo $v['name']?></td>
        <td><?php echo dr_date($v['lastdate'], null, 'red')?></td>
        <td><a class="btn btn-xs yellow" href="?m=collection&c=node&a=col_url_list&nodeid=<?php echo $v['nodeid']?>"><?php echo L('collection_web_site')?></a> 
        <a class="btn btn-xs green" href="?m=collection&c=node&a=col_content&nodeid=<?php echo $v['nodeid']?>"><?php echo L('collection_content')?></a>
        <a class="btn btn-xs red" href="?m=collection&c=node&a=publist&nodeid=<?php echo $v['nodeid']?>&status=2&menuid=<?php echo $this->input->get('menuid');?>"><?php echo L('public_content')?></a>
        </td>
        <td>
        <a class="btn btn-xs blue" href="javascript:void(0)" onclick="test_spider(<?php echo $v['nodeid']?>)"><?php echo L('test')?></a>
        <a class="btn btn-xs green" href="?m=collection&c=node&a=edit&nodeid=<?php echo $v['nodeid']?>&menuid=<?php echo $this->input->get('menuid');?>"><?php echo L('edit')?></a>
        <a class="btn btn-xs dark" href="javascript:void(0)"  onclick="copy_spider(<?php echo $v['nodeid']?>)"><?php echo L('copy')?></a>
        <a class="btn btn-xs yellow" href="?m=collection&c=node&a=export&nodeid=<?php echo $v['nodeid']?>"><?php echo L('export')?></a>
        
         </td>
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
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
        <label><button type="button" onclick="import_spider()" class="btn green btn-sm"> <i class="fa fa-cloud-upload"></i> <?php echo L('import_collection_points')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
<!--
function test_spider(id) {
    var w = 700;
    var h = 500;
    if (is_mobile()) {
        w = h = '100%';
    }
    var diag = new Dialog({
        id:'test',
        title:'<?php echo L('data_acquisition_testdat')?>',
        url:'<?php echo SELF;?>?m=collection&c=node&a=public_test&nodeid='+id+'&pc_hash='+pc_hash,
        width:w,
        height:h,
        modal:true
    });
    diag.onCancel=function() {
        $DW.close();
    };
    diag.show();
}

function copy_spider(id) {
    artdialog('test','?m=collection&c=node&a=copy&nodeid='+id,'<?php echo L('copy_node')?>',500,220);
}

function import_spider() {
    artdialog('test','?m=collection&c=node&a=node_import&is_iframe=1','<?php echo L('import_collection_points')?>',500,220);
}
//-->
</script>
</body>
</html>