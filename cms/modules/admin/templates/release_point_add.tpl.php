<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="pad-10">
<form action="?m=admin&c=release_point&a=add" method="post" id="myform">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%"  class="table_form">
  <tr>
    <th width="100"><?php echo L('release_point_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="name" id="name" size="30" /></td>
  </tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
    <legend><?php echo L('ftp_server')?></legend>
    <table width="100%"  class="table_form">
  <tr>
    <th width="100"><?php echo L('server_address')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="host" id="host" size="30" /></td>
  </tr>
   <tr>
    <th><?php echo L("server_port")?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="port" id="port" size="30" /></td>
  </tr>
  <tr>
    <th><?php echo L('username')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="username" id="username" size="30" /></td>
  </tr>
    <tr>
    <th><?php echo L('password')?>：</th>
    <td class="y-bg"><input type="password" class="input-text" name="password" id="password" size="30" /></td>
  </tr>
   <tr>
    <th><?php echo L('path')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="path" id="path" size="30" value="/" /></td>
  </tr>
      <tr>
    <th><?php echo L('passive_mode')?>：</th>
    <td class="y-bg"><div class="mt-checkbox-inline">
          <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" class="inputcheckbox" name="pasv" value="1" id="pasv" size="30" /><?php echo L('yes')?> <span></span></label>
        </div></td>
  </tr>
    <tr>
    <th><?php echo L('ssl_connection')?>：</th>
    <td class="y-bg"><div class="mt-checkbox-inline">
          <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" class="inputcheckbox" name="ssl" value="1" id="ssl" size="30" <?php if(!$this->ssl){ echo 'disabled';}?> /><?php echo L('yes')?> <span></span></label><?php if(!$this->ssl){ echo '<span style="color:red">'.L('your_server_will_not_support_the_ssl_connection').'</a>';}?>
        </div></td>
  </tr>
    </tr>
    <tr>
    <th><?php echo L('test_connections')?>：</th>
    <td class="y-bg"><input type="button" class="button" onclick="ftp_test()" value="<?php echo L('test_connections')?>" /></td>
  </tr>
</table>
</fieldset>
</div>
</div>
<script type="text/javascript">
<!--
function ftp_test() {
    if(!$('#host').val()) {
        $('#host').focus();
        return false;
    }
    if(!$('#port').val()) {
        $('#port').focus();
        return false;
    }
    if(!$('#username').val()) {
        $('#username').focus();
        return false;
    }
    if(!$('#password').val()) {
        $('#password').focus();
        return false;
    }
    var host = $('#host').val();
    var port = $('#port').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var pasv = $("input[type='checkbox'][name='pasv']:checked").val();
    var ssl = $("input[type='checkbox'][name='ssl']:checked").val();
    $.get("?",{m:'admin',c:'release_point',a:'public_test_ftp', host:host,port:port,username:username,password:password,pasv:pasv,ssl:ssl}, function(data){
        if (data==1){
            Dialog.alert('<?php echo L('ftp_server_connections_success')?>');
        } else {
            Dialog.alert(data);
        }
    })
}
//-->
</script>
</form>
</body>
</html>