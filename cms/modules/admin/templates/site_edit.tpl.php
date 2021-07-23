<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET;?>';
	var uploadurl = '<?php echo SYS_UPLOAD_URL;?>';
//-->
</script>
<link href="<?php echo JS_PATH?>layui/css/layui.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo CSS_PATH?>bootstrap/js/bootstrap.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery-1.7.2.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>layui/layui.js"></script>
<script type="text/javascript">var catid=0</script>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#name").formValidator({onshow:"<?php echo L("input").L('site_name')?>",onfocus:"<?php echo L("input").L('site_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('site_name')?>"}).ajaxValidator({type : "get",url : "",data :"m=admin&c=site&a=public_name&siteid=<?php echo $data['siteid']?>",datatype : "html",async:'true',success : function(data){if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('site_name').L('exists')?>",onwait : "<?php echo L('connecting')?>"}).defaultPassed();
		$("#dirname").formValidator({onshow:"<?php echo L("input").L('site_dirname')?>",onfocus:"<?php echo L("input").L('site_dirname')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('site_dirname')?>"}).regexValidator({regexp:"username",datatype:"enum",param:'i',onerror:"<?php echo L('site_dirname_err_msg')?>"}).ajaxValidator({type : "get",url : "",data :"m=admin&c=site&a=public_dirname&siteid=<?php echo $data['siteid']?>",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('site_dirname').L('exists')?>",onwait : "<?php echo L('connecting')?>"}).defaultPassed();
		$("#domain").formValidator({onshow:"<?php echo L('site_domain_ex')?>",onfocus:"<?php echo L('site_domain_ex')?>",tipcss:{width:'300px'},empty:false}).inputValidator({onerror:"<?php echo L('site_domain_ex')?>"}).regexValidator({regexp:"http[s]?:\/\/(.+)\/$",onerror:"<?php echo L('site_domain_ex2')?>"});
		$("#template").formValidator({onshow:"<?php echo L('style_name_point')?>",onfocus:"<?php echo L('select_at_least_1')?>"}).inputValidator({onerror:"<?php echo L('select_at_least_1')?>"});
		$('#release_point').formValidator({onshow:"<?php echo L('publishing_sites_to_other_servers')?>",onfocus:"<?php echo L('choose_release_point')?>"}).inputValidator({max:4,onerror:"<?php echo L('most_choose_four')?>"});
		$('#default_style_input').formValidator({tipid:"default_style_msg",onshow:"<?php echo L('please_select_a_style_and_select_the_template')?>",onfocus:"<?php echo L('please_select_a_style_and_select_the_template')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_choose_the_default_style')?>"});
		<?php echo $formValidator;?>
	})
	function checkall(){
		<?php echo $checkall;?>
	}
