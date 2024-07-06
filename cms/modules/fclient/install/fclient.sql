DROP TABLE IF EXISTS `cms_fclient`;
CREATE TABLE IF NOT EXISTS `cms_fclient` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT '作者',
  `username` varchar(200) NOT NULL COMMENT '账号',
  `name` varchar(200) NOT NULL COMMENT '网站名称',
  `domain` varchar(220) NOT NULL COMMENT '网站域名',
  `sn` varchar(220) NOT NULL COMMENT '通信密钥',
  `money` decimal(10,2) unsigned NOT NULL COMMENT '续费价格',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态',
  `endtime` int(10) NOT NULL COMMENT '到期时间',
  `inputtime` int(10) NOT NULL COMMENT '开通时间',
  `setting` mediumtext NOT NULL COMMENT '相关信息',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `endtime` (`endtime`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT='客户站点信息';
INSERT INTO `cms_member_menu` VALUES ('', 'website', '0', 'fclient', 'member', 'init', 't=3', '0', '1', '', '');