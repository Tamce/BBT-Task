<?php
namespace Tamce\BBT;

class Installer extends Models\Model
{
	public function install()
	{
		$this->connect();
		$this->pdo->beginTransaction();
		$this->pdo->exec('SET NAMES utf8');

		// 创建班级信息表
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `info` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);

		// 创建用户表
		// 用户基本属性暂时不拆分为不同字段，使用 json 统一存储在 info 字段内
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(63) CHARACTER SET utf8 NOT NULL,
  `password` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `class` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `info` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `userGroup` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `relationType` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);

		$this->pdo->commit();
	}

	public function uninstall()
	{
		$this->connect();
		$this->pdo->beginTransaction();
		$this->pdo->exec('DROP TABLE IF EXISTS `class`');
		$this->pdo->exec('DROP TABLE IF EXISTS `user`');
		$this->pdo->commit();
	}

	public function reinstall()
	{
		$this->uninstall();
		$this->install();
	}
}
