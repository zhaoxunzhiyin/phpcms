<?php
defined('IS_ADMIN') or exit('No permission resources.');?>
<div id="cms_page_trace" style="position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 10001; color: #000;text-align:left;font-family:'微软雅黑';">
    <div id="cms_page_trace_tab" style="display: none;background:white;margin:0;height: 250px;">
        <div id="cms_page_trace_tab_tit" style="height:32px;padding:0px 12px;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px; background: #efefef;">
            <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">基本</span>
            <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">文件</span>
            <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">错误</span>
            <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">SQL</span>
            <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">调试</span>
        </div>
        <div id="cms_page_trace_tab_cont" style="overflow:auto;height:212px;padding:0;line-height: 24px">
            <?php foreach ($page_trace as $info) {?>
            <div style="display:none; table-layout: fixed; word-wrap:break-word; word-break:break-all;">
                <ol style="padding: 0; margin:0">
                    <?php
                    if (is_array($info)) {
                        foreach ($info as $k => $val) {
                            echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:2px 16px">' . (is_numeric($k) ? '' : $k.' : ') .print_r($val,true). '</li>';
                        }
                    }
                    ?>
                </ol>
                <p style="height:32px;"> </p>
            </div>
            <?php }?>
        </div>
    </div>
    <div id="cms_page_trace_close" style="display:none;text-align:right;height:15px;position:absolute;top:10px;right:12px;cursor:pointer;"> <b style="font-size:28px; line-height: 14px; ">×</b> </div>
</div>
<div id="cms_page_trace_open" style="height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:0;right:0;z-index: 10001; color:#000;line-height:30px;cursor:pointer;">
    <div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px"><?php echo pc_base::load_sys_class('debug')::spent();?>s </div> 
</div>
<script type="text/javascript">
(function(){
    var tab_tit  = document.getElementById('cms_page_trace_tab_tit').getElementsByTagName('span');
    var tab_cont = document.getElementById('cms_page_trace_tab_cont').getElementsByTagName('div');
    var open     = document.getElementById('cms_page_trace_open');
    var close    = document.getElementById('cms_page_trace_close').children[0];
    var trace    = document.getElementById('cms_page_trace_tab');
    var cookie   = document.cookie.match(/cms_show_page_trace=(\d\|\d)/);
    var history  = (cookie && typeof cookie[1] != 'undefined' && cookie[1].split('|')) || [0,0];
    open.onclick = function(){
        trace.style.display = 'block';
        this.style.display = 'none';
        close.parentNode.style.display = 'block';
        history[0] = 1;
        document.cookie = 'cms_show_page_trace='+history.join('|')
    }
    close.onclick = function(){
        trace.style.display = 'none';
        this.parentNode.style.display = 'none';
        open.style.display = 'block';
        history[0] = 0;
        document.cookie = 'cms_show_page_trace='+history.join('|')
    }
    for(var i = 0; i < tab_tit.length; i++){
        tab_tit[i].onclick = (function(i){
            return function(){
                for(var j = 0; j < tab_cont.length; j++){
                    tab_cont[j].style.display = 'none';
                    tab_tit[j].style.color = '#999';
                }
                tab_cont[i].style.display = 'block';
                tab_tit[i].style.color = '#000';
                history[1] = i;
                document.cookie = 'cms_show_page_trace='+history.join('|')
            }
        })(i)
    }
    parseInt(history[0]) && open.click();
    tab_tit[history[1]].click();
})();
//增加上下拖动
//获取元素
var cms_page_trace_tab = document.getElementById('cms_page_trace_tab');
var cms_page_trace_tab_cont = document.getElementById('cms_page_trace_tab_cont');
var title_mov = document.getElementById('cms_page_trace_tab_tit');
var cms_page_trace_tab_height = parseInt(cms_page_trace_tab.style.height);
var cms_page_trace_tab_cont_height = parseInt(cms_page_trace_tab_cont.style.height);
var x = 0;
var y = 0;
var l = 0;
var t = 0;
var isDown = false;
//鼠标按下事件
title_mov.onmousedown = function(e) {
    //获取x坐标和y坐标
    x = e.clientX;
    y = e.clientY;
    //获取左部和顶部的偏移量
    l = title_mov.offsetLeft;
    t = title_mov.offsetTop;
    //开关打开
    isDown = true;
    //设置样式  
    title_mov.style.cursor = 'move';
    cms_page_trace_tab_height = parseInt(cms_page_trace_tab.style.height);
    cms_page_trace_tab_cont_height = parseInt(cms_page_trace_tab_cont.style.height);
}
//鼠标移动
window.onmousemove = function(e) {
    if (isDown == false) {
        return;
    }
    //console.log(e.target);
    if(e.target===title_mov){ 
        //获取x和y
        var nx = e.clientX;
        var ny = e.clientY;
        //计算移动后的左偏移量和顶部的偏移量
        var nl = nx - (x - l);
        var nt = ny - (y - t);

        title_mov.style.left = nl + 'px';
        title_mov.style.top = nt + 'px';
        cms_page_trace_tab.style.height= cms_page_trace_tab_height-nt + 'px'; 
        cms_page_trace_tab_cont.style.height= cms_page_trace_tab_cont_height-nt + 'px'; 
    } else {
        isDown = false;
    }
}
//鼠标抬起事件
title_mov.onmouseup = function() {
    //开关关闭
    isDown = false;
    title_mov.style.cursor = 'default';
}
</script>