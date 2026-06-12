<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="showMsg" style="text-align:center">
    <h5><?php echo L('generate_progress')?></h5>
    <div class="content">
        <?php echo $msg?>
    </div>
</div>
</body>
</html>