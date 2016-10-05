<?php
namespace Tamce\BBT\Models;

use PDO;
use Tamce\BBT\Core\Helper;
use Exception;

class Model
{
	public $pdo;
	public function __construct()
	{
		$this->connect();
	}
	
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
		$this->pdo->beginTransaction();
		// 构造类似 username,password,class 这样的字符串用于 sql 语句
		$col = implode(',', $cols);

		// 构造 ?,?,? 用于 pdo 的 prepare
		$val = substr(str_repeat('?,', count($cols)), 0, -1);
		$stat = $this->pdo->prepare("INSERT INTO `$table` ($col) VALUES ($val)");

		// 按顺序填充值
		$arr = [];
		foreach ($cols as $key) {
			if (!isset($values[$key])) {
				throw new Exception("`$key` is required when insert into `$table`!");
			}
			$arr[] = $values[$key];
		}
		$stat->execute($arr);
		Helper::treatPdoError($stat->errorInfo());
		$this->pdo->commit();
	}

	/**
	 * @return array [0 => pdo构造语句, 1 => 对应的值]
	 */
	protected function where($cond)
	{
		$arr = [];
		$condition = '';
		foreach ($cond as $key => $value) {
			$condition .= "`$key` = ? AND ";
			$arr[] = $value;
		}
		$condition = substr($condition, 0, -5);
		return [$condition, $arr];
	}
}
