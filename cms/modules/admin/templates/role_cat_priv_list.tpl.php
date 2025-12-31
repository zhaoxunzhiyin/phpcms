<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<form action="?m=admin&c=role&a=setting_cat_priv&roleid=<?php echo $roleid?>&siteid=<?php echo $siteid?>&op=2" method="post">
<div class="myfbody">
<div class="table-list" id="load_priv">
<table>
<thead>
<tr>
<th width="60" class='myselect' class="table-center" style="text-align:center"><label class='mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline'><input type='checkbox' class='group-checkable' value='' onclick='select_all(0, this)'><span></span></label></th>
<th align="left"><?php echo L('title_varchar')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('view')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('add')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('edit')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('delete')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('sort')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('push')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('move')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('copy')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('recycle')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('restore')?></th>
<th width="60" class="table-center" style="text-align:center"><?php echo L('update')?></th>
</tr>
</thead>
<tbody>
<?php echo $categorys?>
</tbody>
</table>
</div>
</div>
<div class="portlet-body form myfooter">
<div class="form-actions text-center"><input type="submit" value="<?php echo L('submit')?>" class="button"></div>
</div>
</form>
<script type="text/javascript">
function select_all(name, obj) {
    if (obj.checked) {
        if (name == 0) {
            $.each($("input[type='checkbox']"),function(i,rs){
                if($(this).attr('disabled') != 'disabled'){
                    $(this).prop("checked", true);
                }
            });
            //$("input[type='checkbox']").attr('checked', 'checked');
        } else {
            $.each($("input[type='checkbox'][name='priv[" + name + "][]']"),function(i,rs){
                if($(this).attr('disabled') != 'disabled'){
                    $(this).prop("checked", true);
                }
            });
            //$("input[type='checkbox'][name='priv[" + name + "][]']").attr('checked', 'checked');
        }
    } else {
        if (name == 0) {
            $("input[type='checkbox']").attr('checked', null);
        } else {
            $("input[type='checkbox'][name='priv["+name+"][]']").removeAttr('checked');
        }
    }
}
</script>
</body>
</html>
