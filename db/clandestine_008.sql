--
-- Database: `clandestine`
--
USE `clandestine`;
-- --------------------------------------------------------
--
-- Table structure for table `message_template`
--
CREATE TABLE IF NOT EXISTS `message_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `disabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Alter statement for table `users`
--
ALTER TABLE `users` ADD `last_password_change` DATETIME NULL AFTER `last_login` ;