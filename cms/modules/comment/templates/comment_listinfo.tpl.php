<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<form name="searchform" action="" method="get" >
<input type="hidden" value="comment" name="m">
<input type="hidden" value="comment_admin" name="c">
<input type="hidden" value="listinfo" name="a">
<input type="hidden" value="1" name="search">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo dr_get_csrf_token()?>" name="pc_hash">
<div class="col-md-12 col-sm-12">
            <?php if($max_table > 1) {?>
            <label><?php echo L('choose_database')?></label>
            <label><i class="fa fa-caret-right"></i></label>
            <label><select name="tableid" onchange="show_tbl(this)"><?php for($i=1;$i<=$max_table;$i++) {?><option value="<?php echo $i?>" <?php if($i==$tableid){?>selected<?php }?>><?php echo $this->comment_data_db->db_tablepre?>comment_data_<?php echo $i?></option><?php }?></select></label>
            <?php }?>
</div>
<div class="col-md-12 col-sm-12">
            <label><select name="searchtype">
                <option value='0' <?php if($this->input->get('searchtype')==0) echo 'selected';?>><?php echo L('original').L('title');?></option>
                <option value='1' <?php if($this->input->get('searchtype')==1) echo 'selected';?>><?php echo L('original');?>ID</option>
                <option value='2' <?php if($this->input->get('searchtype')==2) echo 'selected';?>><?php echo L('username');?></option>
            </select></label>
            <label><i class="fa fa-caret-right"></i></label>
            <label><input name="keyword" type="text" value="<?php if(isset($keywords)) echo $keywords;?>" class="input-text" /></label>
</div>
<div class="col-md-12 col-sm-12">
            <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
</div>
</form>
</div>
<form name="myform" id="myform" action="" method="get" >
<input type="hidden" value="comment" name="m">
<input type="hidden" value="comment_admin" name="c">
<input type="hidden" value="del" name="a">
<input type="hidden" value="<?php echo $tableid?>" name="tableid">
<input type="hidden" value="1" name="dosubmit">
<div class="table-list">
    <table width="100%">
        <thead>
            <tr>
             <th width="16" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
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
        <td width="230"><a href="?m=comment&c=comment_admin&a=listinfo&search=1&searchtype=0&keyword=<?php echo urlencode($comment_info['title'])?>&pc_hash=<?php echo dr_get_csrf_token()?>&tableid=<?php echo $tableid?>"><?php echo $comment_info['title']?></td>
        <td align='center' width="72"><a href="javascript:void(0);" onclick="Dialog.confirm('<?php echo L('are_you_sure_you_want_to_delete')?>',function(){redirect('?m=comment&c=comment_admin&a=del&ids=<?php echo $v['id']?>&tableid=<?php echo $tableid?>&dosubmit=1&pc_hash='+pc_hash);});"><?php echo L('delete');?></a> </td>
    </tr>
     <?php }
    }
    ?>
    </tbody>
     </table>
</div>
<input type="hidden" value="<?php echo dr_get_csrf_token();?>" name="pc_hash">
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="submit" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function show_tbl(obj) {
    var pdoname = $(obj).val();
    location.href='?m=comment&c=comment_admin&a=listinfo&tableid='+pdoname+'&pc_hash=<?php echo dr_get_csrf_token()?>';
}
</script>
</body>
</html>