<?php
/**
 * 文件提供单元测试类 Unit
 *
 * @author    ElfStack Dev Team
 * @copyright Copyright 2016 by ElfStack Dev Team, All rights reserved.
 */

namespace ElfStack;

use ElfStack\Unit\Describer;
use ElfStack\Unit\Expector;

/**
 * 单元测试接口类
 *
 * 为用户使用提供借口
 *
 * @package ElfStack
 * @subpackage UnitTest
 * @category UnitTest
 * @license MIT
 * @copyright ElfStack Dev Team 2016, all rights reserved.
 * @author Tamce[github.com/tamce] - ElfStack Dev Team
 */
class Unit
{
	/**
	 * 单元测试标题
	 *
	 * @var string
	 */
	public $title;

	/**
	 * 单元测试开始的时间戳
	 *
	 * 使用 microtime(true) 取得
	 *
	 * @var float
	 */
	public $startTime;

	/**
	 * 报告开头格式
	 *
	 * 可以使用变量，格式为：{{name}}
	 * 在开头中的这些变量将会被实际值替换：
	 * time, memory, numPassed, numFailed, numTotal, title
	 *
	 * 这里的 bootstrap 样式表目前暂时使用绝对路径
	 *
	 * @var string
	 */
	public $reportStart = 
<<<EOD
<html lang="zh-CN"><head>
<meta charset="utf-8"><title>{{title}} - Unit Test Report</title>
<link href="/static/style/bootstrap.min.css" rel="stylesheet" /></head>
<body style="font-family: Helvetica Neue, Helvetica, Microsoft Yahei;">
<center><h2>ElfStack Unit Test Report</h2><h3>{{title}}</h3></center>
<br><br>
<div style="margin: 0 5%;">
<table class="table table-striped table-responsive" style="table-layout: fixed;">
<thead><th>Description</th><th>Expected</th><th>Got</th><th>More Info</th></thead>
<tbody>
EOD;

	/**
	 * 报告项格式
	 *
	 * 可以使用变量，格式为：{{name}}
	 * 在开头中的这些变量将会被实际值替换：
	 * status, description, expected, real, info
	 *
	 * @var string
	 */
	public $reportItem = 
<<<EOD
<tr class="{{status}}">
<td><div style="word-wrap: break-word;">{{description}}</div></td>
<td><pre style="padding:5px;margin:0;">{{expected}}</pre></td>
<td><pre style="padding:5px;margin:0;">{{real}}</pre></td>
<td><div style="word-wrap: break-word; word-break: break-all;">{{info}}</div></td>
</tr>
EOD;

	/**
	 * 报告末尾格式
	 *
	 * 可以使用变量，格式为：{{name}}
	 * 在开头中的这些变量将会被实际值替换：
	 * time, memory, numPassed, numFailed, numTotal, title
	 *
	 * @var string
	 */
	public $reportEnd = '</tbody></table><hr><b>{{numTotal}} Tests Runned, <font color=green>{{numPassed}} Passed</font>, <font color=red>{{numFailed}} Failed</font>.</b><br>Memory usage: {{memory}}<br>Time used: {{time}}</div></body></html>';

	/**
	 * 成功情况下{{status}}的值
	 *
	 * @var string
	 */
	public $succeedText = 'success';

	/**
	 * 失败情况下{{status}}的值
	 *
	 * @var string
	 */
	public $failedText = 'danger';

	/**
	 * 已经保存的单元测试报告文本
	 *
	 * @var string
	 */
	protected $_result = '';

	/**
	 * 记录通过的测试数量
	 *
	 * @var integer
	 */
	protected $numPassed = 0;

	/**
	 * 记录失败的测试数量
	 *
	 * @var integer
	 */
	protected $numFailed = 0;

	/**
	 * 单元测试描述类实例
	 *
	 * @var object
	 */
	protected $describer;

	/**
	 * 构造函数，执行初始化工作
	 */
	public function __construct()
	{
		$this->describer = new Describer($this);
	}

	/**
	 * setup一个单元测试描述
	 *
	 * @param  string $description 描述文本
	 * @param  string $info        更多描述信息
	 * @return object 已经被正确设置的单元测试描述类实例
	 */
	public function describe($description, $info = '')
	{
		$this->describer->setUp($description, $info);
		return $this->describer;
	}

	/**
	 * 重新开始一组单元测试
	 *
	 * 清空已记录的数据，重新开始记录时间
	 *
	 * @param string $title 本组单元测试的标题
	 */
	public function start($title = 'No name test group')
	{
		$this->_result = '';
		$this->numPassed = 0;
		$this->numFailed = 0;
		$this->startTime = microtime(true);
		$this->title = $title;
	}

	/**
	 * 断言与期望相等
	 *
	 * @param  string $description 测试的描述文本
	 * @param  mixed  $var         要测试的变量或值
	 * @param  mixed  $value       期望的值
	 * @param  string $info        测试的更多描述信息
	 * @return bool   是否相等
	 */
	public function assertEqual($description, $var, $value, $info = '')
	{
		ob_start();
		var_dump($var);
		$var = ob_get_contents();
		ob_clean();
		var_dump($value);
		$value = ob_get_contents();
		ob_end_clean();

		$this->writeResult($description, $var === $value, $value, $var, $info);
		return $var === $value;
	}

