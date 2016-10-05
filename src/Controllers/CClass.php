<?php
namespace Tamce\BBT\Controllers;

use Tamce\BBT\Models\MClass;
use Tamce\BBT\Core\Helper;
use UserGroup;
use PDO;

// 由于命名冲突问题故使用 CClass
class CClass extends Controller
{
	// POST /api/class
	public function create()
	{
		Helper::ensureLogin();
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
			$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
		}
		$mclass = new MClass;
		$result = $mclass->find(['classname' => $this->request('classname')]);
		if (count($result) > 0) {
			$this->response(['status' => 'error', 'info' => 'This class is existed!']);
		}
		$mclass->create(['classname' => $this->request('classname'), 'info' => '{}']);
		$this->response(['status' => 'success', 'data' => $mclass->find(['classname' => $this->request('classname')])[0]]);
	}

	// GET /api/class
	public function listClass()
	{
		Helper::ensureLogin();
		$mclass = new MClass;
		$this->response(['status' => 'success', 'data' => $mclass->all()]);
	}

	// GET /api/class/{classname}
	public function show($classname)
	{
		Helper::ensureLogin();
		$mclass = new MClass;
		$result = $mclass->find(['classname' => $classname]);
		if (count($result) == 0) {
			$this->response(['status' => 'error', 'info' => 'Class Not Found!'], 404);
		}
		$this->response(['status' => 'success', 'data' => $result[0]]);
	}

	// DELETE /api/class/{classname}
	public function delete($classname)
	{
		Helper::ensureLogin();
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
			$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
		}
		$mclass = new MClass;
		$mclass->delete(['classname' => $classname]);
	}

	// GET /api/class/{classname}/{type}
	public function member($classname, $type)
	{
		Helper::ensureLogin();
		Helper::loadConstants();
		$type = strtolower($type);
		if ($_SESSION['user']['userGroup'] == UserGroup::Student) {
			if ($_SESSION['user']['classname'] != $classname) {
				$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
			}
		}
		$where = '';
		switch ($type) {
			case 'students':
			case 'student':
				$where = '`userGroup`='.UserGroup::Student;
				break;
			case 'teachers':
			case 'teacher':
				$where = '`userGroup`='.UserGroup::Teacher;
				break;
			case 'all':
				$where = '`userGroup`='.UserGroup::Student.' OR `userGroup`='.UserGroup::Teacher;
				break;
		}

		$mclass = new MClass;
		$stat = $mclass->pdo->prepare("SELECT * FROM `user` WHERE `classname` LIKE ? AND ($where)");
		$stat->execute(["%$classname%"]);
		$like = $stat->fetchAll(PDO::FETCH_ASSOC);
		$result = [];
		foreach ($like as &$p) {
			if ($p['userGroup'] == UserGroup::Teacher) {
				if (in_array($classname, json_decode($p['classname'], true))) {
					$result[] = $p;
				}
			} else {
				$result[] = $p;
			}
		}
		$count = $this->queryString('count', count($result));
		$count = $count > count($result) ? count($result) : $count;
		$data = [];
		for ($i = (int)$this->queryString('begin'); $i < count($result); ++$i) {
			$data[] = Helper::packUser($result[$i]);
			if (count($data) == $count) break;
		}
		$this->response(['status' => 'success', 'data' => $data, 'totalCount' => count($result)]);
	}

	// GET /api/export/{classname}/{type}
	public function export($classname, $type)
	{
		Helper::ensureLogin();
		Helper::loadConstants();
		if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
			$this->response(['status' => 'error', 'info' => 'User does not have privilege!'], 403);
		}

		$type = strtolower($type);
		$where = '';
		switch ($type) {
			case 'students':
			case 'student':
				$where = '`userGroup`='.UserGroup::Student;
				break;
			case 'teachers':
			case 'teacher':
				$where = '`userGroup`='.UserGroup::Teacher;
				break;
			case 'all':
				$where = '`userGroup`='.UserGroup::Student.' OR `userGroup`='.UserGroup::Teacher;
				break;
		}

		$mclass = new MClass;
		$stat = $mclass->pdo->prepare("SELECT * FROM `user` WHERE `classname` LIKE ? AND ($where)");
		$stat->execute(["%$classname%"]);
		$like = $stat->fetchAll(PDO::FETCH_ASSOC);
		$result = [];
		foreach ($like as &$p) {
			if ($p['userGroup'] == UserGroup::Teacher) {
				if (in_array($classname, json_decode($p['classname'], true))) {
					$result[] = Helper::packUser($p);
				}
			} else {
				$result[] = Helper::packUser($p);
			}
		}
		Helper::exportXls($result);
	}
}
