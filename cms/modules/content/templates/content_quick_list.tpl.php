<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
$menu_data = $this->menu_db->get_one(array('name' => 'content_manage', 'm' => 'content', 'c' => 'content', 'a' => 'init'));?>
<style type="text/css">
#search_div{ position:absolute; top:23px; border:1px solid #dfdfdf; text-align:left; padding:1px; left:89px;*left:88px; width:263px;*width:260px; background-color:#FFF; display:none; font-size:12px}
#search_div li{line-height:24px;}
#search_div li a{padding-left:6px;display:block}
#search_div li a:hover{background-color:#e2eaff}
</style>
<div class="showMsg" style="text-align:center">
    <h5><?php echo L('quick_into');?></h5>
    <div class="content">
    <input type="text" size="41" id="cat_search" value="<?php echo L('search_category');?>" onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;">
    <ul id="search_div"></ul>
    </div>
</div>
<script type="text/javascript">
<!--
$(document).ready(function() {
    $('#cat_search').keyup(function(){
        var value = $("#cat_search").val();
        if (value.length > 0){
            $.getJSON('?m=admin&c=category&a=public_ajax_search', {catname: value}, function(data){
                if (data != null) {
                    var str = '';
                    $.each(data, function(i,n){
                        if(n.type=='0') {
                            str += '<li><a href="?m=content&c=content&a=init&menuid=<?php echo $menu_data['id']?>&catid='+n.catid+'&pc_hash='+pc_hash+'">'+n.catname+'</a></li>';
                        } else {
                            str += '<li><a href="?m=content&c=content&a=add&menuid=<?php echo $menu_data['id']?>&catid='+n.catid+'&pc_hash='+pc_hash+'">'+n.catname+'</a></li>';
                        }
                    });
                    $('#search_div').html(str);
                    $('#search_div').show();
                } else {
                    $('#search_div').hide();
                }
            });
        } else {
            $('#search_div').hide();
        }
    });
})
//-->
</script>
</body>
</html>