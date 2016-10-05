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
				$this->response(['status' => 'success', 'info' => 'Login successfully', 'data' => $_SESSION['user'], 'session' => session_id(), 'credential' => $_SESSION['credential']]);
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
		$stat = $user->pdo->prepare('SELECT * FROM `user` WHERE `name` LIKE ?');
		$stat->execute(['%'.$this->queryString('search').'%']);
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
			if (in_array($p['classname'], $_SESSION['user']['classname'])) {
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
					$classes = $_SESSION['user']['classname'];
					if (!in_array($verify['classname'], $classes)) {
						$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
					}
				} elseif ($_SESSION['user']['userGroup'] == UserGroup::Admin) {
				} else {
					$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
				}
				break;
			case UserGroup::Admin:
				if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
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
		echo empty($data) ? 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAADwUlEQVRoge1a8fEpMRC+35v3Px3QAR0cFdABHdABHdABKkAFqIAOUAEd3LvvZjKTOdlkk0ty74dvZscgl+yXTTab3fvJciQfhD91KxAbX8Lvjo8j/Df0ALfbLTmdTsnlcikE3yEy2u12Id1ut5A0TYvvQZAFwPV6zebzeZYrjRPASfAs+kBfPuGV8Pl8zsbjsTNJStAn+vYBL4Qfj0cQoiriGKtWwqvVKms2m8HJCsFY2+22HsIxrErJdDqNRxjLqtfr1UZWCHSwXeLWhDFAfnTUTlYIdLEhbU34f7CsytJBCNe5Z00ymUz8EoY3pgZrtVrZ4XAo2gwGA+9k0OdisSjG6HQ6ZDuO92YRxh7RHT3l2UV0NJvNKhMdjUYvkRaIU+2ho2k//xSsDciXcrLZbMj/85lNhsPhy++ImfEsIOJkESPn+674PB6Pxefz+SxibXzPFU/ysLJoXwb+7/f7pC75JCXr9ZomY7IuQrrEYAkstViABU366OJv4/VwuVyamkQFrG8CVgcJ3WxiphLGXotpYYzF0YmystbC2r0gYbfbsdr5QPkuTYHUSTeb3PtsTAvDpzQaDaNO0F0FkjB3OeP4iQ3d0SSLalmThHWBhhDMdNX7qSsQ7Jj0A4cyyD2MM9EEnL0crxkCqnO/DBWHSoRF8FAHvBO+3+/GDoNlFhlQRWFlqDiQhLnuvy5wtpKKw8cl4n8tYY6PUYEknLt9Z2ViALcrE1QcSMIchxQzpCyDY2EVh0pLGjWjuiDu0bYgCXPcviiOxQaW836/N7ZTcSAJc4MK7d0zELh3dCUHKlblZBaE+Cp0cYALAbe0Y3V5AHQZQlmgQAzSNkUAXC5U0DotTrwKYE8hsebqSDiAv8AY3PNXJA9fYJrRRDF7aZqS1zOftVwAyxKFM9VYOqGurcasJXLDckdYUgLIdFDE0Q4V/NzqVlV8tMUzeNa1hgWdKTilaeWqHT5Nl3EkCjhWR1+c9I1JdGOxKg+oLJQ7Rc5IkDZlEm3SQJxMi05MNSZ2qUU18/LS0SlqXcN1JMtJObGLaShUqQaR9ycsDYcm/sOxxq3qyZD7sBFvxTQB1dLWOQhXuBD2Xi4VKHttiCo7WAW2hFFO5cLplQdVBObz/LUhDF2CvvIAYACVpWUPLqoWUN4WXMKwbPCXWmSo9nRZQhF29R2VX0yDZ9QFC74JY6wqPsPbq4eUtX0ShlVrf/VQBs7k8t72QRh9+qpQBnl9GFZAOAnFXZafCGDQh+9iHeullnfCr03Eu+JL+N3xJfzu+Af73lrj2oFmcQAAAABJRU5ErkJggg==' : $data;
	}

	// POST /api/user/avatar
	public function uploadAvatar()
	{
		Helper::ensureLogin();
		$muser = new MUser;
		$avatar = file_get_contents('php://input');
		$muser->update(['username' => $_SESSION['user']['username']], ['avatar' => $avatar]);
		$_SESSION['avatar'] = $avatar;
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
