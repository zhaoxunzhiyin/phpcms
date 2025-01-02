<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<div style="font-size:14px;line-height:25px;word-wrap: break-word;word-break: break-all;"><?php echo $content;?></div>
</div>
</body>
</html> 