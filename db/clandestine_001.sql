--
-- Database: `clandestine`
--
USE `clandestine`;

--
-- Alter statement for table `usergroups`
--
ALTER TABLE `usergroups` ADD `disabled` TINYINT NULL ;
ALTER TABLE `usergroups` ENGINE = InnoDB;


--
-- Alter statement for table `users`
--
ALTER TABLE `users` CHANGE `last_name` `last_name` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `first_name` ;
ALTER TABLE `users` DROP `role_id` ;
ALTER TABLE `users` DROP `status` ;
ALTER TABLE `users` DROP `account_type` ;
ALTER TABLE `users` CHANGE `created_at` `created_at` DATETIME NOT NULL AFTER `security_answer` ;
ALTER TABLE `users` CHANGE `wallet_amount` `wallet_amount` FLOAT( 16, 8 ) NULL DEFAULT '0.00000000' AFTER `security_answer` ;
ALTER TABLE `users` CHANGE `usergroup` `usergroup` INT( 11 ) NOT NULL AFTER `wallet_amount` ;
ALTER TABLE `users` ADD `premium` TINYINT NULL ,
ADD `disabled` TINYINT NULL ;
ALTER TABLE `users` CHANGE `last_name` `last_name` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ;
ALTER TABLE `users` CHANGE `security_question` `security_question` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `security_answer` `security_answer` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE `users` ADD INDEX ( `usergroup` ) ;
ALTER TABLE `users` ADD UNIQUE (
`email`
);
ALTER TABLE `users` ENGINE = InnoDB;
ALTER TABLE `users` ADD FOREIGN KEY ( `usergroup` ) REFERENCES `clandestine`.`usergroups` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;