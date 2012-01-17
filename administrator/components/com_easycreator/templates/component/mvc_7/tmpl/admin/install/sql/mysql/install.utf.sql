DROP TABLE IF EXISTS `#___ECR_COM_TBL_NAME_`;

CREATE TABLE `#___ECR_COM_TBL_NAME_` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `greeting` VARCHAR(25) NOT NULL,
  `content` TEXT NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#___ECR_COM_TBL_NAME_` (`greeting`, `content`)
VALUES
('Hello, World!', '<p>The A \"hello world\" program is a computer program that prints out \"Hello, world!\"
 on a display device. It is used in many introductory tutorials for teaching a programming language. Such a program is typically one
 of the simplest programs possible in a computer language. Some are surprisingly complex, especially in some graphical user interface (GUI)
 contexts, but most are very simple, especially those which rely heavily on a particular command line interpreter (\"shell\") to perform the
 actual output. In many embedded systems, the text may be sent to a one or two-line liquid crystal display (LCD), or some other appropriate
 signal, such as a LED being turned on, may substitute for the message.<p>');
