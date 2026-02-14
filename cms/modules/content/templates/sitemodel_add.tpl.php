<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad-lr-10">
<form action="?m=content&c=sitemodel&a=add" method="post" id="myform">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%"  class="table_form">
  <tr>
    <th width="150"><?php echo L('model_name')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="info[name]" id="name" size="30" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','name','tablename',12);"/></label></td>
  </tr>
  <tr>
    <th><?php echo L('model_tablename')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="info[tablename]" id="tablename" size="30"/></label></td>
  </tr>
  <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="info[description]" id="description" size="30"/></label></td>
  </tr>
  <tr>
    <th><?php echo L('封面栏目分页')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[pcatpost]" value="1"  /> <?php echo L('open')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[pcatpost]" value="0" checked /> <?php echo L('close')?> <span></span></label>
    </div>
    <div class="onShow"><?php echo L('栏目封面模板可支持分页功能')?></div></td>
  </tr>
  <tr>
    <th><?php echo L('上下篇循环显示')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[previous]" value="1"  /> <?php echo L('open')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[previous]" value="0" checked /> <?php echo L('close')?> <span></span></label>
    </div>
    <div class="onShow"><?php echo L('上一篇下一篇循环显示')?></div></td>
  </tr>
  <tr>
    <th><?php echo L('updatetime_check')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[updatetime_select]" value="0" checked /> <?php echo L('check_not_default')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[updatetime_select]" value="1"  /> <?php echo L('check_default')?> <span></span></label>
    </div></td>
  </tr>
  <tr>
    <th><?php echo L('自动填充内容描述')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_auto]" value="0" checked /> <?php echo L('自动填充')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_auto]" value="1"  /> <?php echo L('手动填充')?> <span></span></label>
    </div>
    <div class="onShow"><?php echo L('当描述为空时，系统提取内容中的文字来填充描述字段')?></div></td>
  </tr>
  <tr>
    <th><?php echo L('提取内容描述字数')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="setting[desc_limit]" id="desc_limit" size="30" value="200" /></label><div class="onShow"><?php echo L('在内容中提取描述信息的最大字数限制')?></div></td>
  </tr>
  <tr>
    <th><?php echo L('清理描述中的空格')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_clear]" value="0" checked /> <?php echo L('不清理')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_clear]" value="1"  /> <?php echo L('清理空格')?> <span></span></label>
    </div>
    <div class="onShow"><?php echo L('提取描述字段时是否情况空格符号，一般英文站点不需要清理空格')?></div></td>
  </tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
    <legend><?php echo L('template_setting')?></legend>
    <table width="100%"  class="table_form">
    <tr>
  <th width="200"><?php echo L('available_styles');?></th>
        <td>
        <?php echo form::select($style_list, '', 'name="info[default_style]" id="default_style" onchange="load_file_list(this.value)"', L('please_select'))?> 
        </td>
</tr>
        <tr>
        <th width="200"><?php echo L('category_index_tpl')?>：</th>
        <td  id="category_template">
        </td>
      </tr>
      <tr>
        <th width="200"><?php echo L('category_list_tpl')?>：</th>
        <td  id="list_template">
        </td>
      </tr>
      <tr>
        <th width="200"><?php echo L('content_tpl')?>：</th>
        <td  id="show_template">
        </td>
      </tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
    <legend><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" id="other" value="1" name="other"> <?php echo L('other_template_setting')?> <span></span></label></legend>
    <table width="100%" id="other_tab" class="table_form" style="display:none;">
        <tr>
        <th width="200"><?php echo L('admin_content_list')?></th>
        <td  id="admin_list_template"><?php echo $admin_list_template;?>
        </td>
      </tr>
      <tr>
        <th width="200"><?php echo L('member_content_add')?></th>
        <td  id="member_add_template"><?php echo form::select_template($default_style,'member', '', 'name="setting[member_add_template]" id="template_member_add"', 'content_publish')?>
        </td>
      </tr>
</table>
</fieldset>
</form>
</div>
<script language="JavaScript">
<!--
    function load_file_list(id) {
        $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&catid=', function(data){$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
    }
    $("#other").click(function() {
        if ($('#other').is(':checked')) {
            $('#other_tab').show();
        } else {
            $('#other_tab').hide();
        }
    })
    //-->
</script>
</body>
</html>