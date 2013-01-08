SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `account_status` (
  `account_status_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `status_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hierarchical_order` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`account_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `achievement` (
  `achievement_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `modifed_datetime` datetime DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unlocked_actions` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `avatar_part` (
  `avatar_part_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `layer` tinyint(3) unsigned NOT NULL,
  `layer_order_id` smallint(5) unsigned NOT NULL,
  `grouping` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `gender` enum('Male','Female','Either') COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path_to_thumbnail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_inactive` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`avatar_part_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `banned_ip` (
  `banned_ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `user_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date_issued` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expiry_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`banned_ip`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `block` (
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `my_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `target_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `block_reason_id` tinyint(3) unsigned NOT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`my_user_id`,`target_user_id`),
  KEY `target_user_id` (`target_user_id`),
  KEY `block_reason_id` (`block_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (my_user_id) */;

CREATE TABLE `block_reason` (
  `block_reason_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `dropdown_choice` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `view_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`block_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `check_in` (
  `check_in_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  `location_id` mediumint(8) unsigned NOT NULL,
  `check_in_image_url` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `check_in_link_id` mediumint(8) unsigned NOT NULL,
  `check_in_link_url` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  KEY `check_in_id` (`check_in_id`),
  KEY `user_id` (`user_id`),
  KEY `location_id` (`location_id`),
  KEY `check_in_thumbnail_id` (`check_in_image_url`(255)),
  KEY `check_in_link_id` (`check_in_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `check_in_link` (
  `check_in_link_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `check_in_link_image_url` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`check_in_link_id`),
  KEY `check_in_link_image_id` (`check_in_link_image_url`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `conversation` (
  `conversation_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime NOT NULL,
  `creator_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_sender_id` mediumint(8) unsigned NOT NULL,
  `last_message` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`conversation_id`),
  KEY `creator_id` (`creator_id`),
  KEY `created_datetime` (`created_datetime`),
  KEY `modified_datetime` (`modified_datetime`),
  KEY `last_sender_id` (`last_sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (conversation_id) */;

CREATE TABLE `conversation_participant` (
  `conversation_id` int(9) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `participant_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_deleted` enum('1') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`conversation_id`,`participant_id`),
  KEY `created_datetime` (`created_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (conversation_id,participant_id) */;

CREATE TABLE `follow` (
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `my_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `target_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`my_user_id`,`target_user_id`),
  KEY `target_user_id` (`target_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (my_user_id) */;

CREATE TABLE `friendship` (
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `my_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `target_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_id` smallint(3) unsigned DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`my_user_id`,`target_user_id`),
  KEY `target_user_id` (`target_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (my_user_id) */;

CREATE TABLE `friend_request` (
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `my_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `target_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `request_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`my_user_id`,`target_user_id`),
  KEY `target_user_id` (`target_user_id`),
  KEY `request_code` (`request_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (my_user_id) */;

CREATE TABLE `location` (
  `location_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(320) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `phone_number` varchar(21) COLLATE utf8_unicode_ci NOT NULL,
  `website_url` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `email_address` varchar(320) COLLATE utf8_unicode_ci NOT NULL,
  `location_thumbnail_url` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `location_thumbnail_id` (`location_thumbnail_url`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `message` (
  `message_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` int(9) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `sender_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `body` varchar(17000) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `created_datetime` (`created_datetime`),
  KEY `conversation_id` (`conversation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (message_id) */;

CREATE TABLE `message_state` (
  `message_id` int(9) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `participant_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_read` enum('1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_deleted` enum('1') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`message_id`,`participant_id`),
  KEY `participant_id` (`participant_id`),
  KEY `created_datetime` (`created_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (message_id,participant_id) */;

CREATE TABLE `notification` (
  `notification_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `is_read` enum('1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` varchar(5000) COLLATE utf8_unicode_ci NOT NULL,
  `primary_link` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `notification_type_id` mediumint(8) unsigned NOT NULL,
  `notification_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `notification_type_id` (`notification_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (notification_id) */;

CREATE TABLE `notification_type` (
  `notification_type_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `notification_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `template` varchar(350) COLLATE utf8_unicode_ci NOT NULL,
  `application_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`notification_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `online_guest` (
  `php_session_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_active` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`php_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `online_member` (
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_active` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `page_hits` (
  `page_date` date NOT NULL DEFAULT '0000-00-00',
  `page_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `guest_hits` int(8) NOT NULL DEFAULT '0',
  `member_hits` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_date`,`page_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `php_session` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `access` int(10) unsigned DEFAULT NULL,
  `data` varchar(21000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `power` (
  `power_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hint` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`power_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `recent_visitor` (
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visitor_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`),
  KEY `visitor_id` (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `role` (
  `role_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `role_group_id` mediumint(8) unsigned NOT NULL,
  `is_user_selectable` tinyint(1) unsigned DEFAULT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hint` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`role_id`),
  KEY `role_group_id` (`role_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `role_group` (
  `role_group_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `is_group_single_role_limited` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`role_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `role_power` (
  `role_id` mediumint(8) unsigned NOT NULL,
  `power_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`role_id`,`power_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `role_setting` (
  `role_id` mediumint(8) unsigned NOT NULL,
  `setting_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`role_id`,`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `setting` (
  `setting_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `setting_group_id` mediumint(8) unsigned NOT NULL,
  `type` enum('boolean','string','number') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hint` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `is_on_by_default` tinyint(1) unsigned NOT NULL,
  `is_shown_at_signup` tinyint(1) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`setting_id`),
  KEY `setting_group_id` (`setting_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `setting_group` (
  `setting_group_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`setting_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sitemap` (
  `link_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent` smallint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ignore_in_sitemap` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_id` tinyint(2) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `theme` (
  `theme_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `theme_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thumb_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `css_file_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `translation_message` (
  `page_address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `message_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created_datetime` datetime NOT NULL,
  `en` text COLLATE utf8_unicode_ci,
  `ab` text COLLATE utf8_unicode_ci,
  `aa` text COLLATE utf8_unicode_ci,
  `af` text COLLATE utf8_unicode_ci,
  `ak` text COLLATE utf8_unicode_ci,
  `sq` text COLLATE utf8_unicode_ci,
  `am` text COLLATE utf8_unicode_ci,
  `ar` text COLLATE utf8_unicode_ci,
  `an` text COLLATE utf8_unicode_ci,
  `hy` text COLLATE utf8_unicode_ci,
  `as` text COLLATE utf8_unicode_ci,
  `av` text COLLATE utf8_unicode_ci,
  `ae` text COLLATE utf8_unicode_ci,
  `ay` text COLLATE utf8_unicode_ci,
  `az` text COLLATE utf8_unicode_ci,
  `bm` text COLLATE utf8_unicode_ci,
  `ba` text COLLATE utf8_unicode_ci,
  `eu` text COLLATE utf8_unicode_ci,
  `be` text COLLATE utf8_unicode_ci,
  `bn` text COLLATE utf8_unicode_ci,
  `bh` text COLLATE utf8_unicode_ci,
  `bi` text COLLATE utf8_unicode_ci,
  `bs` text COLLATE utf8_unicode_ci,
  `br` text COLLATE utf8_unicode_ci,
  `bg` text COLLATE utf8_unicode_ci,
  `my` text COLLATE utf8_unicode_ci,
  `ca` text COLLATE utf8_unicode_ci,
  `ch` text COLLATE utf8_unicode_ci,
  `ce` text COLLATE utf8_unicode_ci,
  `ny` text COLLATE utf8_unicode_ci,
  `zh` text COLLATE utf8_unicode_ci,
  `cv` text COLLATE utf8_unicode_ci,
  `kw` text COLLATE utf8_unicode_ci,
  `co` text COLLATE utf8_unicode_ci,
  `cr` text COLLATE utf8_unicode_ci,
  `hr` text COLLATE utf8_unicode_ci,
  `cs` text COLLATE utf8_unicode_ci,
  `da` text COLLATE utf8_unicode_ci,
  `dv` text COLLATE utf8_unicode_ci,
  `nl` text COLLATE utf8_unicode_ci,
  `dz` text COLLATE utf8_unicode_ci,
  `eo` text COLLATE utf8_unicode_ci,
  `et` text COLLATE utf8_unicode_ci,
  `ee` text COLLATE utf8_unicode_ci,
  `fo` text COLLATE utf8_unicode_ci,
  `fj` text COLLATE utf8_unicode_ci,
  `fi` text COLLATE utf8_unicode_ci,
  `fr` text COLLATE utf8_unicode_ci,
  `ff` text COLLATE utf8_unicode_ci,
  `gl` text COLLATE utf8_unicode_ci,
  `ka` text COLLATE utf8_unicode_ci,
  `de` text COLLATE utf8_unicode_ci,
  `el` text COLLATE utf8_unicode_ci,
  `gn` text COLLATE utf8_unicode_ci,
  `gu` text COLLATE utf8_unicode_ci,
  `ht` text COLLATE utf8_unicode_ci,
  `ha` text COLLATE utf8_unicode_ci,
  `he` text COLLATE utf8_unicode_ci,
  `hz` text COLLATE utf8_unicode_ci,
  `hi` text COLLATE utf8_unicode_ci,
  `ho` text COLLATE utf8_unicode_ci,
  `hu` text COLLATE utf8_unicode_ci,
  `ia` text COLLATE utf8_unicode_ci,
  `id` text COLLATE utf8_unicode_ci,
  `ie` text COLLATE utf8_unicode_ci,
  `ga` text COLLATE utf8_unicode_ci,
  `ig` text COLLATE utf8_unicode_ci,
  `ik` text COLLATE utf8_unicode_ci,
  `io` text COLLATE utf8_unicode_ci,
  `is` text COLLATE utf8_unicode_ci,
  `it` text COLLATE utf8_unicode_ci,
  `iu` text COLLATE utf8_unicode_ci,
  `ja` text COLLATE utf8_unicode_ci,
  `jv` text COLLATE utf8_unicode_ci,
  `kl` text COLLATE utf8_unicode_ci,
  `kn` text COLLATE utf8_unicode_ci,
  `kr` text COLLATE utf8_unicode_ci,
  `ks` text COLLATE utf8_unicode_ci,
  `kk` text COLLATE utf8_unicode_ci,
  `km` text COLLATE utf8_unicode_ci,
  `ki` text COLLATE utf8_unicode_ci,
  `rw` text COLLATE utf8_unicode_ci,
  `ky` text COLLATE utf8_unicode_ci,
  `kv` text COLLATE utf8_unicode_ci,
  `kg` text COLLATE utf8_unicode_ci,
  `ko` text COLLATE utf8_unicode_ci,
  `ku` text COLLATE utf8_unicode_ci,
  `kj` text COLLATE utf8_unicode_ci,
  `la` text COLLATE utf8_unicode_ci,
  `lb` text COLLATE utf8_unicode_ci,
  `lg` text COLLATE utf8_unicode_ci,
  `li` text COLLATE utf8_unicode_ci,
  `ln` text COLLATE utf8_unicode_ci,
  `lo` text COLLATE utf8_unicode_ci,
  `lt` text COLLATE utf8_unicode_ci,
  `lu` text COLLATE utf8_unicode_ci,
  `lv` text COLLATE utf8_unicode_ci,
  `gv` text COLLATE utf8_unicode_ci,
  `mk` text COLLATE utf8_unicode_ci,
  `mg` text COLLATE utf8_unicode_ci,
  `ms` text COLLATE utf8_unicode_ci,
  `ml` text COLLATE utf8_unicode_ci,
  `mt` text COLLATE utf8_unicode_ci,
  `mi` text COLLATE utf8_unicode_ci,
  `mr` text COLLATE utf8_unicode_ci,
  `mh` text COLLATE utf8_unicode_ci,
  `mn` text COLLATE utf8_unicode_ci,
  `na` text COLLATE utf8_unicode_ci,
  `nv` text COLLATE utf8_unicode_ci,
  `nb` text COLLATE utf8_unicode_ci,
  `nd` text COLLATE utf8_unicode_ci,
  `ne` text COLLATE utf8_unicode_ci,
  `ng` text COLLATE utf8_unicode_ci,
  `nn` text COLLATE utf8_unicode_ci,
  `no` text COLLATE utf8_unicode_ci,
  `ii` text COLLATE utf8_unicode_ci,
  `nr` text COLLATE utf8_unicode_ci,
  `oc` text COLLATE utf8_unicode_ci,
  `oj` text COLLATE utf8_unicode_ci,
  `cu` text COLLATE utf8_unicode_ci,
  `om` text COLLATE utf8_unicode_ci,
  `or` text COLLATE utf8_unicode_ci,
  `os` text COLLATE utf8_unicode_ci,
  `pa` text COLLATE utf8_unicode_ci,
  `pi` text COLLATE utf8_unicode_ci,
  `fa` text COLLATE utf8_unicode_ci,
  `pl` text COLLATE utf8_unicode_ci,
  `ps` text COLLATE utf8_unicode_ci,
  `pt` text COLLATE utf8_unicode_ci,
  `qu` text COLLATE utf8_unicode_ci,
  `rm` text COLLATE utf8_unicode_ci,
  `rn` text COLLATE utf8_unicode_ci,
  `ro` text COLLATE utf8_unicode_ci,
  `ru` text COLLATE utf8_unicode_ci,
  `sa` text COLLATE utf8_unicode_ci,
  `sc` text COLLATE utf8_unicode_ci,
  `sd` text COLLATE utf8_unicode_ci,
  `se` text COLLATE utf8_unicode_ci,
  `sm` text COLLATE utf8_unicode_ci,
  `sg` text COLLATE utf8_unicode_ci,
  `sr` text COLLATE utf8_unicode_ci,
  `gd` text COLLATE utf8_unicode_ci,
  `sn` text COLLATE utf8_unicode_ci,
  `si` text COLLATE utf8_unicode_ci,
  `sk` text COLLATE utf8_unicode_ci,
  `sl` text COLLATE utf8_unicode_ci,
  `so` text COLLATE utf8_unicode_ci,
  `st` text COLLATE utf8_unicode_ci,
  `es` text COLLATE utf8_unicode_ci,
  `su` text COLLATE utf8_unicode_ci,
  `sw` text COLLATE utf8_unicode_ci,
  `ss` text COLLATE utf8_unicode_ci,
  `sv` text COLLATE utf8_unicode_ci,
  `ta` text COLLATE utf8_unicode_ci,
  `te` text COLLATE utf8_unicode_ci,
  `tg` text COLLATE utf8_unicode_ci,
  `th` text COLLATE utf8_unicode_ci,
  `ti` text COLLATE utf8_unicode_ci,
  `bo` text COLLATE utf8_unicode_ci,
  `tk` text COLLATE utf8_unicode_ci,
  `tl` text COLLATE utf8_unicode_ci,
  `tn` text COLLATE utf8_unicode_ci,
  `to` text COLLATE utf8_unicode_ci,
  `tr` text COLLATE utf8_unicode_ci,
  `ts` text COLLATE utf8_unicode_ci,
  `tt` text COLLATE utf8_unicode_ci,
  `tw` text COLLATE utf8_unicode_ci,
  `ty` text COLLATE utf8_unicode_ci,
  `ug` text COLLATE utf8_unicode_ci,
  `uk` text COLLATE utf8_unicode_ci,
  `ur` text COLLATE utf8_unicode_ci,
  `uz` text COLLATE utf8_unicode_ci,
  `ve` text COLLATE utf8_unicode_ci,
  `vi` text COLLATE utf8_unicode_ci,
  `vo` text COLLATE utf8_unicode_ci,
  `wa` text COLLATE utf8_unicode_ci,
  `cy` text COLLATE utf8_unicode_ci,
  `wo` text COLLATE utf8_unicode_ci,
  `fy` text COLLATE utf8_unicode_ci,
  `xh` text COLLATE utf8_unicode_ci,
  `yi` text COLLATE utf8_unicode_ci,
  `yo` text COLLATE utf8_unicode_ci,
  `za` text COLLATE utf8_unicode_ci,
  `zu` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`page_address`,`message_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (page_address) */;

CREATE TABLE `unsubscribe_request` (
  `unsubscribe_request_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(300) CHARACTER SET latin1 NOT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`unsubscribe_request_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_account` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `first_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `middle_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `alternative_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `account_status_id` mediumint(8) unsigned DEFAULT NULL,
  `thumbnail_id` int(10) unsigned DEFAULT NULL,
  `avatar_path` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `last_online` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `latitude` double NOT NULL DEFAULT '28',
  `longitude` double NOT NULL DEFAULT '-82',
  `gmt_offset` tinyint(2) NOT NULL DEFAULT '-5',
  `is_login_collection_validated` tinyint(1) unsigned DEFAULT NULL,
  `is_online` tinyint(1) unsigned DEFAULT NULL,
  `is_closed` tinyint(1) unsigned DEFAULT NULL,
  `password_hash` mediumblob,
  `unread_messages` smallint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `last_online` (`last_online`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`),
  KEY `account_status_id` (`account_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_achievement` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `achievement_id` smallint(5) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_avatar` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `avatar_part_id` smallint(5) unsigned NOT NULL,
  `layer` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`layer`),
  KEY `avatar_part_id` (`avatar_part_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_history` (
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page_title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`page_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_invite` (
  `user_invite_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `code` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `code_used_by` mediumint(8) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`user_invite_id`),
  KEY `code` (`code`(255),`code_used_by`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_login` (
  `user_login_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `unique_identifier` varchar(320) CHARACTER SET latin1 NOT NULL,
  `user_login_provider_id` mediumint(7) NOT NULL,
  `serialized_credentials` text COLLATE utf8_unicode_ci,
  `current_failed_attempts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `total_failed_attempts` smallint(5) unsigned NOT NULL DEFAULT '0',
  `last_failed_attempt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_verified` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_login_id`),
  UNIQUE KEY `unique_identifier` (`unique_identifier`,`user_login_provider_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_login_history` (
  `user_id` mediumint(8) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `proxy` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '2',
  KEY `ip` (`ip`),
  KEY `login` (`login`(85)),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_login_provider` (
  `user_login_provider_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `provider_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `login_type` enum('HybridAuth','MinnowAuth') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_validation_required` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_login_provider_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user_login_provider` (`user_login_provider_id`, `created_datetime`, `modified_datetime`, `provider_name`, `login_type`, `is_validation_required`) VALUES
(1, '2012-11-27 15:32:02', NULL, 'Email', 'MinnowAuth', 1),
(2, '2012-11-27 15:32:02', NULL, 'SMS', 'MinnowAuth', 1),
(3, '2012-11-27 15:32:02', NULL, 'Facebook', 'HybridAuth', 0),
(4, '2012-11-27 15:32:02', NULL, 'Twitter', 'HybridAuth', 0),
(5, '2012-11-27 15:32:02', NULL, 'Instagram', 'HybridAuth', 0),
(6, '2012-11-27 15:32:02', NULL, 'AOL', 'HybridAuth', 0),
(7, '2012-11-27 15:32:02', NULL, 'Google', 'HybridAuth', 0),
(8, '2012-11-27 15:32:02', NULL, 'MySpace', 'HybridAuth', 0),
(9, '2012-11-27 15:32:02', NULL, 'Live', 'HybridAuth', 0),
(10, '2012-11-27 15:32:02', NULL, 'LinkedIn', 'HybridAuth', 0),
(11, '2012-11-27 15:32:02', NULL, 'OpenID', 'HybridAuth', 0),
(12, '2012-11-27 15:32:02', NULL, 'Foursquare', 'HybridAuth', 0);

CREATE TABLE `user_login_validation` (
  `user_login_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT NULL,
  `code` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_login_id`),
  KEY `user_id` (`user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_power` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `power_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`power_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_profile` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_role` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `role_id` mediumint(8) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_session` (
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `proxy` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_agent` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `access_token` varchar(256) CHARACTER SET latin1 NOT NULL,
  `php_session_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `php_session_id` (`php_session_id`),
  KEY `access_token` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;

CREATE TABLE `user_setting` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `setting_id` mediumint(8) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`,`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (user_id) */;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;
