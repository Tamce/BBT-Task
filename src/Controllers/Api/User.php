<?php
namespace Tamce\BBT\Controllers\Api;

use Tamce\BBT\Models\User as MUser;
use Tamce\BBT\Core\Helper;

class User
{
	public function validate()
	{
		header('Content-Type: application/json');
		if (isset($_SESSION['login'] && $_SESSION['login'])) {
			echo json_encode(['status' => 'notice', 'data' => 'You have already login!', 'jump' => '/profile']);
			return;
		}
		if (!isset($_POST['username'], $_POST['password'])) {
			Helper::abort(400);
		}
		$muser = new MUser;
		$result = $muser->find(['username' => $_POST['username']]);
		if (!empty($result)) {
			$user = $result[0];
			if (Helper::validatePassword($_POST['password'], $user['password'])) {
				// 验证通过
				unset($user['password']);
				$_SESSION['user'] = $user;
				$_SESSION['user']['info'] = json_decode($_SESSION['user']['info'], true);
				$_SESSION['login'] = true;
				echo json_encode(['status' => 'success', 'data' => $user, 'jump' => '/profile']);
				return;
			}	
		}
		echo json_encode(['status' => 'failed', 'info' => 'User not exist or invalid password!']);
	}

	public function create()
	{
		header('Content-Type: application/json');
		$muser = new MUser;

		// 先确定没有相同的用户名
		if (count($muser->find(['username' => Helper::request('username')])) > 0) {
			echo json_encode(['status' => 'failed', 'info' => 'This username is existed!']);
			return;
		}

		// 构造数组
		$info = ['username' => Helper::request('username'),
				 'password' => Helper::request('password'),
				 'info' => '{}',
				 'userGroup' => Helper::request('userGroup')];
		$muser->filter($info);
		$muser->create($info);
		$_SESSION['user'] = $muser->find(['username' => $info['username']])[0];
		$_SESSION['user']['info'] = json_decode($_SESSION['user']['info'], true);
		$_SESSION['login'] = true;
		unset($_SESSION['user']['password']);
		echo json_encode(['status' => 'success', 'jump' => '/profile']);
	}

	public function profile()
	{
		header('Content-Type: application/json');
		if (isset($_SESSION['login']) and $_SESSION['login']) {
			echo json_encode(['status' => 'success', 'data' => $_SESSION['user']]);
			return;
		}
		echo json_decode(['status' => 'failed', 'info' => 'User not login!', 'jump' => '/login']);
	}

	public function update()
	{
		header('Content-Type: application/json');
		if (isset($_SESSION['login']) and $_SESSION['login']) {
			$user = new MUser;
			$user->update(['username' => $_SESSION['user']['username']], [
					'info' => json_encode([
						'name' => Helper::request('name'),
						'gender' => Helper::request('gender'),
						'grade' => Helper::request('grade')
						])
				]);
			$_SESSION['user'] = $user->find(['username' => $_SESSION['user']['username']])[0];
			$_SESSION['user']['info'] = json_decode($_SESSION['user']['info'], true);
			unset($_SESSION['user']['password']);
			echo json_encode($_SESSION['user']);
			return;
		}
		echo json_decode(['status' => 'failed', 'info' => 'User not login!', 'jump' => '/login']);
	}
}
