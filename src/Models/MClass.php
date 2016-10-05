<?php
namespace Tamce\BBT\Models;

use PDO;
use Exception;

class MClass extends Model
{
	protected $required = ['classname', 'info'];
	public function create(array $info)
	{
		foreach ($this->required as $key) {
			if (!isset($info[$key])) {
				throw new Exception("`$key` is required when creating a class.");
			}
		}
		$this->insert('class', $this->required, $info);
	}

	public function delete(array $cond)
	{
		$arr = $this->where($cond);
		$this->pdo->prepare('DELETE FROM `class` WHERE '.$arr[0])->execute($arr[1]);
	}

	public function find(array $cond)
	{
		$arr = $this->where($cond);
		$stat = $this->pdo->prepare('SELECT * FROM `class` WHERE '.$arr[0]);
		$stat->execute($arr[1]);
		return $stat->fetchAll(PDO::FETCH_ASSOC);
	}

	public function all()
	{
		return $this->pdo->query('SELECT * FROM `class`')->fetchAll(PDO::FETCH_ASSOC);
	}
}
