<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
.pull-left {float: left!important;}
.pull-right {float: right!important;}
#iframecontent{position:relative;overflow:hidden;padding: 0;margin: 0;}
#iframecontent iframe{border:none;}
.openclose{width: 8px;position: relative;background: #f5f6f8;}
#openclose{display:inline-block;width: 8px;height:24px;position:absolute;top:50%;left:0px;}
.treelistframe{border-right: 3px solid #e6e8ed;}
</style>
<section id="iframecontent">
  <section class="treelistframe pull-left">
    <iframe width="180px" name="treemain" id="treemain" scrolling="auto" height="auto" allowtransparency="true" frameborder="0" src="?m=content&c=content&a=public_categorys&type=add&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo dr_get_csrf_token();?>"></iframe>
  </section>
  <section class="openclose pull-left">
    <a href="javascript:OpenClose();" id="openclose" onmouseover="layer.tips('<?php echo L('spread_or_closed', '', 'admin');?>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-chevron-left"></i></a>
  </section>
  <section id="iframecontent">
    <iframe width="100%" name="right" id="right" scrolling="auto" height="auto" allowtransparency="true" frameborder="0" src="?m=content&c=content&a=public_init&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo dr_get_csrf_token();?>"></iframe>
  </section>
</section>
<script type="text/javascript">
var iframeWindowSize = function() {
    return ["Height", "Width"].map(function(name) {
        return window["inner" + name] || document.compatMode === "CSS1Compat" && document.documentElement["client" + name] || document.body["client" + name]
    })
}
window.onload = function() {
    if (!+"\v1" && !document.querySelector) {
        document.body.onresize = iframeresize
    } else {
        window.onresize = iframeresize
    }
    function iframeresize() {
        iframeSize();
        return false
    }
}
function iframeSize() {
    var str = iframeWindowSize();
    var pxstrs = new Array();
    iframestrs = str.toString().split(",");
    var heights = iframestrs[0],Body = $('body');
    if (iframestrs[1] < 980) {
        Body.attr('scroll', '');
    } else {
        Body.attr('scroll', 'no');
    }
    $('#treemain').height(heights-5);
    $('#right').height(heights-5);
    $('.openclose').height(heights);
    iframeWindowSize();
}
iframeSize();
function OpenClose() {
    if($("#openclose").data('clicknum')==1) {
        $(".treelistframe").show();
        $("#openclose").children('.fa').addClass("fa-chevron-left").removeClass("fa-chevron-right");
        $("#openclose").data('clicknum', 0);
    } else {
        $(".treelistframe").hide();
        $("#openclose").children('.fa').addClass("fa-chevron-right").removeClass("fa-chevron-left");
        $("#openclose").data('clicknum', 1);
    }
    return false;
}
</script>
</body>
</html>