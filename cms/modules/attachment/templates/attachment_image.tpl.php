<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<link href="<?php echo JS_PATH;?>cropper/cropper.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH;?>cropper/css/main.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>cropper/cropper.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>cropper/js/main.js" type="text/javascript"></script>
<script type="text/javascript">
$(function (){
    var img = $('#image');
    var realWidth;//真实的宽度
    var realHeight;//真实的高度
    $("<img/>").attr("src", $(img).attr("src")).load(function() {
        $('#image_cc').html(this.width+'X'+this.height);
    });
});
</script>
<div  class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form class="form-horizontal" method="post" style="margin-top: -20px;" role="form" id="myform">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center margin-bottom-20">
                <a href="<?php echo $info['url'];?>" target="_blank"><?php echo $info['url'];?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="img-container">
                    <img id="image" src="<?php echo $info['url'];?>?<?php echo SYS_TIME;?>" alt="Picture">
                </div>
                <div class="margin-top-10 margin-bottom-10 text-center" style="clear: both">
                    <?php echo L('图片实际尺寸：');?><span id="image_cc"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="docs-preview clearfix">
                    <div class="img-preview preview-lg"></div>
                </div>

                <div class="docs-data">
                    <div class="input-group input-group-sm">
                        <label class="input-group-addon" for="dataX">X</label>
                        <input type="text" class="form-control" readonly name="data[x]" id="dataX" placeholder="x">
                        <span class="input-group-addon">px</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <label class="input-group-addon" for="dataY">Y</label>
                        <input type="text" class="form-control" readonly name="data[y]" id="dataY" placeholder="y">
                        <span class="input-group-addon">px</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <label class="input-group-addon" for="dataWidth">Width</label>
                        <input type="text" class="form-control" readonly name="data[w]" id="dataWidth" placeholder="width">
                        <span class="input-group-addon">px</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <label class="input-group-addon" for="dataHeight">Height</label>
                        <input type="text" class="form-control" readonly name="data[h]" id="dataHeight" placeholder="height">
                        <span class="input-group-addon">px</span>
                    </div>

                    <div class="input-group input-group-sm hide">
                        <label class="input-group-addon" for="dataRotate">Rotate</label>
                        <input type="text" class="form-control" name="data[r]" id="dataRotate" placeholder="rotate">
                        <span class="input-group-addon">deg</span>
                    </div>

                    <div class="input-group input-group-sm hide">
                        <label class="input-group-addon" for="dataScaleX">ScaleX</label>
                        <input type="text" class="form-control" name="data[sx]" id="dataScaleX" placeholder="scaleX">
                        <span class="input-group-addon">px</span>
                    </div>

                    <div class="input-group input-group-sm hide">
                        <label class="input-group-addon" for="dataScaleY">ScaleY</label>
                        <input type="text" class="form-control" name="data[sy]" id="dataScaleY" placeholder="scaleY">
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
                <div class="docs-buttons">

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;zoom&quot;, 0.1)">
              <span class="fa fa-search-plus"></span>
            </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;zoom&quot;, -0.1)">
              <span class="fa fa-search-minus"></span>
            </span>
                    </button>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="move" data-option="-10" data-second-option="0" title="Move Left">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;move&quot;, -10, 0)">
              <span class="fa fa-arrow-left"></span>
            </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="move" data-option="10" data-second-option="0" title="Move Right">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;move&quot;, 10, 0)">
              <span class="fa fa-arrow-right"></span>
            </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="-10" title="Move Up">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;move&quot;, 0, -10)">
              <span class="fa fa-arrow-up"></span>
            </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="10" title="Move Down">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;move&quot;, 0, 10)">
              <span class="fa fa-arrow-down"></span>
            </span>
                    </button>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;scaleX&quot;, -1)">
              <span class="fa fa-arrows-h"></span>
            </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;scaleY&quot;, -1)">
              <span class="fa fa-arrows-v"></span>
            </span>
                    </button>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="crop" title="Crop">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;crop&quot;)">
              <span class="fa fa-check"></span>
            </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="clear" title="Clear">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;clear&quot;)">
              <span class="fa fa-remove"></span>
            </span>

                        <button type="button" class="btn btn-primary" data-method="reset" title="Reset">
            <span class="docs-tooltip" data-toggle="tooltip" title="$().cropper(&quot;reset&quot;)">
              <span class="fa fa-refresh"></span>
            </span>
                        </button>
                    </button>
                </div>




            </div>

                <div class="docs-toggles">
                <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary active">
                        <input type="radio" class="sr-only" id="aspectRatio0" name="aspectRatio" value="1.7777777777777777">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 16 / 9">
              16:9
            </span>
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" class="sr-only" id="aspectRatio1" name="aspectRatio" value="1.3333333333333333">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 4 / 3">
              4:3
            </span>
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" class="sr-only" id="aspectRatio2" name="aspectRatio" value="1">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 1 / 1">
              1:1
            </span>
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" class="sr-only" id="aspectRatio3" name="aspectRatio" value="0.6666666666666666">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 2 / 3">
              2:3
            </span>
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" class="sr-only" id="aspectRatio4" name="aspectRatio" value="NaN">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: NaN">
              Free
            </span>
                    </label>
                </div>

                <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary active">
                        <input type="radio" class="sr-only" id="viewMode0" name="viewMode" value="0" checked>
                        <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 0">
              VM0
            </span>
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" class="sr-only" id="viewMode1" name="viewMode" value="1">
                        <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 1">
              VM1
            </span>
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" class="sr-only" id="viewMode2" name="viewMode" value="2">
                        <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 2">
              VM2
            </span>
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" class="sr-only" id="viewMode3" name="viewMode" value="3">
                        <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 3">
              VM3
            </span>
                    </label>
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