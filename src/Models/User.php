<?php
namespace Tamce\BBT\Models;

use Exception;
use PDO;
use Tamce\BBT\Core\Helper;

class User extends Model
{
	protected $required = ['username', 'password', 'userGroup'];
	protected $cols = ['id', 'username', 'password', 'name', 'gender', 'userGroup', 'avatar', 'classname', 'newUser'];

	public function create(array $info)
	{
		// 确保 $required 键已被赋值
		foreach ($this->required as $req) {
			if (!isset($info[$req])) {
				throw new Exception("`$req` is required to create a User!");
			}
		}

		$info['password'] = Helper::encryptPassword($info['password']);
		$this->insert('user', $this->required, $info);
	}

	// Todo 验证&过滤
	public function filter(array $info)
	{
		return true;
	}

	public function update(array $cond, array $new)
	{
		$this->pdo->beginTransaction();
		$str = '';
		$arr = [];
		foreach ($new as $key => $value) {
			if (!in_array($key, $this->cols)) {
				throw new Exception("Wrong field given `$key`!");
			}
			$str .= "$key = ?, ";
			$arr[] = $value;
		}
		$str = substr($str, 0, -2);
		$where = $this->where($cond);
		$stat = $this->pdo->prepare("UPDATE `user` SET $str WHERE {$where[0]}");
		$arr = array_merge($arr, $where[1]);
		$stat->execute($arr);
		Helper::treatPdoError($stat->errorInfo());
		$this->pdo->commit();
	}

	public function updateVerify(array $info)
	{
		$this->deleteVerify($info['username']);
		$this->insert('verify', ['username', 'name', 'gender', 'classname', 'userGroup'], $info);
	}

	public function deleteVerify($username)
	{
		$this->pdo->prepare('DELETE FROM `verify` WHERE `username`=?')->execute([$username]);
	}

	public function findVerify(array $cond)
	{
		$arr = $this->where($cond);
		$stat = $this->pdo->prepare('SELECT * FROM `verify` WHERE ' . $arr[0]);
		$stat->execute($arr[1]);
		return $stat->fetchAll(PDO::FETCH_ASSOC);
	}

	public function find(array $cond)
	{
		$arr = $this->where($cond);
		$stat = $this->pdo->prepare('SELECT * FROM `user` WHERE ' . $arr[0]);
		$stat->execute($arr[1]);
		return $stat->fetchAll(PDO::FETCH_ASSOC);
	}

	public function all()
	{
		$stat = $this->pdo->query('SELECT * FROM `user`');
		Helper::treatPdoError($stat->errorInfo());
		return $stat->fetchAll(PDO::FETCH_ASSOC);
	}
}
