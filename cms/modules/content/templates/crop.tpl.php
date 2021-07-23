<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH;?>iconfont/iconfont.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>cropper/3.1.6/cropper.css?v=20191120">
<script type="text/javascript" src="<?php echo JS_PATH;?>cropper/3.1.6/cropper.js"></script>
</head>
<body>
<div class="container_box">
    <div class="container">
        <img id="image" src="<?php echo WEB_PATH.$filepath;?>">
    </div>

    <div class="con_right">
        <div class="preview">
            <div class="preview_img small_lg"></div>
            <div class="preview_img small_md"></div>
            <div class="preview_img small_sm"></div>
        </div>

        <div class="toggles">
            <span title="4:3比例"<?php if ($_GET['spec']==1) {?> class="on"<?php }?> onclick="toggle(this, 4/3)">4:3</span>
            <span title="3:2比例"<?php if ($_GET['spec']==2) {?> class="on"<?php }?> onclick="toggle(this, 3/2)">3:2</span>
            <span title="1:1比例"<?php if ($_GET['spec']==3) {?> class="on"<?php }?> onclick="toggle(this, 1/1)">1:1</span>
            <span title="2:3比例"<?php if ($_GET['spec']==4) {?> class="on"<?php }?> onclick="toggle(this, 2/3)">2:3</span>
            <span title="自由裁剪" onclick="toggle(this, NaN)">自由</span>
        </div>

        <!--<div class="toggles">
            <span title="逆时针旋转45度" class="rotate1"><i class="iconfont">&#xe66b;</i></span>
            <span title="顺时针旋转45度" class="rotate2"><i class="iconfont">&#xe66c;</i></span>
            <span title="左右旋转" class="about"><i class="iconfont">&#xe6fd;</i></span>
            <span title="上下旋转" class="updown"><i class="iconfont">&#xe6fc;</i></span>
            <span title="重置" class="reset"><i class="iconfont">&#xe68f;</i></span>
        </div>-->
    </div>

    <div class="clearfix"></div>
    <form  action="" method="post" id="myform">
        <input type="hidden" name="filepath" value="<?php echo $filepath;?>">
        <input type="hidden" value="" name="x" />
        <input type="hidden" value="" name="y" />
        <input type="hidden" value="" name="w" />
        <input type="hidden" value="" name="h" />
    </form>
    <input type="hidden" name="new_filename" id="new_filename">
</div>
<script type="text/javascript">
    init_crop();
    function init_crop(){
        $(".preview_img").html('<img src="' + $("#image").attr('src')  + '">');
        $('.container > img').cropper({
            aspectRatio: <?php echo $spec;?>,
            viewMode : 1,
            preview: '.preview_img', 
            crop: function(data) {
                $("input[name='x']").val(data.x);
                $("input[name='y']").val(data.y);
                $("input[name='w']").val(data.width);
                $("input[name='h']").val(data.height);
            }
        })
    }

    function dosbumit(){
		layer.msg('正在处理中……', {icon:16,shade:0.21,shadeClose:true,time:999999});
        $.ajax({
            type: 'POST',
            url: '<?php echo SELF;?>?m=content&c=content&a=public_crop&module=<?php echo $module;?>&catid=<?php echo $catid;?>', 
            data: $("#myform").serialize(),
            dataType: "json", 
            success: function (res) {
                if(res.code == 1){
                    $("#new_filename").val(res.data.filepath);
                    layer.msg(res.msg, {icon: 6,time:1000});
                    setTimeout(cropper_close, 1500);
                }else{
                    layer.alert(res.msg);
                }
            }
        })
        return false;       
    }

    function cropper_close(){
        var new_filename = $("#new_filename").val();
        dialogOpener.$("#<?php echo $input;?>").val(new_filename);
        dialogOpener.$("#<?php echo $preview;?>").attr("src", new_filename);
        ownerDialog.close();
    }

    function toggle(obj, n) {
        $(obj).addClass('on').siblings().removeClass('on');
        $('#image').cropper('setAspectRatio', n);
    }

    $('.rotate1').on('click', function(){
        $('#image').cropper('rotate', -45);
    });
    $('.rotate2').on('click', function(){
        $('#image').cropper('rotate', 45);
    });
    $('.about').on('click', function(){
        $('#image').cropper("scaleX", -1);
    });
    $('.updown').on('click', function(){
        $('#image').cropper("scaleY", -1);
    });
    $('.reset').on('click', function(){
        $('#image').cropper('reset');
    });
</script>
</body>
</html>