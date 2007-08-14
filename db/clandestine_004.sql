--
-- Database: `clandestine`
--

-- --------------------------------------------------------

--
-- Table structure for table `multifactor`
--

CREATE TABLE IF NOT EXISTS `multifactor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_key` varchar(11) NOT NULL,
  `next_index` int(11) NOT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_key` (`user_key`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `multifactor`
--
ALTER TABLE `multifactor`
  ADD CONSTRAINT `multifactor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;