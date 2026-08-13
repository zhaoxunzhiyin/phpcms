<?php
defined('IS_ADMIN') or exit('No permission resources.');

$this->db->query("ALTER TABLE `$tablename` DROP `$field`");
?>