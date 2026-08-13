DROP TABLE IF EXISTS `cms_customfield`;
CREATE TABLE IF NOT EXISTS `cms_customfield` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `siteid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `description` char(50) NOT NULL,
  `name` char(30) NOT NULL,
  `val` mediumtext NOT NULL,
  `conf` char(255) NOT NULL,
  `listorder` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos` (`name`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;