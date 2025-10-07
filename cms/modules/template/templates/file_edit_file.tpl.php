<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');?>
<link href="<?php echo JS_PATH;?>codemirror/lib/codemirror.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH;?>codemirror/theme/neat.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>codemirror/mode/<?php echo $file_js;?>" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>codemirror/mode/xml/xml.js" type="text/javascript"></script>
<style type="text/css">
.pt{margin-top: 4px;}
</style>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
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

            <div class="form-group">
                <label class="control-label col-md-2"><?php echo L('文件路径');?></label>
                <div class="col-md-10">
                    <p class="form-control-static"><?php echo $local?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2"><?php echo L('文件别名');?></label>
                <div class="col-md-10">
                    <input type="text" class="form-control" id="cname" name="cname" value="<?php echo htmlspecialchars($cname);?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2"><?php echo L('内容编辑');?></label>
                <div class="col-md-10">
                    <div class="col-md-10">
                        <textarea name="file_code" id="file_code"><?php echo $data;?></textarea>
                        <div class="bk10"></div>
                        标题截取：从20个字符开始到100个字符：<a href="javascript:;" onClick="javascript:insertText('{str_cut($r[\'title\'], \'20,100\', \'...\')}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{str_cut($r['title'], '20,100', '...')}</a>前100字符：<a href="javascript:;" onClick="javascript:insertText('{str_cut($r[\'title\'], \'100\', \'...\')}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{str_cut($r['title'], '100', '...')}</a><br />
                        标题样式：随机颜色关闭：
                        <a href="javascript:;" onClick="javascript:insertText('{title_style($r[\'style\'])}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{title_style($r['style'])}</a>随机颜色开启：<a href="javascript:;" onClick="javascript:insertText('{title_style($r[\'style\'], 1)}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{title_style($r['style'], 1)}</a><br />
                        时间格式：<a href="javascript:;" onClick="javascript:insertText('{wordtime($r[\'inputtime\'])}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{wordtime($r['inputtime'])}</a>显示：刚刚、几秒前、几分钟前、几小时前、几天前、几星期前、几个月前、几年前<br />
                        时间格式：<a href="javascript:;" onClick="javascript:insertText('{formatdate($r[\'inputtime\'])}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{formatdate($r['inputtime'])}</a>显示：刚刚、几秒前、几分钟前、几小时前、几天前、几星期前、几个月前、几年前<br />
                        时间格式：<a href="javascript:;" onClick="javascript:insertText('{mtime($r[\'inputtime\'])}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{mtime($r['inputtime'])}</a>显示：今天08:00、昨天08:00、前天08:00<br />
                        时间格式：<a href="javascript:;" onClick="javascript:insertText('{mdate($r[\'inputtime\'])}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{mdate($r['inputtime'])}</a>显示：刚刚、几分钟前、几小时前<br />
                        时间格式：<a href="javascript:;" onClick="javascript:insertText('{timediff(date(\'Y-m-d H:i:s\',$r[\'inputtime\']),date(\'Y-m-d H:i:s\'))}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{timediff(date('Y-m-d H:i:s',$r['inputtime']),date('Y-m-d H:i:s'))}</a>显示：1天1小时1分钟1秒<br />
                        友好的时间：<a href="javascript:;" onClick="javascript:insertText('{dr_fdate($r[\'inputtime\'], \'Y-m-d\')}')" class="btn blue btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{dr_fdate($r['inputtime'], 'Y-m-d')}</a>
                        <div class="bk10"></div>
                        <div id="html_result"></div>
                        <?php if ($is_write==0){echo '<font color="red">'.L("file_does_not_writable").'</font>';}?>
                    </div>
                    <div class="col-md-2">
                        <h3 class="f14"><?php echo L('common_variables')?></h3>
                        <div class="bk10"></div>
                        <a href="javascript:;" onClick="javascript:insertText('{CSS_PATH}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{CSS_PATH}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{JS_PATH}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{JS_PATH}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{IMG_PATH}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{IMG_PATH}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{APP_PATH}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{APP_PATH}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{get_siteid()}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('获取站点ID')?>">{get_siteid()}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{loop $data $n $r}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{loop $data $n $r}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{$r[\'url\']}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{$r['url']}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{$r[\'title\']}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{$r['title']}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{$r[\'thumb\']}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{$r['thumb']}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{clearhtml($r[\'description\'])}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{clearhtml($r['description'])}</a><br />
                        <a href="javascript:;" onClick="javascript:insertText('{code2html($r[\'content\'])}')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('click_into')?>" title="<?php echo L('click_into')?>">{code2html($r['content'])}</a><br />
                        <?php if (is_array($file_t_v[$file_t])) { foreach($file_t_v[$file_t] as $k => $v) {?>
                        <a href="javascript:;" onClick="javascript:insertText('<?php echo $k?>')" class="btn yellow btn-sm pt tooltips" data-container="body" data-placement="top" data-original-title="<?php echo $v ? $v :L('click_into')?>" title="<?php echo L('click_into')?>"><?php echo str_replace('\\', '', $k)?></a><br />
                        <?php } }?>
                    </div>
                </div>
            </div>

        </div>
        
    </div>
</div>
    </div>
</div>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <label><button type="button" id="my_submit" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label>
        <?php if (module_exists('tag')) {?><label><button type="button" onClick="create_tag()" class="btn blue"> <i class="fa fa-plus"></i> <?php echo L('create_tag')?></button></label>
        <label><button type="button" onClick="select_tag()" class="btn dark"> <i class="fa fa-code"></i> <?php echo L('select_tag')?></button></label>
        <?php }?>
    </div>
</div>
</form>
</div>
</div>
</div>
<script type="text/javascript">
var myTextArea = document.getElementById('file_code');
var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
    lineNumbers: true,
    matchBrackets: true,
    styleActiveLine: true,
    theme: "neat",
    mode: '<?php echo $file_ext?>'
});
jQuery(document).ready(function() {
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
            data: {cname:$("#cname").val(), code: myCodeMirror.getValue(), pc_hash: pc_hash, <?php echo SYS_TOKEN_NAME;?>: $("#myform input[name='<?php echo SYS_TOKEN_NAME;?>']").val()},
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

function insertText(text) {
    myCodeMirror.replaceSelection(text);
}

function select_tag() {
    omnipotent('list','?m=tag&c=tag&a=lists',"<?php echo L('tag_list')?>",1,700,500);
}
</script>
</body>
</html>