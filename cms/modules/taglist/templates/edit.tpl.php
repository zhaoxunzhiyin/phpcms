<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form action="?m=taglist&c=taglist&a=edit&id=<?php echo $id;?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
    <tr>
        <th width="20%">关键字:</th>
        <td><input type="text" name="tag[keyword]" id="keyword" size="30" class="input-text" value="<?php echo $keyword;?>"></td>
    </tr>
    <tr>
        <th width="20%">拼音:</th>
        <td><input type="text" name="tag[pinyin]" id="pinyin" size="30" class="input-text" value="<?php echo $pinyin;?>"></td>
    </tr>
</table>
</form>
</div>
</body>
</html>

