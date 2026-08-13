<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo L('预览');?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<?php echo load_css(CSS_PATH.'bootstrap/css/bootstrap.min.css');?>
<?php echo load_css(CSS_PATH.'font-awesome/css/font-awesome.min.css');?>
<?php echo load_css(CSS_PATH.'admin/css/style.css');?>
<?php echo load_css(CSS_PATH.'bootstrap-tagsinput.css');?>
<?php echo load_css(CSS_PATH.'table_form.css');?>
<?php echo load_css(CSS_PATH.'admin/css/my.css');?>
<?php echo load_js(JS_PATH.'Dialog/main.js');?>
<?php echo load_js(CSS_PATH.'bootstrap/js/bootstrap.min.js');?>
<style>
    body {
        min-height: 100%;
        padding-top: 0;
    }

    .navbar-preview {
        height: 50px;
        padding-left: 0;
        padding-right: 0;
        margin-bottom: 0;
        border-radius: 0;

        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1030;

        background: #40aae3;
        background: linear-gradient(270deg, #40aae3) center / cover;
        background-size: 800% 800%;
        -webkit-animation: navbaranimation 30s ease infinite;
        -moz-animation: navbaranimation 30s ease infinite;
        animation: navbaranimation 30s ease infinite;
    }

    .navbar-preview a {
        color: #fff;
    }

    .navbar-preview .col-xs-6 {
        height: 50px;
        line-height: 50px;
    }

    .iframe-preview {
        position: absolute;
        height: calc(100% - 55px);
        width: 100%;
        border: none;
        margin-top: 50px;
    }

    .iframe-preview-mobile {
        width: 400px;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 0 10px rgba(0, 0, 0, .085);
    }

    .nav-link {
        display: block;
        padding: .5rem 1rem;
    }

    .nav-preview {
        padding-left: 0;
        margin: 0;
        list-style: none;
        text-align: center;
    }

    .nav-preview li {
        display: inline;
    }

    .nav-preview li > a, .nav-preview li > span {
        display: inline-block;
        padding: 0 14px;
        font-size: 16px;
    }

    .nav-preview .popover-title {
        padding: 5px 14px;
        line-height: 28px;
    }

    @-webkit-keyframes navbaranimation {
        0% {
            background-position: 0% 50%
        }
        50% {
            background-position: 100% 50%
        }
        100% {
            background-position: 0% 50%
        }
    }

    @-moz-keyframes navbaranimation {
        0% {
            background-position: 0% 50%
        }
        50% {
            background-position: 100% 50%
        }
        100% {
            background-position: 0% 50%
        }
    }

    @keyframes navbaranimation {
        0% {
            background-position: 0% 50%
        }
        50% {
            background-position: 100% 50%
        }
        100% {
            background-position: 0% 50%
        }
    }

</style>
</head>

<body>

<?php if($demo == 'pc') { ?>
<iframe class="iframe-preview " width="100%" src="<?php echo $url;?>"></iframe>
<?php } else {?>
<iframe class="iframe-preview iframe-preview-mobile " width="100%" src="<?php echo $url;?>"></iframe>
<?php } ?>

<nav class="navbar navbar-preview">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-6 col-sm-4 text-left">

            </div>
            <div class="col-xs-6 col-sm-4 ">
                <ul class="nav-preview" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link btn-desktop <?php if($demo == 'pc') { ?>active<?php } ?>" href="javascript:" data-url="<?php echo $siteurl;?>" data-toggle="tooltip" data-placement="left" title="PC端浏览" data-original-title="PC端浏览">
                            <i class="fa fa-desktop"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-mobile <?php if($demo == 'mobile') { ?>active<?php } ?>" href="javascript:" data-url="<?php echo $sitemobileurl;?>" data-toggle="tooltip" data-placement="right" title="移动端预览" data-original-title="移动端预览">
                            <i class="fa fa-mobile fa-2x" style="font-size:1.4em;"></i>
                        </a>
                    </li>

                </ul>
            </div>
            <div class="col-xs-6 col-sm-4 text-right">

            </div>
        </div>
    </div>
</nav>

<script>
    $(function () {
        $(document).on('click', ".nav-link", function (e) {
            $(".iframe-preview").toggleClass("iframe-preview-mobile", $(this).hasClass("btn-mobile"));
            $(".nav-link").removeClass("active");
            $(this).addClass("active");
            $(".iframe-preview").attr('src', $(this).data('url'));
            return false;
        });
    });
</script>
</body>
</html>