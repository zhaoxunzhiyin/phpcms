<?php
defined('IS_ADMIN') or exit('No permission resources.');
$tips = $cname ? ' COMMENT \''.$cname.'\'' : '';
$defaultvalue = isset($setting['defaultvalue']) ? $setting['defaultvalue'] : '';
$minnumber = isset($setting['minnumber']) ? $setting['minnumber'] : 1;
$decimaldigits = isset($setting['decimaldigits']) ? $setting['decimaldigits'] : '';

switch($field_type) {
	case 'varchar':
		if(!$maxlength) $maxlength = 255;
		$maxlength = min($maxlength, 255);
		$fieldtype = isset($issystem) ? 'CHAR' : 'VARCHAR';
		$sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` $fieldtype( $maxlength ) NOT NULL DEFAULT '$defaultvalue' $tips";
		$this->db->query($sql);
	break;

	case 'tinyint':
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' $tips");
	break;

	case 'number':
		$minnumber = intval($minnumber);
		$defaultvalue = $decimaldigits == 0 ? intval($defaultvalue) : floatval($defaultvalue);
		$sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` ".($decimaldigits == 0 ? 'INT' : 'FLOAT')." ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
		$this->db->query($sql);
	break;

	case 'smallint':
		$minnumber = intval($minnumber);
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` SMALLINT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL $tips");
	break;

	case 'int':
		$minnumber = intval($minnumber);
		$defaultvalue = intval($defaultvalue);
		$sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` INT ".($minnumber >= 0 ? 'UNSIGNED' : '')." NOT NULL DEFAULT '$defaultvalue' $tips";
		$this->db->query($sql);
	break;

	case 'mediumtext':
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` MEDIUMTEXT NOT NULL $tips");
	break;
	
	case 'text':
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` TEXT NOT NULL $tips");
	break;

	case 'date':
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` DATE NULL $tips");
	break;
	
	case 'datetime':
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` DATETIME NULL $tips");
	break;
	
	case 'timestamp':
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` TIMESTAMP NOT NULL $tips");
	break;
	//特殊自定义字段
	case 'pages':
		$this->db->query("ALTER TABLE `$tablename` CHANGE `paginationtype` `paginationtype` TINYINT( 1 ) NOT NULL DEFAULT '0' $tips");
		$this->db->query("ALTER TABLE `$tablename` CHANGE `maxcharperpage` `maxcharperpage` MEDIUMINT( 6 ) NOT NULL DEFAULT '0' $tips");
	break;
	case 'readpoint':
		$defaultvalue = intval($defaultvalue);
		$this->db->query("ALTER TABLE `$tablename` CHANGE `$oldfield` `readpoint` smallint(5) unsigned NOT NULL default '$defaultvalue' $tips");
		$this->db->query("ALTER TABLE `$tablename` CHANGE `paytype` `paytype` tinyint(1) unsigned NOT NULL default '0' $tips");
	break;

}
?>