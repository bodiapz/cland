--
-- Database: `clandestine`
--
USE `clandestine`;
-- --------------------------------------------------------

--
-- Table structure for table `multifactor`
--
CREATE TABLE IF NOT EXISTS `temp_mailbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `size` varchar(20) NOT NULL,
  `smtp` tinyint(4) DEFAULT NULL,
  `updated` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Alter statement for table `content`
--
ALTER TABLE `content` ADD UNIQUE (
`alias`
);

--
-- Alter statement for table `mailbox`
--
ALTER TABLE `mailbox` CHANGE `password` `password` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;