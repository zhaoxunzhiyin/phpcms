<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<div class="col-tab">
        <ul class="tabBut cu-li">
            <li class="on"><a href="?m=vote&c=vote&a=statistics_userlist&subjectid=<?php echo $subjectid;?>"><?php echo L('user_list')?></a></li>
            <li><a href="?m=vote&c=vote&a=statistics&subjectid=<?php echo $subjectid;?>"><?php echo L('vote_result')?></a></li>
        </ul>
            <div class="content pad-10" style="height:auto">
<form name="myform" action="?m=vote&c=vote&a=delete_statistics" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th><?php echo L('username')?></th>
            <th width="155" align="center"><?php echo L('up_vote_time')?></th>
            <th width="14%" align="center"><?php echo L('ip')?></th>
         </tr>
    </thead>
<tbody>
<?php
if(is_array($infos)){
    foreach($infos as $info){
        ?>
    <tr>
        <td><?php if($info['username']=="")echo L('guest');else echo $info['username']?></td>
        <td align="center" width="155"><?php echo date("Y-m-d h-i",$info['time']);?></td>
        <td align="center" width="14%"><?php echo $info['ip'];?></td>
         </tr>
    <?php
    }
}
?>
</tbody>
</table>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
    artdialog('edit','?m=vote&c=vote&a=edit&subjectid='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
</script>
