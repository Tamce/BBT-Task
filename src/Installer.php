<?php
namespace Tamce\BBT;

use Tamce\BBT\Core\Helper;

class Installer extends Models\Model
{
	public function install()
	{
		$this->connect();
		$this->pdo->beginTransaction();
		$this->pdo->exec('SET NAMES utf8');

/* 基础需求不需要
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
*/

/* 基础需求不需要
		// 创建关系表
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXIST `relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `classId` int(10) unsigned NOT NULL,
  `relation` int(5) NOT NULL,
  PRIMARY KEY (`id`)	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);
*/

		// 创建用户表
		// 用户基本属性暂时不拆分为不同字段，使用 json 统一存储在 info 字段内
		// 那么我们暂时规定 userGroup 1:学生 2:老师
		// accountStatus - 基础需求不需要
		$this->pdo->exec(
<<<EOD
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `info` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `userGroup` int(5) NOT NULL,
  `accountStatus` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOD
			);

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

		Helper::treatPdoError($this->pdo->errorInfo());
		$this->pdo->commit();
	}

	public function uninstall()
	{
		$this->connect();
		$this->pdo->beginTransaction();
		// $this->pdo->exec('DROP TABLE IF EXISTS `class`');
		// $this->pdo->exec('DROP TABLE IF EXISTS `relation`');
		$this->pdo->exec('DROP TABLE IF EXISTS `user`');
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
