<?php
namespace Tamce\BBT\Models;

class User extends Model implements Tamce\BBT\Core\IDatabase
{
	public function __construct()
	{
		parent::__construct();
		$this->connect();
	}

	public function create()
	{

	}

	public function update()
	{

	}
}
