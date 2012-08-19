CREATE TABLE `wp_wdpv_post_votes` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`blog_id` INT(10) NOT NULL,
	`site_id` INT(10) NOT NULL,
	`post_id` INT(10) NOT NULL,
	`user_id` INT(10) NOT NULL,
	`user_ip` INT(10) NOT NULL,
	`vote` INT(1) NOT NULL,
	`date` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT