<?php
require __DIR__ . '/../vendor/autoload.php';

use ElfStack\Renderer;

// 关闭 E_NOTICE 级别错误输出
// error_reporting(E_ALL & ~E_NOTICE);

// 从 HTTP 头中获取会话信息
if (isset($_SERVER['HTTP_X_SESSION_ID'])) {
	session_id($_SERVER['HTTP_X_SESSION_ID']);
	session_start();
	// 确保重现会话的令牌一致
	if (!isset($_SESSION['credential'], $_SERVER['HTTP_X_CREDENTIAL']) or $_SERVER['HTTP_X_CREDENTIAL'] != $_SESSION['credential']) {
		header('Content-Type: application/json');
		die(json_encode(['status' => 'error', 'info' => '401 Unauthorized']));
	}
} else {
	session_start();
}

// Setup Renderer default path
Renderer::path(__DIR__ . '/../src/Views/');

// Setup Router and Start routes
require __DIR__ . '/../src/Config/Routes.php';
