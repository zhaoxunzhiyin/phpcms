<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<style type="text/css">
.table-list td b{color:#666}
.tpl_style{background-color:#FBFAE3}
.table-list tbody>tr>td, .table-list tbody>tr>th, .table-list thead>tr>td, .table-list thead>tr>th {border: 1px solid #e7ecf1;}
</style>
<form name="myform" id="myform" action="?m=admin&c=category&a=batch_edit" method="post">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="pad_10">
<div class="note note-danger">
    <p><a href="?m=admin&c=category&a=init&menuid=<?php echo $this->input->get('menuid');?>"><?php echo L('category_manage');?></a></p>
</div>
<div class="explain-col">
<?php echo L('category_batch_tips');?></a>
</div>
<div class="bk10"></div>
<div class="myfbody">
<div id="table-lists" class="table-scrollable">
    <table cellspacing="0" class="table table-striped table-bordered table-hover dataTable">
        <thead>
     <tr class="heading">
        <?php
        foreach($batch_array as $catid=>$cat) {
            $batch_array[$catid]['setting'] = string2array($cat['setting']);
            echo "<th width='300' align='left'><strong>{$cat['catname']} （catid: <font color='red'>{$catid}</font>）</strong></th>";
        }
        ?>
     </tr>
        </thead>
    <tbody>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('catname')?>：</b><br><input type='text' name='info[<?php echo $catid;?>][catname]' id='catname' class='input-text' value='<?php echo $cat['catname']?>' style='width:250px'></td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('catdir')?>：</b><br><input type='text' name='info[<?php echo $catid;?>][catdir]' id='catname' class='input-text' value='<?php echo $cat['catdir']?>' style='width:250px'></td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('catgory_img')?>：</b><br><?php echo form::images('info['.$catid.'][image]', 'image'.$catid, $cat['image'], 'content', '', (is_mobile() ? 14 : 15));?></td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('description')?>：</b><br><textarea name="info[<?php echo $catid;?>][description]" maxlength="255" style="width:240px;height:40px;"><?php echo $cat['description'];?></textarea></td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td class="tpl_style"><b><?php echo L('available_styles')?>：</b><br>
        <?php echo form::select($template_list, $cat['setting']['template_list'], 'name="setting['.$catid.'][template_list]" id="template_list" onchange="load_file_list(this.value,'.$catid.')"', L('please_select'))?> 
        </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
            
    ?>
        <td class="tpl_style"><b><?php echo L('page_templates')?>：</b><br>
        <div id="category_template<?php echo $catid;?>">
        <?php echo form::select_template($cat['setting']['template_list'], 'content',$cat['setting']['page_template'],'name="setting['.$catid.'][page_template]" style="width:250px"','page');?>
        </div>
        </td>
    <?php
        }
    ?>
     </tr>
    
    
    <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('ismenu')?>：</b><br>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input boxid="ismenu" type='radio' name='info[<?php echo $catid;?>][ismenu]' value='1' <?php if($cat['ismenu']) echo 'checked';?> onclick="change_radio(event,'ismenu',1)"> <?php echo L('yes');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input boxid="ismenu" type='radio' name='info[<?php echo $catid;?>][ismenu]' value='0' <?php if(!$cat['ismenu']) echo 'checked';?> onclick="change_radio(event,'ismenu',0)"> <?php echo L('no');?> <span></span></label>
        </div>
      </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('继承下级')?>：</b><br>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input boxid="getchild" type='radio' name='setting[<?php echo $catid;?>][getchild]' value='1' <?php if($cat['setting']['getchild']) echo 'checked';?> onclick="change_radio(event,'getchild',1)"> <?php echo L('open');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input boxid="getchild" type='radio' name='setting[<?php echo $catid;?>][getchild]' value='0' <?php if(!$cat['setting']['getchild']) echo 'checked';?> onclick="change_radio(event,'getchild',0)"> <?php echo L('close');?> <span></span></label>
        </div>
      </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('可用')?>：</b><br>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input boxid="disabled" type='radio' name='info[<?php echo $catid;?>][disabled]' value='0' <?php if(!$cat['disabled']) echo 'checked';?> onclick="change_radio(event,'disabled',0)"> <?php echo L('可用');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input boxid="disabled" type='radio' name='info[<?php echo $catid;?>][disabled]' value='1' <?php if($cat['disabled']) echo 'checked';?> onclick="change_radio(event,'disabled',1)"> <?php echo L('禁用');?> <span></span></label>
        </div>
      </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('您现在的位置')?>：</b><br>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input boxid="iscatpos" type='radio' name='setting[<?php echo $catid;?>][iscatpos]' value='1' <?php if($cat['setting']['iscatpos']) echo 'checked';?> onclick="change_radio(event,'iscatpos',1)"> <?php echo L('display');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input boxid="iscatpos" type='radio' name='setting[<?php echo $catid;?>][iscatpos]' value='0' <?php if(!$cat['setting']['iscatpos']) echo 'checked';?> onclick="change_radio(event,'iscatpos',0)"> <?php echo L('hidden');?> <span></span></label>
        </div>
      </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('左侧')?>：</b><br>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input boxid="isleft" type='radio' name='setting[<?php echo $catid;?>][isleft]' value='1' <?php if($cat['setting']['isleft']) echo 'checked';?> onclick="change_radio(event,'isleft',1)"> <?php echo L('display');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input boxid="isleft" type='radio' name='setting[<?php echo $catid;?>][isleft]' value='0' <?php if(!$cat['setting']['isleft']) echo 'checked';?> onclick="change_radio(event,'isleft',0)"> <?php echo L('hidden');?> <span></span></label>
        </div>
      </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('html_category')?>：</b><br>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input boxid="ishtml" catid="<?php echo $catid;?>" type='radio' name='setting[<?php echo $catid;?>][ishtml]' value='1' <?php if($cat['setting']['ishtml']) echo 'checked';?> onClick="change_radio(event,'ishtml',1,'category');urlrule('category',1,<?php echo $catid;?>)"> <?php echo L('yes');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input boxid="ishtml" catid="<?php echo $catid;?>" type='radio' name='setting[<?php echo $catid;?>][ishtml]' value='0' <?php if(!$cat['setting']['ishtml']) echo 'checked';?>  onClick="change_radio(event,'ishtml',0,'category');urlrule('category',0,<?php echo $catid;?>)"> <?php echo L('no');?> <span></span></label>
        </div>
      </td>
    <?php
        }
    ?>
     </tr>
     
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('category_urlrules')?>：</b><br>
        <div id="category_php_ruleid<?php echo $catid;?>" style="display:<?php if($cat['setting']['ishtml']) echo 'none';?>">
    <?php
        echo form::urlrule('content','category',0,$cat['setting']['category_ruleid'],'name="category_php_ruleid['.$catid.']" style="width:250px;"');
    ?>
    </div>
    <div id="category_html_ruleid<?php echo $catid;?>" style="display:<?php if(!$cat['setting']['ishtml']) echo 'none';?>">
    <?php
        echo form::urlrule('content','category',1,$cat['setting']['category_ruleid'],'name="category_html_ruleid['.$catid.']" style="width:250px;"');
    ?>
    </div>
      </td>
    <?php
        }
    ?>
     </tr>

     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('meta_title')?>：</b><br>
        <input name='setting[<?php echo $catid;?>][meta_title]' type='text' value='<?php echo $cat['setting']['meta_title'];?>' style='width:250px'>
          </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('meta_keywords')?>：</b><br>
        <input name='setting[<?php echo $catid;?>][meta_keywords]' type='text' value='<?php echo $cat['setting']['meta_keywords'];?>' style='width:250px'>
          </td>
    <?php
        }
    ?>
     </tr>
     <tr>
     <?php
        foreach($batch_array as $catid=>$cat) {
    ?>
        <td><b><?php echo L('meta_description')?>：</b><br>
        <input name='setting[<?php echo $catid;?>][meta_description]' type='text' value='<?php echo $cat['setting']['meta_description'];?>' style='width:250px'>
          </td>
    <?php
        }
    ?>
     </tr>
    
    
    </tbody>
    </table>
