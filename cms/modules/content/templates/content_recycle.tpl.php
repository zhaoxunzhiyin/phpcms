<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<link href="<?php echo JS_PATH;?>bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            orientation: "left",
            autoclose: true
        });
    }
    $(":text").removeClass('input-text');
});
</script>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li class="dropdown"> <a href="?m=content&c=content&a=recycle_init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>" class="on"> <i class="fa fa-trash-o"></i>  <?php echo L('recycle');?></a> <a class="dropdown-toggle on" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"><i class="fa fa-angle-double-down"></i></a>
            <ul class="dropdown-menu">
                <li><a href="?m=content&c=content&a=init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>"> <i class="fa fa-check"></i> <?php echo L('check_passed');?> </a></li>
                <li><a href="?m=content&c=content&a=recycle_init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>"> <i class="fa fa-trash-o"></i> <?php echo L('recycle');?> </a></li>
                <li class="divider"></li>
                <li><a href="javascript:dr_iframe_show('<?php echo L('批量更新内容URL');?>', '?m=content&c=create_html&a=public_show_url&modelid=1&catid[]=<?php echo $param['catid'];?>', '500px', '300px')"> <i class="fa fa-refresh"></i> <?php echo L('更新URL');?> </a></li>
                <li><a href="javascript:dr_iframe_show('<?php echo L('模型配置');?>', '?m=content&c=sitemodel&a=edit&modelid=<?php echo $modelid;?>', '80%', '80%')"> <i class="fa fa-cog"></i> <?php echo L('模型配置');?> </a></li>
                <li><a href="javascript:dr_iframe_show('<?php echo L('模型内容字段');?>','?m=content&c=sitemodel_field&a=init&modelid=<?php echo $modelid;?>&is_menu=1', '80%', '90%');"> <i class="fa fa-code"></i> <?php echo L('模型内容字段');?> </a></li>
            </ul> <i class="fa fa-circle"></i>
        </li>
        <li> <a href="?m=content&c=content&a=init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>" class=""> <i class="fa fa-reply"></i> <?php echo L('返回');?></a> <i class="fa fa-circle"></i> </li>
        <li> <a href="javascript:;" onclick="javascript:dr_content_submit('?m=content&c=content&a=add&menuid=<?php echo $param['menuid'];?>&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>','add');"> <i class="fa fa-plus"></i> <?php echo L('add_content');?></a> </li>
    </ul>
</div>
<div class="page-body" style="margin-top: 20px;margin-bottom:30px;padding-top:15px;">
<div class="right-card-box">
    <div class="row table-search-tool">
        <form name="searchform" action="" method="get" >
        <input type="hidden" value="content" name="m">
        <input type="hidden" value="content" name="c">
        <input type="hidden" value="recycle_init" name="a">
        <input type="hidden" value="<?php echo $param['catid'];?>" name="catid">
        <input type="hidden" value="1" name="search">
        <input type="hidden" value="<?php echo dr_get_csrf_token();?>" name="pc_hash">
        <div class="col-md-12 col-sm-12">
        <label><select id="posids" name="posids"><option value='' <?php if($param['posids']=='') echo 'selected';?>><?php echo L('all');?></option>
        <option value="1" <?php if($param['posids']==1) echo 'selected';?>><?php echo L('elite');?></option>
        <option value="2" <?php if($param['posids']==2) echo 'selected';?>><?php echo L('no_elite');?></option>
        </select></label>
        </div>
        <div class="col-md-12 col-sm-12">
        <label><select name="field" class="form-control">
            <option value="id"> ID </option>
            <?php foreach($field as $t) {?>
            <?php if (dr_is_admin_search_field($t)) {?>
            <option value="<?php echo $t['field'];?>"<?php if ($param['field']==$t['field']) {?> selected<?php }?>><?php echo L($t['name']);?></option>
            <?php }}?>
        </select></label>
        <label><i class="fa fa-caret-right"></i></label>
        <label><input type="text" class="form-control" placeholder="" value="<?php echo $param['keyword'];?>" name="keyword" /></label>
        </div>
        <div class="col-md-12 col-sm-12">
        <label>
            <div class="input-group input-medium date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
                <input type="text" class="form-control" value="<?php echo $param['start_time'];?>" name="start_time" id="start_time">
                <span class="input-group-addon"> - </span>
                <input type="text" class="form-control" value="<?php echo $param['end_time'];?>" name="end_time" id="end_time">
            </div>
        </label>
        </div>
        <div class="col-md-12 col-sm-12">
        <label><button type="submit" class="btn blue btn-sm onloading"><i class="fa fa-search"></i> <?php echo L('search');?></button></label>
        </div>
        </form>
    </div>
