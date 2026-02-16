<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<div class="col-md-12 col-sm-12">
<label><?php echo L('all_linktype')?></label>
<label><i class="fa fa-caret-right"></i></label>
<label><a href="?m=guestbook&c=guestbook"><?php echo L('all')?></a></label>
<label><a href="?m=guestbook&c=guestbook&typeid=0">默认分类</a></label>
            <?php
    if(is_array($type_arr)){
    foreach($type_arr as $typeid => $type){
        ?>
            <label><a href="?m=guestbook&c=guestbook&typeid=<?php echo $typeid;?>"><?php echo $type;?></a></label>
            <?php }}?>

</div>
</div>
  <form name="myform" id="myform" action="?m=guestbook&c=guestbook&a=listorder" method="post" >
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
            <th width="80" align="center"><?php echo L('listorder')?></th>
            <th align="center"><?php echo L('guestbook_name')?></th>
            <th align="center"><?php echo L('sex')?></th>
            <th align="center"><?php echo L('lxqq')?></th>
            <th align="center"><?php echo L('email')?></th>
            <th align="center"><?php echo L('shouji')?></th>
            <th align="center"><?php echo L('web_description')?></th>
            <th align="center"><?php echo L('typeid')?></th>
            <th width="160" align="center"><?php echo L('lytime')?></th>
            <th width="100" align="center"><?php echo L('status')?></th>
            <th align="center"><?php echo L('operations_manage')?></th>
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
                        <input type="checkbox" class="checkboxes" name="guestid[]" value="<?php echo $info['guestid']?>" />
                        <span></span>
                    </label></td>
            <td align="center"><input name='listorders[<?php echo $info['guestid']?>]' type='text' value='<?php echo $info['listorder']?>' class="displayorder form-control input-sm input-inline input-mini"></td>
            <td align="center"><?php echo $info['name']?></td>
            <td align="center"><?php echo $info['sex']?></td>
            <td align="center"><?php echo $info['lxqq'];?></td>
            <td align="center"><?php echo $info['email'];?></td>
            <td align="center"><?php echo $info['shouji'];?></td>
            <td align="center"><?php echo str_cut($info['introduce'] ,'50');?></td>
            <td align="center"><?php if($info['typeid']==0){echo "默认分类";}else{echo $type_arr[$info['typeid']];}?></td>
            <td align="center"><?php echo dr_date($info['addtime'], null, 'red');?></td>
            <td align="center"><?php if($info['passed']=='0'){?>
              <a class="btn btn-xs yellow" onClick="confirmurl('?m=guestbook&c=guestbook&a=check&guestid=<?php echo $info['guestid']?>', '<?php echo L('pass_or_not')?>');"><?php echo L('audit')?></a>
              <?php }else{echo L('passed');}?>
              <br>
              <?php if($info['reply']==''){ echo "<font color=red>【未回复】</font>";}else{echo "<font color=green>【已回复】</font>";}?>
              </td>
            <td align="center"><a class="btn btn-xs blue" href="javascript:void(0);"
            onclick="show(<?php echo $info['guestid']?>, '<?php echo new_addslashes($info['name'])?>')"
            title="<?php echo L('reply')?>"><?php echo L('reply')?></a> <a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=guestbook&c=guestbook&a=delete&guestid=<?php echo $info['guestid']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
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
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('listorder')?></button></label>
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=guestbook&c=guestbook&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
  </form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function show(id, name) {
    artdialog('edit','?m=guestbook&c=guestbook&a=show&guestid='+id,'<?php echo L('show')?> '+name+' ',700,450);
}
</script>
</body>
</html>