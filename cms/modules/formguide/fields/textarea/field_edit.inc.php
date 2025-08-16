<?php 
if(!$maxlength) $maxlength = 255;
$maxlength = min($maxlength, 255);
$tips = $cname ? ' COMMENT \''.$cname.'\'' : '';
$fieldtype = $issystem ? 'CHAR' : 'VARCHAR';
$sql = "ALTER TABLE `$tablename` CHANGE `$field` `$field` $fieldtype( $maxlength ) NOT NULL DEFAULT '$defaultvalue' $tips";
$db->query($sql);
?>