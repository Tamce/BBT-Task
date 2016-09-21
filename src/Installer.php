<?php
namespace Tamce\BBT;

class Installer extends Models\Model
{
	public function install()
	{
		$this->connect();
		$this->pdo->beginTransaction();
		$this->pdo->exec('SET NAMES utf8');

		// Create Connection table
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `connection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student` int(10) unsigned NOT NULL,
  `teacher` int(10) unsigned NOT NULL,
  `connectionType` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);

		// Create User table
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(63) NOT NULL,
  `password` varchar(63) NOT NULL,
  `info` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);

		$this->pdo->commit();
	}
}
