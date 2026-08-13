<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form action="?m=vote&c=vote&a=add" method="post" name="myform" id="myform">
<input type="hidden"name="from_api" value="<?php echo $this->input->get('from_api');?>">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

    <tr id="dr_row_title">
        <th width="100"><?php echo L('vote_title')?> :</th>
        <td><input type="text" name="subject[subject]" id="title"
            size="30" class="input-text"></td>
    </tr>

    <tr>
        <th width="20%"><?php echo L('select_type')?> :</th>
        <td><select name="subject[ischeckbox]" id=""
            onchange="AdsType(this.value)">
            <option value="0"><?php echo L('radio');?></option>
            <option value="1"><?php echo L('checkbox');?></option>
        </select></td>
    </tr>

    <tr id="SizeFormat" style="display: none;">
        <th></th>
        <td><label><?php echo L('minval')?></label>&nbsp;&nbsp;<input name="subject[minval]"
            class="input-text" type="text" size="5"> <?php echo L('item')?> &nbsp;&nbsp;&nbsp;&nbsp; <label><?php echo L('maxval')?></label>&nbsp;&nbsp;<input
            name="subject[maxval]" type="text" class="input-text" size="5"> <?php echo L('item')?></td>
    </tr>

    <tr>
        <th width="20%"><?php echo L('vote_option')?> :</th>
        <td>
        <input type="button" id="addItem" value="<?php echo L('add_option')?>" class="button" onclick="add_option()">

        <div id="option_list_1">
        <div id="dr_row_option1"><br> <input type="text"
            name="option[]" id="option1" size="40" require="true"
            id="opt1"/></div>

        <div id="dr_row_option2"><br>
        <input type="text"
            name="option[]" id="option2"  size="40"
            id="opt2" /></div>

        </div>
        
        <div id="new_option"></div>


        </td>
    </tr>


    <tr>
        <th><?php echo L('fromdate')?> :</th>
        <td><?php echo form::date('subject[fromdate]', '', '')?></td>
    </tr>
    <tr>
        <th><?php echo L('todate')?> :</th>
        <td><?php echo form::date('subject[todate]', '', '')?></td>
    </tr>
    <tr>
        <th><?php echo L('vote_description')?></th>
        <td><textarea name="subject[description]" id="description" cols="60"
            rows="6"></textarea></td>
    </tr>


    <tr>
        <th><?php echo L('allowview')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="subject[allowview]" type="radio" value="1" checked>&nbsp;<?php echo L('allow')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="subject[allowview]" type="radio" value="0">&nbsp;<?php echo L('not_allow')?> <span></span></label>
        </div></td>
    </tr>
    <tr>
        <th><?php echo L('allowguest')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="subject[allowguest]" type="radio" value="1" <?php if($allowguest == 1) {?>checked<?php }?>>&nbsp;<?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="subject[allowguest]" type="radio" value="0" <?php if($allowguest == 0) {?>checked<?php }?>>&nbsp;<?php echo L('no')?> <span></span></label>
        </div></td>
    </tr>
    <tr>
        <th><?php echo L('credit')?>：</th>
        <td><input name="subject[credit]" type="text" value="<?php echo $credit;?>" size='5'></td>
    </tr>
    
    <tr>
        <th><?php echo L('interval')?>： </th>
        <td> <input type="text" name="subject[interval]" value="<?php echo $interval;?>" size='5' /> <?php echo L('more_ip')?>，<font color=red>0</font> <?php echo L('one_ip')?></td>
    </tr>
    
    <tr id="dr_row_style">
        <th><?php echo L('vote_style')?>：</th>
        <td>
        <?php echo form::select($template_list, $default_style, 'name="vote_subject[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?> 
        </td>
    </tr>
    
    <tr>
        <th><?php echo L('template')?>：</th>
        <td id="show_template">
        <?php echo form::select_template($default_style, 'vote', $vote_tp_template, 'name="vote_subject[vote_tp_template]"', 'vote_tp');?>
        </td>
    </tr>
    <tr>
        <th><?php echo L('enabled')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="subject[enabled]" type="radio" value="1" <?php if($enabled == 1) {?>checked<?php }?>>&nbsp;<?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="subject[enabled]" type="radio" value="0" <?php if($enabled == 0) {?>checked<?php }?>>&nbsp;<?php echo L('no')?> <span></span></label>
        </div></td>
    </tr>
</table>
</form>
</div>
</body>
</html>

<script language="javascript" type="text/javascript">
function AdsType(adstype) {
    $('#SizeFormat').css('display', 'none');
    if(adstype=='0') {
        
    } else if(adstype=='1') {
        $('#SizeFormat').css('display', '');
    }
}
</script>

<script language="javascript">
var i = 1;
function add_option() {
    //var i = 1;
    var htmloptions = '';
    htmloptions += '<div id='+i+'><span><br><input type="text" name="option[]" size="40" msg="<?php echo L('must_input')?>" value="" class="input-text"/><input type="button" value="<?php echo L('del')?>"  onclick="del('+i+')" class="button"/><br></span></div>';
    $(htmloptions).appendTo('#new_option'); 
    var htmloptions = '';
    i = i+1;
}
function del(o){
 $("div [id=\'"+o+"\']").remove();    
}

function load_file_list(id) {
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=vote&templates=vote_tp&name=vote_subject&pc_hash='+pc_hash, function(data){$('#show_template').html(data.vote_tp_template);});
}
</script>