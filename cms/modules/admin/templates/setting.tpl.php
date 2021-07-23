<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
	$(function(){
		SwapTab('setting','on','',5,<?php echo $this->input->get('tab') ? $this->input->get('tab') : '1'?>);
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#js_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_js_path')?>",onfocus:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_js_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"});
		$("#css_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_css_path')?>",onfocus:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_css_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"});

		$("#img_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_img_path')?>",onfocus:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_img_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"});
		$("#mobile_js_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_mobile_js_path')?>",onfocus:"<?php echo L('setting_mobile_js_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_mobile_js_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_mobile_js_path').L('setting_end_with_x')?>"});
		$("#mobile_css_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_mobile_css_path')?>",onfocus:"<?php echo L('setting_mobile_css_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_mobile_css_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_mobile_css_path').L('setting_end_with_x')?>"});

		$("#mobile_img_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_mobile_img_path')?>",onfocus:"<?php echo L('setting_mobile_img_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_mobile_img_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_mobile_img_path').L('setting_end_with_x')?>"});

		$("#errorlog_size").formValidator({onshow:"<?php echo L('setting_errorlog_hint')?>",onfocus:"<?php echo L('setting_input').L('setting_error_log_size')?>"}).inputValidator({onerror:"<?php echo L('setting_error_log_size').L('setting_input_error')?>"}).regexValidator({regexp:"num",datatype:"enum",onerror:"<?php echo L('setting_errorlog_type')?>"});	
	})
//-->
</script>
<form action="?m=admin&c=setting&a=save" method="post" id="myform">
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
            <li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',5,1);"><?php echo L('setting_basic_cfg')?></li>
            <li id="tab_setting_2" onclick="SwapTab('setting','on','',5,2);"><?php echo L('setting_safe_cfg')?></li>
            <li id="tab_setting_3" onclick="SwapTab('setting','on','',5,3);"><?php echo L('setting_mail_cfg')?></li>
			<li id="tab_setting_4" onclick="SwapTab('setting','on','',5,4);"><?php echo L('setting_connect')?></li>
			<li id="tab_setting_5" onclick="SwapTab('setting','on','',5,5);"><?php echo L('setting_keyword_enable')?></li>