<form class="form-horizontal" name="myform" id="myform" action="" method="post">
<input name="dosubmit" type="hidden" value="1">
    <div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr class="heading">
            <th align="center" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <?php 
            if(is_array($list_field)){
            foreach($list_field as $i=>$t){
            ?>
            <th<?php if($t['width']){?> width="<?php echo $t['width'];?>"<?php }?><?php if($t['center']){?> style="text-align:center"<?php }?><?php if ($i!='hits') {?> class="<?php echo dr_sorting($i);?>" name="<?php echo $i;?>"<?php }?>><?php echo L($t['name']);?></th>
            <?php }}?>
            <th align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($datas)){
    foreach($datas as $r){
?>   
    <tr>
    <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $r['id']?>" />
                        <span></span>
                    </label></td>
    <?php 
    if(is_array($list_field)){
    foreach($list_field as $i=>$tt){
    ?>
    <td<?php if($tt['center']){?> class="table-center" style="text-align:center"<?php }?>><?php echo dr_list_function($tt['func'], $r[$i], $param, $r, $field[$i], $i);?></td>
    <?php }}?>
    <td align="center"><a href="<?php
        if($r['status']==99) {
            if($r['islink']) {
                echo $r['url'];
            } elseif(strpos($r['url'],'http://')!==false || strpos($r['url'],'https://')!==false) {
                echo $r['url'];
            } else {
                $release_siteurl = substr((string)dr_site_info('domain', dr_cat_value($r['catid'], 'siteid')),0,-1);
                echo $release_siteurl.$r['url'];
            }
        } else {
            echo '?m=content&c=content&a=public_preview&catid='.$r['catid'].'&id='.$r['id'].'';
        }?>" target="_blank" class="btn btn-xs blue"><i class="fa fa-eye"></i> <?php echo L('preview');?></a>
        <a class="btn btn-xs green" id="restore" data-id="<?php echo $r['id'];?>"><i class="fa fa-reply"></i> <?php echo L('recover');?></a></td>
    </tr>
<?php 
    }
}
?>
</tbody>
    </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <?php echo $foot_tpl;?>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
<script>
$(function() {
    $('body').on('click','#restore',function() {
        var data = this;
        Dialog.confirm('确定要还原此内容吗？', function() {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=recycle&recycle=0&catid=<?php echo $param['catid'];?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
                data: {id:$(data).data('id'),<?php echo SYS_TOKEN_NAME;?>:$("#myform input[name='<?php echo SYS_TOKEN_NAME;?>']").val(),dosubmit:1},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    // token 更新
                    if (res.token) {
                        var token = res.token;
                        $("#myform input[name='"+token.name+"']").val(token.value);
                    }
                    if (res.code==1) {
                        setTimeout("window.location.reload(true)", 2000);
                    }
                    dr_tips(res.code, res.msg);
                }
            });
        });
    });
    $('body').on('click','#delAll',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            Dialog.confirm('确认要删除选中的内容吗？', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=delete&catid=<?php echo $param['catid'];?>&pc_hash='+pc_hash,
                    data: $('#myform').serialize(),
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        // token 更新
                        if (res.token) {
                            var token = res.token;
                            $("#myform input[name='"+token.name+"']").val(token.value);
                        }
                        if (res.code==1) {
                            setTimeout("window.location.reload(true)", 2000);
                        }
                        dr_tips(res.code, res.msg);
                    }
                });
            });
        }
    })
    $('body').on('click','#recycle',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            Dialog.confirm('确认要还原选中的内容吗？', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&recycle=0&catid=<?php echo $param['catid'];?>&pc_hash='+pc_hash,
                    data: $('#myform').serialize(),
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        // token 更新
                        if (res.token) {
                            var token = res.token;
                            $("#myform input[name='"+token.name+"']").val(token.value);
                        }
                        if (res.code==1) {
                            setTimeout("window.location.reload(true)", 2000);
                        }
                        dr_tips(res.code, res.msg);
                    }
                });
            });
        }
    })
});
</script>
</div>
</div>
</div>
</body>
</html>