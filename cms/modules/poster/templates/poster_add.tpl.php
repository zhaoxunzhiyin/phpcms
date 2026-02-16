<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
//$show_header = $show_validator = $show_scroll = true;
$show_dialog = true; 
include $this->admin_tpl('header', 'admin');
$thisExt = isset($this->setting['ext'])?$this->setting['ext']:'';
$authkey = upload_key('1,'.$thisExt.',0,1,,,,,,0');
$p = dr_authcode(array(
    'file_upload_limit' => 1,
    'file_types_post' => $thisExt,
    'size' => 0,
    'allowupload' => 1,
    'thumb_width' => '',
    'thumb_height' => '',
    'watermark_enable' => '',
    'attachment' => '',
    'image_reduce' => '',
    'chunk' => 0,
), 'ENCODE');
?>
<?php echo load_js(JS_PATH.'h5upload/h5editor.js');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form method="post" action="?m=poster&c=poster&a=add" id="myform">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<table class="table_form" width="100%" cellspacing="0">
<tbody>
    <tr>
        <th width="120"><?php echo L('poster_title')?>：</th>
        <td><input name="poster[name]" id="name" class="input-text" type="text" size="25"></td>
    </tr>
    <tr>
        <th><?php echo L('for_postion')?>：</th>
        <td><?php if($spaceid) { echo '<b style="color:#F60;">',$sinfo['name'],'</b>&nbsp;[',$TYPES[$sinfo['type']],']<input type="hidden" name="poster[spaceid]" value="'.$spaceid.'">'; } else {?><select name="poster[spaceid]" id="spaceid" onchange="AdsType(this.value)">
        <option value=''><?php echo L('please_select')?></option>
        <?php foreach($spaces as $s) {?>
            <option value="<?php echo $s['spaceid']?>"><?php echo $s['name']?></option>
        <?php } ?></select><?php }?></td>
    </tr>
    <tr>
        <th align="right"  valign="top"><?php echo L('poster_type')?>：</th>
        <td valign="top" colspan="2"><?php echo form::select($setting['type'], '', 'name="poster[type]" id="type" onchange="AdsType(this.value)"', $default);?>
        </td>
    </tr>
    <tr>
        <th><?php echo L('line_time')?>：</th>
        <td><?php echo form::date('poster[startdate]', date('Y-m-d H:i:s', SYS_TIME), 1)?></td>
    </tr>
    <tr>
        <th><?php echo L('down_time')?>：</th>
        <td><?php echo form::date('poster[enddate]', '', 1)?></td>
    </tr>
    </tbody>
    </table><?php if(array_key_exists('images', $setting['type'])) {?><div class="pad-10" id="imagesdiv" style="display:">
    <fieldset>
    <legend><?php echo L('photo_setting')?></legend>
    <?php if($setting['num']>1) { for($i=1; $i<=$setting['num']; $i++) {?>
    <table width="100%"  class="table_form">
    <tbody>
  <tr>
    <th width="80"><?php echo L('linkurl')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[images][<?php echo $i;?>][linkurl]" id="linkurl<?php echo $i;?>" size="30" value="http://" /></td>
    <td rowspan="2"><a href="javascript:h5upload('<?php echo SELF;?>', 'imgurl<?php echo $i;?>_images', '<?php echo L('upload_photo')?>','imgurl<?php echo $i;?>','preview','<?php echo $p?>','poster', '', '<?php echo $authkey?>',<?php echo SYS_EDITOR;?>);void(0);"><img src="<?php echo IMG_PATH;?>icon/upload-pic.png" id="imgurl<?php echo $i;?>_s" width="105" height="88"></a><input type="hidden" id="imgurl<?php echo $i;?>" name="setting[images][<?php echo $i;?>][imageurl]"></td>
  </tr>
  <tr>
    <th><?php echo L('alt')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[images][<?php echo $i;?>][alt]" id="alt<?php echo $i;?>" size="30" /></td>
  </tr>
</table>
<?php } } else {?>
<table width="100%"  class="table_form">
    <tbody>
  <tr>
    <th width="80"><?php echo L('linkurl')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[images][1][linkurl]" id="linkurl3" size="30" value="http://" /></td>
    <td rowspan="2"><a href="javascript:h5upload('<?php echo SELF;?>', 'imgurl_images', '<?php echo L('upload_photo')?>','imgurl','preview','<?php echo $p?>','poster', '', '<?php echo $authkey?>',<?php echo SYS_EDITOR;?>);void(0);"><img src="<?php echo IMG_PATH;?>icon/upload-pic.png" id="imgurl_s" width="105" height="88"></a><input type="hidden" id="imgurl" name="setting[images][1][imageurl]"></td>
  </tr>
  <tr>
    <th><?php echo L('alt')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[images][1][alt]" id="alt3" size="30" /></td>
  </tr>
  </tbody>
</table>
<?php } ?>
</fieldset></div><?php } if(array_key_exists('flash', $setting['type'])) {?>
<div class="pad-10" id="flashdiv" style="display:none;">
    <fieldset>
    <legend><?php echo L('flash_setting')?></legend>
    <?php if($setting['num']>1) { for($i=1; $i<=$setting['num']; $i++) {?>
    <table width="100%"  class="table_form">
    <tbody>
  <tr>
    <th width="80"><?php echo L('flash_url')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[flash][<?php echo $i;?>][flashurl]" id="flashurl<?php echo $i;?>" size="40" /></td>
    <td class="y-bg"><input type="button" class="button" onclick="javascript:h5upload('<?php echo SELF;?>', 'flashurl<?php echo $i;?>_images', '<?php echo L('flash_upload')?>','flashurl<?php echo $i;?>','submit_files','<?php echo $p?>','poster', '', '<?php echo $authkey?>',<?php echo SYS_EDITOR;?>)" value="<?php echo L('flash_upload')?>"></td>
  </tr>
  </tbody>
</table>
<?php } } else {?>
<table width="100%"  class="table_form">
    <tbody>
  <tr>
    <th width="80"><?php echo L('flash_url')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[flash][1][flashurl]" id="flashurl" size="40"  /></td>
    <td class="y-bg"><input type="button" class="button" onclick="javascript:h5upload('<?php echo SELF;?>', 'flashurl_images', '<?php echo L('flash_upload')?>','flashurl','submit_files','<?php echo $p?>','poster', '', '<?php echo $authkey?>',<?php echo SYS_EDITOR;?>)" value="<?php echo L('flash_upload')?>"></td>
  </tr>
  </tbody>
</table>
<?php } ?>
</fieldset></div><?php } if(array_key_exists('text', $setting['type'])) {?><div class="pad-10" id="textdiv" style="display:">
    <fieldset>
    <legend><?php if ($sinfo['type']=='code') { echo L('code_setting'); } else { echo L('word_link'); } ?></legend>
    <table width="100%"  class="table_form">
    <tbody>
    <?php if($sinfo['type']=='code') {?>
  <tr>
    <th width="80"><?php echo L('code_content')?>：</th>
    <td class="y-bg"><textarea name="setting[text][code]" id="code" cols="55" rows="6"><?php echo $info['setting']['code']?></textarea></td>
  </tr>
  <?php } else {?>
  <tr>
    <th width="80"><?php echo L('word_content')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[text][1][title]" value="<?php echo $info['setting'][1]['title']?>" id="title" size="30" /></td>
  </tr>
  <tr>
    <th><?php echo L('linkurl')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[text][1][linkurl]" id="link" size="30" value="<?php echo $info['setting'][1]['linkurl']?>"  /></td>
  </tr><?php }?>
  </tbody>
</table>
</fieldset></div><?php }?>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
                <button type="button" onclick="history.go(-1)" class="btn yellow"> <i class="fa fa-mail-reply-all"></i> <?php echo L('goback')?></button>
            </div>
        </div>
</form>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function AdsType(type) {
    $('#imagesdiv').css('display', 'none');
    $('#flashdiv').css('display', 'none');
    $('#'+type+'div').css('display', '');
}
</script>