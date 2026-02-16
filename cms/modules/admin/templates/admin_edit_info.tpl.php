<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_validator = true;include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<input type="hidden" name="info[userid]" value="<?php echo $userid?>">
<input type="hidden" name="info[username]" value="<?php echo $username?>">
<input type="hidden" name="info[admin_manage_code]" value="<?php echo $admin_manage_code?>" id="admin_manage_code">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('editinfo').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('editinfo');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('username')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo $username;?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('lastlogintime')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo $lastlogintime ? dr_date($lastlogintime, null, 'red') : ''?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('lastloginip')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo $lastloginip;?> <a class="btn btn-xs green" href="javascript:dr_show_ip('<?php echo WEB_PATH;?>api.php?op=ip_address', '<?php echo $lastloginip;?>');"><i class="fa fa-eye" /></i> <?php echo L('查看')?></a></div>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_realname">
                        <label class="col-md-2 control-label"><?php echo L('realname')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_realname" name="info[realname]" value="<?php echo $realname?>">
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_email">
                        <label class="col-md-2 control-label"><?php echo L('email')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_email" name="info[email]" value="<?php echo $email?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_phone">
                        <label class="col-md-2 control-label"><?php echo L('phone')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_phone" name="info[phone]" value="<?php echo $phone?>" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('Language')?></label>
                        <div class="col-md-9">
                            <label><select name="info[lang]">
                                <?php if(in_array('af',$dir_array)) { ?><option value="af" <?php if($lang=='af') echo 'selected="selected"';?>>Afrikaans</option><?php }?>
                                <?php if(in_array('sq',$dir_array)) { ?><option value="sq" <?php if($lang=='sq') echo 'selected="selected"';?>>Shqip - Albanian</option><?php }?>
                                <?php if(in_array('ar',$dir_array)) { ?><option value="ar" <?php if($lang=='ar') echo 'selected="selected"';?>>العربية - Arabic</option><?php }?>
                                <?php if(in_array('az',$dir_array)) { ?><option value="az" <?php if($lang=='az') echo 'selected="selected"';?>>Azərbaycanca - Azerbaijani</option><?php }?>
                                <?php if(in_array('bn',$dir_array)) { ?><option value="bn" <?php if($lang=='bn') echo 'selected="selected"';?>>Bangla</option><?php }?>
                                <?php if(in_array('eu',$dir_array)) { ?><option value="eu" <?php if($lang=='eu') echo 'selected="selected"';?>>Euskara - Basque</option><?php }?>
                                <?php if(in_array('becyr',$dir_array)) { ?><option value="becyr" <?php if($lang=='becyr') echo 'selected="selected"';?>>Беларуская - Belarusian</option><?php }?>
                                <?php if(in_array('belat',$dir_array)) { ?><option value="belat" <?php if($lang=='belat') echo 'selected="selected"';?>>Biełaruskaja - Belarusian latin</option><?php }?>
                                <?php if(in_array('bs',$dir_array)) { ?><option value="bs" <?php if($lang=='bs') echo 'selected="selected"';?>>Bosanski - Bosnian</option><?php }?>
                                <?php if(in_array('ptbr',$dir_array)) { ?><option value="ptbr" <?php if($lang=='ptbr') echo 'selected="selected"';?>>Portugu&ecirc;s - Brazilian portuguese</option><?php }?>
                                <?php if(in_array('bg',$dir_array)) { ?><option value="bg" <?php if($lang=='bg') echo 'selected="selected"';?>>Български - Bulgarian</option><?php }?>
                                <?php if(in_array('ca',$dir_array)) { ?><option value="ca" <?php if($lang=='ca') echo 'selected="selected"';?>>Catal&agrave; - Catalan</option><?php }?>
                                <?php if(in_array('zh-cn',$dir_array)) { ?><option value="zh-cn" <?php if($lang=='zh-cn') echo 'selected="selected"';?>>中文 - Chinese simplified</option><?php }?>
                                <?php if(in_array('zhtw',$dir_array)) { ?><option value="zhtw" <?php if($lang=='zhtw') echo 'selected="selected"';?>>中文 - Chinese traditional</option><?php }?>
                                <?php if(in_array('hr',$dir_array)) { ?><option value="hr" <?php if($lang=='hr') echo 'selected="selected"';?>>Hrvatski - Croatian</option><?php }?>
                                <?php if(in_array('cs',$dir_array)) { ?><option value="cs" <?php if($lang=='cs') echo 'selected="selected"';?>>Česky - Czech</option><?php }?>
                                <?php if(in_array('da',$dir_array)) { ?><option value="da" <?php if($lang=='da') echo 'selected="selected"';?>>Dansk - Danish</option><?php }?>
                                <?php if(in_array('nl',$dir_array)) { ?><option value="nl" <?php if($lang=='nl') echo 'selected="selected"';?>>Nederlands - Dutch</option><?php }?>
                                <?php if(in_array('en',$dir_array)) { ?><option value="en" <?php if($lang=='en') echo 'selected="selected"';?>>English</option><?php }?>
                                <?php if(in_array('et',$dir_array)) { ?><option value="et" <?php if($lang=='et') echo 'selected="selected"';?>>Eesti - Estonian</option><?php }?>
                                <?php if(in_array('fi',$dir_array)) { ?><option value="fi" <?php if($lang=='fi') echo 'selected="selected"';?>>Suomi - Finnish</option><?php }?>
                                <?php if(in_array('fr',$dir_array)) { ?><option value="fr" <?php if($lang=='fr') echo 'selected="selected"';?>>Fran&ccedil;ais - French</option><?php }?>
                                <?php if(in_array('gl',$dir_array)) { ?><option value="gl" <?php if($lang=='gl') echo 'selected="selected"';?>>Galego - Galician</option><?php }?>
                                <?php if(in_array('ka',$dir_array)) { ?><option value="ka" <?php if($lang=='ka') echo 'selected="selected"';?>>ქართული - Georgian</option><?php }?>
                                <?php if(in_array('de',$dir_array)) { ?><option value="de" <?php if($lang=='de') echo 'selected="selected"';?>>Deutsch - German</option><?php }?>
                                <?php if(in_array('el',$dir_array)) { ?><option value="el" <?php if($lang=='el') echo 'selected="selected"';?>>&Epsilon;&lambda;&lambda;&eta;&nu;&iota;&kappa;ά - Greek</option><?php }?>
                                <?php if(in_array('he',$dir_array)) { ?><option value="he" <?php if($lang=='he') echo 'selected="selected"';?>>עברית - Hebrew</option><?php }?>
                                <?php if(in_array('hi',$dir_array)) { ?><option value="hi" <?php if($lang=='hi') echo 'selected="selected"';?>>हिन्दी - Hindi</option><?php }?>
                                <?php if(in_array('hu',$dir_array)) { ?><option value="hu" <?php if($lang=='hu') echo 'selected="selected"';?>>Magyar - Hungarian</option><?php }?>
                                <?php if(in_array('id',$dir_array)) { ?><option value="id" <?php if($lang=='id') echo 'selected="selected"';?>>Bahasa Indonesia - Indonesian</option><?php }?>
                                <?php if(in_array('it',$dir_array)) { ?><option value="it" <?php if($lang=='it') echo 'selected="selected"';?>>Italiano - Italian</option><?php }?>
                                <?php if(in_array('ja',$dir_array)) { ?><option value="ja" <?php if($lang=='ja') echo 'selected="selected"';?>>日本語 - Japanese</option><?php }?>
                                <?php if(in_array('ko',$dir_array)) { ?><option value="ko" <?php if($lang=='ko') echo 'selected="selected"';?>>한국어 - Korean</option><?php }?>
                                <?php if(in_array('lv',$dir_array)) { ?><option value="lv" <?php if($lang=='lv') echo 'selected="selected"';?>>Latvie&scaron;u - Latvian</option><?php }?>
                                <?php if(in_array('lt',$dir_array)) { ?><option value="lt" <?php if($lang=='lt') echo 'selected="selected"';?>>Lietuvių - Lithuanian</option><?php }?>
                                <?php if(in_array('mkcyr',$dir_array)) { ?><option value="mkcyr" <?php if($lang=='mkcyr') echo 'selected="selected"';?>>Macedonian - Macedonian</option><?php }?>
                                <?php if(in_array('ms',$dir_array)) { ?><option value="ms" <?php if($lang=='ms') echo 'selected="selected"';?>>Bahasa Melayu - Malay</option><?php }?>
                                <?php if(in_array('mn',$dir_array)) { ?><option value="mn" <?php if($lang=='mn') echo 'selected="selected"';?>>Монгол - Mongolian</option><?php }?>
                                <?php if(in_array('no',$dir_array)) { ?><option value="no" <?php if($lang=='no') echo 'selected="selected"';?>>Norsk - Norwegian</option><?php }?>
                                <?php if(in_array('fa',$dir_array)) { ?><option value="fa" <?php if($lang=='fa') echo 'selected="selected"';?>>فارسی - Persian</option><?php }?>
                                <?php if(in_array('pl',$dir_array)) { ?><option value="pl" <?php if($lang=='pl') echo 'selected="selected"';?>>Polski - Polish</option><?php }?>
                                <?php if(in_array('pt',$dir_array)) { ?><option value="pt" <?php if($lang=='pt') echo 'selected="selected"';?>>Portugu&ecirc;s - Portuguese</option><?php }?>
                                <?php if(in_array('ro',$dir_array)) { ?><option value="ro" <?php if($lang=='ro') echo 'selected="selected"';?>>Rom&acirc;nă - Romanian</option><?php }?>
                                <?php if(in_array('ru',$dir_array)) { ?><option value="ru" <?php if($lang=='ru') echo 'selected="selected"';?>>Русский - Russian</option><?php }?>
                                <?php if(in_array('srcyr',$dir_array)) { ?><option value="srcyr" <?php if($lang=='srcyr') echo 'selected="selected"';?>>Српски - Serbian</option><?php }?>
                                <?php if(in_array('srlat',$dir_array)) { ?><option value="srlat" <?php if($lang=='srlat') echo 'selected="selected"';?>>Srpski - Serbian latin</option><?php }?>
                                <?php if(in_array('si',$dir_array)) { ?><option value="si" <?php if($lang=='si') echo 'selected="selected"';?>>සිංහල - Sinhala</option><?php }?>
                                <?php if(in_array('af',$dir_array)) { ?><option value="sk" <?php if($lang=='sk') echo 'selected="selected"';?>>Slovenčina - Slovak</option><?php }?>
                                <?php if(in_array('sl',$dir_array)) { ?><option value="sl" <?php if($lang=='sl') echo 'selected="selected"';?>>Sloven&scaron;čina - Slovenian</option><?php }?>
                                <?php if(in_array('es',$dir_array)) { ?><option value="es" <?php if($lang=='es') echo 'selected="selected"';?>>Espa&ntilde;ol - Spanish</option><?php }?>
                                <?php if(in_array('sv',$dir_array)) { ?><option value="sv" <?php if($lang=='sv') echo 'selected="selected"';?>>Svenska - Swedish</option><?php }?>
                                <?php if(in_array('tt',$dir_array)) { ?><option value="tt" <?php if($lang=='tt') echo 'selected="selected"';?>>Tatar&ccedil;a - Tatarish</option><?php }?>
                                <?php if(in_array('th',$dir_array)) { ?><option value="th" <?php if($lang=='th') echo 'selected="selected"';?>>ภาษาไทย - Thai</option><?php }?>
                                <?php if(in_array('tr',$dir_array)) { ?><option value="tr" <?php if($lang=='tr') echo 'selected="selected"';?>>T&uuml;rk&ccedil;e - Turkish</option><?php }?>
                                <?php if(in_array('uk',$dir_array)) { ?><option value="uk" <?php if($lang=='uk') echo 'selected="selected"';?>>Українська - Ukrainian</option><?php }?>
                                <?php if(in_array('uzcyr',$dir_array)) { ?><option value="uzcyr" <?php if($lang=='uzcyr') echo 'selected="selected"';?>>Ўзбекча - Uzbek-cyrillic</option><?php }?>
                                <?php if(in_array('uzlat',$dir_array)) { ?><option value="uzlat" <?php if($lang=='uzlat') echo 'selected="selected"';?>>O&lsquo;zbekcha - Uzbek-latin</option><?php }?>
                            </select></label>
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