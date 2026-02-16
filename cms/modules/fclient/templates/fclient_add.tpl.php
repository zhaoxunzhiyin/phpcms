<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form action="?m=fclient&c=fclient&a=add" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
    <tr>
        <th width="120"><?php echo L('username')?>：</th>
        <td>
        <input type="text" name="fclient[username]" id="username" value="" class="input-text" />
        </td>
    </tr>
    
    <tr>
        <th><?php echo L('name')?>：</th>
        <td><input type="text" name="fclient[name]" id="name" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('domain')?>：</th>
        <td><input type="text" name="fclient[domain]" id="domain" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('sn')?>：</th>
        <td><label><input type="text" name="fclient[sn]" id="sn" size="30" class="input-text" readonly="readonly"></label> <label><button class="button" type="button" onclick="dr_to_aeskey()"><?php echo L('aeskey')?></button></label></td>
    </tr>
    
    <tr>
        <th><?php echo L('access_model')?>：</th>
        <td><div class="mt-radio-inline">
            <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_mode_0').show();$('.dr_mode_1').hide()" name="fclient[setting][mode]" value="0" checked /> <?php echo L('remote_server')?> <span></span></label>
            <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_mode_1').show();$('.dr_mode_0').hide()" name="fclient[setting][mode]" value="1" /> <?php echo L('local_server')?> <span></span></label>
        </div></td>
    </tr>
    
    <tr class="dr_mode_0">
        <th><?php echo L('special_tips')?>：</th>
        <td><?php echo L('remote_server_no_cms')?></td>
    </tr>
    
    <tr class="dr_mode_1" style="display: none;">
        <th><?php echo L('local_web_path')?>：</th>
        <td><div class="input-group">
            <input type="text" name="fclient[setting][webpath]" id="dr_html_dir" value="" class="form-control">
            <span class="input-group-btn">
                <button class="btn blue" onclick="dr_check_domain('dr_html_dir')" type="button"><i class="fa fa-code"></i> <?php echo L('test')?></button>
            </span>
        </div></td>
    </tr>
    
    <tr>
        <th><?php echo L('money')?>：</th>
        <td><input type="text" name="fclient[money]" id="money" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('site_note')?>：</th>
        <td><textarea style="height:100px" name="fclient[setting][mark]"></textarea></td>
    </tr>
    
    <tr>
        <th><?php echo L('status')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="1"> <?php echo L('no_check')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="2" checked> <?php echo L('check_2')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="3"> <?php echo L('check_3')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="fclient[status]" type="radio" value="4"> <?php echo L('check_4')?> <span></span></label>
        </div></td>
    </tr>
    
    <tr>
        <th><?php echo L('note')?>：</th>
        <td><input type="text" name="fclient[setting][note]" id="note" size="30" class="input-text"></td>
    </tr>
    
    <tr>
        <th><?php echo L('inputtime')?>：</th>
        <td><?php echo form::date('fclient[inputtime]',SYS_TIME,0,0,'true',0,0,1);?></td>
    </tr>
    
    <tr>
        <th><?php echo L('endtime')?>：</th>
        <td><?php echo form::date('fclient[endtime]',SYS_TIME+3600*24*999,0,0,'true',0,0,1);?></td>
    </tr>
</table>
</form>
</div>
<script type="text/javascript">
function dr_to_aeskey() {
    $.get("<?php echo SELF;?>?m=fclient&c=fclient&a=public_asckey&pc_hash="+pc_hash, function(data){
        $("#sn").val(data);
    });
}
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