//-->
</script>
<style type="text/css">
label {font-weight: 400;}
.radio-label{border-top:1px solid #e4e2e2; border-left:1px solid #e4e2e2;}
.radio-label td{border-right:1px solid #e4e2e2; border-bottom:1px solid #e4e2e2;background:#f6f9fd;}
.input-text{height: 34px;}
</style>
<div class="pad-10">
<form action="?m=admin&c=site&a=edit&siteid=<?php echo $siteid?>" method="post" id="myform" onsubmit="return checkall()">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="100"><?php echo L('site_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[name]" id="name" size="70" value="<?php echo $data['name']?>" /></td>
  </tr>
  <tr>
    <th><?php echo L('site_dirname')?>：</th>
    <td class="y-bg"><?php if ($siteid == 1) { echo $data['dirname'];} else {?><input type="text" class="input-text" name="info[dirname]" id="dirname" size="70" value="<?php echo $data['dirname']?>" /><?php }?></td>
  </tr>
  <tr>
    <th><?php echo L('site_domain')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[domain]" id="domain" size="70" value="<?php echo $data['domain']?>" /><button type="button" onclick="dr_test_domain('domain','site');" class="button"> <i class="fa fa-send"></i> 测试</button><div id="dr_site_domian_error" style="color: red;display: none"></div></td>
  </tr>
  <tr>
    <th><?php echo L('html_home')?>：</th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[ishtml]" value="1"<?php if($data['ishtml']) echo ' checked';?>> <?php echo L('yes');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[ishtml]" value="0"<?php if(!$data['ishtml']) echo ' checked';?>> <?php echo L('no');?> <span></span></label>
      </div>
    </td>
  </tr>
</table>
</fieldset>
<div class="bk10"></div>
<fieldset>
	<legend><?php echo L('mobile_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th><?php echo L('mobile_auto')?>：</th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobileauto]" value="1"<?php if($data['mobileauto']) echo ' checked';?>> <?php echo L('yes');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobileauto]" value="0"<?php if(!$data['mobileauto']) echo ' checked';?>> <?php echo L('no');?> <span></span></label><br><?php echo L('mobile_auto_desc')?>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php echo L('html_mobile')?>：</th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobilehtml]" value="1"<?php if($data['mobilehtml']) echo ' checked';?>> <?php echo L('yes');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobilehtml]" value="0"<?php if(!$data['mobilehtml']) echo ' checked';?>> <?php echo L('no');?> <span></span></label><br><?php echo L('html_mobile_desc')?>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php echo L('mobile_not_pad')?>：</th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[not_pad]" value="1"<?php if($data['not_pad']) echo ' checked';?>> <?php echo L('yes');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[not_pad]" value="0"<?php if(!$data['not_pad']) echo ' checked';?>> <?php echo L('no');?> <span></span></label><br><?php echo L('mobile_not_pad_desc')?>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php echo L('mobile_domain')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[mobile_domain]" id="mobile_domain" size="70" value="<?php echo $data['mobile_domain']?>" /><button type="button" onclick="dr_test_domain('mobile_domain','mobile');" class="button"> <i class="fa fa-send"></i> 测试</button><div id="dr_mobile_domian_error" style="color: red;display: none"></div></td>
  </tr>
  <tr>
    <th><?php echo L('mobile_template')?>：</th>
    <td class="y-bg"><?php echo L('mobile_template_style')?></td>
  </tr>
</table>
</fieldset>
<div class="bk10"></div>
<fieldset>
	<legend><?php echo L('seo_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="80"><?php echo L('site_title')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[site_title]" id="site_title" size="80" value="<?php echo $data['site_title']?>" /></td>
  </tr>
  <tr>
    <th><?php echo L('keyword_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[keywords]" id="keywords" size="80" value="<?php echo $data['keywords']?>" /></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[description]" id="description" size="80" value="<?php echo $data['description']?>" /></td>
  </tr>
</table>
</fieldset>
<div class="bk10"></div>
<fieldset>
	<legend><?php echo L('field_manage')?></legend>
	<table width="100%"  class="table_form">
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
      <th width="80"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?>
	  </th>
      <td class="y-bg"><?php echo $info['form']?>  <?php echo $info['tips']?></td>
    </tr>
<?php
} }
?>
</table>
</fieldset>
<div class="bk10"></div>
<fieldset>
	<legend><?php echo L('release_point_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="80" valign="top"><?php echo L('release_point')?>：</th>
    <td> <select name="info[release_point][]" size="3" id="release_point" multiple title="<?php echo L('ctrl_more_selected')?>">
    <option value='' <?php if(!$data['release_point']) echo 'selected';?>><?php echo L('not_use_the_publishers_some')?></option>
		  <?php if(is_array($release_point_list) && !empty($release_point_list)): foreach($release_point_list as $v):?>
		  <option value="<?php echo $v['id']?>"<?php if(in_array($v['id'], explode(',',$data['release_point']))){echo ' selected';}?>><?php echo $v['name']?></option>
	<?php endforeach;endif;?>
		</select></td>
  </tr>
</table>
</fieldset>
<div class="bk10"></div>
<fieldset>
	<legend><?php echo L('template_style_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="80" valign="top"><?php echo L('style_name')?>：</th>
    <td class="y-bg"> <select name="template[]" size="3" id="template" multiple title="<?php echo L('ctrl_more_selected')?>" onchange="default_list()">
    
    	<?php 
	    	$default_template_list =  array();
	    	if (isset($data['template'])) {
	    		$dirname = explode(',',$data['template']);
	    	} else {
	    		$dirname = array();
	    	}
	    	if(is_array($template_list)):
    		foreach ($template_list as $key=>$val):
    		$default_template_list[$val['dirname']] = $val['name'];
    	?>
		  <option value="<?php echo $val['dirname']?>" <?php if(in_array($val['dirname'], $dirname)){echo 'selected';}?>><?php echo $val['name']?></option>
		  <?php endforeach;endif;?>
		</select></td>
  </tr>
  <tr>
    <th width="80" valign="top"><?php echo L('default_style')?>：<input type="hidden" name="info[default_style]" id="default_style_input" value="<?php echo $data['default_style']?>"></th>
    <td class="y-bg"><span id="default_style">
	<?php 
		if(is_array($dirname) && !empty($dirname)) foreach ($dirname as $v) {
			echo '<div class="mt-radio-inline"><label class="mt-radio mt-radio-outline"><input type="radio" name="default_style_radio" value="'.$v.'" onclick="$(\'#default_style_input\').val(this.value);" '.($data['default_style']==$v ? 'checked' : '').'> '.$default_template_list[$v].' <span></span></label></div>';
		}
	?>
	</span><span id="default_style_msg"></span></td>
  </tr>
</table>
<script type="text/javascript">
function default_list() {
	var html = '';
	var old = $('#default_style_input').val();
	var checked = '';
	$('#template option:selected').each(function(i,n){
		if (old == $(n).val()) {
			checked = 'checked';
		}
		 html += '<div class="mt-radio-inline"><label class="mt-radio mt-radio-outline"><input type="radio" name="default_style_radio" value="'+$(n).val()+'" onclick="$(\'#default_style_input\').val(this.value);" '+checked+'> '+$(n).text()+' <span></span></label></div>';
	});
	if(!checked)  $('#default_style_input').val('0');
	$('#default_style').html(html);
}
</script>
</fieldset>
<div class="bk10"></div>
<fieldset>
	<legend><?php echo L('site_att_config')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="130" valign="top"><?php echo L('site_att_upload_maxsize')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[upload_maxsize]" id="upload_maxsize" size="10" value="<?php echo $setting['upload_maxsize'] ? $setting['upload_maxsize'] : '2048' ?>"/> KB </td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('site_att_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[upload_allowext]" id="upload_allowext" size="80" value="<?php echo $setting['upload_allowext']?>"/></td>
  </tr>
  <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>>
    <td valign="top" colspan="2"><fieldset>
	<legend><?php echo L('att_ueditor')?></legend>
	<table width="100%" class="radio-label">
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_filename')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[filename]" id="filename" size="50" value="<?php echo $setting['filename'] ? $setting['filename'] : '{yyyy}/{mm}{dd}/{time}{rand:6}' ?>"/><br><?php echo L('ueditor_filename_desc')?></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_image_max_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[imageMaxSize]" id="imageMaxSize" size="10" value="<?php echo $setting['imageMaxSize'] ? $setting['imageMaxSize'] : '2048' ?>"/> KB </td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_image_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[imageAllowFiles]" id="imageAllowFiles" size="80" value="<?php echo $setting['imageAllowFiles'] ? $setting['imageAllowFiles'] : 'png|jpg|jpeg|gif|bmp'?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_catcher_max_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[catcherMaxSize]" id="catcherMaxSize" size="10" value="<?php echo $setting['catcherMaxSize'] ? $setting['catcherMaxSize'] : '2048' ?>"/> KB </td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_catcher_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[catcherAllowFiles]" id="catcherAllowFiles" size="80" value="<?php echo $setting['catcherAllowFiles'] ? $setting['catcherAllowFiles'] : 'png|jpg|jpeg|gif|bmp'?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_video_max_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[videoMaxSize]" id="videoMaxSize" size="10" value="<?php echo $setting['videoMaxSize'] ? $setting['videoMaxSize'] : '102400' ?>"/> KB </td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_video_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[videoAllowFiles]" id="videoAllowFiles" size="80" value="<?php echo $setting['videoAllowFiles'] ? $setting['videoAllowFiles'] : 'flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid'?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_file_max_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[fileMaxSize]" id="fileMaxSize" size="10" value="<?php echo $setting['fileMaxSize'] ? $setting['fileMaxSize'] : '51200' ?>"/> KB </td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_file_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[fileAllowFiles]" id="fileAllowFiles" size="80" value="<?php echo $setting['fileAllowFiles'] ? $setting['fileAllowFiles'] : 'png|jpg|jpeg|gif|bmp|flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid|rar|zip|tar|gz|7z|bz2|cab|iso|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|md|xml'?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_imagemanager_max_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[imageManagerListSize]" id="imageManagerListSize" size="10" value="<?php echo $setting['imageManagerListSize'] ? $setting['imageManagerListSize'] : '20' ?>"/></td>
  </tr>
  <tr style="display: none;">
    <th width="130" valign="top"><?php echo L('ueditor_imagemanager_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[imageManagerAllowFiles]" id="imageManagerAllowFiles" size="80" value="<?php echo $setting['imageManagerAllowFiles'] ? $setting['imageManagerAllowFiles'] : 'png|jpg|jpeg|gif|bmp' ?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_filemanager_max_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[fileManagerListSize]" id="fileManagerListSize" size="10" value="<?php echo $setting['fileManagerListSize'] ? $setting['fileManagerListSize'] : '20' ?>"/></td>
  </tr>
  <tr style="display: none;">
    <th width="130" valign="top"><?php echo L('ueditor_filemanager_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[fileManagerAllowFiles]" id="fileManagerAllowFiles" size="80" value="<?php echo $setting['fileManagerAllowFiles'] ? $setting['fileManagerAllowFiles'] : 'png|jpg|jpeg|gif|bmp|flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid|rar|zip|tar|gz|7z|bz2|cab|iso|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|md|xml' ?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('ueditor_videomanager_max_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[videoManagerListSize]" id="videoManagerListSize" size="10" value="<?php echo $setting['videoManagerListSize'] ? $setting['videoManagerListSize'] : '20' ?>"/></td>
  </tr>
  <tr style="display: none;">
    <th width="130" valign="top"><?php echo L('ueditor_videomanager_allow_ext')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[videoManagerAllowFiles]" id="videoManagerAllowFiles" size="80" value="<?php echo $setting['videoManagerAllowFiles'] ? $setting['videoManagerAllowFiles'] : 'flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid' ?>"/></td>
  </tr>
</table>
</fieldset></td>
  </tr>
  <tr>
    <th><?php echo L('site_att_gb_check')?></th>
    <td class="y-bg"><?php echo $this->check_gd()?></td>
  </tr>
  <tr>
    <th><?php echo L('site_att_watermark_enable')?></th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark_enable]" value="1"<?php echo $setting['watermark_enable']==1 ? ' checked="checked"' : ''?>> <?php echo L('site_att_watermark_open');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark_enable]" value="0"<?php echo $setting['watermark_enable']==0 ? ' checked="checked"' : ''?>> <?php echo L('site_att_watermark_close');?> <span></span></label>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php echo L('site_att_watermark_type')?></th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[type]" value="0" onclick="dr_type(0)"<?php echo $setting['type']==0 ? ' checked="checked"' : ''?>> <?php echo L('site_att_photo');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[type]" value="1" onclick="dr_type(1)"<?php echo $setting['type']==1 ? ' checked="checked"' : ''?>> <?php echo L('site_att_text');?> <span></span></label>
      </div>
    </td>
  </tr>
  <tr class="dr_sy dr_sy_1">
    <th><?php echo L('site_att_text_font')?></th>
    <td class="y-bg">
	  <?php if ($waterfont) {?>
		<select style="height: 34px;background-color: rgb(255, 255, 255);box-shadow: rgba(0, 0, 0, 0.075) 0px 1px 1px inset;padding: 6px 12px;border-width: 1px;border-style: solid;border-color: rgb(194, 202, 216);border-image: initial;border-radius: 4px;transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;" name="setting[wm_font_path]" id="wm_font_path">
			<?php foreach($waterfont as $t) {?>
			<option<?php if ($t==$setting['wm_font_path']) {?> selected=""<?php }?> value="<?php echo $t;?>"><?php echo $t;?></option>
			<?php }?>
		</select>
	  <?php }?><button type="button" class="layui-btn" id="fileupload-font"><i class="layui-icon">&#xe67c;</i><?php echo L('upload');?></button><br><?php echo L('site_att_text_font_desc')?>
     </td>
  </tr>
  <tr class="dr_sy dr_sy_1">
    <th><?php echo L('site_att_watermark_text')?></th>
    <td class="y-bg">
	  <input type="text" class="input-text" name="setting[wm_text]" id="wm_text" size="10" value="<?php echo $setting['wm_text'] ? $setting['wm_text'] : 'cms' ?>" /><br><?php echo L('site_att_text_desc')?>
     </td>
  </tr>
  <tr class="dr_sy dr_sy_1">
    <th><?php echo L('site_att_text_size')?></th>
    <td class="y-bg">
	  <input type="text" class="input-text" name="setting[wm_font_size]" id="wm_font_size" size="10" value="<?php echo intval($setting['wm_font_size'])?>" /><br><?php echo L('site_att_text_size_desc')?>
     </td>
  </tr>
  <tr class="dr_sy dr_sy_1">
    <th><?php echo L('site_att_text_color')?></th>
    <td class="y-bg">
	  <input type="text" class="input-text" name="setting[wm_font_color]" id="wm_font_color" size="10" value="<?php echo $setting['wm_font_color']?>" />
     </td>
  </tr>
  <tr class="dr_sy dr_sy_0">
    <th><?php echo L('site_att_watermark_img')?></th>
    <td class="y-bg">
	  <?php if ($waterfile) {?>
		<select style="height: 34px;background-color: rgb(255, 255, 255);box-shadow: rgba(0, 0, 0, 0.075) 0px 1px 1px inset;padding: 6px 12px;border-width: 1px;border-style: solid;border-color: rgb(194, 202, 216);border-image: initial;border-radius: 4px;transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;" name="setting[wm_overlay_path]" id="wm_overlay_path">
			<?php foreach($waterfile as $t) {?>
			<option<?php if ($t==$setting['wm_overlay_path']) {?> selected=""<?php }?> value="<?php echo $t;?>"><?php echo $t;?></option>
			<?php }?>
		</select>
	  <?php }?><button type="button" class="layui-btn" id="fileupload-img"><i class="layui-icon">&#xe67c;</i><?php echo L('upload');?></button><br><?php echo L('site_att_watermark_img_desc')?>
     </td>
  </tr>
   <tr>
    <th width="130" valign="top"><?php echo L('site_att_watermark_pct')?></th>
    <td class="y-bg"><input type="hidden" class="input-text" name="setting[wm_opacity]" id="wm_opacity" size="10" value="<?php echo $setting['wm_opacity'] ? intval($setting['wm_opacity']) : '100' ?>" /><div id="demo6_slider1" class="noUi-danger"></div><span id="demo6_slider1-span"></span> <?php echo L('site_att_watermark_pct_desc')?></td>
  </tr> 
   <tr>
    <th width="130" valign="top"><?php echo L('site_att_watermark_quality')?></th>
    <td class="y-bg"><input type="hidden" class="input-text" name="setting[quality]" id="quality" size="10" value="<?php echo $setting['quality'] ? intval($setting['quality']) : '80' ?>" /><div id="demo6_slider2" class="noUi-success"></div><span id="demo6_slider2-span"></span> <?php echo L('site_att_watermark_quality_desc')?></td>
  </tr>
  <tr>
    <th><?php echo L('site_att_watermark_padding')?></th>
    <td class="y-bg">
	  <input type="text" class="input-text" name="setting[wm_padding]" id="wm_padding" size="10" value="<?php echo intval($setting['wm_padding'])?>" placeholder="px" /><br><?php echo L('site_att_watermark_padding_desc')?>
     </td>
  </tr>
  <tr>
    <th><?php echo L('site_att_watermark_offset')?></th>
    <td class="y-bg">
	  <?php echo L('site_att_watermark_hor_offset')?>
<input type="text" class="input-text" name="setting[wm_hor_offset]" id="wm_hor_offset" size="10" value="<?php echo intval($setting['wm_hor_offset'])?>" placeholder="px" /> PX <?php echo L('site_att_watermark_vrt_offset')?><input type="text" class="input-text" name="setting[wm_vrt_offset]" id="wm_vrt_offset" size="10" value="<?php echo intval($setting['wm_vrt_offset'])?>" placeholder="px" /> PX
     </td>
  </tr>
  <tr>
    <th><?php echo L('site_att_watermark_photo')?></th>
    <td class="y-bg"><?php echo L('site_att_watermark_minwidth')?>
<input type="text" class="input-text" name="setting[width]" id="width" size="10" value="<?php echo intval($setting['width'])?>" placeholder="px" /> X <?php echo L('site_att_watermark_minheight')?><input type="text" class="input-text" name="setting[height]" id="height" size="10" value="<?php echo intval($setting['height'])?>" placeholder="px" /> PX<br><?php echo L('site_att_watermark_photo_desc')?>
     </td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('site_att_watermark_pos')?></th>
    <td>
      <div class="btn-group fc-3x3" data-toggle="buttons">
        <?php foreach ($locate as $i=>$t) {?>
        <label class="btn btn-default<?php if ($setting['locate'] == $i) {?> active<?php }?><?php if (strpos($i, 'bottom')!==false) {?> btn2<?php }?>"><input type="radio" name="setting[locate]" value="<?php echo $i?>"<?php if ($setting['locate'] == $i) {?> checked<?php }?> class="toggle"><?php echo L($t)?></label>
        <?php }?>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php echo L('site_att_ueditor')?></th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ueditor]" value="0"<?php echo $setting['ueditor']==0 ? ' checked="checked"' : ''?>> <?php echo L('site_att_watermark_ueditor');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ueditor]" value="1"<?php echo $setting['ueditor']==1 ? ' checked="checked"' : ''?>> <?php echo L('site_att_watermark_all');?> <span></span></label><br><?php echo L('site_att_ueditor_desc')?>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php echo L('缩略图水印')?></th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[thumb]" value="0"<?php echo $setting['thumb']==0 ? ' checked="checked"' : ''?>> <?php echo L('按调用参数');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[thumb]" value="1"<?php echo $setting['thumb']==1 ? ' checked="checked"' : ''?>> <?php echo L('site_att_watermark_all');?> <span></span></label><br><?php echo L('是否对缩略图函数thumb的图片进行强制水印')?>
      </div>
    </td>
  </tr>
   <tr>
    <th width="130" valign="top"></th>
    <td class="y-bg"><button type="button" onclick="dr_preview()" class="layui-btn layui-btn-danger layui-btn-sm"> <i class="fa fa-photo"></i> <?php echo L('site_att_watermark_review');?></button></td>
  </tr> 
</table>
</fieldset>
<div class="bk15"></div>
    <input type="submit" class="dialog" id="dosubmit" name="dosubmit" value="<?php echo L('submit')?>" />
</div>
</form>
</div>
<link href="<?php echo JS_PATH?>nouislider/nouislider.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH?>nouislider/nouislider.pips.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>nouislider/wNumb.min.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH?>nouislider/nouislider.min.js" type="text/javascript"></script>
<link href="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
<script type="text/javascript">
function dr_type(v) {
    $('.dr_sy').hide();
    $('.dr_sy_'+v).show();
}
function dr_preview() {
	var linkurl = '?m=admin&c=site&a=public_preview&setting[type]='+$('input[name="setting[type]"]:checked').val()+'&setting[wm_font_path]='+$('#wm_font_path').val()+'&setting[wm_text]='+$('#wm_text').val()+'&setting[wm_font_size]='+$('#wm_font_size').val()+'&setting[wm_font_color]='+$('#wm_font_color').val()+'&setting[wm_overlay_path]='+$('#wm_overlay_path').val()+'&setting[wm_opacity]='+$('#wm_opacity').val()+'&setting[quality]='+$('#quality').val()+'&setting[wm_padding]='+$('#wm_padding').val()+'&setting[wm_hor_offset]='+$('#wm_hor_offset').val()+'&setting[wm_vrt_offset]='+$('#wm_vrt_offset').val()+'&setting[width]='+$('#width').val()+'&setting[height]='+$('#height').val()+'&setting[locate]='+$('input[name="setting[locate]"]:checked').val();
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	var diag = new Dialog({
		id:'preview',
		title:'水印预览',
		html:'<div style="text-align:center"><img style="-webkit-user-select: none;" src="'+linkurl+'"></div>',
		width:'50%',
		height:'60%',
		modal:true,
		draggable:true
	});
	diag.onOk = function(){
		diag.close();
	};
	diag.show();
}
function dr_test_domain(id,name) {
        $('#dr_'+name+'_domian_error').html('正在测试中...');
        $('#dr_'+name+'_domian_error').show();
        $.ajax({type: 'GET',dataType:'json', url: '?m=admin&c=site&a=public_test_'+name+'_domain&siteid=<?php echo $data['siteid']?>&v='+encodeURIComponent($('#'+id).val()),
            success: function(json) {
                if (json.code) {
                    dr_tips(json.code, json.msg);
                    $('#dr_'+name+'_domian_error').hide();
                } else {
                    $('#dr_'+name+'_domian_error').html(json.msg);
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError)
            }
        });
    }
$(function(){
    $("#wm_font_color").minicolors({
        control: $(this).attr('data-control') || 'hue',
        defaultValue: $(this).attr('data-defaultValue') || '',
        inline: $(this).attr('data-inline') === 'true',
        letterCase: $(this).attr('data-letterCase') || 'lowercase',
        opacity: $(this).attr('data-opacity'),
        position: $(this).attr('data-position') || 'bottom left',
        change: function(hex, opacity) {
            if (!hex) return;
            if (opacity) hex += ', ' + opacity;
            if (typeof console === 'object') {
                console.log(hex);
            }
        },
        theme: 'bootstrap'
    });
    dr_type(<?php echo $setting['type']?>);
    // Store the locked state and slider values.
    var lockedState = false,
    lockedSlider = false,
    lockedValues = [60, 80],
    slider1 = document.getElementById('demo6_slider1'),
    slider2 = document.getElementById('demo6_slider2'),
    slider1Value = document.getElementById('demo6_slider1-span'),
    slider2Value = document.getElementById('demo6_slider2-span');

    // When the button is clicked, the locked
    // state is inverted.
    function crossUpdate ( value, slider ) {

        // If the sliders aren't interlocked, don't
        // cross-update.
        if ( !lockedState ) return;

        // Select whether to increase or decrease
        // the other slider value.
        var a = slider1 === slider ? 0 : 1, b = a ? 0 : 1;

        // Offset the slider value.
        value -= lockedValues[b] - lockedValues[a];

        // Set the value
        slider.noUiSlider.set();
    }

    noUiSlider.create(slider1, {
        start: <?php echo $setting['wm_opacity'] ? $setting['wm_opacity'] : '100' ?>,

        // Disable animation on value-setting,
        // so the sliders respond immediately.
        animate: false,
        range: {
            min: 1,
            max: 100
        }
    });

    noUiSlider.create(slider2, {
        start: <?php echo $setting['quality'] ? $setting['quality'] : '80' ?>,
        animate: false,
        range: {
            min: 1,
            max: 100
        }
    });

    slider1.noUiSlider.on('update', function( values, handle ){
        slider1Value.innerHTML = parseInt(values[handle]);
        $('#wm_opacity').val(parseInt(values[handle]));
    });

    slider2.noUiSlider.on('update', function( values, handle ){
        slider2Value.innerHTML = parseInt(values[handle]);
        $('#quality').val(parseInt(values[handle]));
    });

    function setLockedValues ( ) {
        lockedValues = [
            Number(slider1.noUiSlider.get()),
            Number(slider2.noUiSlider.get())
        ];
    }

    slider1.noUiSlider.on('change', setLockedValues);
    slider2.noUiSlider.on('change', setLockedValues);

    slider1.noUiSlider.on('slide', function( values, handle ){
        crossUpdate(values[handle], slider2);
    });

    slider2.noUiSlider.on('slide', function( values, handle ){
        crossUpdate(values[handle], slider1);
    });
	layui.use('upload', function () {
		var upload = layui.upload;
		upload.render({
			elem:'#fileupload-font',
			accept:'file',
			field:'file_data',
			url: '?m=admin&c=site&a=public_upload_index&at=font&pc_hash='+pc_hash,
			exts: 'ttf',
			done: function(data){
				dr_tips(data.code, data.msg);
				if(data.code == 1){
					setTimeout("location.reload(true)", 2000);
				}
			}
		});
		upload.render({
			elem:'#fileupload-img',
			accept:'file',
			field:'file_data',
			url: '?m=admin&c=site&a=public_upload_index&at=img&pc_hash='+pc_hash,
			exts: 'png',
			done: function(data){
				dr_tips(data.code, data.msg);
				if(data.code == 1){
					setTimeout("location.reload(true)", 2000);
				}
			}
		});
	});
});
</script>
</body>
</html>