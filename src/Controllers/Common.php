<?php
namespace Tamce\BBT\Controllers;

use Tamce\BBT\Models\User as MUser;

class Common
{
	public function index()
	{
		echo 'Hello';
	}

	public function add()
	{
		$user = new MUser;
		$user->create(['username' => 'tamce', 'password' => '12345', 'class' => '1', 'info' => 'info', 'userGroup' => 'admin', 'relationType' => 'admin']);
	}
}
