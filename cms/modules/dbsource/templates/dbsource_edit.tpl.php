<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form action="?m=dbsource&c=dbsource_admin&a=edit&id=<?php echo $id?>" method="post" id="myform">
<div>
<fieldset>
    <legend><?php echo L('configure_the_external_data_source')?></legend>
    <table width="100%"  class="table_form">
  <tr>
    <th width="80"><?php echo L('dbsource_name')?>：</th>
    <td class="y-bg"><?php echo $data['name']?></td>
  </tr>
  <tr>
    <th><?php echo L('server_address')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="host" id="host" size="30" value="<?php echo $data['host']?>" /></td>
  </tr>
    <tr>
    <th><?php echo L('server_port')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="port" id="port" size="30" value="<?php echo $data['port']?>" /></td>
  </tr>
    <tr>
    <th><?php echo L('username')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="username" id="username"  size="30" value="<?php echo $data['username']?>" /></td>
  </tr>
      <tr>
    <th><?php echo L('password')?>：</th>
    <td class="y-bg"><input type="password" class="input-text" name="password" id="password"  size="30" value="<?php echo $data['password']?>" /></td>
  </tr>
        <tr>
    <th><?php echo L('database')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="dbname" id="dbname"  size="30"  value="<?php echo $data['dbname']?>" /></td>
  </tr>
  <tr>
    <th><?php echo L('dbtablepre');?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="dbtablepre" id="dbtablepre" value="<?php echo $data['dbtablepre']?>" size="30"/> </td> 
  </tr>
      <tr>
    <th><?php echo L('charset')?>：</th>
    <td class="y-bg"><?php echo form::select(array('gbk'=>'GBK', 'utf8'=>'UTF-8', 'utf8mb4'=>'UTF8MB4', 'gb2312'=>'GB2312', 'latin1'=>'Latin1'), $data['charset'], 'name="charset" id="charset"')?></td>
  </tr>
      <tr>
    <th></th>
    <td class="y-bg"><input type="button" class="button" value="<?php echo L('test_connections')?>" onclick="test_connect()" /></td>
  </tr>
</table>
</fieldset>
</div>
</div>
</form>
<script type="text/javascript">
<!--
    function test_connect() {
        $.get('?m=dbsource&c=dbsource_admin&a=public_test_mysql_connect', {host:$('#host').val(),username:$('#username').val(), password:$('#password').val(), port:$('#port').val()}, function(data){if(data==1){Dialog.alert('<?php echo L('connect_success')?>')}else{Dialog.alert('<?php echo L('connect_failed')?>')}});
}
//-->
</script>
</body>
</html>