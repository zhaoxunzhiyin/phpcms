<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=formguide&c=formguide_info&a=delete" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr class="heading">
            <th align="center" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <?php 
            if(is_array($list_field)){
            foreach($list_field as $i=>$t){
            ?>
            <th<?php if($t['width']){?> width="<?php echo $t['width'];?>"<?php }?><?php if($t['center']){?> style="text-align:center"<?php }?> class="<?php echo dr_sorting($i);?>" name="<?php echo $i;?>"><?php echo L($t['name']);?></th>
            <?php }}?>
            <th align="center"><?php echo L('operation')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($datas)){
    foreach($datas as $d){
?>   
    <tr>
    <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="did[]" value="<?php echo $d['dataid']?>" />
                        <span></span>
                    </label></td>
    <?php 
    if(is_array($list_field)){
    foreach($list_field as $i=>$tt){
    ?>
    <td<?php if($tt['center']){?> class="table-center" style="text-align:center"<?php }?>><?php echo dr_list_function($tt['func'], $d[$i], $param, $d, $field[$i], $i);?></td>
    <?php }}?>
    <td align="center">
        <label><a class="btn btn-xs blue" href="javascript:check('<?php echo $formid?>', '<?php echo $d['dataid']?>', '<?php echo safe_replace($d['username'])?>');void(0);"> <i class="fa fa-eye"></i> <?php echo L('check')?></a></label>
        <label><a class="btn btn-xs red" href="javascript:void(0);" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('delete')))?>',function(){redirect('?m=formguide&c=formguide_info&a=public_delete&formid=<?php echo $formid?>&did=<?php echo $d['dataid']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('del')?></a></label>
        <?php foreach ($clink as $a) {
            echo ' <label><a class="btn '.$a['color'].' btn-xs" href="'.str_replace(['{formid}', '{id}', '{siteid}', '{m}'], [$formid, $d['dataid'], $this->get_siteid(), ROUTE_M], urldecode($a['url'])).'"><i class="'.$a['icon'].'"></i> '.L($a['name']);
            if ($a['field'] && $this->db->field_exists($a['field'])) {
                echo '（'.intval($r[$a['field']]).'）';
                
            }
            echo '</a></label>';
        }?>
    </td>
    </tr>
<?php 
    }
}
?>
</tbody>
    </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <?php echo $foot_tpl;?>
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
function check(id, did, title) {
    var w = 700;
    var h = 500;
    if (is_mobile()) {
        w = h = '100%';
    }
    var diag = new Dialog({
        id:'check',
        title:'<?php echo L('check')?>--'+title+'<?php echo L('submit_info')?>',
        url:'<?php echo SELF;?>?m=formguide&c=formguide_info&a=public_view&formid='+id+'&did='+did+'&pc_hash='+pc_hash,
        width:w,
        height:h,
        modal:true
    });
    diag.onCancel=function() {
        $DW.close();
    };
    diag.show();
}
</script>