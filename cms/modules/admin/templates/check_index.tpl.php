<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.slimscroll.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
    <tr>
        <th width="55"> </th>
        <th width="300"> <?php echo L('检查项目');?> </th>
        <th> <?php echo L('检查结果');?> </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($list as $id=>$t) {?>
    <tr>
        <td>
            <span class="badge badge-success"> <?php echo $id;?> </span>
        </td>
        <td>
            <?php echo $t;?>
        </td>
        <td id="dr_<?php echo $id;?>_result">
            <img style='height:17px' src='<?php echo JS_PATH;?>layer/theme/default/loading-0.gif'>
        </td>
    </tr>
    <script>
        $(function () {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "?m=admin&c=check&a=public_do_index&id=<?php echo $id;?>",
                success: function (json) {
                    $('#dr_<?php echo $id;?>_result').html(json.msg);
                    if (json.code == 0) {
                        $('#dr_<?php echo $id;?>_result').attr('style', 'color:red');
                    } else {
                        $('#dr_<?php echo $id;?>_result').attr('style', 'color:green');
                    }
                },
                error: function(HttpRequest, ajaxOptions, thrownError) {
                    $('#dr_<?php echo $id;?>_result').attr('style', 'color:red');
                    $('#dr_<?php echo $id;?>_result').html(HttpRequest.responseText);
                }
            });
        });
    </script>
    <?php }?>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>