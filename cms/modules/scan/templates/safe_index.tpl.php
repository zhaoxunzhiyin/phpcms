<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.slimscroll.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="note note-danger">
    <p>本插件需要具备PHP技术或者服务器运维技术人员</p>
    <?php if ($is_ok) {?>
    <p><a href="javascript:;" style="color: green">当前环境已能通过安全验证，可有效的抵御web攻击</a></p>
    <?php } else {?>
    <p><a href="javascript:;" style="color: red">当前环境未能通过安全验证，请按下方红字要求设置参数</a></p>
    <?php }?>
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
                url: "?m=scan&c=index&a=public_do_index&id=<?php echo $id;?>",
                success: function (json) {
                    $('#dr_<?php echo $id;?>_result').html(json.msg);
                    if (json.code == 0) {
                        $('#dr_<?php echo $id;?>_result').append("&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:dr_safe_book(<?php echo $id;?>);\" class=\"btn btn-xs red\"> <i class=\"fa fa-book\"></i> 立即设置</a>");
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
<script>
function dr_safe_book(id) {
    dr_iframe_show('设置教程', "?m=scan&c=index&a=public_book_index&id="+id);
}
</script>
</body>
</html>