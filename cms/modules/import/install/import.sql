DROP TABLE IF EXISTS `cms_import`;
CREATE TABLE `phpcms_import` (
  `id` smallint(4) NOT NULL auto_increment,
  `type` varchar(20) default NULL,
  `import_name` varchar(30) NOT NULL,
  `desc` varchar(50) default NULL,
  `addtime` int(10) NOT NULL,
  `last_keyid` int(10) default NULL,
  `lastinputtime` int(10) default NULL,
  `status` smallint(2) default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;