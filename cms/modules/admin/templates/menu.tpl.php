<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<?php if(ROUTE_A=='init') {?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>layui/css/layui.css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>admin/css/global.css" media="all" />
<style type="text/css">
.list_order {text-align: left;}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>layui/layui.js"></script>
<div class="admin-main layui-anim layui-anim-upbit">
    <fieldset class="layui-elem-field layui-field-title">
        <legend><?php echo L('menu_manage');?></legend>
    </fieldset>
    <blockquote class="layui-elem-quote">
        <a href="?m=admin&c=menu&a=add&menuid=<?php echo $this->input->get('menuid');?>" class="layui-btn layui-btn-sm">
            <i class="fa fa-plus"></i> <?php echo L('add_menu');?>
        </a>
        <a class="layui-btn layui-btn-sm" onclick="Dialog.confirm('<?php echo L('confirm_refresh_menu');?>',function() {dr_admin_menu_ajax('?m=admin&c=menu&a=public_init');});">
            <i class="fa fa-refresh"></i> <?php echo L('refresh_menu');?>
        </a>
        <a class="layui-btn layui-btn-normal layui-btn-sm" onclick="openAll();">
            <i class="fa fa-folder-open-o"></i> <?php echo L('open_close');?>
        </a>
    </blockquote>
    <table class="layui-table" id="treeTable" lay-filter="treeTable"></table>
</div>
<script type="text/html" id="icon">
    {{# if(d.icon){ }}
    <i class="{{d.icon}}"></i>
    {{# } }}
</script>
<script type="text/html" id="display">
    <input type="checkbox" name="display" value="{{d.id}}" lay-skin="switch" lay-text="<?php echo L('display');?>|<?php echo L('hidden');?>" lay-filter="display" {{ d.display == 1 ? 'checked' : '' }}>
</script>
<script type="text/html" id="listorder">
    <input name="{{d.id}}" data-id="{{d.id}}" class="list_order layui-input" value="{{d.listorder}}" size="10"/>
</script>
<script type="text/html" id="action">
    <a href="?m=admin&c=menu&a=add&parentid={{d.id}}&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="layui-btn layui-btn-xs"><i class="fa fa-plus"></i> <?php echo L('add_submenu');?></a>
    <a href="?m=admin&c=menu&a=edit&id={{d.id}}&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="layui-btn layui-btn-xs"><i class="fa fa-edit"></i> <?php echo L('modify');?></a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="fa fa-trash-o"></i> <?php echo L('delete');?></a>
</script>
<script type="text/html" id="topBtn">
   <a href="?m=admin&c=menu&a=add&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="layui-btn layui-btn-sm"><?php echo L('add_menu');?></a>
</script>
<script>
    var pc_file = '<?php echo JS_PATH;?>';
    var editObj=null,ptable=null,treeGrid=null,tableId='treeTable',layer=null;
    layui.config({
        base: pc_file + 'layui/extend/'
    }).extend({
        treeGrid:'treeGrid'
    }).use(['jquery','treeGrid','layer','form'], function(){
        var $=layui.jquery;
        treeGrid = layui.treeGrid;
        layer=layui.layer;
		form = layui.form;
        ptable=treeGrid.render({
            id:tableId
            ,elem: '#'+tableId
            ,idField:'id'
            ,url:'?m=admin&c=menu&a=init&pc_hash='+pc_hash
            ,method: 'post'
            ,cellMinWidth: 100
            ,treeId:'id'//树形id字段名称
            ,treeManage:'manage'//树形manage字段名称
            ,treeUpId:'pid'//树形父id字段名称
            ,treeShowName:'title'//以树形式显示的字段
            ,height:'full-140'
            ,isFilter:false
            ,iconOpen:true//是否显示图标【默认显示】
            ,isOpenDefault:true//节点默认是展开还是折叠【默认展开】
            ,cols: [[
                {field: 'id', title: '<?php echo L('number')?>', width: 80, fixed: true},
                {field: 'title', title: '<?php echo L('chinese_name')?>'},
                {field: 'icon',align: 'center', title: '<?php echo L('菜单图标')?>', width: 100,toolbar: '#icon'},
                {field: 'display',align: 'center', title: '<?php echo L('menu_display')?>', width: 150,toolbar: '#display'},
                {field: 'listorder',align: 'center', title: '<?php echo L('listorder');?>', width: 80, templet: '#listorder'},
                {field: 'manage',title: '<?php echo L('operations_manage');?>',width: 240,align: 'center', toolbar: '#action'<?php if(!is_mobile(0)) {?>, fixed: 'right'<?php }?>}
            ]]
            ,page:false
        });
        treeGrid.on('tool('+tableId+')',function (obj) {
			var data = obj.data;
            if(obj.event === 'del'){
                Dialog.confirm('您确定要删除该记录吗？', function() {
                    $.ajax({
                        type: 'post',
                        url: '?m=admin&c=menu&a=delete&dosubmit=1&pc_hash='+pc_hash,
                        data: {id:data.id},
                        dataType: 'json',
                        success: function(res) {
                            if (res.code == 1) {
                                layer.msg(res.msg,{time:1000,icon:1});
                                obj.del();
                            }else{
                                layer.msg(res.msg,{time:1000,icon:2});
                            }
                        }
                    });
                });
            }
        });
        form.on('switch(display)', function(obj){
            loading = layer.load(1, {shade: [0.1,'#fff']});
            var id = this.value;
            var display = obj.elem.checked===true?1:0;
            $.ajax({
                type: 'post',
                url: '?m=admin&c=menu&a=display&dosubmit=1&pc_hash='+pc_hash,
                data: {id:id,display:display},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code == 1){
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload(true);
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                        treeGrid.render;
                        return false;
                    }
                }
            });
        });
        $('body').on('blur','.list_order',function() {
            var id = $(this).attr('data-id');
            var listorder = $(this).val();
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=admin&c=menu&a=listorder&dosubmit=1&pc_hash='+pc_hash,
                data: {id:id,listorder:listorder},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code === 1){
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload(true);
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                        treeGrid.render;
                    }
                }
            });
        });
    });
    function openAll() {
        var treedata=treeGrid.getDataTreeList(tableId);
        treeGrid.treeOpenAll(tableId,!treedata[0][treeGrid.config.cols.isOpen]);
    }
