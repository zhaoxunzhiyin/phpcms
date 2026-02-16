<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');?>
<link href="<?php echo JS_PATH;?>codemirror/lib/codemirror.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH;?>codemirror/theme/neat.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH;?>codemirror/addon/merge/merge.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>codemirror/mode/<?php echo $file_js;?>" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>codemirror/lib/diff_match_patch.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>codemirror/addon/merge/merge.js" type="text/javascript"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo str_replace(array(CMS_PATH,'\\'), array('','/'), TPLPATH).$this->style?>/<?php echo $dir?>/<?php echo $file?></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<div class="row myfbody">
        <div class="col-md-12">
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-green"><?php echo $name;?></span>
        </div>

        <div class="actions">
            <?php if(is_array($backups) && $backups) {?>
            <div class="btn-group">
                <a class="btn green-haze btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <?php echo L('历史文件');?>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu pull-right" style="max-height:400px;overflow-y: scroll;overflow-x: hidden;">
                    <li>
                        <a href="<?php echo $backups_url;?>"> <?php echo L('查看当前文件');?></a>
                    </li>
                    <li class="divider"> </li>
                    <?php foreach($backups as $i) {?>
                    <li>
                        <a href="<?php echo $backups_url;?>&bfile=<?php echo $i['id'];?>"> <?php echo dr_date($i['creat_at'], null, 'red');?></a>
                    </li>
                    <?php }?>
                    <li class="divider"> </li>
                    <li>
                        <a href="javascript:dr_load_ajax('<?php echo L('确定要删除吗？');?>', '<?php echo $backups_del;?>', 1);"> <?php echo L('清空历史文件');?></a>
                    </li>
                </ul>
            </div>
            <?php }?>
            <div class="btn-group">
                <a class="btn" href="<?php echo $reply_url;?>"> <i class="fa fa-mail-reply"></i> <?php echo L('返回列表');?></a>
            </div>
        </div>
    </div>
    <div class="portlet-body form">

        <div class="form-body">

            <div id="view"></div>

            <textarea style="display: none;" id="file_code1"><?php echo $data;?></textarea>
            <textarea style="display: none;" id="file_code2"><?php echo $diff_content;?></textarea>

            <div class="form-group" style="padding-top:30px">
                <div class="col-md-12" id="html_result"></div>
            </div>

        </div>
        
    </div>
</div>
    </div>
</div>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <?php if (module_exists('tag')) {?><label><button type="button" onClick="create_tag()" class="btn blue"> <i class="fa fa-plus"></i> <?php echo L('create_tag')?></button></label>
        <label><button type="button" onClick="select_tag()" class="btn dark"> <i class="fa fa-code"></i> <?php echo L('select_tag')?></button></label>
        <?php }?>
        <label><button type="button" id="my_submit" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label>
    </div>
</div>
</form>
</div>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {

    var value, orig1, orig2, dv, panes = 2, highlight = true, connect = null, collapse = false;
    var target = document.getElementById("view");
    target.innerHTML = "";

    value = $("#file_code1").val();
    orig2 = $("#file_code2").val();

    dv = CodeMirror.MergeView(target, {
        value: value,
        origLeft: null,
        orig: orig2,
        lineNumbers: true,
        matchBrackets: true,
        styleActiveLine: true,
        mode: '<?php echo $file_ext?>',
        highlightDifferences: highlight,
        connect: connect,
        collapseIdentical: collapse
    });

    function toggleDifferences() {
        dv.setShowDifferences(highlight = !highlight);
    }

    function mergeViewHeight(mergeView) {
        function editorHeight(editor) {
            if (!editor) return 0;
            return editor.getScrollInfo().height;
        }
        return Math.max(editorHeight(mergeView.leftOriginal()),
                editorHeight(mergeView.editor()),
                editorHeight(mergeView.rightOriginal()));
    }

    function resize(mergeView) {
        var height = mergeViewHeight(mergeView);
        for(;;) {
            if (mergeView.leftOriginal())
                mergeView.leftOriginal().setSize(null, height);
            mergeView.editor().setSize(null, height);
            if (mergeView.rightOriginal())
                mergeView.rightOriginal().setSize(null, height);

            var newHeight = mergeViewHeight(mergeView);
            if (newHeight >= height) break;
            else height = newHeight;
        }
        mergeView.wrap.style.height = height + "px";
    }

    $('#my_submit').click(function () {

        url = '?m=template&c=file&a=edit_file&style=<?php echo $this->style?>&dir=<?php echo $dir?>&file=<?php echo $file?>';

        var loading = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 1000
        });

        $("#html_result").html(' ... ');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: url,
            data: {code: dv.edit.getValue(), pc_hash: pc_hash, <?php echo SYS_TOKEN_NAME;?>: $("#myform input[name='<?php echo SYS_TOKEN_NAME;?>']").val()},
            success: function(json) {
                layer.close(loading);
                // token 更新
                if (json.token) {
                    var token = json.token;
                    $("#myform input[name='"+token.name+"']").val(token.value);
                }
                if (json.code == 1) {
                    dr_tips(1, json.msg);
                    setTimeout("window.location.reload(true)", 2000)
                } else {
                    dr_tips(0, '<?php echo L('模板语法解析错误')?>');
                    $("#html_result").html('<div class="alert alert-danger">'+json.msg+'</div>');
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, this, thrownError);
            }
        });
    });
});
function create_tag() {
    artdialog('add','?m=tag&c=tag&a=add&ac=js',"<?php echo L('create_tag')?>",700,500);
}

function select_tag() {
    omnipotent('list','?m=tag&c=tag&a=lists',"<?php echo L('tag_list')?>",1,700,500);
}
</script>
</body>
</html>