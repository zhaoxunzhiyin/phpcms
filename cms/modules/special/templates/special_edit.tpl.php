<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_validator = $show_scroll = $show_dialog = 1;
include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="dosubmit" type="hidden" value="1">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('catgory_basic', '', 'admin').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-th-list"></i> <?php if (is_pc()) {echo L('catgory_basic', '', 'admin');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('extend_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('extend_setting');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('special_title')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="title" name="special[title]" value="<?php echo new_html_special_chars($info['title']);?>" ></label>
                            <span class="help-block" id="titleTip"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('special_banner')?></label>
                        <div class="col-md-9">
                            <?php echo form::images('special[banner]', 'banner', $info['banner'], 'special')?>
                            <span class="help-block" id="bannerTip"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('sepcial_thumb')?></label>
                        <div class="col-md-9">
                            <?php echo form::images('special[thumb]', 'thumb', $info['thumb'], 'special', '', '', '', '', '', array(350, 350))?>
                            <span class="help-block" id="thumbTip"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('special_intro')?></label>
                        <div class="col-md-9">
                            <textarea name="special[description]" id="description" class="form-control"><?php echo $info['description'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ishtml')?></label>
                        <div class="col-md-9">
                            <?php echo form::radio(array('1'=>L('yes'), '0'=>L('no')), $info['ishtml'], 'name="special[ishtml]"');?>
                        </div>
                    </div>
                    <div class="form-group" id="file_div" style="display:<?php if($info['ishtml']) {?> <?php } else {?>none<?php }?>;">
                        <label class="col-md-2 control-label"><?php echo L('special_filename')?></label>
                        <div class="col-md-9">
                            <label><input type="text" name="special[filename]" id="filename" class="form-control input-large" <?php if($info['filename']) {?> readonly<?php }?> value="<?php echo $info['filename']?>"></label>
                            <span class="help-block" id="filenameTip"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2"><?php echo L('special_type')?></label>
                        <div class="col-md-9">
                            <div class="table-scrollable">
                                <table class="table table-nomargin table-bordered table-striped table-bordered table-advance">
                                    <thead>
                                        <tr>
                                            <th><?php echo L('type_id')?></th>
                                            <th><?php echo L('type_name')?></th>
                                            <th><?php echo L('type_path')?></th>
                                            <th><?php echo L('listorder')?></th>
                                            <th width="50" style="text-align: center"><button type="button" class="btn blue btn-xs" onClick="dr_add_table_type()"> <i class="fa fa-plus"></i> </button></th>
                                        </tr>
                                    </thead>
                                    <tbody id="dr_type_body">
                                        <?php $ksids = [];
                                        if(is_array($types)) {$k = 1; foreach($types as $t) {?>
                                        <tr id="dr_ftable_type_row_<?php echo $k;?>">
                                            <td><?php echo $t['typeid']?><input type="hidden" name="type[<?php echo $k?>][typeid]" value="<?php echo $t['typeid']?>"></td>
                                            <td><input type="text" class="form-control" <?php if ($k==1) {?>id="type_name"<?php }?> name="type[<?php echo $k?>][name]" value="<?php echo new_html_special_chars($t['name'])?>"></td>
                                            <td><input type="text" class="form-control" <?php if ($k==1) {?>id="type_path"<?php }?> name="type[<?php echo $k?>][typedir]" value="<?php echo $t['typedir']?>"></td>
                                            <td><input type="text" class="form-control" name="type[<?php echo $k?>][listorder]" value="<?php echo $t['listorder']?>"></td>
                                            <td style="text-align: center"><?php if ($k!=1) {?><button type="button" class="btn red btn-xs" onclick="dr_del_table_type(this, <?php echo $k?>)"> <i class="fa fa-trash"></i> </button><?php }?></td>
                                        </tr>
                                        <?php $ksids[] = $k; $k++;
                                        }
                                        $ksid = is_array($ksids) && $ksids ? max($ksids) : 0;
                                        }?>
                                    </tbody>
                                </table>
                            </div>
                            <span class="help-block" id="typeTip"></span>
                            <script>
                            var special_type = {"tpl":" <tr id=\"dr_ftable_type_row_{hang}\"> <td></td><td><input type=\"text\" class=\"form-control\" name=\"type[{hang}][name]\" value=\"\"><\/td> <td><input type=\"text\" class=\"form-control\" name=\"type[{hang}][typedir]\" value=\"\"><\/td> <td><input type=\"text\" class=\"form-control\" name=\"type[{hang}][listorder]\" value=\"{hang}\"><\/td> <td style=\"text-align: center\"><button type=\"button\" class=\"btn red btn-xs\" onClick=\"dr_del_table_type(this)\"> <i class=\"fa fa-trash\"><\/i> <\/button><\/td> <\/tr>","id":<?php echo $ksid;?>};
                            function dr_del_table_type(e, id) {
                                $(e).parent().parent().append('<input type="hidden" name="type['+id+'][del]" value="1">');
                                $(e).parent().parent().fadeOut();
                            }
                            function dr_add_table_type() {
                                var tpl = special_type.tpl;
                                 special_type.id ++;
                                tpl = tpl.replace(/\{hang\}/g, special_type.id);
                                $('#dr_type_body').append(tpl);
                            }
                            </script>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('pics_news')?></label>
                        <div class="col-md-9">
                            <span id="relation"><?php if ($info['pics']) {?><ul id="relation_relation" class="list-dot"><li><span><?php echo $pics['2']?></span><a onclick="remove_relation('relation', 'pics')" class="close" href="javascript:void(0);"></a></li></ul><?php }?></span><input type="button" value="<?php echo L('choose_pic_news')?>" class="button" onclick="import_info('?m=special&c=special&a=public_get_pics','<?php echo L('choose_pic_news')?>', 'msg_id', 'relation', 'pics');"><input type="hidden" name="special[pics]" value="<?php echo $info['pics']?>" id="pics"><span class="onShow">(<?php echo L('choose_pic_model')?>)</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('add_vote')?></label>
                        <div class="col-md-9">
                            <span id="vote_msg"><?php if ($info['voteid']) {?><ul id="relation_vote_msg" class="list-dot"><li><span><?php echo $vote_info['2']?></span><a onclick="remove_relation('vote_msg', 'voteid')" class="close" href="javascript:void(0);"></a></li></ul><?php }?></span><input type="button" class="button" value="<?php echo L('choose_exist_vote')?>" onclick="import_info('?m=vote&c=vote&a=public_get_votelist&from_api=1&target=dialog','<?php echo L('choose_vote')?>', 'msg_id', 'vote_msg', 'voteid');"><input type="hidden" name="special[voteid]" value="<?php echo $info['voteid']?>" id="voteid">&nbsp;<input type="button" class="button" value="<?php echo L('add_new_vote')?>" onclick="import_info('?m=vote&c=vote&a=add&from_api=1&target=dialog','<?php echo L('add_new_vote')?>', 'subject_title', 'vote_msg', 'voteid');">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('index_page')?></label>
                        <div class="col-md-9">
                            <?php echo form::radio(array('0'=>L('no'), '1'=>L('yes')), $info['ispage'], 'name="special[ispage]"');?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('special_status')?></label>
                        <div class="col-md-9">
                            <?php echo form::radio(array('0'=>L('open'), '1'=>L('pause')), $info['disabled'], 'name="special[disabled]"');?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('template_style')?></label>
                        <div class="col-md-9">
                            <?php echo form::select($template_list, $info['style'], 'name="special[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?><?php if ($info['style']) {?><script type="text/javascript">$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style=<?php echo $info['style']?>&module=special&templates=index|list|show&id=<?php echo $info['index_template']?>|<?php echo $info['list_template']?>|<?php echo $info['show_template']?>&name=special', function(data){$('#index_template').html(data.index_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});</script><?php }?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('special_template')?></label>
                        <div class="col-md-9">
                            <label id="index_template"><?php echo form::select_template('default', 'special', $info['index_template'], 'name="special[index_template]"', 'index');?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('special_type_template')?></label>
                        <div class="col-md-9">
                            <label id="list_template"><?php echo form::select_template('default', 'special', $info['list_template'], 'name="special[list_template]"', 'list');?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('special_content_template')?></label>
                        <div class="col-md-9">
                            <label id="show_template"><?php echo form::select_template('default', 'special', $info['show_template'], 'name="special[show_template]"', 'show');?></label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function import_info(url, title, msgID, htmlID, valID) {
    if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
    var w = 600;
    var h = 400;
    if (is_mobile()) {
        w = h = '100%';
    }
    var diag = new Dialog({
        id:'selectid',
        title:title,
        url:'<?php echo SELF;?>'+url,
        width:w,
        height:h,
        modal:true
    });
    diag.onOk = function(){
        var text = $DW.$V("#"+msgID);
        var data = text.split('|');
        if (data[2]) {
            $('#'+htmlID).html('<ul id="relation_'+htmlID+'" class="list-dot"><li><span>'+data[2]+'</span><a onclick="remove_relation(\''+htmlID+'\', \''+valID+'\')" class="close" href="javascript:;"></a></li></ul>');
        } else {
            var form = $DW.$('#dosubmit');
            form.click();
            $('#'+htmlID).html('<ul id="relation_'+htmlID+'" class="list-dot"><li><span>'+text+'</span><a onclick="remove_relation(\''+htmlID+'\', \''+valID+'\')" class="close" href="javascript:;"></a></li></ul>');
        }
        $('#'+valID).val(text);
        diag.close();
        return false;
    };
    diag.onCancel=function() {
        $DW.close();
    };
    diag.show();void(0);
}

function remove_relation(htmlID, valID) {
    $('#relation_'+htmlID).html('');
    $('#'+valID).val('');
}

function load_file_list(id) {
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=special&templates=index|list|show&name=special', function(data){$('#index_template').html(data.index_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
}

$(document).ready(function(){
    $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
    $('#title').formValidator({autotip:true,onshow:"<?php echo L('please_input_special_title')?>",onfocus:"<?php echo L('min_3_title')?>",oncorrect:"<?php echo L('true')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_input_special_title')?>"}).ajaxValidator({type:"get",url:"",data:"m=special&c=special&a=public_check_special&id=<?php echo $this->input->get('specialid')?>",datatype:"html",cached:false,async:'true',success : function(data) {
        if( data == "1" )
        {
            return true;
        }
        else
        {
            return false;
        }
    },
    error: function(){Dialog.alert("<?php echo L('server_no_data')?>");},
    onerror : "<?php echo L('special_exist')?>",
    onwait : "<?php echo L('checking')?>"
}).defaultPassed();
    $('#banner').formValidator({autotip:true,onshow:"<?php echo L('please_upload_banner')?>",oncorrect:"<?php echo L('true')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_upload_banner')?>"}).defaultPassed();
    $('#thumb').formValidator({autotip:true,onshow:"<?php echo L('please_upload_thumb')?>",oncorrect:"<?php echo L('true')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_upload_thumb')?>"}).defaultPassed();
    $('#filename').formValidator({autotip:true,onshow:"<?php echo L('special_file')?>",onfocus:'<?php echo L('use_letters')?>',oncorrect:"<?php echo L('true')?>"}).functionValidator({
        fun:function(val,elem){
        if($("input:radio[name='special[ishtml]']:checked").val()==0){
            return true;
        }else if($("input:radio[name='special[ishtml]']:checked").val()==1 && val==''){
            return "<?php echo L('please_input_name')?>"
        }  else {
            return true;
        }
    }
}).regexValidator({regexp:"^\\w*$", onerror:"<?php echo L('error')?>"}).defaultPassed();
    $("#type_name").formValidator({tipid:"typeTip",onshow:"<?php echo L('input_type_name')?>",onfocus:"<?php echo L('input_type_name')?>",oncorrect:"<?php echo L('true')?>"}).inputValidator({min:1,onerror:"<?php echo L('input_type_name')?>"}).defaultPassed();
    $('#type_path').formValidator({tipid:"typeTip",onshow:"<?php echo L('input_type_path')?>",onfocus:"<?php echo L('input_type_path')?>",oncorrect:"<?php echo L('true')?>"}).inputValidator({min:2,onerror:"<?php echo L('input_type_path')?>"}).regexValidator({regexp:"^\\w*$", onerror:"<?php echo L('error')?>"}).defaultPassed();
});
$("input:radio[name='special[ishtml]']").click(function (){
    if($("input:radio[name='special[ishtml]']:checked").val()==0) {
        $("#file_div").hide();
    } else if($("input:radio[name='special[ishtml]']:checked").val()==1) {
        $("#file_div").show();
    }
});
</script>