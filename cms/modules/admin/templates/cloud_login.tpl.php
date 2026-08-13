<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="margin-top:20px;margin-bottom:30px;">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
    <div class="myfbody">
        <div class="col-md-3"></div>
        <div class="col-md-6">

            <div class="portlet light bordered" style="margin-top: 40px">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-blue"><?php echo L('绑定账号');?></span>
                    </div>
                </div>
                <div class="portlet-body form">

                    <div class="form-body">

                        <div class="form-group" id="dr_row_username">
                            <label class="col-xs-3 control-label ">官方账号</label>
                            <div class="col-xs-7">
                                <input type="text" id="dr_username" class="form-control" name="data[username]">
                            </div>
                        </div>
                        <div class="form-group" id="dr_row_password">
                            <label class="col-xs-3 control-label ">登录密码</label>
                            <div class="col-xs-7">
                                <input type="password" id="dr_password" class="form-control" name="data[password]">
                            </div>
                        </div>
                        <div class="form-group">

                            <label class="col-xs-3 control-label "></label>
                            <div class="col-xs-7">
                            <button type="button" onclick="dr_post_submit('?m=admin&c=cloud&a=login&menuid=<?php echo $this->input->get('menuid')?>', 'myform', 3000, '?m=admin&c=cloud&a=upgrade&menuid=<?php echo $this->input->get('menuid')?>&pc_hash=<?php echo dr_get_csrf_token()?>');" class="btn red " style="margin-right: 20px;"> 绑定账号 </button>
                                <a href="<?php echo CMS_CLOUD;?>index.php?m=member&c=index&a=register&siteid=1" target="_blank">免费注册账号</a>
                        </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>

    </div>

</form>
</div>
</div>
</div>
</div>
</body>
</html>