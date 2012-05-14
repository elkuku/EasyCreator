CREATE TABLE `#__ECR_COM_TBL_NAME` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `greeting` VARCHAR(25) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__ECR_COM_TBL_NAME` (`greeting`)
VALUES
('Hello, World!'),
('Bonjour, Monde!'),
('Ciao, Mondo!'),
('Hallo, Welt ;)');
