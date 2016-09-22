<?php
namespace Tamce\BBT\Controllers;

use Tamce\BBT\Models\User as MUser;
use Tamce\BBT\Core\Helper;

class User
{
	public function validate($username)
	{
		$muser = new MUser;
		$user = $muser->find(['username' => $username]);
		header('Content-Type: application/json');
		if (Helper::validatePassword($_POST['password'], $user->password)) {
			// 验证通过
			unset($user->password);
			$_SESSION['user'] = $user;
			echo json_encode(['status' => 'succeed', 'data' => $user]);
		} else {
			echo json_encode(['status' => 'failed']);
		}
	}
}
