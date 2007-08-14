--
-- Database: `clandestine`
--
USE `clandestine`;
-- --------------------------------------------------------
--
-- Alter Statement for table `login_history`
--
ALTER TABLE `login_history` ADD `type` VARCHAR( 20 ) NOT NULL AFTER `ip` ;
ALTER TABLE `login_history` ADD `ip_country` VARCHAR( 5 ) NOT NULL AFTER `ip` ;

--
-- Alter Statement for table `users`
--
RENAME TABLE `clandestine`.`usergroups` TO `clandestine`.`group` ;

--
-- Alter Statement for table `users`
--
ALTER TABLE `users` CHANGE `usergroup` `group_id` INT( 11 ) NULL ;
ALTER TABLE `users` DROP FOREIGN KEY `users_ibfk_1` ;
ALTER TABLE `users` ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY ( `group_id` ) REFERENCES `clandestine`.`group` (
`id`
) ON DELETE SET NULL ON UPDATE SET NULL ;
ALTER TABLE `group` ADD UNIQUE (
`name`
);