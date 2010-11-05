DROP TABLE IF EXISTS `#___ECR_COM_TBL_NAME_`;
 
CREATE TABLE `#___ECR_COM_TBL_NAME_` (
	`id` INT(11) NOT NULL AUTO_INCREMENT
	, `greeting` VARCHAR(25) NOT NULL
	, `catid` int(11) NOT NULL DEFAULT '0'
	, `params` TEXT NOT NULL DEFAULT ''
	
	, PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `#___ECR_COM_TBL_NAME_` (`greeting`) VALUES
        ('Hello World!'),
        ('Good bye World!');
