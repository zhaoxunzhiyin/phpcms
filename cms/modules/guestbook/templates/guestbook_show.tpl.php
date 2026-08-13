<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad_10">
<form action="?m=guestbook&c=guestbook&a=show&guestid=<?php echo $guestid;?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">


    <tr>
        <th width="20%"><?php echo L('typeid')?>：</th>
        <td><select name="guestbook[typeid]" id="">
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
        <th width="20%"><?php echo L('guestbook_name')?>：</th>
        <td><?php echo $name;?></td>
    </tr>
    <tr>
        <th width="20%"><?php echo L('sex')?>：</th>
        <td><?php echo $sex;?></td>
    </tr>
    <tr>
        <th width="20%"><?php echo L('lxqq')?>：</th>
        <td><?php echo $lxqq;?></td>
    </tr>
    <tr>
        <th width="20%"><?php echo L('email')?>：</th>
        <td><?php echo $email;?></td>
    </tr>    
    <tr>
        <th width="20%"><?php echo L('shouji')?>：</th>
        <td><?php echo $shouji;?></td>
    </tr>
    <tr>
        <th><?php echo L('web_description')?>：</th>
        <td><?php echo $introduce;?></td>
    </tr>

 
    <tr>
      <th><?php echo L('reply')?>：</th>
      <td> 
        <textarea name="guestbook[reply]" id="reply" cols="45" rows="5"><?php echo $reply;?></textarea></td>
      </tr>
    <tr>
        <th><?php echo L('elite')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="guestbook[elite]" type="radio" value="1" <?php if($elite==1){echo "checked";}?>>&nbsp;<?php echo L('yes')?><span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="guestbook[elite]" type="radio" value="0" <?php if($elite==0){echo "checked";}?>>&nbsp;<?php echo L('no')?><span></span></label>
        </div></td>
    </tr>
     
    <tr>
        <th><?php echo L('passed')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="guestbook[passed]" type="radio" value="1" <?php if($passed==1){echo "checked";}?>>&nbsp;<?php echo L('yes')?><span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="guestbook[passed]" type="radio" value="0" <?php if($passed==0){echo "checked";}?>>&nbsp;<?php echo L('no')?><span></span></label>
        </div></td>
    </tr>
</table>
</form>
</div>
</body>
</html>

