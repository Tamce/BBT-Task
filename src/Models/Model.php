<?php
namespace Tamce\BBT\Models;

use PDO;

class Model
{
	protected $pdo;
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
}
