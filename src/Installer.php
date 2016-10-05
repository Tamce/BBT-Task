<?php
namespace Tamce\BBT;

use Tamce\BBT\Core\Helper;

class Installer extends Models\Model
{
	public function install()
	{
		$this->pdo->beginTransaction();
		$this->pdo->exec('SET NAMES utf8');

		// 创建班级信息表
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classname` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `info` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);
		Helper::treatPdoError($this->pdo->errorInfo());

		// 创建用户表
		// 用户基本属性暂时不拆分为不同字段，使用 json 统一存储在 info 字段内
		// 那么我们暂时规定 userGroup 1:学生 2:老师 3:超级管理员
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin,
  `gender` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin,
  `avatar` text CHARACTER SET utf8 COLLATE utf8_bin,
  `userGroup` int(5) NOT NULL,
  `classname` text CHARACTER SET utf8 COLLATE utf8_bin,
  `newUser` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);
		Helper::treatPdoError($this->pdo->errorInfo());

		// 创建审核表
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `verify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `userGroup` int(5) NOT NULL,
  `name` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `gender` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `classname` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);
		Helper::treatPdoError($this->pdo->errorInfo());

/* 在学籍管理系统中也许没有必要
		// 创建用户组表
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `info` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `privilege` int(10) NOT NULL DEFAULT 0,  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);
*/

		$this->pdo->commit();
	}

	public function uninstall()
	{
		$this->pdo->beginTransaction();
		$this->pdo->exec('DROP TABLE IF EXISTS `class`');
		$this->pdo->exec('DROP TABLE IF EXISTS `user`');
		$this->pdo->exec('DROP TABLE IF EXISTS `verify`');
		Helper::treatPdoError($this->pdo->errorInfo());
		// $this->pdo->exec('DROP TABLE IF EXISTS `group`');
		$this->pdo->commit();
	}

	public function reinstall()
	{
		$this->uninstall();
		$this->install();
	}
}
