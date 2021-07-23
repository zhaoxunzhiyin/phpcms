<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>

<div class="pad_10">
<div id="searchid">
<form name="searchform" action="" method="get" >
<input type="hidden" value="comment" name="m">
<input type="hidden" value="comment_admin" name="c">
<input type="hidden" value="listinfo" name="a">
<input type="hidden" value="1" name="search">
<input type="hidden" value="<?php echo $_SESSION['pc_hash']?>" name="pc_hash">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">	
			<?php if($max_table > 1) {?>
			<?php echo L('choose_database')?>：<select name="tableid" onchange="show_tbl(this)"><?php for($i=1;$i<=$max_table;$i++) {?><option value="<?php echo $i?>" <?php if($i==$tableid){?>selected<?php }?>><?php echo $this->comment_data_db->db_tablepre?>comment_data_<?php echo $i?></option><?php }?></select>
			<?php }?>
			<select name="searchtype">
				<option value='0' <?php if($_GET['searchtype']==0) echo 'selected';?>><?php echo L('original').L('title');?></option>
				<option value='1' <?php if($_GET['searchtype']==1) echo 'selected';?>><?php echo L('original');?>ID</option>
				<option value='2' <?php if($_GET['searchtype']==2) echo 'selected';?>><?php echo L('username');?></option>
			</select>
			<input name="keyword" type="text" value="<?php if(isset($keywords)) echo $keywords;?>" class="input-text" />
			<input type="submit" name="search" class="button" value="<?php echo L('search');?>" />
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
</div>
</div>
<div class="pad-lr-10">
<form name="myform" id="myform" action="" method="get" >
<input type="hidden" value="comment" name="m">
<input type="hidden" value="comment_admin" name="c">
<input type="hidden" value="del" name="a">
<input type="hidden" value="<?php echo $tableid?>" name="tableid">
<input type="hidden" value="1" name="dosubmit">
<div class="table-list comment">
    <table width="100%">
        <thead>
            <tr>
			 <th width="16" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('ids[]');" />
                        <span></span>
                    </label></th>
			<th width="130"><?php echo L('author')?></th>
			<th><?php echo L('comment')?></th>
			<th width="230"><?php echo L('original').L('title');?></th>
			<th width="72"><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
		<tbody class="add_comment">
    <?php
	if(is_array($data)) {
		foreach($data as $v) {
			$comment_info = $this->comment_db->get_one(array('commentid'=>$v['commentid']));
			if (strpos($v['content'], '<div class="content">') !==false) {
				$pos = strrpos($v['content'], '</div>');
				$v['content'] = substr($v['content'], $pos+6);
			}
	?>
     <tr id="tbody_<?php echo $v['id']?>">
		<td align="center" width="16" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $v['id'];?>" />
                        <span></span>
                    </label></td> 
		<td width="130"><?php echo $v['username']?><br /><?php echo $v['ip']?></td>
		<td><font color="#888888"><?php echo L('chez')?> <?php echo format::date($v['creat_at'], 1)?> <?php echo L('release')?></font><br /><?php echo $v['content']?></td>
		<td width="230"><a href="?m=comment&c=comment_admin&a=listinfo&search=1&searchtype=0&keyword=<?php echo urlencode($comment_info['title'])?>&pc_hash=<?php echo $_SESSION['pc_hash']?>&tableid=<?php echo $tableid?>"><?php echo $comment_info['title']?></td>
		<td align='center' width="72"><a href="###" onclick="Dialog.confirm('<?php echo L('are_you_sure_you_want_to_delete')?>',function(){redirect('?m=comment&c=comment_admin&a=del&ids=<?php echo $v['id']?>&tableid=<?php echo $tableid?>&dosubmit=1&pc_hash='+pc_hash);});"><?php echo L('delete');?></a> </td>
	</tr>
     <?php }
	}
	?>
	</tbody>
     </table>
    <div class="btn"><label for="check_box"><?php echo L('selected_all');?>/<?php echo L('cancel');?></label>
		<input type="hidden" value="<?php echo $_SESSION['pc_hash'];?>" name="pc_hash">
		<input type="submit" class="button" value="<?php echo L('delete');?>" />
	</div>
    <div id="pages"><?php echo $pages;?></div>
</div>
</form>
</div>
<script type="text/javascript">
window.top.$('#display_center_id').css('display','none');
function show_tbl(obj) {
	var pdoname = $(obj).val();
	location.href='?m=comment&c=comment_admin&a=listinfo&tableid='+pdoname+'&pc_hash=<?php echo $_SESSION['pc_hash']?>';
}
</script>
</body>
</html>