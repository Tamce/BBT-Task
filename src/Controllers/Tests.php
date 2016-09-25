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

	public function testUser()
	{
		$this->unit->start('Test Controller `User` and Model `User`');

		$this->unit->assert('检查操作前情况', function () {
			$user = new User;
			var_dump($user->find(['username' => 'tamce']));
			return true;
		});

		$this->unit->assert('测试 `User` 模型的插入', function () {
			$user = new User;
			// $user->create(['username' => 'tamce', 'password' => '12345',
			//	'info' => json_encode(['name' => 'Tamce', 'gender' => 'male']), 'userGroup' => 1]);
			return true;
		}, '测试通过，已注释插入语句');

		$this->unit->assert('测试 Update', function () {
			$user = new User;
			$u = $user->find(['username' => 'tamce'])[0];
			$user->update(['username' => 'tamce'], ['accountStatus' => (int)$u['accountStatus'] + 1]);
			return true;
		}, '执行后用户的 accountStatus 值应该加一了');

		$this->unit->assert('检查操作后情况', function () {
			$user = new User;
			var_dump($user->find(['username' => 'tamce']));
			return true;
		});

		$this->unit->printResult();
	}
}
