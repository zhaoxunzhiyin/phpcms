<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET;?>';
	var uploadurl = '<?php echo SYS_UPLOAD_URL;?>';
//-->
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript">var catid=0</script>
<script type="text/javascript"> 
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#catname").formValidator({onshow:"<?php echo L('input_catname');?>",onfocus:"<?php echo L('input_catname');?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L('input_catname');?>"});
		$("#catdir").formValidator({onshow:"<?php echo L('input_dirname');?>",onfocus:"<?php echo L('input_dirname');?>"}).regexValidator({regexp:"^([a-zA-Z0-9]|[_-]){0,30}$",onerror:"<?php echo L('enter_the_correct_catname');?>"}).inputValidator({min:1,onerror:"<?php echo L('input_dirname');?>"}).ajaxValidator({type : "get",url : "",data :"m=admin&c=category&a=public_check_catdir",datatype : "html",cached:false,getdata:{parentid:'parentid'},async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('catname_have_exists');?>",onwait : "<?php echo L('connecting');?>"});
	})
//-->
</script>

<form name="myform" id="myform" action="?m=admin&c=category&a=add" method="post">
<div class="pad-10">
<div class="col-tab">

<ul class="tabBut cu-li">
            <li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',6,1);"><?php echo L('catgory_basic');?></li>
            <li id="tab_setting_2" onclick="SwapTab('setting','on','',6,2);"><?php echo L('catgory_createhtml');?></li>
            <li id="tab_setting_3" onclick="SwapTab('setting','on','',6,3);"><?php echo L('catgory_template');?></li>
            <li id="tab_setting_4" onclick="SwapTab('setting','on','',6,4);"><?php echo L('catgory_seo');?></li>
            <li id="tab_setting_5" onclick="SwapTab('setting','on','',6,5);"><?php echo L('catgory_private');?></li>
            <li id="tab_setting_6" onclick="SwapTab('setting','on','',6,6);"><?php echo L('field_manage');?></li>
</ul>
<div id="div_setting_1" class="contentList pad-10">

<table width="100%" class="table_form ">
<tr>
     <th><?php echo L('add_category_types');?>：</th>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='addtype' value='0' checked id="normal_addid"> <?php echo L('normal_add');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='addtype' value='1'  onclick="$('#catdir_tr').html(' ');$('#normal_add').html(' ');$('#normal_add').css('display','none');$('#batch_add').css('display','');$('#normal_addid').attr('disabled','true');this.disabled='true'"> <?php echo L('batch_add');?> <span></span></label>
        </div></td>
    </tr>
      <tr>
        <th width="200"><?php echo L('parent_category')?>：</th>
        <td>
		<?php echo form::select_category('category_content_'.$this->siteid,$parentid,'name="info[parentid]" id="parentid"',L('please_select_parent_category'),0,-1);?>
		</td>
      </tr>
     <tr>
        <th><?php echo L('catname')?>：</th>
        <td>
        <span id="normal_add"><input type="text" name="info[catname]" id="catname" class="input-text" value="" onblur="topinyin('catdir','catname','?m=admin&c=category&a=public_ajax_pinyin');"></span>
        <span id="batch_add" style="display:none"> 
        <table width="100%" class="sss"><tr><td width="310"><textarea name="batch_add" maxlength="255" style="width:300px;height:60px;"></textarea></td>
        <td align="left">
        <?php echo L('batch_add_tips');?>
 </td></tr></table>
        </span>
		</td>
      </tr>
	<tr id="catdir_tr">
        <th><?php echo L('catdir')?>：</th>
        <td><input type="text" name="info[catdir]" id="catdir" class="input-text" value=""></td>
      </tr>
	<tr>
        <th><?php echo L('catgory_img')?>：</th>
        <td><?php echo form::images('info[image]', 'image', $image, 'content');?></td>
      </tr>
	<tr>
        <th><?php echo L('description')?>：</th>
        <td>
		<textarea name="info[description]" maxlength="255" style="width:300px;height:60px;"><?php echo $description;?></textarea>
		</td>
      </tr>
<tr>
     <th><?php echo L('ismenu');?>：</th>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='1' checked> <?php echo L('yes');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='0'  > <?php echo L('no');?> <span></span></label>
        </div></td>
    </tr>
<tr>
     <th><?php echo L('可用');?>：</th>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[disabled]' value='0' checked> <?php echo L('可用');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[disabled]' value='1'  > <?php echo L('禁用');?> <span></span></label>
        </div><?php echo L('禁用状态下此栏目不能正常访问');?></td>
    </tr>
<tr>
     <th><?php echo L('您现在的位置');?>：</th>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='1' checked> <?php echo L('display');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='0'  > <?php echo L('hidden');?> <span></span></label>
        </div><?php echo L('前端栏目面包屑导航调用不会显示，但可以正常访问，您现在的位置不显示');?></td>
    </tr>
<tr>
     <th><?php echo L('左侧');?>：</th>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='1' checked> <?php echo L('display');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='0'> <?php echo L('hidden');?> <span></span></label>
        </div><?php echo L('前端栏目调用左侧不会显示，但可以正常访问');?></td>
    </tr>
</table>

