<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form action="?m=link&c=link&a=edit&linkid=<?php echo $linkid;?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">


    <tr>
        <th width="20%"><?php echo L('typeid')?>：</th>
        <td><select name="link[typeid]" id="">
        <option value="0" <?php if($typeid=='0'){echo "selected";}?>>默认分类</option>
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
        <th width="100"><?php echo L('link_type')?>：</th>
        <td>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="link[linktype]" type="radio" value="1"<?php if($linktype == 1){?> checked="checked"<?php }?> onclick="$('#logolink').show()" class="radio_style"> <?php echo L('logo_link')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="link[linktype]" value="0"<?php if($linktype == 0){?> checked="checked"<?php }?> onclick="$('#logolink').hide()" class="radio_style"> <?php echo L('word_link')?> <span></span></label>
        </div>
        </td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('link_name')?>：</th>
        <td><input type="text" name="link[name]" id="name"
            size="30" class="input-text" value="<?php echo $name;?>"></td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('url')?>：</th>
        <td><input type="text" name="link[url]" id="url"
            size="30" class="input-text" value="<?php echo $url;?>"></td>
    </tr>
    <tr id="logolink"<?php if($linktype==0){?> style="display: none;"<?php }?>>
        <th width="100"><?php echo L('logo')?>：</th>
        <td><?php echo form::images('link[logo]', 'logo', $info['logo'], 'link')?></td>
    </tr>
    
    <tr>
        <th width="100"><?php echo L('username')?>：</th>
        <td><input type="text" name="link[username]" id="username"
            size="30" class="input-text" value="<?php echo $username;?>"></td>
    </tr>

 
    <tr>
        <th><?php echo L('web_description')?>：</th>
        <td><textarea name="link[introduce]" id="introduce" cols="50"
            rows="6"><?php echo $introduce;?></textarea></td>
    </tr>

 
    <tr>
        <th><?php echo L('elite')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="link[elite]" type="radio" value="1" <?php if($elite==1){echo "checked";}?>>&nbsp;<?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="link[elite]" type="radio" value="0" <?php if($elite==0){echo "checked";}?>>&nbsp;<?php echo L('no')?> <span></span></label>
        </div></td>
    </tr>
     
    <tr>
        <th><?php echo L('passed')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="link[passed]" type="radio" value="1" <?php if($passed==1){echo "checked";}?>>&nbsp;<?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="link[passed]" type="radio" value="0" <?php if($passed==0){echo "checked";}?>>&nbsp;<?php echo L('no')?> <span></span></label>
        </div></td>
    </tr>
</table>
</form>
</div>
</body>
</html>

