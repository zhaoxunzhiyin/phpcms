<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<div id="closeParentTime" style="display:none"></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
/*$(function(){
	window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.treemain.location = '?m=content&c=content&a=public_categorys&type=add&menuid=<?php echo $this->input->get('menuid');?>';
})
if(window.top.$("#current_pos").data('clicknum')==1 || window.top.$("#current_pos").data('clicknum')==null) {
    parent.document.getElementById('display_center_id').style.display='';
    parent.document.getElementById('display_menu_id').style.display='';
    parent.document.getElementById('center_frame').src = '?m=content&c=content&a=public_categorys&type=add&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>';
    window.top.$("#current_pos").data('clicknum',0);
}*/
//-->
</SCRIPT>
<link rel="stylesheet" href="<?php echo JS_PATH;?>layui/css/layui.css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>admin/css/global.css" media="all" />
<style type="text/css">
.list_order {text-align: left;}
#keyword, #search {height: 32px;line-height: 32px;}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>layui/layui.js"></script>
<div class="admin-main layui-anim layui-anim-upbit">
    <fieldset class="layui-elem-field layui-field-title">
        <legend><?php echo L('list');?></legend>
    </fieldset>
    <blockquote class="layui-elem-quote">
        <?php 
        foreach($datas2 as $r) {
            echo "<a href=\"?m=content&c=content&a=initall&modelid=".$r['modelid']."&menuid=822&pc_hash=".$pc_hash."\" class=\"layui-btn layui-btn-sm";
            if($r['modelid']==$modelid) echo " layui-btn-danger";
            if ($r['modelid']==2) {
                echo "\"><i class=\"fa fa-download\"></i> ".$r['name']."</a>";
            } else if ($r['modelid']==3) {
                echo "\"><i class=\"fa fa-image\"></i> ".$r['name']."</a>";
            } else {
                echo "\"><i class=\"fa fa-list\"></i> ".$r['name']."</a>";
            }
        }
        ?>
        <a href="javascript:;" onclick="javascript:$('#searchid').toggle();" class="layui-btn layui-btn-sm layui-btn-normal">
            <i class="fa fa-search"></i> <?php echo L('search');?>
        </a>
<?php
echo "<br>";
echo "<br>";
if(is_array($infos)){
    foreach($infos as $info){
        $r = $this->db->get_one(array('status'=>$status,'username'=>$info['username']), "COUNT(*) AS num");
        echo "<a class=\"layui-btn layui-btn-sm";
        if($info['username']==$keyword) echo ' layui-btn-danger';
        echo "\">";
        echo $info['realname'] ? $info['realname'] : $info['username'];
        echo "(总".$r['num'].")</a>";
    }
}
echo "<br>";
echo "<br>";
if(is_array($infos)){
    foreach($infos as $info){
        $r2 = $this->db->get_one("status=".$status." and username='".$info['username']."' and `inputtime` > '".strtotime(date("Ymd", time()))."' and `inputtime` < '".strtotime(date("Ymd", strtotime('+1 day',time())))."'", "COUNT(*) AS num");
        echo "<a class=\"layui-btn layui-btn-sm";
        if($info['username']==$keyword) echo ' layui-btn-danger';
        echo "\">";
        echo $info['realname'] ? $info['realname'] : $info['username'];
        echo "(今".$r2['num'].")</a>";
    }
}
?>
    </blockquote>
    <div class="demoTable" id="searchid" style="display:none;">
        <?php echo L('addtime');?>：
        <?php echo form::date('start_time',$this->input->get('start_time'),0,0,'false');?>- &nbsp;<?php echo form::date('end_time',$this->input->get('end_time'),0,0,'false');?>
                <select id="posids" name="posids"><option value='' <?php if($this->input->get('posids')=='') echo 'selected';?>><?php echo L('all');?></option>
                <option value="1" <?php if($this->input->get('posids')==1) echo 'selected';?>><?php echo L('elite');?></option>
                <option value="2" <?php if($this->input->get('posids')==2) echo 'selected';?>><?php echo L('no_elite');?></option>
                </select>                
                <select id="searchtype" name="searchtype">
                    <option value='0' <?php if($this->input->get('searchtype')==0) echo 'selected';?>><?php echo L('title');?></option>
                    <option value='1' <?php if($this->input->get('searchtype')==1) echo 'selected';?>><?php echo L('intro');?></option>
                    <option value='2' <?php if($this->input->get('searchtype')==2) echo 'selected';?>><?php echo L('username');?></option>
                    <option value='3' <?php if($this->input->get('searchtype')==3) echo 'selected';?>>ID</option>
                </select>
        <div class="layui-inline">
            <input class="layui-input" name="keyword" id="keyword" <?php if(isset($keyword)) echo $keyword;?> placeholder="请输入关键字">
        </div>
        <button class="layui-btn" id="search" data-type="reload"><i class="fa fa-search"></i> <?php echo L('search');?></button>
        <div style="clear: both;"></div>
    </div>
    <table class="layui-table" id="list" lay-filter="list"></table>
