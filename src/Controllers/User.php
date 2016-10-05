<?php
namespace Tamce\BBT\Controllers;

use Tamce\BBT\Models\User as MUser;
use Tamce\BBT\Core\Helper;
use PDO;
use UserGroup;
use Constants;

class User extends Controller
{
	// POST /api/authorization
	public function authorize()
	{
		if (isset($_SESSION['login']) && $_SESSION['login']) {
			$this->response(['status' => 'notice', 'info' => 'You have already login!', 'data' => $_SESSION['user'], 'session' => session_id(), 'credential' => $_SESSION['credential']]);
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
				$_SESSION['avatar'] = $user['avatar'];
				$_SESSION['user'] = Helper::packUser($user);
				$_SESSION['login'] = true;
				$_SESSION['credential'] = Helper::randomString(15);
				$this->response(['status' => 'success', 'info' => 'Login successfully', 'data' => $user, 'session' => session_id(), 'credential' => $_SESSION['credential']]);
			}
		}
		$this->response(['status' => 'error', 'info' => 'User not exist or incorrect password!'], 401);
	}

	// GET /api/users?begin=xxx&count=xxx&search=xxx
	// TODO Search
	public function listUser()
	{
		Helper::ensureLogin();
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
			$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
		}

		$user = new Muser;
		$stat = $user->pdo->prepare('SELECT * FROM `user` WHERE `name` LIKE ?')->execute(['%'.$this->queryString('search').'%']);
		$result = $stat->fetchAll(PDO::FETCH_ASSOC);
		$data = [];
		for ($i = (int) $this->queryString('begin'); $i < $this->queryString('count', count($result)); ++$i) {
			$data[] = Helper::packUser($result[$i]);
		}
		$this->response(['status' => 'success', 'data' => $data, 'totalCount' => count($result)]);
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
					$user->update(['username' => $_SESSION['user']['username']], [
							'name' => $this->request('name'),
							'gender' => $this->request('gender'),
							'classname' => $this->request('classname'),
							'newUser' => 0
						]);
					$_SESSION['user'] = Helper::packUser($user->find(['username' => $_SESSION['user']['username']])[0]);
					$this->response(['status' => 'success', 'info' => 'Data change successfully!', 'data' => $_SESSION['user']]);
				}
				$user->updateVerify(['username' => $_SESSION['user']['username'],
						'name' => $this->request('name'),
						'gender' => $this->request('gender'),
						'classname' => $this->request('classname'),
						'userGroup' => $_SESSION['user']['userGroup']
					]);
				$this->response(['status' => 'notice', 'info' => "Your request to edit your profile has saved!\nWaiting for verify...", 'data' => $_SESSION['user']]);
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

		Helper::loadConstants();
		if ($this->request('userGroup') == UserGroup::Admin) {
			if ($this->request('key') != Constants::AdminKey) {
				$this->response(['status' => 'error', 'info' => 'Invalid key, you cannot create an Admin!']);
			}
		} elseif ($this->request('userGroup') == UserGroup::Teacher) {
			if ($this->request('key') != Constants::TeacherKey) {
				$this->response(['status' => 'error', 'info' => 'Invalid key, you cannot create a Teacher!']);
			}
		}

		$muser = new MUser;

		// 先确定没有相同的用户名
		if (count($muser->find(['username' => $this->request('username')])) > 0) {
			$this->response(['status' => 'error', 'info' => 'This username is existed!']);
			return;
		}

		// 构造数组
		$info = ['username' => $this->request('username'),
				 'password' => $this->request('password'),
				 'userGroup' => $this->request('userGroup', UserGroup::Student)];
		$muser->filter($info);
		$muser->create($info);
		$_SESSION['user'] = Helper::packUser($muser->find(['username' => $info['username']])[0]);
		$_SESSION['login'] = true;
		$_SESSION['avatar'] = '';
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

		// 限制学生只能查看同班的信息
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] != UserGroup::Student or
		   ($_SESSION['user']['userGroup'] == UserGroup::Student and
		   	($info['userGroup'] == UserGroup::Teacher ?
		   		in_array($_SESSION['user']['classname'], json_decode($info['classname'], true)) :
		   		$_SESSION['user']['classname'] == $info['classname']))) {
			$this->response(['info' => 'success', 'data' => Helper::packUser($info)]);
		}
		$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
	}

	// GET /api/verify_update
	public function verifyList()
	{
		Helper::ensureLogin();
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] == UserGroup::Student) {
			$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
		}
		$muser = new MUser;
		$result = $muser->pdo->query('SELECT * FROM `verify`')->fetchAll(PDO::FETCH_ASSOC);
		if ($_SESSION['user']['userGroup'] == UserGroup::Admin) {
			$this->response(['status' => 'success', 'data' => $result]);
		}
		// Teacher:
		$data = [];
		foreach ($result as &$p) {
			if (in_array($p['classname'], json_decode($_SESSION['user']['classname'], true))) {
				$data[] = $p;
			}
		}
		$this->response(['status' => 'success', 'data' => $data]);

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
					$classes = json_decode($_SESSION['user']['classname'], true);
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
		$muser->update(['username' => $verify['username']], ['classname' => $verify['classname'], 'name' => $verify['name'], 'gender' => $verify['gender']]);
		$muser->deleteVerify($verify['username']);
		$this->response(['status' => 'success', 'info' => 'Operation Complete!']);
	}

	// GET /api/user/avatar
	// GET /api/user/{username}/avatar
	public function avatar($username = null)
	{
		Helper::ensureLogin();
		if (is_null($username)) {
			$data = $_SESSION['avatar'];
		} else {
			$muser = new MUser;
			$result = $muser->find(['username' => $username]);
			if (count($result) == 0) {
				$this->response(['status' => 'error', 'info' => 'User Not Found!'], 404);
			}
			$data = $result[0]['avatar'];
		}
		// echo $data;
		echo empty($data) ? '<svg aria-hidden="true" version="1.1" viewBox="0 0 16 16" width="50" height="50"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path></svg>' : $data;
	}

	// POST /api/user/avatar
	public function uploadAvatar()
	{
		Helper::ensureLogin();
		$muser = new MUser;
		$muser->update(['username' => $_SESSION['user']['username']], ['avatar' => $this->request('avatar')]);
		$this->response(['status' => 'success', 'info' => 'Operation Complete!']);
	}

	// GET /api/export/all
	public function export()
	{
		Helper::ensureLogin();
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
			$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
		}

		$muser = new Muser;
		$result = $muser->all();
		foreach ($result as &$p) {
			$p = Helper::packUser($p);
		}
		Helper::exportXls($result);
	}
}
