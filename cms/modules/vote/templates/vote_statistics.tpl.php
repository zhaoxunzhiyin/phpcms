<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
<li><a href="?m=vote&c=vote&a=statistics_userlist&subjectid=<?php echo $subjectid;?>"><?php echo L('user_list')?></a></li>
<li class="on"><a href="?m=vote&c=vote&a=statistics&subjectid=<?php echo $subjectid;?>"><?php echo L('vote_result')?></a></li>
</ul>
<div class="content pad-10" style="height:auto">
<form name="myform" action="?m=vote&c=vote&a=delete_statistics" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th><?php echo L('vote_option')?></th>
            <th width="10%" align="center"><?php echo L('vote_num')?></th>
            <th width='30%' align="center"><?php echo L('pic_view')?></th>
        </tr>
    </thead>
<tbody>
<?php
$i = 1;
if(is_array($options)){
foreach($options as $info){
    //没有人投票则 百分比都为 0%
    if($vote_data['total']==0){
        $per=0;
    }else{
        $per=intval($vote_data[$info['optionid']]/$vote_data['total']*100);
    }
    
?>
    <tr>
        <td> <?php echo $i.' , '.$info['option']?> </td>
        <td align="center"><?php echo $vote_data[$info['optionid']];?></td>
        <td align="center">
        <div class="vote_bar">
            <div style="width:<?php echo $per?>%"><span><?php echo $per;?> %</span> </div>
        </div>
        </td>
        
        </tr>
    <?php
    $i++;
    }
}
?>
</tbody>
</table>
</div>
<div id="pages"><?php echo L('vote_all_num')?>  <?php echo $vote_data['total'];?></div>
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
