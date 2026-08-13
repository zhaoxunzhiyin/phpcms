DROP TABLE IF EXISTS `cms_slider`;
CREATE TABLE IF NOT EXISTS `cms_slider` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `siteid` smallint(5) unsigned DEFAULT '1' COMMENT '站点id',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '所属位置',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '手机图片',
  `icon` varchar(255) NULL DEFAULT NULL COMMENT '图标标示',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `isshow` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `description` mediumtext COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`,`isshow`,`listorder`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;