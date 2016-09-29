<?php
require __DIR__ . '/../vendor/autoload.php';

use ElfStack\Renderer;

// 关闭 E_NOTICE 级别错误输出
error_reporting(E_ALL & ~E_NOTICE);
if (isset($_SERVER['HTTP_X_SESSION_ID'])) {
	session_id($_SERVER['HTTP_X_SESSION_ID']);
}
session_start();

// Setup Renderer default path
Renderer::path(__DIR__ . '/../src/Views/');

// Setup Router and Start routes
require __DIR__ . '/../src/Config/Routes.php';
