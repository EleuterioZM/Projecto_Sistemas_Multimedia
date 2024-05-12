ALTER TABLE `#__convertforms_conversions` ADD INDEX `user_id` (`user_id`);
ALTER TABLE `#__convertforms_conversions` ADD INDEX `state_created` (`state`, `created`);