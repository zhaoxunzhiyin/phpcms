<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form action="?m=link&c=link&a=add" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">


    <tr>
        <th width="20%"><?php echo L('typeid')?>：</th>
        <td><select name="link[typeid]" id="">
        <option value="0">默认分类</option>
        <?php
          $i=0;
          foreach($types as $typeid=>$type){
          $i++;
        ?>
        <option value="<?php echo $type['typeid'];?>"><?php echo $type['name'];?></option>
        <?php }?>
        </select></td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('link_type')?>：</th>
        <td>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="link[linktype]" type="radio" value="1" checked="checked" onclick="$('#logolink').show()" class="radio_style"> <?php echo L('logo_link')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="link[linktype]" value="0" onclick="$('#logolink').hide()" class="radio_style"> <?php echo L('word_link')?> <span></span></label>
        </div>
        </td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('link_name')?>：</th>
        <td><input type="text" name="link[name]" id="name"
            size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('url')?>：</th>
        <td><input type="text" name="link[url]" id="url"
            size="30" class="input-text"></td>
    </tr>
    
    <tr id="logolink">
        <th width="100"><?php echo L('logo')?>：</th>
        <td><?php echo form::images('link[logo]', 'logo', '', 'link')?></td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('username')?>：</th>
        <td><input type="text" name="link[username]" id="username"
            size="30" class="input-text"></td>
    </tr>

 
    <tr>
        <th><?php echo L('web_description')?>：</th>
        <td><textarea name="link[introduce]" id="introduce" cols="50"
            rows="6"></textarea></td>
    </tr>

 
    <tr>
        <th><?php echo L('elite')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="link[elite]" type="radio" value="1">&nbsp;<?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="link[elite]" type="radio" value="0" checked>&nbsp;<?php echo L('no')?> <span></span></label>
        </div></td>
    </tr>
     
    <tr>
        <th><?php echo L('passed')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="link[passed]" type="radio" value="1" checked>&nbsp;<?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="link[passed]" type="radio" value="0">&nbsp;<?php echo L('no')?> <span></span></label>
        </div></td>
    </tr>
</table>
</form>
</div>
</body>
</html> 