	/**
	 * 断言与期望不相等
	 *
	 * @param  string $description 测试的描述文本
	 * @param  mixed  $var         要测试的变量或值
	 * @param  mixed  $value       期望的值
	 * @param  string $info        测试的更多描述信息
	 * @return bool   是否不相等
	 */
	public function assertUnequal($description = 'No Name Test', $var, $value, $info = '')
	{
		ob_start();
		var_dump($var);
		$var = ob_get_contents();
		ob_clean();
		var_dump($value);
		$value = ob_get_contents();
		ob_end_clean();
		
		$this->writeResult($description, $var !== $value, $value, $var, $info);
		return $var !== $value;
	}

	/**
	 * 执行断言方法
	 *
	 * 函数可通过返回bool值(或能被转换为bool型的其他类型)来设置测试是否通过；
	 * 也可通过返回包含键`__overide_unit_test`的数组或者对象
	 * 这样的话将会将其转换bool型作为判断测试通过的依据
	 *
	 * @param  string   $description 测试的描述文本
	 * @param  function $foo         可执行的函数
	 * @param  string   $info        测试的更多描述信息
	 * @return mixed    函数的返回值
	 */
	public function assert($description, $foo, $info = '')
	{
		if (is_callable($foo))
		{
			$func = is_array($foo) ? $foo[0]->{$foo[1]} : $foo;
			// 捕捉函数输出
			ob_start();
			$returnVal = $func();
			$functionOutput =
<<<EOF
Callable Function,
Outputs shows below.
<hr style="margin: 5px 0px; width: 30%; border-color: #888;" />
EOF;
			$functionOutput .= ob_get_contents();
			ob_end_clean();
			$passed = (bool) $returnVal;

			$overide = false;
			$overideVal = null;
			// 判断函数返回值转换为bool时是否需要被覆写
			if (is_array($returnVal) or is_object($returnVal)) {
				if (is_object($returnVal) and isset($returnVal->__overide_unit_test)) {
					$overide = true;
					$overideVal = $returnVal->__overide_unit_test;
				} elseif (is_array($returnVal) and isset($returnVal['__overide_unit_test'])) {
					$overide = true;
					$overideVal = $returnVal['__overide_unit_test'];
				}

				if ($overide) {
					$passed = (bool) $overideVal;
				}
			}

			$info .= empty($info) ? '' : '<hr style="border-color: #ddd; margin: 5px 0px; width: 30%;" />';
			if ($overide) {
				$info .= '<small>Return value overide: (bool) ' . ($passed ? 'true' : 'false') . '</small>';
			} else {
				$info .= '<small>Function return: (bool) ' . ($passed ? 'true' : 'false') . '</small>';
			}
			// 取得返回值的易读信息
			ob_start();
			var_dump($returnVal);
			$got = ob_get_contents();
			ob_end_clean();

			$this->writeResult($description, $passed, $functionOutput, $got, $info);
			return $returnVal;
		}
		throw new \Exception("Not a valid callable function in Class `Unit`!", 1);
	}

	/**
	 * 返回统计结果文本
	 *
	 * @return string 结果文本
	 */
	public function result()
	{
		$memory = memory_get_usage();
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$base = floor(log($memory, 1024));
	    $memory = $memory / pow(1024, $base) . ' ' . $units[$base];

		$time = (microtime(true) - $this->startTime) * 100 . ' ms';
		$patterns = array(	'{{memory}}' => $memory,
							'{{time}}' => $time,
							'{{numPassed}}' => $this->numPassed,
							'{{numFailed}}' => $this->numFailed,
							'{{numTotal}}' => $this->numPassed + $this->numFailed,
							'{{title}}' => $this->title
						);
		return 	$this->_parseVars($this->reportStart, $patterns) .
				$this->_result . 
				$this->_parseVars($this->reportEnd, $patterns);
	}

	/**
	 * 打印结果文本
	 */
	public function printResult()
	{
		echo $this->result();
	}

	/**
	 * 记录并写入单次测试的结果
	 *
	 * @param string  $description 测试的描述
	 * @param boolean $success     测试是否通过
	 * @param mixed   $expected    期望的值
	 * @param mixed   $real        实际得到的值
	 * @param string  $info        要记录的附加信息
	 */
	protected function writeResult($description, $success, $expected = '', $real = '', $info = '')
	{
		$expected = print_r($expected, true);
		$real = print_r($real, true);
		$this->numPassed += $success;
		$this->numFailed += !$success;
		$status = $success ? $this->succeedText : $this->failedText;

		$replaces = array('{{status}}' => $status,
						 '{{description}}' => $description,
						 '{{expected}}' => $expected,
						 '{{real}}' => $real,
						 '{{info}}' => $info
						);
		$result = $this->_parseVars($this->reportItem, $replaces);
		
		$this->_result .= $result;
	}

	/**
	 * 执行变量的替换
	 *
	 * @param  string $str 待处理的字符串
	 * @param  array  $patterns 包含search => replace对的数组
	 * @return string 处理结果
	 */
	protected function _parseVars($str, array $patterns)
	{
		foreach ($patterns as $pattern => $value) {
			$str = str_replace($pattern, $value, $str);
		}
		return $str;
	}
}	// End of class Unit