</ul>
<div id="div_setting_1" class="contentList pad-10">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_admin_email')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[admin_email]" id="admin_email" size="30" value="<?php echo $admin_email?>"/></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_category_ajax')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[category_ajax]" id="category_ajax" size="5" value="<?php echo $category_ajax?>"/>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo L('setting_category_ajax_desc')?></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_gzip')?></th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[gzip]" value="1"<?php echo ($gzip=='1') ? ' checked' : ''?>> <?php echo L('setting_yes');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[gzip]" value="0"<?php echo ($gzip=='0') ? ' checked' : ''?>> <?php echo L('setting_no');?> <span></span></label>
      </div>
    </td>
  </tr>
  <tr>
    <th width="120"><?php echo L('editormode')?></th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[editor]" value="0"<?php echo ($editor=='0') ? ' checked' : ''?>> <?php echo L('UEditor');?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[editor]" value="1"<?php echo ($editor=='1') ? ' checked' : ''?>> <?php echo L('CKEditor');?> <span></span></label>
      </div>
    </td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_js_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[js_path]" id="js_path" size="50" value="<?php echo JS_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_css_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[css_path]" id="css_path" size="50" value="<?php echo CSS_PATH?>"/></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_img_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[img_path]" id="img_path" size="50" value="<?php echo IMG_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_mobile_js_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[mobile_js_path]" id="mobile_js_path" size="50" value="<?php echo MOBILE_JS_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_mobile_css_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[mobile_css_path]" id="mobile_css_path" size="50" value="<?php echo MOBILE_CSS_PATH?>"/></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_mobile_img_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[mobile_img_path]" id="mobile_img_path" size="50" value="<?php echo MOBILE_IMG_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_timezone')?></th>
    <td class="y-bg"><select name="setconfig[timezone]">
        <option value=""> -- </option>
        <option value="-12"<?php echo ($timezone=='-12') ? ' selected' : ''?>>(GMT -12:00)</option>
        <option value="-11"<?php echo ($timezone=='-11') ? ' selected' : ''?>>(GMT -11:00)</option>
        <option value="-10"<?php echo ($timezone=='-10') ? ' selected' : ''?>>(GMT -10:00)</option>
        <option value="-9"<?php echo ($timezone=='-9') ? ' selected' : ''?>>(GMT -09:00)</option>
        <option value="-8"<?php echo ($timezone=='-8') ? ' selected' : ''?>>(GMT -08:00)</option>
        <option value="-7"<?php echo ($timezone=='-7') ? ' selected' : ''?>>(GMT -07:00)</option>
        <option value="-6"<?php echo ($timezone=='-6') ? ' selected' : ''?>>(GMT -06:00)</option>
        <option value="-5"<?php echo ($timezone=='-5') ? ' selected' : ''?>>(GMT -05:00)</option>
        <option value="-4"<?php echo ($timezone=='-4') ? ' selected' : ''?>>(GMT -04:00)</option>
        <option value="-3.5"<?php echo ($timezone=='-3.5') ? ' selected' : ''?>>(GMT -03:30)</option>
        <option value="-3"<?php echo ($timezone=='-3') ? ' selected' : ''?>>(GMT -03:00)</option>
        <option value="-2"<?php echo ($timezone=='-2') ? ' selected' : ''?>>(GMT -02:00)</option>
        <option value="-1"<?php echo ($timezone=='-1') ? ' selected' : ''?>>(GMT -01:00)</option>
        <option value="0"<?php echo ($timezone=='0') ? ' selected' : ''?>>(GMT)</option>
        <option value="1"<?php echo ($timezone=='1') ? ' selected' : ''?>>(GMT +01:00)</option>
        <option value="2"<?php echo ($timezone=='2') ? ' selected' : ''?>>(GMT +02:00)</option>
        <option value="3"<?php echo ($timezone=='3') ? ' selected' : ''?>>(GMT +03:00)</option>
        <option value="3.5"<?php echo ($timezone=='3.5') ? ' selected' : ''?>>(GMT +03:30)</option>
        <option value="4"<?php echo ($timezone=='4') ? ' selected' : ''?>>(GMT +04:00)</option>
        <option value="4.5"<?php echo ($timezone=='4.5') ? ' selected' : ''?>>(GMT +04:30)</option>
        <option value="5"<?php echo ($timezone=='5') ? ' selected' : ''?>>(GMT +05:00)</option>
        <option value="5.5"<?php echo ($timezone=='5.5') ? ' selected' : ''?>>(GMT +05:30)</option>
        <option value="5.75"<?php echo ($timezone=='6') ? ' selected' : ''?>>(GMT +05:45)</option>
        <option value="6"<?php echo ($timezone=='6.5') ? ' selected' : ''?>>(GMT +06:00)</option>
        <option value="6.5"<?php echo ($timezone=='7') ? ' selected' : ''?>>(GMT +06:30)</option>
        <option value="7"<?php echo ($timezone=='7.5') ? ' selected' : ''?>>(GMT +07:00)</option>
        <option value="8"<?php echo ($timezone=='' || $timezone=='8') ? ' selected' : ''?>>(GMT +08:00)</option>
        <option value="9"<?php echo ($timezone=='9') ? ' selected' : ''?>>(GMT +09:00)</option>
        <option value="9.5"<?php echo ($timezone=='9.5') ? ' selected' : ''?>>(GMT +09:30)</option>
        <option value="10"<?php echo ($timezone=='10') ? ' selected' : ''?>>(GMT +10:00)</option>
        <option value="11"<?php echo ($timezone=='11') ? ' selected' : ''?>>(GMT +11:00)</option>
        <option value="12"<?php echo ($timezone=='12') ? ' selected' : ''?>>(GMT +12:00)</option>
    </select></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_time')?></th>
    <td class="y-bg" id="site_time"><?php echo dr_date(SYS_TIME);?></td>
  </tr>
