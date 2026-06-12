<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
$show_header = true;
include $this->admin_tpl('header','admin');
?>
<div class="subnav"> 
    <?php if(is_mobile()) {?>
    <div class="content-menu btn-group dropdown-btn-group"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-th-large"></i> <?php echo L('菜单')?> <i class="fa fa-angle-down"></i></a>
        <ul class="dropdown-menu">
            <?php if(isset($big_menu)) { foreach($big_menu as $big) { echo '<li><a class="tooltips add fb" href="'.$big[0].'" data-container="body" data-placement="bottom" data-original-title="'.$big[1].'"><i class="fa fa-plus"></i> '.$big[1].'</a></li><div class="dropdown-line"></div>'; } }?>
            <li><a class="tooltips on" href="?m=special&c=special" data-container="body" data-placement="bottom" data-original-title="<?php echo L('special_list');?>"><i class="iconm fa fa-reorder"></i> <?php echo L('special_list')?></a></li>
        </ul>
    </div>
    <?php } else {?>
    <div class="content-menu ib-a">
    <?php if(isset($big_menu)) { foreach($big_menu as $big) { echo '<a class="tooltips add fb" href="'.$big[0].'" data-container="body" data-placement="bottom" data-original-title="'.$big[1].'"><i class="fa fa-plus"></i> '.$big[1].'</a><i class="fa fa-circle"></i>'; } }?><a class="tooltips on" href="?m=special&c=special" data-container="body" data-placement="bottom" data-original-title="<?php echo L('special_list');?>"><i class="iconm fa fa-reorder"></i> <?php echo L('special_list')?></a>
    </div>
    <?php }?>
</div>
<div class="content-header"></div>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=special&c=content&a=listorder&specialid=<?php echo $this->input->get('specialid')?>" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%">
        <thead>
            <tr>
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="80"><?php echo L('listorder')?></th>
            <th width="80">ID</th>
            <th><?php echo L('content_title')?></th>
            <th width="120"><?php echo L('for_type')?></th>
            <th width="100"><?php echo L('inputman')?></th>
            <th width="160"><?php echo L('update_time')?></th>
            <th><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
<tbody>
    <?php foreach ($datas as $r) {
        if ($r['curl']) {
            $content_arr = explode('|', $r['curl']);
            $r['url'] = dr_go($content_arr['1'], $content_arr['0']);
        }
    ?>
        <tr>
        <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $r['id'];?>" />
                        <span></span>
                    </label></td>
        <td align='center'><input name='listorders[<?php echo $r['id'];?>]' type='text' value='<?php echo $r['listorder'];?>' class='displayorder form-control input-sm input-inline input-mini'></td>
        <td align='center'><?php echo $r['id'];?></td>
        <td><a href="<?php echo $r['url'];?>" target="_blank"><?php echo $r['title'];?></a></td>
        <td align='center'><?php echo $types[$r['typeid']]['name'];?></td>
        <td align='center'><?php echo $r['username'];?></td>
        <td align='center'><?php echo dr_date($r['updatetime'], null, 'red');?></td>
        <td align='center'><a class="btn btn-xs green" href="javascript:;" onclick="javascript:dr_content_submit('?m=special&c=content&a=edit&specialid=<?php echo $r['specialid']?>&id=<?php echo $r['id']?>','edit')"> <i class="fa fa-edit"></i> <?php echo L('content_edit')?></a> </td>
    </tr>
     <?php }?>
</tbody>
     </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('listorder')?></button></label>
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){document.myform.action='?m=special&c=content&a=delete&specialid=<?php echo $this->input->get('specialid')?>';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript">
setcookie('refersh_time', 0);
function refersh_window() {
    var refersh_time = getcookie('refersh_time');
    if(refersh_time==1) {
        window.location.reload(true);
    }
}

setInterval("refersh_window()", 5000);
</script>
</body>
</html>