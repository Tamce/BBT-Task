<?php
namespace Tamce\BBT\Models;

use Exception;
use Tamce\BBT\Core\Helper;

class User extends Model
{
	// 注意：顺序影响行为
	protected $required = ['username', 'password', 'class', 'info', 'userGroup', 'relationType'];
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

	public function update()
	{

	}

	public function find(array $cond)
	{
		
	}
}
