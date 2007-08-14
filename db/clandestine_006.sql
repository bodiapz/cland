--
-- Database: `clandestine`
--
USE `clandestine`;
-- --------------------------------------------------------

--
-- Alter statement for table `payments`
--
ALTER TABLE `payments`
  DROP `method`,
  DROP `status`,
  DROP `description`,
  DROP `valid_upto`,
  DROP `updated_at`,
  DROP `next_due_date`;
ALTER TABLE `payments` ADD `next_due_date` DATE NULL AFTER `created_at` ;
ALTER TABLE `payments` ADD `paid` TINYINT NULL AFTER `next_due_date` ;
ALTER TABLE `payments` ADD `disabled` TINYINT NULL AFTER `paid` ;
ALTER TABLE `packages` ENGINE = InnoDB;
ALTER TABLE `payments` ADD FOREIGN KEY ( `package_id` ) REFERENCES `clandestine`.`packages` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `payments` ADD FOREIGN KEY ( `user_id` ) REFERENCES `clandestine`.`users` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- Alter statement for table `tickets`
--
ALTER TABLE `tickets` CHANGE `periority` `priority` ENUM( 'low', 'medium', 'high' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'low';
ALTER TABLE `tickets` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL AFTER `tid` ;
ALTER TABLE `tickets` CHANGE `status` `status` ENUM( 'open', 'hold', 'processing', 'resolved', 'closed' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'open' AFTER `detail`;
ALTER TABLE `tickets` CHANGE `priority` `priority` ENUM( 'low', 'medium', 'high' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'low' AFTER `status` ;
ALTER TABLE `tickets` ADD INDEX ( `user_id` ) ;
ALTER TABLE `tickets` ADD FOREIGN KEY ( `user_id` ) REFERENCES `clandestine`.`users` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- Alter statement for table `comments`
--
ALTER TABLE `comments` CHANGE `detail` `comment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `user_id` ;
ALTER TABLE `comments` CHANGE `user_id` `user_id` INT( 11 ) NULL ;


