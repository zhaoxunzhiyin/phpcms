<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap/css/bootstrap.min.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>calendar/win2k.css"/>
<script type="text/javascript" src="<?php echo JS_PATH;?>calendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>calendar/lang/en.js"></script>
<script type="text/javascript">
$(function(){
	$(":text").removeClass('input-text');
})
</script>
<style type="text/css">
.page-content {margin-left: 0px;margin-top: 0;padding: 25px 20px 10px;}
.main-content {background: #f5f6f8;}
.note.note-danger {background-color: #fef7f8;border-color: #f0868e;color: #210406;}
.note.note-danger {border-radius: 4px;border-left: 4px solid #f0868e;background-color: #ffffff;color: #888;}
.my-content-top-tool {margin-top: -25px;margin-bottom: 10px;}
.note {margin: 0 0 20px;padding: 15px 30px 15px 15px;border-left: 5px solid #eee;border-radius: 0 4px 4px 0;}
.note, .tabs-right.nav-tabs>li>a:focus, .tabs-right.nav-tabs>li>a:hover {-webkit-border-radius: 0 4px 4px 0;-moz-border-radius: 0 4px 4px 0;-ms-border-radius: 0 4px 4px 0;-o-border-radius: 0 4px 4px 0;}
.note p:last-child {margin-bottom: 0;}
.note p {margin: 0;}
.note p, .page-loading, .panel .panel-body {font-size: 13px;}
.note.note-danger a {color: #666;}
.portlet.light>.portlet-title {padding: 0;color: #181C32;font-weight: 500;}
.portlet.bordered>.portlet-title {border-bottom: 0;}
.portlet>.portlet-title {padding: 0;margin-bottom: 2px;-webkit-border-radius: 4px 4px 0 0;-moz-border-radius: 4px 4px 0 0;-ms-border-radius: 4px 4px 0 0;-o-border-radius: 4px 4px 0 0;border-radius: 4px 4px 0 0;}
.portlet>.portlet-title>.caption {float: left;display: inline-block;font-size: 18px;line-height: 18px;padding: 10px 0;}
.portlet.light>.portlet-title>.caption.caption-md>.caption-subject, .portlet.light>.portlet-title>.caption>.caption-subject {font-size: 15px;}
.font-dark {color: #2f353b!important;}
.btn:not(.btn-sm):not(.btn-lg) {line-height: 1.44;}
.btn {outline: 0!important;}
.btn, .form-control {box-shadow: none!important;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
.btn, .btn-danger.active, .btn-danger:active, .btn-default.active, .btn-default:active, .btn-info.active, .btn-info:active, .btn-primary.active, .btn-primary:active, .btn-success.active, .btn-success:active, .btn-warning.active, .btn-warning:active, .btn.active, .btn:active, .dropdown-menu>.disabled>a:focus, .dropdown-menu>.disabled>a:hover, .form-control, .navbar-toggle, .open>.btn-danger.dropdown-toggle, .open>.btn-default.dropdown-toggle, .open>.btn-info.dropdown-toggle, .open>.btn-primary.dropdown-toggle, .open>.btn-success.dropdown-toggle, .open>.btn-warning.dropdown-toggle {background-image: none;}
.btn, .btn-group, .btn-group-vertical, .caret, .checkbox-inline, .radio-inline, img {vertical-align: middle;}
.btn.blue:not(.btn-outline) {color: #FFF;background-color: #3598dc;border-color: #3598dc;}
.btn.blue:not(.btn-outline).active, .btn.blue:not(.btn-outline):active, .btn.blue:not(.btn-outline):hover, .open>.btn.blue:not(.btn-outline).dropdown-toggle {color: #FFF;background-color: #217ebd;border-color: #1f78b5;}
.btn.green:not(.btn-outline) {color: #FFF;background-color: #32c5d2;border-color: #32c5d2;}
.btn.green:not(.btn-outline).active, .btn.green:not(.btn-outline):active, .btn.green:not(.btn-outline):hover, .open>.btn.green:not(.btn-outline).dropdown-toggle {color: #FFF;background-color: #26a1ab;border-color: #2499a3;}
.btn.yellow:not(.btn-outline) {color: #fff;background-color: #c49f47;border-color: #c49f47;}
.btn.yellow:not(.btn-outline).active, .btn.yellow:not(.btn-outline):active, .btn.yellow:not(.btn-outline):hover, .open>.btn.yellow:not(.btn-outline).dropdown-toggle {color: #fff;background-color: #a48334;border-color: #9c7c32;}
.btn.red:not(.btn-outline).active, .btn.red:not(.btn-outline):active, .btn.red:not(.btn-outline):hover, .open>.btn.red:not(.btn-outline).dropdown-toggle {color: #fff;background-color: #e12330;border-color: #dc1e2b;}
.btn.red:not(.btn-outline) {color: #fff;background-color: #e7505a;border-color: #e7505a;}
.btn.dark:not(.btn-outline) {color: #FFF;background-color: #2f353b;border-color: #2f353b;}
.btn.dark:not(.btn-outline).active, .btn.dark:not(.btn-outline):active, .btn.dark:not(.btn-outline):hover, .open>.btn.dark:not(.btn-outline).dropdown-toggle {color: #FFF;background-color: #181c1f;border-color: #141619;}
@media (max-width:480px) {select[multiple],select[size]{width:100% !important;}}
</style>
<div class="page-content main-content">
<div class="note note-danger">
    <p><?php echo L('确保网站目录必须有可写权限');?></p>
</div>
<div class="portlet bordered light form-horizontal">
    <div class="portlet-body">
        <div class="form-body">
            <form id="myform_category_show">
                <input type="hidden" name="dosubmit" value="1">
                <div class="form-group ">
                    <label class="col-md-2 control-label"><?php echo L('according_model');?></label>
                    <div class="col-md-9">
                        <label>
                        <?php $models = getcache('model','commons');
                        $model_datas = array();
                        foreach($models as $_k=>$_v) {
                            if($_v['siteid']!=$this->siteid) continue;
                            $model_datas[$_v['modelid']] = $_v['name'];
                        }
                        echo form::select($model_datas,$modelid,'name="modelid" onchange="change_model(this.value)"');
                        ?></label>
                    </div>
                </div>
                <div class="form-group ">
                    <label class="col-md-2 control-label"><?php echo L('每页生成数量');?></label>
                    <div class="col-md-9">
                        <label><input type="text" placeholder="<?php echo L('建议不要太多');?>" class="form-control" value="10" name="pagesize"></label>
                        <span class="help-block">请与模板调用数量相同</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('last_information');?></label>
                    <div class="col-md-9">
                        <label><input type="text" placeholder="<?php echo L('last_information').'几'.L('information_items');?>" class="form-control" value="" name="number"></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('按内容ID范围');?></label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <div class="input-group input-daterange">
                                <input type="text" placeholder="<?php echo L('按ID开始');?>" class="form-control" value="" name="fromid">
                                <span class="input-group-addon"> <?php echo L('到');?> </span>
                                <input type="text" placeholder="<?php echo L('按ID结束');?>" class="form-control" value="" name="toid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('按发布时间范围');?></label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <div class="input-group input-daterange">
                                <input type="text" placeholder="<?php echo L('按发布时间范围');?>" class="form-control" value="" name="fromdate" id="fromdate">
                                <script type="text/javascript">
                                    Calendar.setup({
                                    weekNumbers: true,
                                    inputField : "fromdate",
                                    trigger    : "fromdate",
                                    dateFormat: "%Y-%m-%d",
                                    showTime: false,
                                    minuteStep: 1,
                                    onSelect   : function() {this.hide();}
                                    });
                                </script>
                                <span class="input-group-addon"> <?php echo L('到');?> </span>
                                <input type="text" placeholder="<?php echo L('按发布时间范围');?>" class="form-control" value="" name="todate" id="todate">
                                <script type="text/javascript">
                                    Calendar.setup({
                                    weekNumbers: true,
                                    inputField : "todate",
                                    trigger    : "todate",
                                    dateFormat: "%Y-%m-%d",
                                    showTime: false,
                                    minuteStep: 1,
                                    onSelect   : function() {this.hide();}
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('按所选栏目');?></label>
                    <div class="col-md-9">
                        <select class="bs-select form-control" name='catids[]' id='catids' multiple="multiple" style="width:350px;height:260px;" title="<?php echo L('push_ctrl_to_select');?>">
                            <option value='0' selected><?php echo L('no_limit_category');?></option>
                            <?php echo $string;?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('生成内容页面');?></label>
                    <div class="col-md-9">
                        <label><button type="button" onclick="dr_bfb('<?php echo L('生成内容页面');?>', 'myform_category_show', '?m=content&c=create_html&a=show')" class="btn dark"> <i class="fa fa-th-large"></i> <?php echo L('开始生成静态');?> </button></label>
                        <label><button type="button" onclick="dr_bfb('<?php echo L('生成内容页面');?>', 'myform_category_show', '?m=content&c=create_html&a=public_show_point')" class="btn red"> <i class="fa fa-th-large"></i> <?php echo L('上次未执行完毕时继续执行');?> </button></label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<script language="javascript">
function change_model(modelid) {
    window.location.href='?m=content&c=create_html&a=show&modelid='+modelid+'&pc_hash='+pc_hash;
}
</script>
</body>
</html>