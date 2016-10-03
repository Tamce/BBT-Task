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
				$_SESSION['user'] = Helper::packUser($user);
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
		Helper::ensureLogin();
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
			$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
		}

		$user = new Muser;
		$result = $user->all($this->queryString('begin'), $this->queryString('count'));
		foreach ($result as &$v) {
			$v = Helper::packUser($v);
		}
		$this->response(['status' => 'success', 'data' => $result, 'totalCount' => $user->count()]);
	}

	// * /api/user
	public function current()
	{
		Helper::ensureLogin();
		switch ($this->method())
		{
			case 'PATCH':
				$user = new MUser;
				// 如果是新用户允许更改一次信息而无需审核
				if ($_SESSION['user']['newUser'] != 0) {
					$user->update(['username'] => $_SESSION['user']['username'], [
							'info' => json_encode([
								'name' => $this->request('name'),
								'gender' => $this->request('gender')
								]),
							'classname' => $this->request('classname'),
							'newUser' => 0
						]);
					$_SESSION['user'] = Helper::packUser($user->find(['username' => $_SESSION['user']['username']])[0]);
					$this->response(['status' => 'success', 'info' => 'Data change successfully!', 'data' => $_SESSION['user']]);
				}
				$user->updateVerify(['username' => $_SESSION['user']['username']], [
						'info' => json_encode([
							'name' => $this->request('name'),
							'gender' => $this->request('gender')
							]),
						'classname' => $this->request('classname')
					]);
				$this->response(['status' => 'success', 'info' => 'Data saved! Waiting for verify...', 'data' => $_SESSION['user']);
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
		$_SESSION['user'] = Helper::packUser($muser->find(['username' => $info['username']])[0]);
		$_SESSION['login'] = true;
		$_SESSION['credential'] = Helper::randomString(15);
		$this->response(['status' => 'success', 'data' => $_SESSION['user'], 'credential' => $_SESSION['credential'], 'session' => session_id()]);
	}

	// GET /api/user/{username}
	public function info($username)
	{
		Helper::ensureLogin();
		$muser = new MUser;
		$info = $muser->find(['username' => $username]);
		if (count($info) == 0) {
			$this->response(['status' => 'error', 'info' => 'User Not Found!'], 404);
		}
		$info = $info[0];

		// 限制用户只能查看同班的信息
		if ($_SESSION['user']['classname'] == $info['classname']) {
			$this->response(Helper::packUser($info));
		}
		$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
	}

	// GET /api/verify_update/{username}
	public function verifyUpdate($username)
	{
		Helper::ensureLogin();
		Helper::loadConstants();

		$muser = new Muser;
		$verify = $muser->findVerify(['username' => $username]);
		if (count($verify) == 0) {
			$this->response(['status' => 'error', 'info' => 'Verify Request Not Found!'], 404);
		}
		$verify = $verify[0];

		// 按照待审核用户的用户组确认当前用户是否拥有权限审核
		switch ($verify['userGroup']) {
			case UserGroup::Teacher:
				if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
					$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
				}
				break;
			case UserGroup::Student:
				if ($_SESSION['user']['userGroup'] == UserGroup::Teacher) {
					$classes = json_decode($_SESSION['user']['classname']);
					if (!in_array($verify['classname'], $classes)) {
						$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
					}
				} elseif ($_SESSION['user']['userGroup'] == UserGroup::Admin) {
				} else {
					$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
				}
				break;
			case UserGroup::Admin:
				if ($_SESSION['user']['userGroup'] != Admin) {
					$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
				}
				break;
			default:
				$this->response(['status' => 'error', 'info' => '500 Internal Server Error!'], 500);
				break;
		}
		// 权限检查完毕
		$muser->update(['username' => $verify['username']], ['info' => $verify['info']]);
		$muser->deleteVerify($verify['username']);
		$this->response(['status' => 'success', 'info' => 'Operation Complete!']);
	}

	// GET /api/user/avatar
	// GET /api/user/{username}/avatar
	public function avatar($username = null)
	{

	}

	// POST /api/user/avatar
	public function uploadAvatar()
	{

	}
}
