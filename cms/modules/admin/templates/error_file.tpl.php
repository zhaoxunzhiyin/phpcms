<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<style>
body {padding: 25px 20px 10px;}
.note.note-danger {border-radius: 4px;border-left: 4px solid #f0868e;background-color: #fef7f8;color: #888;}
.note {margin: 0 0 20px;padding: 15px 30px 15px 15px;border-left: 5px solid #eee;border-radius: 0 4px 4px 0;}
</style>
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
<div class="note note-danger" style="margin-top: 0px;">
    <p><?php echo str_replace(CACHE_PATH, 'CACHE_PATH/', $file)?></p>
</div>
<div class="form-group">
    <textarea id="file_code" name="code"><?php echo $code?></textarea>
</div>
</body>
</html>