CREATE TABLE IF NOT EXISTS `$tablename` (
  `userid` MEDIUMINT(8) unsigned NOT NULL,
   UNIQUE KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;