</script>
</body>
</html>


<?php } elseif(ROUTE_A=='add') {?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#language").formValidator({onshow:"<?php echo L("input").L('chinese_name')?>",onfocus:"<?php echo L("input").L('chinese_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('chinese_name')?>"});
		$("#name").formValidator({onshow:"<?php echo L("input").L('menu_name')?>",onfocus:"<?php echo L("input").L('menu_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('menu_name')?>"});
		$("#m").formValidator({onshow:"<?php echo L("input").L('module_name')?>",onfocus:"<?php echo L("input").L('module_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('module_name')?>"});
		$("#c").formValidator({onshow:"<?php echo L("input").L('file_name')?>",onfocus:"<?php echo L("input").L('file_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('file_name')?>"});
		$("#a").formValidator({tipid:'a_tip',onshow:"<?php echo L("input").L('action_name')?>",onfocus:"<?php echo L("input").L('action_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('action_name')?>"});
	})
//-->
</script>
<div class="common-form">
<form name="myform" id="myform" action="?m=admin&c=menu&a=add" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" >
        <option value="0"><?php echo L('no_parent_menu')?></option>
<?php echo $select_categorys;?>
</select></td>
      </tr>
      <tr>
        <th > <?php echo L('chinese_name')?>：</th>
        <td><input type="text" name="language" id="language" class="input-text" ></td>
      </tr>
      <tr>
        <th><?php echo L('menu_name')?>：</th>
        <td><input type="text" name="info[name]" id="name" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('module_name')?>：</th>
        <td><input type="text" name="info[m]" id="m" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('file_name')?>：</th>
        <td><input type="text" name="info[c]" id="c" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('action_name')?>：</th>
        <td><input type="text" name="info[a]" id="a" class="input-text" > <span id="a_tip"></span><?php echo L('ajax_tip')?></td>
      </tr>
	<tr>
        <th><?php echo L('att_data')?>：</th>
        <td><input type="text" name="info[data]" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('菜单图标')?>：</th>
        <td><input type="text" id="menu_icon" name="info[icon]" class="input-text" ><input type="button" name="icon" id="icon" value="选择图标" class="button" onclick="menuicon('icons','?m=admin&c=menu&a=public_icon&value='+$('#menu_icon').val(),'选择图标','80%','80%')"></td>
      </tr>
	<tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" checked> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0"> <?php echo L('no')?> <span></span></label>
        </div></td>
      </tr>
	  <tr>
        <th><?php echo L('show_in_model')?>：</th>
        <td><div class="mt-checkbox-inline">
          <?php foreach($models as $_k => $_m) {?><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="info[<?php echo $_k?>]" value="1"> <?php echo $_m?> <span></span></label><?php }?>
        </div></td>
      </tr>
