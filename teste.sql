DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `autor` int(11) NOT NULL,
  `titulo` varchar(128) NOT NULL,
  `conteudo` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `autor` (`autor`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `posts` (`id`, `autor`, `titulo`, `conteudo`) VALUES
(1, 2, 'postagem de teste', 'testando'),
(2, 1, 'postagem de teste 2', 'testando 2');

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(64) NOT NULL,
  `sobrenome` varchar(64) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `nome`, `sobrenome`, `data`) VALUES
(1, 'User', ' tester ', '2018-04-08 22:56:50'),
(2, 'User 2', ' tester 2 ', '2018-05-20 22:56:50')
;
