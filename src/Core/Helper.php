<?php
namespace Tamce\BBT\Core;

class Helper
{
	const SALT_LENGTH = 15;
	static public function encryptPassword($password, $salt = null)
	{
		if (empty($salt)) {
			$salt = self::randomString(self::SALT_LENGTH);
		}
		return $salt . crypt($password, $salt);
	}

	static public function validatePassword($password, $hash)
	{
		return hash_equals($hash, self::encryptPassword($password, substr($hash, 0, self::SALT_LENGTH)));
	}

	static public function randomString($num, $dic = null)
	{
		if (empty($dic)) {
			$dic = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		}
		if ($num < 0) {
			$num *= -1;
		}
		$result = '';
		for ($i = 0; $i < $num; ++$i) {
			$result .= $dic[mt_rand(0, strlen($dic) - 1)];
		}
		return $result;
	}
}
