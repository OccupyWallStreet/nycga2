--
-- Database: Simple:Press Version 4.3.0
--

--
-- NOTE: Prefix used here is the default 'wp_'
--

-- --------------------------------------------------------



# Table:   wp_sfdefpermissions
# ------------------------------------------------------------

CREATE TABLE `wp_sfdefpermissions` (
  `permission_id` mediumint(8) unsigned NOT NULL auto_increment,
  `group_id` mediumint(8) unsigned NOT NULL default '0',
  `usergroup_id` mediumint(8) unsigned NOT NULL default '0',
  `permission_role` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`permission_id`),
  KEY `usergroup_id_idx` (`usergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfforums
# ------------------------------------------------------------

CREATE TABLE `wp_sfforums` (
  `forum_id` bigint(20) NOT NULL auto_increment,
  `forum_name` varchar(200) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `forum_seq` int(4) default NULL,
  `forum_desc` text,
  `forum_status` int(4) NOT NULL default '0',
  `forum_slug` text NOT NULL,
  `forum_rss` text,
  `forum_icon` varchar(25) default NULL,
  `post_id` bigint(20) default NULL,
  `topic_count` mediumint(8) default '0',
  `forum_rss_private` smallint(1) NOT NULL default '0',
  `topic_status_set` bigint(20) default '0',
  `post_count` mediumint(8) default '0',
  `post_ratings` smallint(1) NOT NULL default '0',
  `use_tags` smallint(1) NOT NULL default '1',
  `parent` bigint(20) NOT NULL default '0',
  `children` text,
  `forum_message` text,
  `in_sitemap` smallint(1) NOT NULL default '1',
  PRIMARY KEY  (`forum_id`),
  KEY `groupf_idx` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfgroups
# ------------------------------------------------------------

CREATE TABLE `wp_sfgroups` (
  `group_id` bigint(20) NOT NULL auto_increment,
  `group_name` text NOT NULL,
  `group_seq` int(4) default NULL,
  `group_desc` text,
  `group_rss` text,
  `group_icon` varchar(25) default NULL,
  `group_message` text,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sflinks
# ------------------------------------------------------------

CREATE TABLE `wp_sflinks` (
  `id` int(4) NOT NULL auto_increment,
  `post_id` int(20) default '0',
  `forum_id` int(20) default '0',
  `topic_id` int(20) default '0',
  `syncedit` smallint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sflog
# ------------------------------------------------------------

CREATE TABLE `wp_sflog` (
  `id` int(4) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL,
  `install_date` date NOT NULL,
  `release_type` varchar(15) default NULL,
  `version` varchar(10) NOT NULL,
  `build` int(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfmembers
# ------------------------------------------------------------

CREATE TABLE `wp_sfmembers` (
  `user_id` bigint(20) NOT NULL default '0',
  `display_name` varchar(100) default NULL,
  `pm` smallint(1) NOT NULL default '0',
  `moderator` smallint(1) NOT NULL default '0',
  `avatar` longtext,
  `signature` text,
  `posts` int(4) default NULL,
  `lastvisit` datetime default NULL,
  `subscribe` longtext,
  `buddies` longtext,
  `newposts` longtext,
  `checktime` datetime default NULL,
  `admin` smallint(1) NOT NULL default '0',
  `watches` longtext,
  `posts_rated` longtext,
  `admin_options` longtext,
  `user_options` longtext,
  `feedkey` varchar(36) default NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfmemberships
# ------------------------------------------------------------

CREATE TABLE `wp_sfmemberships` (
  `membership_id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `usergroup_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`membership_id`),
  KEY `user_id_idx` (`user_id`),
  KEY `usergroup_id_idx` (`usergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfmessages
# ------------------------------------------------------------

CREATE TABLE `wp_sfmessages` (
  `message_id` bigint(20) NOT NULL auto_increment,
  `sent_date` datetime NOT NULL,
  `from_id` bigint(20) default NULL,
  `to_id` bigint(20) default NULL,
  `title` varchar(200) default NULL,
  `message` text,
  `message_status` smallint(1) NOT NULL default '0',
  `inbox` smallint(1) NOT NULL default '1',
  `sentbox` smallint(1) NOT NULL default '1',
  `is_reply` smallint(1) NOT NULL default '0',
  `message_slug` text NOT NULL,
  `type` smallint(2) NOT NULL default '1',
  PRIMARY KEY  (`message_id`),
  KEY `from_id_idx` (`from_id`),
  KEY `to_id_idx` (`to_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfmeta
# ------------------------------------------------------------

CREATE TABLE `wp_sfmeta` (
  `meta_id` bigint(20) NOT NULL auto_increment,
  `meta_type` varchar(20) NOT NULL,
  `meta_key` varchar(100) default NULL,
  `meta_value` longtext,
  PRIMARY KEY  (`meta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfnotice
# ------------------------------------------------------------

CREATE TABLE `wp_sfnotice` (
  `id` varchar(30) NOT NULL,
  `item` varchar(15) default NULL,
  `message` longtext,
  `ndate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfoptions
# ------------------------------------------------------------

CREATE TABLE `wp_sfoptions` (
  `option_id` bigint(20) unsigned NOT NULL auto_increment,
  `option_name` varchar(64) NOT NULL default '',
  `option_value` longtext NOT NULL,
  PRIMARY KEY  (`option_name`),
  KEY `option_id` (`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfpermissions
# ------------------------------------------------------------

CREATE TABLE `wp_sfpermissions` (
  `permission_id` mediumint(8) unsigned NOT NULL auto_increment,
  `forum_id` mediumint(8) unsigned NOT NULL default '0',
  `usergroup_id` mediumint(8) unsigned NOT NULL default '0',
  `permission_role` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`permission_id`),
  KEY `usergroup_id_idx` (`usergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfpostratings
# ------------------------------------------------------------

CREATE TABLE `wp_sfpostratings` (
  `rating_id` bigint(20) NOT NULL auto_increment,
  `post_id` bigint(20) NOT NULL,
  `vote_count` bigint(20) NOT NULL,
  `ratings_sum` bigint(20) NOT NULL,
  `ips` longtext,
  `members` longtext,
  PRIMARY KEY  (`rating_id`),
  KEY `post_id_idx` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfposts
# ------------------------------------------------------------

CREATE TABLE `wp_sfposts` (
  `post_id` bigint(20) NOT NULL auto_increment,
  `post_content` text,
  `post_date` datetime NOT NULL,
  `topic_id` bigint(20) NOT NULL,
  `user_id` bigint(20) default NULL,
  `forum_id` bigint(20) NOT NULL,
  `guest_name` varchar(20) default NULL,
  `guest_email` varchar(50) default NULL,
  `post_status` int(4) NOT NULL default '0',
  `post_pinned` smallint(1) NOT NULL default '0',
  `post_index` mediumint(8) default '0',
  `post_edit` mediumtext,
  `poster_ip` varchar(39) NOT NULL,
  `comment_id` bigint(20) default NULL,
  PRIMARY KEY  (`post_id`),
  KEY `topicp_idx` (`topic_id`),
  KEY `forump_idx` (`forum_id`),
  KEY `user_id_idx` (`user_id`),
  FULLTEXT KEY `post_content` (`post_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfroles
# ------------------------------------------------------------

CREATE TABLE `wp_sfroles` (
  `role_id` mediumint(8) unsigned NOT NULL auto_increment,
  `role_name` varchar(50) NOT NULL default '',
  `role_desc` varchar(150) NOT NULL default '',
  `role_actions` longtext NOT NULL,
  PRIMARY KEY  (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfsettings
# ------------------------------------------------------------

CREATE TABLE `wp_sfsettings` (
  `setting_id` bigint(20) NOT NULL auto_increment,
  `setting_name` varchar(50) NOT NULL,
  `setting_value` longtext,
  `setting_date` datetime NOT NULL,
  PRIMARY KEY  (`setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sftagmeta
# ------------------------------------------------------------

CREATE TABLE `wp_sftagmeta` (
  `meta_id` bigint(20) NOT NULL auto_increment,
  `tag_id` bigint(20) default '0',
  `topic_id` bigint(20) default '0',
  `forum_id` bigint(20) default '0',
  PRIMARY KEY  (`meta_id`),
  KEY `tag_idx` (`tag_id`),
  KEY `topic_idx` (`topic_id`),
  KEY `forum_idx` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sftags
# ------------------------------------------------------------

CREATE TABLE `wp_sftags` (
  `tag_id` bigint(20) NOT NULL auto_increment,
  `tag_name` varchar(50) default NULL,
  `tag_slug` varchar(200) NOT NULL,
  `tag_count` bigint(20) default '0',
  PRIMARY KEY  (`tag_id`),
  FULLTEXT KEY `tag_name` (`tag_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sftopics
# ------------------------------------------------------------

CREATE TABLE `wp_sftopics` (
  `topic_id` bigint(20) NOT NULL auto_increment,
  `topic_name` varchar(200) NOT NULL,
  `topic_date` datetime NOT NULL,
  `topic_status` int(4) NOT NULL default '0',
  `forum_id` bigint(20) NOT NULL,
  `user_id` bigint(20) default NULL,
  `topic_pinned` smallint(1) NOT NULL default '0',
  `topic_subs` longtext,
  `topic_opened` bigint(20) NOT NULL default '0',
  `blog_post_id` bigint(20) NOT NULL default '0',
  `topic_slug` text NOT NULL,
  `post_id` bigint(20) default NULL,
  `post_count` mediumint(8) default '0',
  `topic_status_flag` bigint(20) default '0',
  `topic_watches` longtext,
  PRIMARY KEY  (`topic_id`),
  KEY `forumt_idx` (`forum_id`),
  FULLTEXT KEY `topic_name_idx` (`topic_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sftrack
# ------------------------------------------------------------

CREATE TABLE `wp_sftrack` (
  `id` bigint(20) NOT NULL auto_increment,
  `trackuserid` bigint(20) default '0',
  `trackname` varchar(50) NOT NULL,
  `trackdate` datetime NOT NULL,
  `forum_id` bigint(20) default NULL,
  `topic_id` bigint(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfusergroups
# ------------------------------------------------------------

CREATE TABLE `wp_sfusergroups` (
  `usergroup_id` mediumint(8) unsigned NOT NULL auto_increment,
  `usergroup_name` text NOT NULL,
  `usergroup_desc` text,
  `usergroup_is_moderator` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`usergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Table:   wp_sfwaiting
# ------------------------------------------------------------

CREATE TABLE `wp_sfwaiting` (
  `topic_id` bigint(20) NOT NULL,
  `forum_id` bigint(20) NOT NULL,
  `post_count` int(4) NOT NULL,
  `post_id` bigint(20) NOT NULL default '0',
  `user_id` bigint(20) default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id_idx` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;