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
        <li class="dropdown"> <?php if($steps){?><a href="?m=content&c=content&a=init&catid=<?php echo $param['catid'];?>&steps=<?php echo $steps;?>&pc_hash=<?php echo dr_get_csrf_token();?>" class="on"> <i class="fa fa-check"></i>  <?php echo L('workflow_'.$steps);?></a><?php }elseif($param['reject']){?><a href="?m=content&c=content&a=init&catid=<?php echo $param['catid'];?>&reject=1&pc_hash=<?php echo dr_get_csrf_token();?>" class="on"> <i class="fa fa-sign-out"></i>  <?php echo L('reject');?></a><?php }else{?><a href="?m=content&c=content&a=init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>" class="on"> <i class="fa fa-check"></i>  <?php echo L('check_passed');?></a><?php }?> <a class="dropdown-toggle on" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"><i class="fa fa-angle-double-down"></i></a>
            <ul class="dropdown-menu">
                <li><a href="?m=content&c=content&a=init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>"> <i class="fa fa-check"></i> <?php echo L('check_passed');?> </a></li>
                <li><a href="?m=content&c=content&a=recycle_init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>"> <i class="fa fa-trash-o"></i> <?php echo L('recycle');?> </a></li>
                <?php echo $workflow_menu;?>
                <li class="divider"></li>
                <li><a href="javascript:dr_iframe_show('<?php echo L('批量更新内容URL');?>', '?m=content&c=create_html&a=public_show_url&modelid=1&catid[]=<?php echo $param['catid'];?>', '500px', '300px')"> <i class="fa fa-refresh"></i> <?php echo L('更新URL');?> </a></li>
                <li><a href="javascript:dr_iframe_show('<?php echo L('模型配置');?>', '?m=content&c=sitemodel&a=edit&modelid=<?php echo $modelid;?>', '80%', '80%')"> <i class="fa fa-cog"></i> <?php echo L('模型配置');?> </a></li>
                <li><a href="javascript:dr_iframe_show('<?php echo L('模型内容字段');?>','?m=content&c=sitemodel_field&a=init&modelid=<?php echo $modelid;?>&is_menu=1', '80%', '90%');"> <i class="fa fa-code"></i> <?php echo L('模型内容字段');?> </a></li>
                <?php if($setting['ishtml']) {?>
                <li class="divider"></li>
                <li><a href="javascript:;" onclick="dr_bfb('<?php echo L('update_htmls',array('catname'=>$category['catname']));?>', 'myform', '?m=content&c=create_html&a=category&dosubmit=1&catids[0]=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>&referer=<?php echo urlencode($_SERVER['QUERY_STRING']);?>')"> <i class="fa fa-html5"></i> <?php echo L('生成栏目');?> </a></li>
                <?php }?>
            </ul> <i class="fa fa-circle"></i>
        </li>
        <?php if($steps || $param['reject']){?>
        <li> <a href="?m=content&c=content&a=init&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>" class=""> <i class="fa fa-reply"></i> <?php echo L('返回');?></a> <i class="fa fa-circle"></i> </li>
        <?php }?>
        <li> <a href="javascript:;" onclick="javascript:dr_content_submit('?m=content&c=content&a=add&menuid=<?php echo $param['menuid'];?>&catid=<?php echo $param['catid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>','add');"> <i class="fa fa-plus"></i> <?php echo L('add_content');?></a> </li>
    </ul>