</div>
<div id="div_setting_2" class="contentList pad-10 hidden">
<table width="100%" class="table_form ">
		<tr>
      <th width="200"><?php echo L('html_category');?>：</th>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[ishtml]' value='1' <?php if($setting['ishtml']) echo 'checked';?> onClick="$('#category_php_ruleid').css('display','none');$('#category_html_ruleid').css('display','')"> <?php echo L('yes');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[ishtml]' value='0' <?php if(!$setting['ishtml']) echo 'checked';?>  onClick="$('#category_php_ruleid').css('display','');$('#category_html_ruleid').css('display','none')"> <?php echo L('no');?> <span></span></label>
        </div>
	  </td>
    </tr>
	
	<tr>
      <th><?php echo L('urlrule_url');?>：</th>
      <td><div id="category_php_ruleid" style="display:<?php if($setting['ishtml']) echo 'none';?>">
	<?php
		echo form::urlrule('content','category',0,$setting['category_ruleid'],'name="category_php_ruleid"');
	?>
	</div>
	<div id="category_html_ruleid" style="display:<?php if(!$setting['ishtml']) echo 'none';?>">
	<?php
		echo form::urlrule('content','category',1,$setting['category_ruleid'],'name="category_html_ruleid"');
	?>
	</div>
	</td>
    </tr>
</table>
</div>
<div id="div_setting_3" class="contentList pad-10 hidden">
<table width="100%" class="table_form ">
<tr>
  <th width="200"><?php echo L('available_styles');?>：</th>
        <td>
		<?php echo form::select($template_list, 'default', 'name="setting[template_list]" id="template_list" onchange="load_file_list(this.value)"', L('please_select'))?> 
		</td>
</tr>
		<tr>
        <th width="200"><?php echo L('page_templates')?>：</th>
        <td  id="page_template">
		</td>
      </tr>
</table>
</div>
<div id="div_setting_4" class="contentList pad-10 hidden">
<table width="100%" class="table_form ">
	<tr>
      <th width="200"><?php echo L('meta_title');?></th>
      <td><input name='setting[meta_title]' type='text' id='meta_title' value='<?php echo $setting['meta_title'];?>' size='60' maxlength='60'></td>
    </tr>
    <tr>
      <th ><?php echo L('meta_keywords');?></th>
      <td><textarea name='setting[meta_keywords]' id='meta_keywords' style="width:90%;height:40px"><?php echo $setting['meta_keywords'];?></textarea></td>
    </tr>
    <tr>
      <th ><strong><?php echo L('meta_description');?></th>
      <td><textarea name='setting[meta_description]' id='meta_description' style="width:90%;height:50px"><?php echo $setting['meta_description'];?></textarea></td>
    </tr>
</table>
</div>
<div id="div_setting_5" class="contentList pad-10 hidden">
<table width="100%" >
		<tr>
        <th width="200"><?php echo L('role_private')?>：</th>
        <td>
			<table width="100%" class="table-list">
			  <thead>
				<tr>
				  <th align="left" width="200"><?php echo L('role_name');?></th><th><?php echo L('edit');?></th>
			  </tr>
			    </thead>
				 <tbody>
				<?php
				$roles = getcache('role','commons');
				foreach($roles as $roleid=> $rolrname) {
				$disabled = $roleid==1 ? 'disabled' : '';
				?>
		  		<tr>
				  <td><?php echo $rolrname?></td>
				  <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> value="init,<?php echo $roleid;?>" ><span></span></label></td>
			  </tr>
			  <?php }?>
	
			 </tbody>
			</table>
		</td>

      </tr>
		<tr><td colspan=2><hr style="border:1px dotted #F2F2F2;"></td>
		</tr>

	  <tr>
        <th width="200"><?php echo L('group_private')?>：</th>
        <td>
			<table width="100%" class="table-list">
			  <thead>
				<tr>
				  <th align="left" width="200"><?php echo L('group_name');?></th><th><?php echo L('allow_vistor');?></th>
			  </tr>
			    </thead>
				 <tbody>
			<?php
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
			if($_value['groupid']==1) continue;
			?>
		  		<tr>
				  <td><?php echo $_value['name'];?></td>
				  <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="priv_groupid[]" value="visit,<?php echo $_value['groupid'];?>" ><span></span></label></td>
			  </tr>
			<?php }?>
			 </tbody>
			</table>
		</td>
      </tr>
</table>
</div>
<div id="div_setting_6" class="contentList pad-10 hidden">
<table width="100%" class="table_form">
<?php
if(is_array($forminfos['base'])) {
 foreach($forminfos['base'] as $field=>$info) {
	 if($info['isomnipotent']) continue;
	 if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<tr>
      <th width="200"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?>
	  </th>
      <td class="y-bg"><?php echo $info['form']?>  <?php echo $info['tips']?></td>
    </tr>
<?php
} }
?>
</table>   
</div>

 <div class="bk15"></div>
	<input name="catid" type="hidden" value="<?php echo $catid;?>">
	<input name="type" type="hidden" value="<?php echo $type;?>">
    <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">

</form>
</div>

</div>
<!--table_form_off-->
</div>

<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
	function SwapTab(name,cls_show,cls_hide,cnt,cur){
		for(i=1;i<=cnt;i++){
			if(i==cur){
				 $('#div_'+name+'_'+i).show();
				 $('#tab_'+name+'_'+i).attr('class',cls_show);
			}else{
				 $('#div_'+name+'_'+i).hide();
				 $('#tab_'+name+'_'+i).attr('class',cls_hide);
			}
		}
	}
	function load_file_list(id) {
		if(id=='') return false;
		$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&catid=<?php echo $parentid?>&type=1', function(data){$('#page_template').html(data.page_template);});
	}
	<?php echo "load_file_list('default')"?>
//-->
</script>