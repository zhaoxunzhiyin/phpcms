<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<?php if(ROUTE_A=='manage') {?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
<form name="myform" action="?m=member&c=member_menu&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="80"><?php echo L('listorder');?></th>
            <th width="100">id</th>
            <th><?php echo L('menu_name');?></th>
            <th><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php echo $categorys;?>
    </tbody>
    </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('listorder')?></button></label>
    </div>
    <div class="col-md-7 list-page"></div>
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
        $("#language").formValidator({onshow:"<?php echo L("input", '', 'admin').L('chinese_name')?>",onfocus:"<?php echo L("input").L('chinese_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('chinese_name')?>"});
        $("#name").formValidator({onshow:"<?php echo L("input").L('menu_name')?>",onfocus:"<?php echo L("input").L('menu_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('menu_name')?>"});
        $("#m").formValidator({onshow:"<?php echo L("input").L('module_name')?>",onfocus:"<?php echo L("input").L('module_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('module_name')?>"});
        $("#c").formValidator({onshow:"<?php echo L("input").L('file_name')?>",onfocus:"<?php echo L("input").L('file_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('file_name')?>"});
        $("#a").formValidator({tipid:'a_tip',onshow:"<?php echo L("input").L('action_name')?>",onfocus:"<?php echo L("input").L('action_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('action_name')?>"});
    })
//-->
</script>
<div class="pad_10">
<form name="myform" id="myform" action="?m=member&c=member_menu&a=add" method="post">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" >
        <option value="0"><?php echo L('no_parent_menu')?></option>

</select></td>
      </tr>
      <tr>
        <th> <?php echo L('chinese_name')?>：</th>
        <td><input type="text" name="language" id="language" class="input-text" ></td>
      </tr>

      <tr>
        <th><?php echo L('menu_name')?>：</th>
        <td><input type="text" name="info[name]" id="name" class="input-text" ></td>
      </tr>
<?php if(!$this->input->get('isurl')) {?>
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
<?php }?>
    <tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" checked> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0"> <?php echo L('no')?> <span></span></label>
        </div></td>
      </tr>

    <tr>
        <th><?php echo L('isurl')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isurl]" value="1" onclick="redirect('<?php echo dr_now_url().'&isurl=1';?>')" <?php if($this->input->get('isurl') && $this->input->get('isurl')==1) echo 'checked';?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isurl]" value="0" <?php if(!$this->input->get('isurl')) echo 'checked';?> onclick="redirect('<?php echo dr_now_url().'&isurl=0';?>')"> <?php echo L('no')?> <span></span></label>
        </div></td>
      </tr>
<?php if($this->input->get('isurl') && $this->input->get('isurl')==1) {?>
    <tr>
        <th><?php echo L('url')?>：</th>
        <td><input type="text" name="info[url]" class="input-text" size=80></td>
    </tr>
<?php }?>
</table>
<!--table_form_off-->
</div>
</div>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=member&c=member_menu&a=add', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</div>

</form>

<?php } elseif(ROUTE_A=='edit') {?>
<script type="text/javascript">
<!--
    $(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#language").formValidator({onshow:"<?php echo L("input", '', 'admin').L('chinese_name')?>",onfocus:"<?php echo L("input").L('chinese_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('chinese_name')?>"});
        $("#name").formValidator({onshow:"<?php echo L("input").L('menu_name')?>",onfocus:"<?php echo L("input").L('menu_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('menu_name')?>"});
        $("#m").formValidator({onshow:"<?php echo L("input").L('module_name')?>",onfocus:"<?php echo L("input").L('module_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('module_name')?>"});
        $("#c").formValidator({onshow:"<?php echo L("input").L('file_name')?>",onfocus:"<?php echo L("input").L('file_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('file_name')?>"});
        $("#a").formValidator({tipid:'a_tip',onshow:"<?php echo L("input").L('action_name')?>",onfocus:"<?php echo L("input").L('action_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('action_name')?>"});
    })
//-->
</script>
<div class="pad_10">
<form name="myform" id="myform" action="?m=member&c=member_menu&a=edit" method="post">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<input name="id" type="hidden" value="<?php echo $id?>">
<div class="myfbody">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" style="width:200px;">
 <option value="0"><?php echo L('no_parent_menu')?></option>

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
<?php if(empty($isurl)) {?>
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
<?php }?>
    <tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" <?php if($display) echo 'checked';?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0" <?php if(!$display) echo 'checked';?>> <?php echo L('no')?> <span></span></label>
        </div></td>
      </tr>

    <tr>
        <th><?php echo L('isurl')?>：</th>
        <td><div class="mt-radio-inline">
        <?php if($isurl) {?>
            <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isurl]" value="1" checked> <?php echo L('yes')?> <span></span></label>
        <?php } else {?>
            <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isurl]" value="0" checked> <?php echo L('no')?> <span></span></label>
        <?php }?>
        </div>
        </td>
      </tr>
<?php if(($this->input->get('isurl') && $this->input->get('isurl')==1) || $isurl) {?>
    <tr>
        <th><?php echo L('url')?>：</th>
        <td><input type="text" name="info[url]" class="input-text" size=80 value="<?php echo $url?>"></td>
    </tr>
<?php }?>
</table>
<!--table_form_off-->
</div>
</div>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=member&c=member_menu&a=edit', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</div>

</form>
<?php }?>
</body>
</html>