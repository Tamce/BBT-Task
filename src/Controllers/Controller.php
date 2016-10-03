<?php
namespace Tamce\BBT\Controllers;

class Controller
{
	public $request;
	public $queryString;
	public function __construct()
	{
		parse_str(file_get_contents('php://input'), $this->request);
		parse_str($_SERVER['QUERY_STRING'], $this->queryString);
	}

	public function response($data, $statusCode = null)
	{
		if (!empty($statusCode)) {
			http_response_code($statusCode);
		}
		if (is_array($data) or is_object($data)) {
			header('Content-Type: application/json');
			echo json_encode($data);
			exit();
		}
		echo $data;
		exit();
	}

	public function request($key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->request;
		}
		return isset($this->request[$key]) ? $this->request[$key] : $default;
	}

	public function queryString($key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->queryString;
		}
		return isset($this->queryString[$key]) ? $this->queryString[$key] : $default;
	}

	public function method($method = null)
	{
		if (is_null($method)) {
			return strtoupper($_SERVER['REQUEST_METHOD']);
		}
		return strtoupper($method) === strtoupper($_SERVER['REQUEST_METHOD']);
	}
}
