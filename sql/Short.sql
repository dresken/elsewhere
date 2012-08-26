SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE IF NOT EXISTS `domains` (
  `domain_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(1000) NOT NULL,
  `creator` varchar(256) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `domains_permissions`
--

CREATE TABLE IF NOT EXISTS `domains_permissions` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `redirection_id` int(10) unsigned NOT NULL,
  `generation_id` int(10) unsigned NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `prefix` varchar(256) NOT NULL,
  PRIMARY KEY (`permission_id`),
  KEY `redirection_id` (`redirection_id`),
  KEY `generation_id` (`generation_id`),
  KEY `domain_id` (`domain_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `generation_types`
--

CREATE TABLE IF NOT EXISTS `generation_types` (
  `generation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` tinytext NOT NULL,
  PRIMARY KEY (`generation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(256) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hostnames`
--

CREATE TABLE IF NOT EXISTS `hostnames` (
  `hostname_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `hostname` varchar(256) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `creator` varchar(256) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hostname_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `redirection_types`
--

CREATE TABLE IF NOT EXISTS `redirection_types` (
  `redirection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` tinytext NOT NULL,
  PRIMARY KEY (`redirection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `urls`
--

CREATE TABLE IF NOT EXISTS `urls` (
  `url_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_path` varchar(256) NOT NULL,
  `destination` varchar(256) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `creator` varchar(256) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_access` date NOT NULL,
  `generation_id` int(10) unsigned NOT NULL,
  `redirection_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`url_id`),
  KEY `domain_id` (`domain_id`),
  KEY `generation_id` (`generation_id`),
  KEY `redirection_id` (`redirection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `domains_permissions`
--
ALTER TABLE `domains_permissions`
  ADD CONSTRAINT `domains_permissions_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`),
  ADD CONSTRAINT `domains_permissions_ibfk_1` FOREIGN KEY (`redirection_id`) REFERENCES `redirection_types` (`redirection_id`),
  ADD CONSTRAINT `domains_permissions_ibfk_2` FOREIGN KEY (`generation_id`) REFERENCES `generation_types` (`generation_id`),
  ADD CONSTRAINT `domains_permissions_ibfk_3` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`domain_id`);

--
-- Constraints for table `hostnames`
--
ALTER TABLE `hostnames`
  ADD CONSTRAINT `hostnames_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`domain_id`);

--
-- Constraints for table `urls`
--
ALTER TABLE `urls`
  ADD CONSTRAINT `urls_ibfk_3` FOREIGN KEY (`redirection_id`) REFERENCES `redirection_types` (`redirection_id`),
  ADD CONSTRAINT `urls_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`domain_id`),
  ADD CONSTRAINT `urls_ibfk_2` FOREIGN KEY (`generation_id`) REFERENCES `generation_types` (`generation_id`);
