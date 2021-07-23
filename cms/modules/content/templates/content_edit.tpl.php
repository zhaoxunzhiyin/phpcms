<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<link rel="stylesheet" href="<?php echo JS_PATH?>layui/css/layui.css" media="all" />
<style type="text/css">
html,body{background:#f5f6f8!important;}
body{padding: 20px 20px 0px 20px;}
.input-text, .measure-input, textarea, input.date, input.endDate, .input-focus {padding: 6px 12px;height: 32px;}
.keywords {height: 100%!important;}
</style>
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
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap/css/bootstrap.min.css" media="all" />
<style type="text/css">
.my-sysfield .col-md-2 {width: 100%!important;}
.my-sysfield .control-label {text-align: left!important;margin-bottom: 10px;}
</style>
<script type="text/javascript">var catid=<?php echo $catid;?></script>
<form name="myform" id="myform" action="?m=content&c=content&a=edit" class="form-horizontal" onsubmit="return checkall()" method="post" enctype="multipart/form-data">
<div class="myfbody">
        <div class="row ">
            <div class="col-md-9">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green sbold "></span>
                        </div>
                        <div class="actions">
                            <div class="btn-group">
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body clear">
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
<div class="form-group">
    <label class="control-label col-md-2"><?php if($info['star']){ ?><span class="required" aria-required="true"> * </span><?php } ?><?php echo $info['name']?></label>
    <div class="col-md-10">
		<?php echo $info['form']?>
		<span class="help-block"><?php echo $info['tips']?></span>
	</div>
</div>
<?php
} }
?>
                        </div>
                    </div>
                </div>

                
            </div>
            <div class="col-md-3 my-sysfield">
                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="form-body clear">
<?php
if(is_array($forminfos['senior'])) {
 foreach($forminfos['senior'] as $field=>$info) {
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
<div class="form-group">
    <label class="control-label col-md-2"><?php if($info['star']){ ?><span class="required" aria-required="true"> * </span><?php } ?><?php echo $info['name']?></label>
    <div class="col-md-10">
		<?php echo $info['form']?>
		<span class="help-block"><?php echo $info['tips']?></span>
	</div>
</div>
<?php
} }
?>

                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<input value="<?php if($r['upgrade']) echo $r['url'];?>" type="hidden" name="upgrade">
<input value="<?php echo $id;?>" type="hidden" name="id"><input value="<?php echo L('save_close');?>" type="submit" name="dosubmit" id="dosubmit" class="dialog">
<input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" id="dosubmit_continue" class="dialog">
</form>

</body>
</html>
<script type="text/javascript">
<!--
//只能放到最下面
$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();
	boxid = $(obj).attr('id');
	if($('#'+boxid).attr('boxid')!=undefined) {
		check_content(boxid);
	}
	})}});
	<?php echo $formValidator;?>
	
/*
 * 加载禁用外边链接
 */

})
document.title='<?php echo L('edit_content').addslashes($data['title']);?>';
self.moveTo(0, 0);
function refersh_window() {
	setcookie('refersh_time', 1);
}
function checkall(){
	<?php echo $checkall;?>
}
//-->
</script>