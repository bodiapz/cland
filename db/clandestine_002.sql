--
-- Database: `clandestine`
--
USE `clandestine`;

--
-- Drop statement for table `resources`, `roles`, `role_resources`
--
DROP TABLE resources;
DROP TABLE roles;
DROP TABLE role_resources;

--
-- Alter statement for table `packages`
--
ALTER TABLE `packages` DROP `user_id` ;
ALTER TABLE `packages` CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `name` ;
ALTER TABLE `packages` CHANGE `term` `term` INT( 11 ) NULL DEFAULT '0' AFTER `cost` ;
ALTER TABLE `packages` DROP `status` ;
ALTER TABLE `packages` ADD `disabled` TINYINT NULL ;
ALTER TABLE `payments` ADD `package_id` INT NOT NULL AFTER `id` ,
ADD INDEX ( `package_id` ) ;

--
-- Alter statement for table `payments`
--
ALTER TABLE `payments` CHANGE `user_id` `user_id` INT( 11 ) NULL DEFAULT NULL AFTER `package_id` ;
ALTER TABLE `payments` ADD INDEX ( `user_id` ) ;
ALTER TABLE `payments` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;
ALTER TABLE `payments` CHANGE `method` `method` ENUM( 'bc', 'other' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'bc' AFTER `amount` ;
ALTER TABLE `payments` CHANGE `status` `status` ENUM( 'received', 'pending' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'pending' AFTER `method` ;
ALTER TABLE `payments` CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `status` ;
ALTER TABLE `payments` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL AFTER `next_due_date` ;
ALTER TABLE `payments` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL AFTER `created_at` ;
ALTER TABLE `payments` CHANGE `created_at` `created_at` DATETIME NOT NULL ;