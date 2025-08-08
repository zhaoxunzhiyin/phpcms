<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<link href="<?php echo JS_PATH?>codemirror/lib/codemirror.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH?>codemirror/theme/neat.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH?>codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var myTextArea = document.getElementById('file_code');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme: "neat",
            mode: 'javascript'
        });
    });

    var getWindowSize = function(){
        return ["Height","Width"].map(function(name){
            return window["inner"+name] ||
                document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
        });
    }

    function wSize(){
        var str=getWindowSize();
        var strs= new Array(); //定义一数组
        strs=str.toString().split(","); //字符分割
        var heights = strs[0]-140,Body = $('body');
        $('#file_code').height(heights);
        $('.CodeMirror').height(heights);
    }

    $(function(){
        wSize();
    });

</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger" style="margin-top: 0px;">
    <p><?php if (isset($func)) {?>
<?php echo L('characteristic_function')?>：<?php
if ($func) {
    foreach ($func as $val) {
        if($val) {
            echo '<a class="btn green">'.$val.'</a> ';
        }
    }
}
}
if (isset($code)) {?>
<?php echo L('characteristic_key')?>：<?php
if($code) {
    foreach ($code as $val) {
        if($val) {
            echo '<a class="btn green">'.new_html_special_chars($val).'</a> ';
        }
    }
}
}?></p>
</div>
<div class="form-group">
    <textarea id="file_code" name="code"><?php echo new_html_special_chars($html);?></textarea>
</div>
</div>
</div>
</div>
</body>
</html>