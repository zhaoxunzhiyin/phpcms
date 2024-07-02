<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<?php if(ROUTE_A=='init') {?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                <div class="page-body">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form class="form-horizontal" role="form" id="myform">
<div class="table-list">
    <table class="table-checkable">
        <thead>
        <tr class="heading">
        <th class="myselect">
            <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                <span></span>
            </label>
        </th>
        <th width="70" style="text-align:center"><?php echo L('listorder')?></th>
        <th width="60" style="text-align:center"> <?php echo L('可用')?> </th>
        <th width="300"> <?php echo L('chinese_name')?> </th>
        <th width="80" style="text-align:center"> <?php echo L('类型')?> </th>
        <th><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($array)){
    foreach($array as $info){
?>
<tr>
<td class="myselect">
<label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
<input type="checkbox" class="checkboxes<?php echo $info['tid'];?> group-checkable" data-set=".checkboxes<?php echo $info['id'];?>"  name="ids[]" value="<?php echo $info['id'];?>" />
<span></span>
</label>
</td>
<td style="text-align:center"> <input type="text" onblur="dr_ajax_save(this.value, '?m=admin&c=menu&a=listorder&id=<?php echo $info['id'];?>&pc_hash='+pc_hash, 'listorder')" value="<?php echo $info['listorder'];?>" class="displayorder form-control input-sm input-inline input-mini"> </td>
<td style="text-align:center"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=admin&c=menu&a=display_edit&id=<?php echo $info['id'];?>&pc_hash='+pc_hash, 0);" class="badge badge-<?php if ($info['display']) {?>yes<?php } else { ?>no<?php }?>"><i class="fa fa-<?php if ($info['display']) {?>check<?php } else { ?>times<?php }?>"></i></a></td>
<td><?php echo $info['spacer'].' '.$info['title'];?></td>
<td style="text-align:center"><?php echo $info['type'];?></td>
<td>
<a href="?m=admin&c=menu&a=add&parentid=<?php echo $info['id'];?>&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="btn btn-xs blue"> <i class="fa fa-plus"></i> <?php echo L('add')?> </a>
<a href="?m=admin&c=menu&a=edit&id=<?php echo $info['id'];?>&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a>
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
    <div class="col-md-12 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <button type="button" onclick="ajax_option('?m=admin&c=menu&a=delete&menuid=<?php echo $this->input->get('menuid');?>', '<?php echo L('你确定要删除它们吗？');?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
<?php } elseif(ROUTE_A=='add') {?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#language").formValidator({onshow:"<?php echo L("input").L('chinese_name')?>",onfocus:"<?php echo L("input").L('chinese_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('chinese_name')?>"});
		$("#name").formValidator({onshow:"<?php echo L("input").L('menu_name')?>",onfocus:"<?php echo L("input").L('menu_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('menu_name')?>"});
		$("#m").formValidator({onshow:"<?php echo L("input").L('module_name')?>",onfocus:"<?php echo L("input").L('module_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('module_name')?>"});
		$("#c").formValidator({onshow:"<?php echo L("input").L('file_name')?>",onfocus:"<?php echo L("input").L('file_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('file_name')?>"});
		$("#a").formValidator({tipid:'a_tip',onshow:"<?php echo L("input").L('action_name')?>",onfocus:"<?php echo L("input").L('action_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('action_name')?>"});
	})
//-->
</script>
<div class="pad_10">
<form name="myform" id="myform" action="?m=admin&c=menu&a=add" method="post">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" >
        <option value="0"><?php echo L('no_parent_menu')?></option>
<?php echo $select_categorys;?>
</select></td>
      </tr>
      <tr>
        <th > <?php echo L('chinese_name')?>：</th>
        <td><input type="text" name="language" id="language" class="input-text" ></td>
      </tr>
      <tr>
        <th><?php echo L('menu_name')?>：</th>
        <td><input type="text" name="info[name]" id="name" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('module_name')?>：</th>
        <td><input type="text" name="info[m]" id="m" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('file_name')?>：</th>
        <td><input type="text" name="info[c]" id="c" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('action_name')?>：</th>
        <td><input type="text" name="info[a]" id="a" class="input-text" > <span id="a_tip"></span><?php echo L('ajax_tip')?></td>
      </tr>
	<tr>
        <th><?php echo L('att_data')?>：</th>
        <td><input type="text" name="info[data]" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('菜单图标')?>：</th>
        <td><label><input type="text" id="menu_icon" name="info[icon]" class="input-text" ></label> <label><input type="button" name="icon" id="icon" value="选择图标" class="button" onclick="menuicon('icons','?m=admin&c=menu&a=public_icon&value='+$('#menu_icon').val(),'选择图标','80%','80%')"></label></td>
      </tr>
	<tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" checked> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0"> <?php echo L('no')?> <span></span></label>
        </div></td>
      </tr>
</table>
</div>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=admin&c=menu&a=add', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>

</form>
</div>
<?php } elseif(ROUTE_A=='edit') {?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#language").formValidator({onshow:"<?php echo L("input").L('chinese_name')?>",onfocus:"<?php echo L("input").L('chinese_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('chinese_name')?>"});
		$("#name").formValidator({onshow:"<?php echo L("input").L('menu_name')?>",onfocus:"<?php echo L("input").L('menu_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('menu_name')?>"});
		$("#m").formValidator({onshow:"<?php echo L("input").L('module_name')?>",onfocus:"<?php echo L("input").L('module_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('module_name')?>"});
		$("#c").formValidator({onshow:"<?php echo L("input").L('file_name')?>",onfocus:"<?php echo L("input").L('file_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('file_name')?>"});
		$("#a").formValidator({tipid:'a_tip',onshow:"<?php echo L("input").L('action_name')?>",onfocus:"<?php echo L("input").L('action_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('action_name')?>"});
	})
