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