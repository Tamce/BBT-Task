<?php
namespace Tamce\BBT\Controllers;

use ElfStack\Unit;
use Tamce\BBT\Models\User;

class Tests
{
	public function __construct()
	{
		$this->unit = new Unit();
	}

	public function testPdo()
	{
		$this->unit->start('Test PDO Insert');

		$this->unit->assert('手动插入测试', function () {
			$user = new User;
			$s = $user->pdo->prepare('INSERT INTO `user` (username,password,class,info,userGroup,relationType) VALUES (?,?,?,?,?,?)');
			$s->execute(['tamce', 'password', 'class', 'info', 'group', 'relationType']);
			var_dump($s->errorInfo());
			return $s->errorInfo()[0] === '00000';
		});

		$this->unit->printResult();
	}
}
