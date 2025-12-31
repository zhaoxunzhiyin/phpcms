<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="pad_10">
<form action="?m=fclient&c=fclient&a=edit&id=<?php echo $id;?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
    <tr>
        <th width="120"><?php echo L('username')?>：</th>
        <td>
        <input type="text" name="fclient[username]" id="username" value="<?php echo $username;?>" class="input-text" />
        </td>
    </tr>
    
    <tr>
        <th><?php echo L('name')?>：</th>
        <td><input type="text" name="fclient[name]" id="name" value="<?php echo $name;?>" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('domain')?>：</th>
        <td><input type="text" name="fclient[domain]" id="domain" value="<?php echo $domain;?>" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('sn')?>：</th>
        <td><input type="text" name="fclient[sn]" id="sn" size="30" value="<?php echo $sn;?>" class="input-text" readonly="readonly"></td>
    </tr>
    
    <tr>
        <th><?php echo L('access_model')?>：</th>
        <td><div class="mt-radio-inline">
            <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_mode_0').show();$('.dr_mode_1').hide()" name="fclient[setting][mode]" value="0"<?php if (empty($setting['mode'])) echo ' checked';?> /> <?php echo L('remote_server')?> <span></span></label>
            <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_mode_1').show();$('.dr_mode_0').hide()" name="fclient[setting][mode]" value="1"<?php if ($setting['mode']) echo ' checked';?> /> <?php echo L('local_server')?> <span></span></label>
        </div></td>
    </tr>
    
    <tr class="dr_mode_0"<?php if (!empty($setting['mode'])) echo ' style="display: none;"';?>>
        <th><?php echo L('special_tips')?>：</th>
        <td><?php echo L('remote_server_no_cms')?></td>
    </tr>
    
    <tr class="dr_mode_1"<?php if (empty($setting['mode'])) echo ' style="display: none;"';?>>
        <th><?php echo L('local_web_path')?>：</th>
        <td><div class="input-group">
            <input type="text" name="fclient[setting][webpath]" id="dr_html_dir" value="<?php echo $setting['webpath'];?>" class="form-control">
            <span class="input-group-btn">
                <button class="btn blue" onclick="dr_check_domain('dr_html_dir')" type="button"><i class="fa fa-code"></i> <?php echo L('test')?></button>
            </span>
        </div></td>
    </tr>
    
    <tr>
        <th><?php echo L('money')?>：</th>
        <td><input type="text" name="fclient[money]" id="money" value="<?php echo $money;?>" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('site_note')?>：</th>
        <td><textarea style="height:100px" name="fclient[setting][mark]"><?php echo $setting['mark'];?></textarea></td>
    </tr>
    
    <tr>
        <th><?php echo L('status')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="1"<?php if($status==1){echo " checked";}?>> <?php echo L('no_check')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="2"<?php if($status==2){echo " checked";}?>> <?php echo L('check_2')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="3"<?php if($status==3){echo " checked";}?>> <?php echo L('check_3')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="4"<?php if($status==4){echo " checked";}?>> <?php echo L('check_4')?> <span></span></label>
        </div></td>
    </tr>
    
    <tr>
        <th><?php echo L('note')?>：</th>
        <td><input type="text" name="fclient[setting][note]" id="note" value="<?php echo $setting['note'];?>" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('inputtime')?>：</th>
        <td><?php echo form::date('fclient[inputtime]',$inputtime ? dr_date($inputtime, 'Y-m-d') : '',0,0,'true',0,0,1);?></td>
    </tr>
    
    <tr>
        <th><?php echo L('endtime')?>：</th>
        <td><?php echo form::date('fclient[endtime]',$endtime ? dr_date($endtime, 'Y-m-d') : '',0,0,'true',0,0,1);?></td>
    </tr>
</table>
</form>
</div>
<script type="text/javascript">
function dr_check_domain(id) {
    $.ajax({type: "GET",dataType:"json", url: "?m=fclient&c=fclient&a=public_test_dir&v="+encodeURIComponent($("#"+id).val()),
        success: function(json) {
            dr_tips(json.code, json.msg);
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
</script>
</body>
</html>