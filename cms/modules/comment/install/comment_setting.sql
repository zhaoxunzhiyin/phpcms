DROP TABLE IF EXISTS `cms_comment_setting`;
CREATE TABLE IF NOT EXISTS `cms_comment_setting` (
  `siteid` smallint(5) NOT NULL default '0' COMMENT '站点ID',
  `link` tinyint(1) default '0' COMMENT '是否显示按钮',
  `guest` tinyint(1) default '0' COMMENT '是否允许游客评论',
  `check` tinyint(1) default '0' COMMENT '是否需要审核',
  `code` tinyint(1) default '0' COMMENT '是否开启验证码',
  `add_point`  tinyint(3) UNSIGNED NULL DEFAULT 0 COMMENT '添加的积分数' ,
  `del_point`  tinyint(3) UNSIGNED NULL DEFAULT 0 COMMENT '扣除的积分数' ,
  PRIMARY KEY  (`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
INSERT INTO `cms_comment_setting` (`siteid`, `link`, `guest`, `check`, `code`, `add_point`, `del_point`) VALUES (1, 0, 0, 0, 0, 0, 0);
