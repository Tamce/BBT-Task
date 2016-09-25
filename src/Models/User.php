<?php
namespace Tamce\BBT\Models;

use Exception;
use PDO;
use Tamce\BBT\Core\Helper;

class User extends Model
{
	protected $required = ['username', 'password', 'info', 'userGroup'];
	protected $cols = ['id', 'username', 'password', 'info', 'userGroup', 'accountStatus'];
	public function __construct()
	{
		parent::__construct();
		$this->connect();
	}

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

	public function find(array $cond)
	{
		$arr = $this->where($cond);
		$stat = $this->pdo->prepare('SELECT * FROM `user` WHERE ' . $arr[0]);
		$stat->execute($arr[1]);
		return $stat->fetchAll(PDO::FETCH_ASSOC);
	}
}
