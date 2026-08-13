<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">var bs_selectAllText = '全选';var bs_deselectAllText = '全删';var bs_noneSelectedText = '没有选择'; var bs_noneResultsText = '没有找到 {0}';</script>
<link href="<?php echo JS_PATH?>bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function(){$(":text").removeClass('input-text');$('.bs-select').selectpicker();});</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input type="hidden" name="dosubmit" value="1">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('google_baidunews').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-map-o"></i> <?php if (is_pc()) {echo L('google_baidunews');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('google_sitemaps').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-sitemap"></i> <?php if (is_pc()) {echo L('google_sitemaps');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('google_info').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-exclamation-circle"></i> <?php if (is_pc()) {echo L('google_info');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('google_ismake')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="mark" value="1" checked> <?php echo L('setting_yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="mark" value="0"> <?php echo L('setting_no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('google_select_db')?></label>
                        <div class="col-md-9">
                            <label><select name='catids[]' id='catids' class="form-control bs-select" multiple="multiple" data-actions-box="true" data-title="<?php echo L('请选择');?>">
                            <?php echo $string;?>
                            </select></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('google_period')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="time" name="time" value="40" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('Email')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="email" name="email" value="cms@cms.cn" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('google_nums')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="baidunum" name="baidunum" value="20" >
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('google_rate')?></label>
                        <div class="col-md-9">
                            <label><select name="content_priority">
                            <option value="1">1</option><option value="0.9">0.9</option>
                            <option value="0.8">0.8</option><option selected="" value="0.7">0.7</option>
                            <option value="0.6">0.6</option><option value="0.5">0.5</option>
                            <option value="0.4">0.4</option><option value="0.3">0.3</option>
                            <option value="0.2">0.2</option><option value="0.1">0.1</option>
                            </select></label>
                            <label><select name="content_changefreq">
                            <option value="always"><?php echo L('google_update')?></option><option value="hourly"><?php echo L('google_hour')?></option>
                            <option value="daily"><?php echo L('google_day')?></option><option selected="" value="weekly"><?php echo L('google_week')?></option>
                            <option value="monthly"><?php echo L('google_month')?></option><option value="yearly"><?php echo L('google_year')?></option>
                            <option value="never"><?php echo L('google_noupdate')?></option>
                            </select></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('google_nums')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="num" name="num" value="20" >
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('explain')?></label>
                        <div class="col-md-9">
                            <span class="help-block"><?php echo L('google_infos')?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('google_startmake')?></button>
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