</table>
<!--table_form_off-->
</div>
    <div class="bk15"></div>
	<div class="btn"><input type="submit" id="dosubmit" class="button" name="dosubmit" value="<?php echo L('submit')?>"/></div>
</div>

</form>

<?php } elseif(ROUTE_A=='edit') {?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#language").formValidator({onshow:"<?php echo L("input").L('chinese_name')?>",onfocus:"<?php echo L("input").L('chinese_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('chinese_name')?>"});
		$("#name").formValidator({onshow:"<?php echo L("input").L('menu_name')?>",onfocus:"<?php echo L("input").L('menu_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('menu_name')?>"});
		$("#m").formValidator({onshow:"<?php echo L("input").L('module_name')?>",onfocus:"<?php echo L("input").L('module_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('module_name')?>"});
		$("#c").formValidator({onshow:"<?php echo L("input").L('file_name')?>",onfocus:"<?php echo L("input").L('file_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('file_name')?>"});
		$("#a").formValidator({tipid:'a_tip',onshow:"<?php echo L("input").L('action_name')?>",onfocus:"<?php echo L("input").L('action_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('action_name')?>"});
	})
//-->
</script>
<div class="common-form">
<form name="myform" id="myform" action="?m=admin&c=menu&a=edit" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" style="width:200px;">
 <option value="0"><?php echo L('no_parent_menu')?></option>
<?php echo $select_categorys;?>
</select></td>
      </tr>
      <tr>
        <th> <?php echo L('for_chinese_lan')?>：</th>
        <td><input type="text" name="language" id="language" class="input-text" value="<?php echo L($name,'','',1)?>"></td>
      </tr>
      <tr>
        <th><?php echo L('menu_name')?>：</th>
        <td><input type="text" name="info[name]" id="name" class="input-text" value="<?php echo $name?>"></td>
      </tr>
	<tr>
        <th><?php echo L('module_name')?>：</th>
        <td><input type="text" name="info[m]" id="m" class="input-text" value="<?php echo $m?>"></td>
      </tr>
	<tr>
        <th><?php echo L('file_name')?>：</th>
        <td><input type="text" name="info[c]" id="c" class="input-text" value="<?php echo $c?>"></td>
      </tr>
	<tr>
        <th><?php echo L('action_name')?>：</th>
        <td><input type="text" name="info[a]" id="a" class="input-text" value="<?php echo $a?>">  <span id="a_tip"></span><?php echo L('ajax_tip')?></td>
      </tr>
	<tr>
        <th><?php echo L('att_data')?>：</th>
        <td><input type="text" name="info[data]" class="input-text" value="<?php echo $data?>"></td>
      </tr>
	<tr>
        <th><?php echo L('菜单图标')?>：</th>
        <td><input type="text" id="menu_icon" name="info[icon]" class="input-text" value="<?php echo $icon?>"><input type="button" name="icon" id="icon" value="选择图标" class="button" onclick="menuicon('icons','?m=admin&c=menu&a=public_icon&value='+$('#menu_icon').val(),'选择图标','80%','80%')"></td>
      </tr>
	<tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" <?php if($display) echo 'checked';?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0" <?php if(!$display) echo 'checked';?>> <?php echo L('no')?> <span></span></label>
        </div></td>
      </tr>
	<tr>
        <th><?php echo L('show_in_model')?>：</th>
        <td><div class="mt-checkbox-inline">
          <?php foreach($models as $_k => $_m) {?><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="info[<?php echo $_k?>]" value="1"<?php if (${$_k}) {?> checked<?php }?>> <?php echo $_m?> <span></span></label><?php }?>
        </div></td>
      </tr>

</table>
<!--table_form_off-->
</div>
    <div class="bk15"></div>
	<input name="id" type="hidden" value="<?php echo $id?>">
    <div class="btn"><input type="submit" id="dosubmit" class="button" name="dosubmit" value="<?php echo L('submit')?>"/></div>
</div>

</form>
<?php }?>
</body>
</html>