</table>
</div>
<div id="div_setting_2" class="contentList pad-10 hidden">
	<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('need_check_come_url')?></th>
    <td class="y-bg">
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setconfig[needcheckcomeurl]" value="1" type="radio" <?php echo ($needcheckcomeurl=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setconfig[needcheckcomeurl]" value="0" type="radio" <?php echo ($needcheckcomeurl=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?> <span></span></label>
        </div>
     </td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_admin_log')?></th>
    <td class="y-bg">
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setconfig[admin_log]" value="1" type="radio" <?php echo ($admin_log=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setconfig[admin_log]" value="0" type="radio" <?php echo ($admin_log=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?> <span></span></label>
        </div>
     </td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_error_log')?></th>
    <td class="y-bg">
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setconfig[errorlog]" value="1" type="radio" <?php echo ($errorlog=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setconfig[errorlog]" value="0" type="radio" <?php echo ($errorlog=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?> <span></span></label>
        </div>
     </td>
  </tr> 
  <tr>
    <th><?php echo L('setting_error_log_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[errorlog_size]" id="errorlog_size" size="5" value="<?php echo $errorlog_size?>"/> MB</td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_admin_code')?></th>
    <td class="y-bg">
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincode]" value="0" type="radio" <?php echo (!$sysadmincode) ? ' checked' : ''?>> <?php echo L('setting_yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincode]" value="1" type="radio" <?php echo ($sysadmincode) ? ' checked' : ''?>> <?php echo L('setting_no')?> <span></span></label>
        </div>
     </td>
  </tr> 
  <tr>
    <th><?php echo L('setting_maxloginfailedtimes')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[maxloginfailedtimes]" id="maxloginfailedtimes" size="10" value="<?php echo intval($maxloginfailedtimes)?>"/><?php echo L('setting_maxloginfailedtimes_desc')?></td>
  </tr>
  <tr>
    <th><?php echo L('setting_time_limit')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[sysadminlogintimes]" id="sysadminlogintimes" size="10" value="<?php echo intval($sysadminlogintimes)?>"/><?php echo L('setting_time_limit_desc')?></td>
  </tr>
  <tr>
    <th><?php echo L('setting_keys')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[auth_key]" id="auth_key" size="40" value="<?php echo $auth_key ? '************' : '';?>"/><button class="button" type="button" name="button" onclick="to_key()"> <?php echo L('setting_regenerate')?> </button><br><?php echo L('setting_keys_desc')?></td>
  </tr>
  <tr>
    <th><?php echo L('setting_minrefreshtime')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[minrefreshtime]" id="minrefreshtime" size="10" value="<?php echo $minrefreshtime?>"/> <?php echo L('miao')?></td>
  </tr>
</table>
</div>
<div id="div_setting_3" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('mail_type')?></th>
    <td class="y-bg">
     <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[mail_type]" checkbox="mail_type" value="1" onclick="showsmtp(this)" type="radio" <?php echo $mail_type==1 ? ' checked' : ''?>> <?php echo L('mail_type_smtp')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[mail_type]" checkbox="mail_type" value="0" onclick="showsmtp(this)" type="radio" <?php echo $mail_type==0 ? ' checked' : ''?> <?php if(substr(strtolower(PHP_OS), 0, 3) == 'win') echo 'disabled'; ?>/> <?php echo L('mail_type_mail')?> <span></span></label>
        </div>
	</td>
  </tr>
  <tbody id="smtpcfg" style="<?php if($mail_type == 0) echo 'display:none'?>">
  <tr>
    <th><?php echo L('mail_server')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_server]" id="mail_server" size="30" value="<?php echo $mail_server?>"/></td>
  </tr>  
  <tr>
    <th><?php echo L('mail_port')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_port]" id="mail_port" size="30" value="<?php echo $mail_port?>"/></td>
  </tr> 
  <tr>
    <th><?php echo L('mail_from')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_from]" id="mail_from" size="30" value="<?php echo $mail_from?>"/></td>
  </tr>   
  <tr>
    <th><?php echo L('mail_auth')?></th>
    <td class="y-bg">
    <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[mail_auth]" checkbox="mail_auth" value="1" type="radio" <?php echo $mail_auth==1 ? ' checked' : ''?>> <?php echo L('mail_auth_open')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[mail_auth]" checkbox="mail_auth" value="0" type="radio" <?php echo $mail_auth==0 ? ' checked' : ''?>> <?php echo L('mail_auth_close')?> <span></span></label>
        </div></td>
  </tr> 
	  <tr>
	    <th><?php echo L('mail_user')?></th>
	    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_user]" id="mail_user" size="30" value="<?php echo $mail_user?>"/></td>
	  </tr> 
	  <tr>
	    <th><?php echo L('mail_password')?></th>
	    <td class="y-bg"><input type="password" class="input-text" name="setting[mail_password]" id="mail_password" size="30" value="<?php echo $mail_password?>"/></td>
	  </tr>
 </tbody>
  <tr>
    <th><?php echo L('mail_test')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="mail_to" id="mail_to" size="30" value=""/> <input type="button" class="button" onClick="javascript:test_mail();" value="<?php echo L('mail_test_send')?>"></td>
  </tr>           
  </table>
