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
  `template` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;