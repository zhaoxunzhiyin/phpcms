<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form method="post" action="?m=announce&c=admin_announce&a=edit&aid=<?php echo $this->input->get('aid')?>" name="myform" id="myform">
<table class="table_form" width="100%">
<tbody>
    <tr>
        <th width="80"><?php echo L('announce_title')?></th>
        <td><input name="announce[title]" id="title" value="<?php echo new_html_special_chars($an_info['title'])?>" class="input-text" type="text" size="50" ></td>
    </tr>
    <tr>
        <th><?php echo L('startdate')?>：</th>
        <td><?php echo form::date('announce[starttime]', $an_info['starttime'], 0)?></td>
    </tr>
    <tr>
        <th><?php echo L('enddate')?>：</th>
        <td><?php $an_info['endtime'] = $an_info['endtime']=='0000-00-00' ? '' : $an_info['endtime']; echo form::date('announce[endtime]', $an_info['endtime'], 0);?></td>
    </tr>
    <tr>
        <th><?php echo L('announce_content')?></th>
        <td >
        <textarea name="announce[content]" id="content" class="dr_ueditor dr_ueditor_content"><?php echo $an_info['content']?></textarea>
        <?php echo form::editor('content','basic');?>
        </td>
    </tr>
    <tr>
          <th><strong><?php echo L('available_style')?>：</strong></th>
        <td><?php echo form::select($template_list, $an_info['style'], 'name="announce[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?></td>
    </tr>
    <tr>
        <th><?php echo L('template_select')?>：</th>
        <td  id="show_template"><?php if ($an_info['style']) echo '<script type="text/javascript">$.getJSON(\'?m=admin&c=category&a=public_tpl_file_list&style='.$an_info['style'].'&id='.$an_info['show_template'].'&module=announce&templates=show&name=announce&pc_hash=\'+pc_hash, function(data){$(\'#show_template\').html(data.show_template);});</script>'?></td>
    </tr>
    <tr>
        <th><?php echo L('announce_status')?></th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="announce[passed]" type="radio" value="1" <?php if($an_info['passed']==1) {?>checked<?php }?>>&nbsp;<?php echo L('pass')?><span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="announce[passed]" type="radio" value="0" <?php if($an_info['passed']==0) {?>checked<?php }?>>&nbsp;<?php echo L('unpass')?><span></span></label>
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
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=announce&templates=show&name=announce&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);});
}
</script>