</div>

<div id="div_setting_4" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_snda_enable')?></th>
    <td class="y-bg">
	 APP key <input type="text" class="input-text" name="setconfig[snda_akey]" id="snda_akey" size="20" value="<?php echo $snda_akey ?>"/>
	 APP secret key <input type="text" class="input-text" name="setconfig[snda_skey]" id="snda_skey" size="40" value="<?php echo $snda_skey ?>"/> <a href="http://code.snda.com/index/oauth" target="_blank"><?php echo L('click_register')?></a>
    </td>
  </tr>
  <tr>
    <th><?php echo L('setting_connect_sina')?></th>
    <td class="y-bg">
	App key <input type="text" class="input-text" name="setconfig[sina_akey]" id="sina_akey" size="20" value="<?php echo $sina_akey ?>"/>
	App secret key <input type="text" class="input-text" name="setconfig[sina_skey]" id="sina_skey" size="40" value="<?php echo $sina_skey ?>"/> <a href="http://open.t.sina.com.cn/wiki/index.php/<?php echo L('connect_micro')?>" target="_blank"><?php echo L('click_register')?></a>
	</td>
  </tr>
  <tr>
    <th><?php echo L('setting_connect_qq')?></th>
    <td class="y-bg">
	App key <input type="text" class="input-text" name="setconfig[qq_akey]" id="qq_akey" size="20" value="<?php echo $qq_akey ?>"/>
	App secret key <input type="text" class="input-text" name="setconfig[qq_skey]" id="qq_skey" size="40" value="<?php echo $qq_skey ?>"/> <a href="http://open.t.qq.com/" target="_blank"><?php echo L('click_register')?></a>
	</td>
  </tr> 
  <tr>
    <th><?php echo L('setting_connect_qqnew')?></th>
    <td class="y-bg">
	App I D  &nbsp;<input type="text" class="input-text" name="setconfig[qq_appid]" id="qq_appid" size="20" value="<?php echo $qq_appid;?>"/>
	App key <input type="text" class="input-text" name="setconfig[qq_appkey]" id="qq_appkey" size="40" value="<?php echo $qq_appkey;?>"/> 
	<?php echo L('setting_connect_qqcallback')?> <input type="text" class="input-text" name="setconfig[qq_callback]" id="qq_callback" size="40" value="<?php echo $qq_callback;?>"/>
	<a href="http://connect.qq.com" target="_blank"><?php echo L('click_register')?></a>
	</td>
  </tr> 
  </table>
</div>