//-->
</script>
<div class="pad_10">
<form name="myform" id="myform" action="?m=admin&c=menu&a=edit" method="post">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<input name="id" type="hidden" value="<?php echo $id?>">
<div class="myfbody">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" style="width:200px;">
 <option value="0"><?php echo L('no_parent_menu')?></option>
<?php echo $select_categorys;?>
</select></td>
      </tr>
      <tr>
        <th> <?php echo L('for_chinese_lan')?>：</th>
        <td><input type="text" name="language" id="language" class="input-text" value="<?php echo L($name,'','',1)?>"></td>
      </tr>
      <tr>
        <th><?php echo L('menu_name')?>：</th>
        <td><input type="text" name="info[name]" id="name" class="input-text" value="<?php echo $name?>"></td>
      </tr>
	<tr>
        <th><?php echo L('module_name')?>：</th>
        <td><input type="text" name="info[m]" id="m" class="input-text" value="<?php echo $m?>"></td>
      </tr>
	<tr>
        <th><?php echo L('file_name')?>：</th>
        <td><input type="text" name="info[c]" id="c" class="input-text" value="<?php echo $c?>"></td>
      </tr>
	<tr>
        <th><?php echo L('action_name')?>：</th>
        <td><input type="text" name="info[a]" id="a" class="input-text" value="<?php echo $a?>">  <span id="a_tip"></span><?php echo L('ajax_tip')?></td>
      </tr>
	<tr>
        <th><?php echo L('att_data')?>：</th>
        <td><input type="text" name="info[data]" class="input-text" value="<?php echo $data?>"></td>
      </tr>
	<tr>
        <th><?php echo L('菜单图标')?>：</th>
        <td><label><input type="text" id="menu_icon" name="info[icon]" class="input-text" value="<?php echo $icon?>"></label> <label><input type="button" name="icon" id="icon" value="选择图标" class="button" onclick="menuicon('icons','?m=admin&c=menu&a=public_icon&value='+$('#menu_icon').val(),'选择图标','80%','80%')"></label></td>
      </tr>
	<tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" <?php if($display) echo 'checked';?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0" <?php if(!$display) echo 'checked';?>> <?php echo L('no')?> <span></span></label>
        </div></td>
      </tr>
</table>
</div>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=admin&c=menu&a=edit', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</form>
</div>
<?php }?>
</body>
</html>