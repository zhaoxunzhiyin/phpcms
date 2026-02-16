<?php
defined('IS_ADMIN') or exit('No permission resources.');
$tips = $cname ? ' COMMENT \''.$cname.'\'' : '';
$defaultvalue = isset($setting['defaultvalue']) ? $setting['defaultvalue'] : '';
//正整数 UNSIGNED && SIGNED
$minnumber = isset($setting['minnumber']) ? $setting['minnumber'] : 1;
$decimaldigits = isset($setting['decimaldigits']) ? $setting['decimaldigits'] : '';

switch($field_type) {
	case 'varchar':
		if(!$maxlength) $maxlength = 255;
		$maxlength = min($maxlength, 255);
		$sql = "ALTER TABLE `$tablename` ADD `$field` VARCHAR( $maxlength ) NOT NULL DEFAULT '$defaultvalue' $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;

	case 'tinyint':
		if(!$maxlength) $maxlength = 3;
		$minnumber = intval($minnumber);
		$defaultvalue = intval($defaultvalue);
		$sql = "ALTER TABLE `$tablename` ADD `$field` TINYINT( $maxlength ) ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;
	
	case 'number':
		$minnumber = intval($minnumber);
		$defaultvalue = $decimaldigits == 0 ? intval($defaultvalue) : floatval($defaultvalue);
		$sql = "ALTER TABLE `$tablename` ADD `$field` ".($decimaldigits == 0 ? 'INT' : 'FLOAT')." ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;

	case 'smallint':
		$minnumber = intval($minnumber);
		$sql = "ALTER TABLE `$tablename` ADD `$field` SMALLINT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;

	case 'int':
		$minnumber = intval($minnumber);
		$defaultvalue = intval($defaultvalue);
		$sql = "ALTER TABLE `$tablename` ADD `$field` INT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;

	case 'mediumint':
		$minnumber = intval($minnumber);
		$defaultvalue = intval($defaultvalue);
		$sql = "ALTER TABLE `$tablename` ADD `$field` INT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;

	case 'mediumtext':
		$sql = "ALTER TABLE `$tablename` ADD `$field` MEDIUMTEXT NOT NULL $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;
	
	case 'text':
		$sql = "ALTER TABLE `$tablename` ADD `$field` TEXT NOT NULL $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;

	case 'date':
		$sql = "ALTER TABLE `$tablename` ADD `$field` DATE NULL $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;
	
	case 'datetime':
		$sql = "ALTER TABLE `$tablename` ADD `$field` DATETIME NULL $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;
	
	case 'timestamp':
		$sql = "ALTER TABLE `$tablename` ADD `$field` TIMESTAMP NOT NULL $tips";
		if (!$unrunsql) $this->db->query($sql);
	break;
	//特殊自定义字段
	case 'pages':
		$this->db->query("ALTER TABLE `$tablename` ADD `paginationtype` TINYINT( 1 ) NOT NULL DEFAULT '0' $tips");
		$this->db->query("ALTER TABLE `$tablename` ADD `maxcharperpage` MEDIUMINT( 6 ) NOT NULL DEFAULT '0' $tips");
	break;
	case 'readpoint':
		$defaultvalue = intval($defaultvalue);
		$this->db->query("ALTER TABLE `$tablename` ADD `readpoint` smallint(5) unsigned NOT NULL default '$defaultvalue' $tips");
		$this->db->query("ALTER TABLE `$tablename` ADD `paytype` tinyint(1) unsigned NOT NULL default '0' $tips");
	break;
}
?>