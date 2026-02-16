<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
$menu_data = $this->menu_db->get_one(array('name' => 'version_update', 'm' => 'admin', 'c' => 'cloud', 'a' => 'upgrade'));?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form class="form-horizontal" role="form" id="myform">
<div class="row">
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-blue sbold ">系统信息</span>
                </div>

            </div>
            <div class="portlet-body">
                <div class="form-body yun-list">

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('系统版本');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><a href="javascript:;" layuimini-content-href="?m=admin&c=cloud&a=upgrade&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token();?>" data-title="版本升级" data-icon="fa fa-refresh"> <i class="fa fa-refresh"></i> <?php echo CMS_VERSION?></a><a id="dr_cms_update" href="javascript:;" layuimini-content-href="?m=admin&c=cloud&a=upgrade&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token();?>" data-title="版本升级" data-icon="fa fa-refresh" style="margin-left: 10px;display: none" class="badge badge-danger badge-roundless">  </a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('发布时间');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo dr_date(strtotime(CMS_UPDATETIME), 'Y-m-d', 'red');?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('下载时间');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo dr_date(strtotime(CMS_DOWNTIME), null, 'red');?></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-blue sbold "><?php echo L('服务器信息');?></span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-body yun-list">


                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('上传最大值');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><a href="javascript:dr_iframe_show('show', '?m=admin&c=cloud&a=config');"><?php echo @ini_get("upload_max_filesize");?></a></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('POST最大值');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><a href="javascript:dr_iframe_show('show', '?m=admin&c=cloud&a=config');"><?php echo @ini_get("post_max_size");?></a></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('PHP内存上限');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><a href="javascript:dr_iframe_show('show', '?m=admin&c=cloud&a=config');"><?php echo @ini_get("memory_limit");?></a></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('提交表单数量');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><a href="javascript:dr_iframe_show('show', '?m=admin&c=cloud&a=config');"><?php echo @ini_get("max_input_vars");?></a></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('Web网站目录');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo CMS_PATH;?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('核心程序目录');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo PC_PATH;?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('附件存储目录');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo SYS_UPLOAD_PATH;?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo L('模板文件目录');?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo TPLPATH;?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
</div>
<script>
$(function () {
    $.ajax({type: "GET",dataType:"json", url: "?m=admin&c=index&a=public_version_cms",
        success: function(json) {
            if (json.code) {
                $('#dr_cms_update').show();
                $('#dr_cms_update').html('<i class="fa fa-refresh"></i> '+json.msg);
            }
        }
    });
});
</script>
<script src="<?php echo JS_PATH?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
layui.use(['layer', 'miniTab'], function () {
    var $ = layui.jquery,
        layer = layui.layer,
        miniTab = layui.miniTab;
    miniTab.listen();
});
</script>
</div>
</div>
</body>
</html>