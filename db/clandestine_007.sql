--
-- Database: `clandestine`
--
USE `clandestine`;
-- --------------------------------------------------------

--
-- Alter statement for table `temp_mailbox`
--
ALTER TABLE `temp_mailbox` CHANGE `password` `password` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `size` `size` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ;

--
-- Alter statement for table `mailbox`
--
ALTER TABLE `mailbox` ADD `updated_at` DATETIME NULL AFTER `created_at` ;
ALTER TABLE `mailbox` ADD FOREIGN KEY ( `user_id` ) REFERENCES `clandestine`.`users` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- Alter statement for table `payments`
--
ALTER TABLE `payments` ADD `payment_date` DATE NULL AFTER `amount` ;
ALTER TABLE `payments` CHANGE `next_due_date` `next_due_date` DATE NULL DEFAULT NULL AFTER `payment_date` ;
ALTER TABLE `payments` ADD `updated_at` DATETIME NULL AFTER `created_at` ;
ALTER TABLE `payments` CHANGE `amount` `amount` FLOAT( 16, 2 ) NULL DEFAULT '0.00';
ALTER TABLE `packages` CHANGE `cost` `cost` FLOAT( 16, 2 ) NULL DEFAULT '0.00';