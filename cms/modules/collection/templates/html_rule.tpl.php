<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div style="padding: 0px 0px 0px 20px;"><?php echo form::checkbox(self::$html_tag, '', 'name="html_rule"', '', '100')?><div class="bk15"></div><center><input type="button" value="<?php echo L('select_all')?>" class="button" onclick="rule('html_rule')"> <input type="button" class="button" value="<?php echo L('invert')?>" onclick="anti_selectall('html_rule')"></center></div>
<script type="text/javascript">
function rule(name) {
    $("input[name='"+name+"']").each(function() {
        $(this).attr("checked","checked");
        
    });
}
function anti_selectall(obj) {
    $("input[name='"+obj+"']").each(function(i,n){
        if (this.checked) {
            this.checked = false;
        } else {
            this.checked = true;
        }});
}
</script>
</body>
</html>