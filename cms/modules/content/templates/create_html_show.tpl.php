<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">var bs_selectAllText = '全选';var bs_deselectAllText = '全删';var bs_noneSelectedText = '没有选择'; var bs_noneResultsText = '没有找到 {0}';</script>
<link href="<?php echo JS_PATH?>bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function(){$('.bs-select').selectpicker();});</script>
<link href="<?php echo JS_PATH;?>bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH;?>bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            format: "yyyy-mm-dd",
            orientation: "left",
            autoclose: true
        });
    }
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
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
                    </div>
                </div>
                <div class="form-group ">
                    <label class="col-md-2 control-label"><?php echo L('最新发布数量');?></label>
                    <div class="col-md-9">
                        <label><input type="text" placeholder="<?php echo L('按最新发布数量');?>" class="form-control" value="" name="number"></label>
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
                            <div class="input-group date-picker input-daterange " data-date="" data-date-format="yyyy-mm-dd">
                                <input type="text" placeholder="<?php echo L('按发布时间范围');?>" class="form-control" value="" name="fromdate">
                                <span class="input-group-addon"> <?php echo L('到');?> </span>
                                <input type="text" placeholder="<?php echo L('按发布时间范围');?>" class="form-control" value="" name="todate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('按所选栏目');?></label>
                    <div class="col-md-9">
                        <select class="bs-select form-control"<?php if (dr_count($categorys) > 30) {echo 'data-live-search="true"';}?> name='catids[]' id='catids' multiple="multiple" style="width:350px;height:260px;" title="<?php echo L('no_limit_category');?>">
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
                        <label><button type="button" onclick="iframe_show('<?php echo L('批量更新内容URL');?>', '?m=content&c=create_html&a=public_show_url&modelid=<?php echo $modelid;?>&'+$('#myform_category_show').serialize())" class="btn default"> <i class="fa fa-link"></i> <?php echo L('批量更新内容URL');?> </button></label>
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
</div>
</div>
</body>
</html>