</div>
<div class="page-body" style="margin-top: 20px;margin-bottom:30px;padding-top:15px;">
<div class="right-card-box">
    <div class="row table-search-tool">
        <form name="searchform" action="" method="get" >
        <input type="hidden" value="content" name="m">
        <input type="hidden" value="content" name="c">
        <input type="hidden" value="init" name="a">
        <input type="hidden" value="<?php echo $param['catid'];?>" name="catid">
        <input type="hidden" value="<?php echo $steps;?>" name="steps">
        <input type="hidden" value="1" name="search">
        <input type="hidden" value="<?php echo dr_get_csrf_token();?>" name="pc_hash">
        <div class="col-md-12 col-sm-12">
        <label><select id="posids" name="posids" class="form-control"><option value='' <?php if($param['posids']=='') echo 'selected';?>><?php echo L('all');?></option>
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
    <td align="center"><label><a href="<?php
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
        }?>" target="_blank"<?php if($r['status']!=99) {?> onclick='window.open("?m=content&c=content&a=public_preview&catid=<?php echo $r['catid'];?>&id=<?php echo $r['id'];?>","manage")'<?php }?> class="btn btn-xs blue"><i class="fa fa-eye"></i> <?php echo L('preview');?></a></label>
        <label><a href="javascript:;" onclick="javascript:dr_content_submit('?m=content&c=content&a=edit&catid=<?php echo $r['catid'];?>&id=<?php echo $r['id'];?>','edit')" class="btn btn-xs green"><i class="fa fa-edit"></i> <?php echo L('edit');?></a></label>
        <?php foreach ($clink as $a) {
            echo ' <label><a class="btn '.$a['color'].' btn-xs" href="'.str_replace(['{id_encode}', '{modelid}', '{catid}', '{id}', '{siteid}', '{m}'], [id_encode('content_'.$r['catid'],$r['id'],$this->siteid), $modelid, $param['catid'], $r['id'], $this->siteid, ROUTE_M], urldecode($a['url'])).'"><i class="'.$a['icon'].'"></i> '.L($a['name']);
            if ($a['field'] && $this->db->field_exists($a['field'])) {
                echo '（'.intval($r[$a['field']]).'）';
                
            }
            echo '</a></label>';
        }?>
        </td>
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
                    url: '?m=content&c=content&a=delete&catid=<?php echo $param['catid'];?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
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
            Dialog.confirm('确认要删除选中的内容吗？您可以在回收站恢复！', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&recycle=1&catid=<?php echo $param['catid'];?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
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
    $('body').on('click','#push',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            artdialog('contentpush','?m=content&c=push&action=position_list&catid=<?php echo $param['catid']?>&modelid=<?php echo $modelid?>&id='+ids.toString().replace(new RegExp(",","g"),'|'),'<?php echo L('push');?>：',800,500);
        }
    })
    $('body').on('click','#copy',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            artdialog('contentcopy','?m=content&c=copy&a=init&module=content&action=category_list_copy&modelid=<?php echo $modelid?>&catid=<?php echo $param['catid']?>&id='+ids.toString().replace(new RegExp(",","g"),'|'),'<?php echo L('copy');?>：',800,500);
        }
    })
    <?php if (module_exists('bdts')) {?>
    $('body').on('click','#bdts',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=bdts&c=bdts&a=add&modelid=<?php echo $modelid;?>&pc_hash='+pc_hash,
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
        }
    })
    <?php }?>
    $('body').on('click','#remove',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            artdialog('contentremove','?m=content&c=content&a=remove&catid=<?php echo $param['catid']?>&ids='+ids,'<?php echo L('remove');?>：',800,500);
        }
    })
    <?php if($setting['content_ishtml']) {?>
    $('body').on('click','#createhtml',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=create_html&a=batch_show&catid=<?php echo $param['catid'];?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
                data: $('#myform').serialize(),
                dataType: 'json',
                success:function(json) {
                    layer.close(loading);
                    // token 更新
                    if (json.token) {
                        var token = json.token;
                        $("#myform input[name='"+token.name+"']").val(token.value);
                    }
                    if (json.code == 1) {
                       dr_bfb('<?php echo L('生成内容页面');?>', '', json.msg);
                    } else {
                        dr_tips(0, json.msg);
                    }
                    return false;
                },
                error:function(HttpRequest, ajaxOptions, thrownError) {
                    dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
                }
            });
        }
    })
    <?php }?>
    <?php if($status!=99) {?>
    $('body').on('click','#passed',function() {
        var ids = [];
        $('input[name="ids[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=pass&catid=<?php echo $param['catid'];?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
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
        }
    })
    <?php }?>
});
function view_comment(id) {
    var w = 800;
    var h = 500;
    openwinx('view_comment','?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id+'&pc_hash='+pc_hash,'<?php echo L('view_comment')?>',w,h);
}
</script>
</div>
</div>
</div>
</body>
</html>