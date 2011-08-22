DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `slogan` longtext NOT NULL,
  `footer` longtext NOT NULL,
  `author` longtext NOT NULL,
  `copyright` longtext NOT NULL,
  `keywords` longtext NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publicDefault` int(1) NOT NULL,
  `loginDefault` int(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `root` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publicDefault` (`publicDefault`),
  UNIQUE KEY `loginDefault` (`loginDefault`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
INSERT INTO `modules` (`id`, `publicDefault`, `loginDefault`, `name`, `root`) VALUES (1, 1, 1, 'Website', 'cms');

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentID` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `visibility` int(1) NOT NULL,
  `url` longtext NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `plugins`;
CREATE TABLE IF NOT EXISTS `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `type` varchar(4) NOT NULL,
  `name` varchar(50) NOT NULL,
  `root` varchar(50) NOT NULL,
  `URL` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
INSERT INTO `plugins` (`id`, `position`, `type`, `name`, `root`, `URL`) VALUES (1, 1, 'link', 'Home', 'home', '');

DROP TABLE IF EXISTS `seo`;
CREATE TABLE IF NOT EXISTS `seo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meta` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `template-admin`;
CREATE TABLE IF NOT EXISTS `template-admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `selected` int(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pluginIconColor` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
INSERT INTO `template-admin` (`id`, `selected`, `name`, `pluginIconColor`) VALUES (1, 1, 'admin-skin', 'white');

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` longtext NOT NULL,
  `firstname` longtext NOT NULL,
  `lastname` longtext NOT NULL,
  `username` longtext NOT NULL,
  `password` longtext NOT NULL,
  `emailaddress1` longtext NOT NULL,
  `emailaddress2` longtext NOT NULL,
  `emailaddress3` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;