</div>
<input type="hidden" name="dosubmit" value="1" />
<input type="hidden" name="pc_hash" value="<?php echo dr_get_csrf_token();?>" />
<input type="hidden" name="type" value="<?php echo $type;?>" />
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=admin&c=category&a=batch_edit', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</div>
</div>
</div>
</form>
<script language="JavaScript">
<!--
$(document).keydown(function(event) {
    if(event.keyCode==37) {
        window.scrollBy(-100,0);
    } else if(event.keyCode==39) {
        window.scrollBy(100,0);
    }
});
function change_radio(oEvent,boxid,value,type) {
    altKey = oEvent.altKey;
    if(altKey) {
        var obj = $("input[boxid="+boxid+"][value="+value+"]");
        obj.prop('checked',true);
        if(type){
            obj.each(function(){    
                urlrule(type,value,$(this).attr('catid'));            
            })
        }
    }    
}
function urlrule(type,html,catid) {
    if(type=='category') {
        if(html) {
            $('#category_php_ruleid'+catid).css('display','none');$('#category_html_ruleid'+catid).css('display','');
        } else {
            $('#category_php_ruleid'+catid).css('display','');$('#category_html_ruleid'+catid).css('display','none');
        }
    } else {
        if(html) {
            $('#show_php_ruleid'+catid).css('display','none');$('#show_html_ruleid'+catid).css('display','');
        } else {
            $('#show_php_ruleid'+catid).css('display','');$('#show_html_ruleid'+catid).css('display','none');
        }
    }    
}
function load_file_list(id,catid) {
    if(id=='') return false;
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&batch_str=1&type=1&style='+id+'&catid='+catid, function(data){
    if(data==null) return false;
    $('#category_template'+catid).html(data.page_template);});
}
//-->
</script>
</body>
</html>