<div id="div_setting_5" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_keyword')?></th>
    <td class="y-bg">
    <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setconfig[keywordapi]" value="0" type="radio"<?php echo ($keywordapi=='0') ? ' checked' : ''?> onclick="$('#baidu').hide();$('#xunfei').hide();"> <?php echo L('setting_default')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setconfig[keywordapi]" value="1" type="radio"<?php echo ($keywordapi=='1') ? ' checked' : ''?> onclick="$('#baidu').show();$('#xunfei').hide();"> <?php echo L('setting_baidu')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setconfig[keywordapi]" value="2" type="radio"<?php echo ($keywordapi=='2') ? ' checked' : ''?> onclick="$('#xunfei').show();$('#baidu').hide();"> <?php echo L('setting_xunfei')?> <span></span></label>
        </div>
	</td>
  </tr>
  <tr>
    <th width="120" valign="top"><?php echo L('setting_qcnum')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[baidu_qcnum]" id="baidu_qcnum" size="20" value="<?php echo $baidu_qcnum ?>"/></td>
  </tr>
  <tr id="baidu"<?php echo ($keywordapi=='2' || $keywordapi=='0') ? ' style="display: none;"' : ''?>>
    <th width="120"><?php echo L('setting_baidu_enable')?></th>
    <td class="y-bg"><fieldset>
    <legend><?php echo L('setting_baidu_enable')?></legend>
    <table width="100%"  class="table_form">
  <tr style="display: none;">
    <th width="130" valign="top"><?php echo L('setting_keyword_appid')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[baidu_aid]" id="baidu_aid" size="40" value="<?php echo $baidu_aid ?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('setting_keyword_key')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[baidu_skey]" id="baidu_skey" size="40" value="<?php echo $baidu_skey ?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('setting_keyword_skey')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[baidu_arcretkey]" id="baidu_arcretkey" size="40" value="<?php echo $baidu_arcretkey ?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><a href="https://cloud.baidu.com/" target="_blank"><?php echo L('setting_keyword_register')?></a></th>
    <td class="y-bg"><?php echo L('setting_baidu_keyword')?></a></td>
  </tr>
</table>
</fieldset></td>
  </tr>
  <tr id="xunfei"<?php echo ($keywordapi=='1' || $keywordapi=='0') ? ' style="display: none;"' : ''?>>
    <th width="120"><?php echo L('setting_xunfei_enable')?></th>
    <td class="y-bg"><fieldset>
    <legend><?php echo L('setting_xunfei_enable')?></legend>
    <table width="100%"  class="table_form">
  <tr>
    <th width="130" valign="top"><?php echo L('setting_keyword_appid')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[xunfei_aid]" id="xunfei_aid" size="40" value="<?php echo $xunfei_aid ?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><?php echo L('setting_keyword_key')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[xunfei_skey]" id="xunfei_skey" size="40" value="<?php echo $xunfei_skey ?>"/></td>
  </tr>
  <tr>
    <th width="130" valign="top"><a href="https://console.xfyun.cn/services/ke" target="_blank"><?php echo L('setting_keyword_register')?></a></th>
    <td class="y-bg"><?php echo L('setting_xunfei_keyword')?></td>
  </tr>
</table>
</fieldset></td>
  </tr>
  </table>
</div>

<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</div>
</div>
</form>
</body>
<script type="text/javascript">
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
function showsmtp(obj,hiddenid){
	hiddenid = hiddenid ? hiddenid : 'smtpcfg';
	var status = $(obj).val();
	if(status == 1) $("#"+hiddenid).show();
	else  $("#"+hiddenid).hide();
}
function test_mail() {
	var mail_type = $('input[checkbox=mail_type][checked]').val();
	var mail_auth = $('input[checkbox=mail_auth][checked]').val();
    $.post('?m=admin&c=setting&a=public_test_mail&mail_to='+$('#mail_to').val(),{mail_type:mail_type,mail_server:$('#mail_server').val(),mail_port:$('#mail_port').val(),mail_user:$('#mail_user').val(),mail_password:$('#mail_password').val(),mail_auth:mail_auth,mail_from:$('#mail_from').val()}, function(data){
	Dialog.alert(data);
	});
}
function to_key() {
   $.get('?m=admin&c=setting&a=public_syskey&pc_hash='+pc_hash, function(data){
		$('#auth_key').val(data);
	});
}
$(function() {
	setInterval(dr_site_time, 1000);
});
function dr_site_time() {
	$.ajax({
		type: "get",
		dataType: "json",
		url: "?m=admin&c=setting&a=public_site_time&pc_hash="+pc_hash,
		success: function(json) {
			$('#site_time').html(json.msg);
		}
	});
}
</script>
</html>