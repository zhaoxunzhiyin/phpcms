<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form action="?m=slider&c=slider&a=edit&id=<?php echo $id; ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">


    <tr>
        <th width="100"><?php echo L('typeid')?>：</th>
        <td><select name="slider[typeid]" id="">
        <?php
          $i=0;
          foreach($types as $type_key=>$type){
          $i++;
        ?>
        <option value="<?php echo $type['typeid'];?>" <?php if($type['typeid']==$typeid){echo "selected";}?>><?php echo $type['name'];?></option>
        <?php }?>
             
        </select></td>
    </tr>
    
    
    <tr>
        <th width="100"><?php echo L('slider_name')?>：</th>
        <td><input type="text" name="slider[name]" id="name"
            size="30" class="input-text" value="<?php echo $name;?>"></td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('url')?>：</th>
        <td><input type="text" name="slider[url]" id="url"
            size="30" class="input-text" value="<?php echo $url;?>"></td>
    </tr>

    <tr>
        <th width="100"><?php echo L('image')?>：</th>
        <td><?php echo form::images('slider[image]', 'image', $info['image'], 'slider')?></td>
    </tr>

    <tr>
        <th width="100"><?php echo L('pic')?>：</th>
        <td><?php echo form::images('slider[pic]', 'pic', $info['pic'], 'slider')?></td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('icon')?>：</th>
        <td><label><input type="text" id="menu_icon" name="slider[icon]" class="input-text" value="<?php echo $info['icon'];?>"></label> <label><input type="button" name="icon" id="icon" value="<?php echo L('choose_icon')?>" class="button" onclick="menuicon('icons','?m=admin&c=menu&a=public_icon&value='+$('#menu_icon').val(),'<?php echo L('choose_icon')?>','80%','80%')"></label></td>
    </tr>

    <tr>
        <th width="100"><?php echo L('slider_desc')?>：</th>
        <td><textarea name="slider[description]" id="description" rows="4" cols="50"><?php echo $description;?></textarea></td>
    </tr>

    
    <tr>
        <th width="100"><?php echo L('slider_listorder')?>：</th>
        <td><input type="text" name="slider[listorder]" id="listorder"
            size="10" class="input-text" value="<?php echo $listorder;?>"></td>
    </tr>

     
    <tr>
        <th><?php echo L('status')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="slider[isshow]" type="radio" value="1" <?php if($isshow==1){echo "checked";}?>>&nbsp;<?php echo L('isshow')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="slider[isshow]" type="radio" value="0" <?php if($isshow==0){echo "checked";}?>>&nbsp;<?php echo L('notshow')?> <span></span></label>
        </div></td>
    </tr>
</table>
</form>
</div>
</body>
</html>

