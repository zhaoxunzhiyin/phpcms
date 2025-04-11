<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form method="post" action="?m=announce&c=admin_announce&a=add" name="myform" id="myform">
<table class="table_form" width="100%" cellspacing="0">
<tbody>
    <tr>
        <th width="80"><strong><?php echo L('announce_title')?></strong></th>
        <td><input name="announce[title]" id="title" class="input-text" type="text" size="50" ></td>
    </tr>
    <tr>
        <th><strong><?php echo L('startdate')?>：</strong></th>
        <td><?php echo form::date('announce[starttime]', date('Y-m-d H:i:s'), 0)?></td>
    </tr>
    <tr>
        <th><strong><?php echo L('enddate')?>：</strong></th>
        <td><?php echo form::date('announce[endtime]', $an_info['endtime'], 0);?></td>
    </tr>
    <tr>
        <th><strong><?php echo L('announce_content')?></strong></th>
        <td><textarea name="announce[content]" id="content" class="dr_ueditor dr_ueditor_content"></textarea><?php echo form::editor('content');?></td>
    </tr>
    <tr>
          <th><strong><?php echo L('available_style')?>：</strong></th>
        <td>
        <?php echo form::select($template_list, $info['default_style'], 'name="announce[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?> 
        </td>
    </tr>
    <tr>
        <th><strong><?php echo L('template_select')?>：</strong></th>
        <td id="show_template"><script type="text/javascript">$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style=<?php echo $info['default_style']?>&module=announce&templates=show&name=announce&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);});</script></td>
    </tr>
    <tr>
        <th><strong><?php echo L('announce_status')?></strong></th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="announce[passed]" type="radio" value="1" checked>&nbsp;<?php echo L('pass')?><span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="announce[passed]" type="radio" value="0">&nbsp;<?php echo L('unpass')?><span></span></label>
        </div></td>
    </tr>
    </tbody>
</table>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function load_file_list(id) {
    if (id=='') return false;
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=announce&templates=show&name=announce&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);});
}
</script>