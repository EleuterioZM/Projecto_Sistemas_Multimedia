CREATE TABLE IF NOT EXISTS `#__convertforms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__convertforms_campaigns` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `service` varchar(50) NOT NULL,
  `params` mediumtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__convertforms_conversions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `campaign_id` int(10) NOT NULL,
  `form_id` int(11) NOT NULL,
  `visitor_id` varchar(64) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `params` mediumtext COLLATE utf8mb4_unicode_ci,
  `state` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `created` (`created`),
  KEY `form_id` (`form_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `user_id` (`user_id`),
  KEY `state_created` (`state`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__convertforms_submission_meta` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `submission_id` int(10) NOT NULL,
  `meta_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `params` mediumtext COLLATE utf8mb4_unicode_ci,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `submission_id` (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__convertforms_campaigns` 
  (`name`, `state`, `service`) VALUES
  ('Demo Campaign', 1, '0');