</div>
<script type="text/html" id="attribute">
    {{# if(d.thumb){ }}
    <img src="<?php echo IMG_PATH;?>icon/small_img.gif" onmouseover="layer.tips('<img src={{d.thumb}}>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();">
    {{# } }}
    {{# if(d.posids==1){ }}
    <img src="<?php echo IMG_PATH;?>icon/small_elite.png" onmouseover="layer.tips('<?php echo L('elite');?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
    {{# } }}
    {{# if(d.islink==1){ }}
    <img src="<?php echo IMG_PATH;?>icon/link.png" onmouseover="layer.tips('<?php echo L('islink_url');?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
    {{# } }}
</script>
<script type="text/html" id="hits">
    <span style="display: block;" onmouseover="layer.tips('<?php echo L('today_hits');?>：{{d.dayviews}}<br><?php echo L('yestoday_hits');?>：{{d.yesterdayviews}}<br><?php echo L('week_hits');?>：{{d.weekviews}}<br><?php echo L('month_hits');?>：{{d.monthviews}}',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">{{d.views}}</span>
</script>
<script type="text/html" id="username">
    {{# if(d.sysadd==0){ }}
    <a href='javascript:;' onclick="omnipotent('member','?m=member&c=member&a=memberinfo&username={{d.deusername}}&pc_hash=<?php echo $this->input->get('pc_hash');?>','<?php echo L('view_memberlinfo');?>',1,700,500);">{{d.username}}</a><img src="<?php echo IMG_PATH;?>icon/contribute.png" onmouseover="layer.tips('<?php echo L('member_contribute');?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
    {{# } else { }}
    {{d.username}}
    {{# } }}
</script>
<script type="text/html" id="action">
    <a href="{{d.url}}" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal"><i class="fa fa-eye"></i> <?php echo L('preview');?></a>
    <a href="javascript:;" onclick="javascript:contentopen('?m=content&c=content&a=edit&catid={{d.catid}}&id={{d.id}}','<?php echo L('edit').L('content');?>')" class="layui-btn layui-btn-xs"><i class="fa fa-edit"></i> <?php echo L('edit');?></a>
    <a href="javascript:view_comment('{{d.idencode}}','{{d.safetitle}}')" class="layui-btn layui-btn-xs layui-btn-danger"><i class="fa fa-comment"></i> <?php echo L('comment');?></a>
</script>
<script>
layui.use(['table'], function(){
    var table = layui.table, $ = layui.jquery;
    var tableIn = table.render({
        id: 'content',
        elem: '#list',
        url:'?m=content&c=content&a=initall&modelid=<?php echo $modelid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
        method: 'post',
        cellMinWidth: 80,
        page: true,
        cols: [[
            {field: 'id', title: '<?php echo L('number');?>', width: 80, sort: true, fixed: 'left'},
            {field: 'title', title: '<?php echo L('title');?>', minWidth:340, sort: true, edit: 'text'},
            {field: 'attribute', title: '<?php echo L('attribute');?>', templet: '#attribute', width:100},
            {field: 'hits', title: '<?php echo L('hits');?>', width:100, templet: '#hits', sort: true},
            {field: 'publish_user', title: '<?php echo L('publish_user');?>', width:100, templet: '#username', sort: true},
            {field: 'updatetime', title: '<?php echo L('updatetime');?>', width:180, sort: true},
            {width: 240, align: 'center', toolbar: '#action',title:'<?php echo L('operations_manage');?>'<?php if(!is_mobile(0)) {?>, fixed: 'right'<?php }?>}
        ]],
        limit: 10
    });
    //搜索
    $('#search').on('click', function () {
        var keyword = $('#keyword').val();
        var start_time = $('#start_time').val();
        var end_time = $('#end_time').val();
        var posids = $('#posids').val();
        var searchtype = $('#searchtype').val();
        /*if ($.trim(keyword) === '') {
            layer.msg('请输入关键字！', {icon: 0});
            return;
        }*/
        tableIn.reload({ page: {page: 1}, where: {keyword: keyword,start_time: start_time,end_time: end_time,posids: posids,searchtype: searchtype} });
    });
    //监听单元格编辑
    table.on('edit(list)',function(obj) {
        var value = obj.value, data = obj.data, field = obj.field;
        if (field=='title' && value=='') {
            layer.tips('标题不能为空',this,{tips: [1, '#000']});
            return false;
        }else{
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=update&dosubmit=1&modelid=<?php echo $modelid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
                data: {id:data.id,field:field,value:value},
                dataType: 'json',
                success: function(res) {
                    if (res.code == 1) {
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            tableIn.reload();
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                    }
                }
            });
        }
    });
});
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript"> 
<!--
function view_comment(id, name) {
    var diag = new Dialog({
        id:'view_comment',
        title:'<?php echo L('view_comment');?>：'+name,
        url:'<?php echo SELF;?>?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id+'&pc_hash='+pc_hash,
        width:800,
        height:500,
        modal:true
    });
    diag.onCancel=function() {
        $DW.close();
    };
    diag.show();
}
setcookie('refersh_time', 0);
function refersh_window() {
    var refersh_time = getcookie('refersh_time');
    if(refersh_time==1) {
        location.reload(true);
    }
}
setInterval("refersh_window()", 3000);
//-->
</script>
</body>
</html>