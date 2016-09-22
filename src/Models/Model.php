<?php
namespace Tamce\BBT\Models;

use PDO;

class Model
{
	public $pdo;
	public function __construct() {}
	public function connect()
	{
		if (!is_null($this->pdo)) {
			return $this->pdo;
		}
		$db = include __DIR__ . '/../Config/Database.php';
		$this->pdo = new PDO("{$db['driver']}:host={$db['host']};dbname={$db['dbname']}", $db['user'], $db['pass']);
	}

	public function disconnect()
	{
		$this->pdo = null;
	}

	protected function insert($table, array $cols, array $values)
	{
		echo '<pre>';
		$this->pdo->beginTransaction();
		// 构造类似 username,password,class 这样的字符串用于 sql 语句
		$col = implode(',', $cols);

		// 构造 ?,?,? 用于 pdo 的 prepare
		$val = substr(str_repeat('?,', count($cols)), 0, -1);
		var_dump("INSERT INTO `$table` ($col) VALUES ($val)");
		$stat = $this->pdo->prepare("INSERT INTO `$table` ($col) VALUES ($val)");

		// 按顺序填充值
		$arr = [];
		foreach ($cols as $key) {
			if (!isset($values[$key])) {
				throw new Exception("`$key` is required when insert into `$table`!");
			}
			$arr[] = $values[$key];
		}
		var_dump($arr);
		$stat->execute($arr);
		var_dump($this->pdo->commit());
	}
}
