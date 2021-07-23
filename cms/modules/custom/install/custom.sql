DROP TABLE IF EXISTS `cms_custom`;
CREATE TABLE `cms_custom` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `siteid` int(5) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` tinytext,
  `inputtime` int(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) TYPE=MyISAM;