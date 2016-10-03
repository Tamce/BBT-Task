<?php
namespace Tamce\BBT\Controllers;

use Tamce\BBT\Models\User as MUser;
use Tamce\BBT\Core\Helper;

class User extends Controller
{
	// POST /api/authorization
	public function authorize()
	{
		if (isset($_SESSION['login']) && $_SESSION['login']) {
			$this->response(['status' => 'notice', 'info' => 'You have already login!']);
		}
		if (empty($this->request('username')) or empty($this->request('password'))) {
			Helper::abort(400);
		}
		$muser = new MUser;
		$result = $muser->find(['username' => $this->request('username')]);
		if (!empty($result)) {
			$user = $result[0];
			if (Helper::validatePassword($this->request('password'), $user['password'])) {
				// 验证通过
				unset($user['password']);
				$_SESSION['user'] = $user;
				$_SESSION['user']['info'] = json_decode($_SESSION['user']['info'], true);
				$_SESSION['login'] = true;
				$_SESSION['credential'] = Helper::randomString(15);
				$this->response(['status' => 'success', 'info' => 'Login successfully', 'data' => $user, 'session' => session_id(), 'credential' => $_SESSION['credential']]);
			}
		}
		$this->response(['status' => 'error', 'info' => 'User not exist or incorrect password!'], 401);
	}

	// GET /api/users
	public function listUser()
	{
		// todo 权限限定
		$user = new Muser;
		$result = $user->all($this->queryString('begin'), $this->queryString('count'));
		foreach ($result as &$v) {
			unset($v['password']);
		}
		$this->response(['status' => 'success', 'data' => $result, 'totalCount' => $user->count()]);
	}

	// * /api/user
	public function current()
	{
		if (!isset($_SESSION['login']) or !$_SESSION['login']) {
			$this->response(['status' => 'error', 'info' => 'Authorization required!'], 401);
		}
		switch ($this->method())
		{
			case 'PATCH':
				$user = new MUser;
				$user->updateVerify(['username' => $_SESSION['user']['username']], [
						'info' => json_encode([
							'name' => Helper::request('name'),
							'gender' => Helper::request('gender'),
							'grade' => Helper::request('grade')
							])
					]);
				$this->response(['status' => 'success', 'data' => $_SESSION['user']);
				break;
			case 'GET':
				$this->response(['status' => 'success', 'data' => $_SESSION['user']]);
				break;
			default:
				$this->response(['status' => 'error', 'info' => '405 Method Not Allowed!'], 405);
				break;
		}
	}

	// POST /api/users
	public function create()
	{
		if (isset($_SESSION['login']) and $_SESSION['login']) {
			$this->response(['status' => 'error', 'info' => 'You have already login and cannot register a new user!\nPlease logout first.']);
		}
		$muser = new MUser;

		// 先确定没有相同的用户名
		if (count($muser->find(['username' => Helper::request('username')])) > 0) {
			$this->response(['status' => 'error', 'info' => 'This username is existed!']);
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
		$_SESSION['credential'] = Helper::randomString(15);
		unset($_SESSION['user']['password']);
		$this->response(['status' => 'success', 'data' => $_SESSION['user'], 'credential' => $_SESSION['credential'], 'session' => session_id()]);
	}

	// GET /api/user/{username}
	public function info($username)
	{

	}

	// GET /api/verify_update/{vid}
	public function verifyUpdate($vid)
	{

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
		echo json_decode(['status' => 'error', 'info' => 'User not login!', 'jump' => '/login']);
	}
}
