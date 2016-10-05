<?php
namespace ElfStack;

defined('ELFENRO_ROUTE_PATH') ? '' : define('ELFENRO_ROUTE_PATH', urldecode(parse_url($_SERVER['REQUEST_URI'])['path']));
use Exception;

class Router
{
	/**
	 * 用来标记路由是否完成,如果完成则之后的路由规则实效
	 *
	 * @static
	 * @var boolean
	 */
	static public $done = false;

	static public $classPrefix = '';

	/**
	 * 需要分析的uri，从常量 ELFENRO_ROUTE_PATH 中获取
	 *
	 * @static
	 * @var string
	 */
	static public $uri = ELFENRO_ROUTE_PATH;
	
	static public function __callStatic($foo, array $args)
	{
		if (count($args) != 2) {
			throw new Exception("Invalid count of arguments.");
		}
		// 如果要调用的静态方法名称是以下其中一种
		// 则进行指定 HTTP 方法的请求匹配
		if (in_array($foo, ['get', 'post', 'patch', 'delete', 'put'])) {
			$args[] = $foo;
			return call_user_func_array('self::route', $args);
		}
		throw new \Exception('Call to undefined static function: '.$foo);
	}

	static public function method($method, $allowOverride = true)
	{
		// 从 HTTP 头中获取 X-Method-Override 来重写 HTTP 动词
		if ($allowOverride and isset($_SERVER['HTTP_X_METHOD_OVERRIDE'])) {
			return strtolower($_SERVER['HTTP_X_METHOD_OVERRIDE']) === strtolower($method);
		}
		return strtolower($_SERVER['REQUEST_METHOD']) === strtolower($method);
	}

	static public function route($uri, $callback, $method = null, $allowOverride = true)
	{
		if (self::$done) {
			return false;
		}
		if (self::reroute($uri, $callback, $method, $allowOverride)) {
			self::$done = true;
			return true;
		}
		return false;
	}

	static public function reroute($uri, $callback, $method = null, $allowOverride = true)
	{
		if (!empty($method)) {
			if (self::method($method, $allowOverride) === false) {
				return false;
			}
		}
		if ($uri === '*') {
			$uri = '(.*)';
		}
		$matches = array();
		$uri = preg_replace('({[\w ]+})', '([^/]+)', $uri);
		if (preg_match('#^'.$uri.'$#', self::$uri, $matches) > 0) {
			array_shift($matches);
			self::call($callback, $matches);
			return true;
		}
		return false;
	}

	static protected function call($callback, $args)
	{
		if (is_callable($callback)) {
			return call_user_func_array($callback, $args);
		}

		// if $callback is string, we analyse it as ((file#)class@)method
		if (is_string($callback)) {
			$arr = [];
			preg_match('/^(([^\#]+)\#){0,1}(([^@]+)@){0,1}(\w+)$/', $callback, $arr);
			$arr = ['file' => $arr[2], 'class' => $arr[4], 'method' => $arr[5]];
			if (empty($arr['method'])) {
				throw new Exception('Invalid argument(s), Cannot analyse action.');
			}
			// Pass to array treater
			$callback = $arr;
		}

		if (is_array($callback)) {
			if (!empty($callback['file'])) {
				if (!file_exists($callback['file'])) {
					throw new Exception("Cannot find specific file: ".$callback['file']);
				}
				require_once($callback['file']);
			}
			if (!empty($callback['class'])) {
				$class = $callback['class'];
				if (!class_exists($class)) {
					throw new Exception('The specific class `'.$class.'` does not exists in scope!');
				}
				$method = [new $class, $callback['method']];
				if (!method_exists($method[0], $method[1])) {
					throw new Exception('Non-exist method `'.$method[1].'` in class: '.$class);
				}
			} else {
				$method = $callback['method'];
			}

			if (!is_callable($method)) {
				throw new Exception('Cannot call specific action: '.print_r($method, true));
			}
			return call_user_func_array($method, $args);
		}
	}
}

return Router::class;
