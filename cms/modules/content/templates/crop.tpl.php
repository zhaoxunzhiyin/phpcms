<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>cropper/cropper.min.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>cropper/cropper.min.js"></script>
<div class="container_crop">
    <div class="container_img">
        <img id="image" src="<?php echo $data['url'];?>">
    </div>

    <div class="con_right">
        <div class="preview">
            <div class="preview_img small_lg"></div>
            <div class="preview_img small_md"></div>
            <div class="preview_img small_sm"></div>
        </div>

        <div class="toggles">
            <span title="4:3比例"<?php if ($spec==1) {?> class="on"<?php }?> onclick="toggle(this, 4/3)">4:3</span>
            <span title="3:2比例"<?php if ($spec==2) {?> class="on"<?php }?> onclick="toggle(this, 3/2)">3:2</span>
            <span title="1:1比例"<?php if ($spec==3) {?> class="on"<?php }?> onclick="toggle(this, 1/1)">1:1</span>
            <span title="2:3比例"<?php if ($spec==4) {?> class="on"<?php }?> onclick="toggle(this, 2/3)">2:3</span>
            <span title="自由裁剪" onclick="toggle(this, NaN)">自由</span>
        </div>

        <div class="toggles">
            <span title="放大" class="plus"><i class="fa fa-search-plus"></i></span>
            <span title="缩小" class="minus"><i class="fa fa-search-minus"></i></span>
            <span title="操作" class="crop"><i class="fa fa-check"></i></span>
            <span title="清除" class="clear"><i class="fa fa-times"></i></span>
            <span title="重置" class="reset"><i class="fa fa-refresh"></i></span>
        </div>

        <div class="toggles">
            <span title="逆时针旋转90度" class="rotatex90" style="width:25%;"><i class="fa fa-rotate-left"></i></span>
            <span title="逆时针旋转270度" class="rotatex270" style="width:25%;"><i class="fa fa-rotate-left"></i></span>
            <span title="左右旋转" class="scalex" style="width:25%;"><i class="fa fa-arrows-h"></i></span>
            <span title="上下旋转" class="scaley" style="width:25%;"><i class="fa fa-arrows-v"></i></span>
        </div>

        <div class="toggles">
            <span title="左移" class="left" style="width:25%;"><i class="fa fa-arrow-left"></i></span>
            <span title="右移" class="right" style="width:25%;"><i class="fa fa-arrow-right"></i></span>
            <span title="上移" class="up" style="width:25%;"><i class="fa fa-arrow-up"></i></span>
            <span title="下移" class="down" style="width:25%;"><i class="fa fa-arrow-down"></i></span>
        </div>
    </div>

    <div class="clearfix"></div>
    <form action="" method="post" id="myform">
        <input type="hidden" name="data[aid]" value="<?php echo $aid;?>">
        <input type="hidden" value="" name="data[x]" id="dataX" />
        <input type="hidden" value="" name="data[y]" id="dataY" />
        <input type="hidden" value="" name="data[w]" id="dataWidth" />
        <input type="hidden" value="" name="data[h]" id="dataHeight" />
        <input type="hidden" value="" name="data[r]" id="dataRotate" />
        <input type="hidden" value="" name="data[sx]" id="dataScaleX" />
        <input type="hidden" value="" name="data[sy]" id="dataScaleY" />
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $(".preview_img").html('<img src="' + $("#image").attr('src')  + '">');
        $('.container_img > img').cropper({
            aspectRatio: <?php echo $spec;?>,
            viewMode : 1,
            preview: '.preview_img', 
            crop: function(data) {
                $("#dataX").val(Math.round(data.detail.x));
                $("#dataY").val(Math.round(data.detail.y));
                $("#dataWidth").val(Math.round(data.detail.width));
                $("#dataHeight").val(Math.round(data.detail.height));
                $("#dataRotate").val(Math.round(data.detail.rotate));
                $("#dataScaleX").val(Math.round(data.detail.scaleX));
                $("#dataScaleY").val(Math.round(data.detail.scaleY));
            }
        })
    });

    function dosbumit(){
        dr_tips('', '正在处理中……', 999999);
        $.ajax({
            type: 'POST',
            url: '<?php echo SELF;?>?m=content&c=content&a=public_crop&module=<?php echo $module;?>&catid=<?php echo $catid;?>', 
            data: $("#myform").serialize(),
            dataType: "json", 
            success: function (res) {
                // token 更新
                if (res.token) {
                    var token = res.token;
                    $("#myform input[name='"+token.name+"']").val(token.value);
                }
                dr_tips(res.code, res.msg);
                if(res.code == 1){
                    dialogOpener.$("#<?php echo $input;?>").val(res.data.aid);
                    <?php if ($files_row) {?>
                    dialogOpener.$("#<?php echo $files_row;?>").find('.preview a').attr("href", "javascript:preview('"+res.data.filepath+"')");
                    dialogOpener.$("#<?php echo $files_row;?>").find('.preview img').attr("src", res.data.filepath);
                    <?php } else {?>
                    dialogOpener.$("#<?php echo $preview;?>").attr("src", res.data.filepath);
                    <?php }?>
                    ownerDialog.close();
                }
            }
        })
        return false;       
    }

    function toggle(obj, n) {
        $(obj).addClass('on').siblings().removeClass('on');
        $('#image').cropper('setAspectRatio', n);
    }

    $('.plus').on('click', function(){
        $('#image').cropper("zoom", 0.1);
    });
    $('.minus').on('click', function(){
        $('#image').cropper("zoom", -0.1);
    });
    $('.crop').on('click', function(){
        $('#image').cropper("crop");
    });
    $('.clear').on('click', function(){
        $('#image').cropper("clear");
    });
    $('.reset').on('click', function(){
        $('#image').cropper('reset');
    });
    $('.rotatex90').on('click', function(){
        $('#image').cropper('rotate', -90);
    });
    $('.rotatex270').on('click', function(){
        $('#image').cropper('rotate', -270);
    });
    $('.scalex').on('click', function(){
        if ($(this).attr('data-option')==-1) {
            $(this).attr('data-option', 1);
            $('#image').cropper("scaleX", 1);
        } else {
            $(this).attr('data-option', -1);
            $('#image').cropper("scaleX", -1);
        }
    });
    $('.scaley').on('click', function(){
        if ($(this).attr('data-option')==-1) {
            $(this).attr('data-option', 1);
            $('#image').cropper("scaleY", 1);
        } else {
            $(this).attr('data-option', -1);
            $('#image').cropper("scaleY", -1);
        }
    });
    $('.left').on('click', function(){
        $('#image').cropper("move", -10, 0);
    });
    $('.right').on('click', function(){
        $('#image').cropper("move", 10, 0);
    });
    $('.up').on('click', function(){
        $('#image').cropper("move", 0, -10);
    });
    $('.down').on('click', function(){
        $('#image').cropper("move", 0, 10);
    });
</script>
</body>
</html>