<?php
namespace ElfStack\Unit;

use ElfStack\Unit;

/**
 * 单元测试期望类
 *
 * 被单元测试描述类所包含使用
 *
 * @package ElfStack
 * @subpackage UnitTest
 * @category UnitTest
 * @license MIT
 * @copyright ElfStack Dev Team 2016, all rights reserved.
 * @author Tamce[github.com/tamce] - ElfStack Dev Team
 */
class Expector
{
	/**
	 * 某次测试的描述
	 *
	 * @var string
	 */
	public $description;

	/**
	 * 某次测试的更多描述信息
	 *
	 * @var string
	 */
	public $info;

	/**
	 * 要测试的变量值
	 *
	 * @var mixed
	 */
	public $var;

	/**
	 * 单元测试接口类实例
	 *
	 * @var object
	 */
	protected $_unit;

	/**
	 * 构造函数，执行初始化工作
	 *
	 * @param Unit $unit 单元测试类接口
	 */
	function __construct(Unit $unit)
	{
		$this->_unit = $unit;
	}

	/**
	 * 为单元测试setup一个期望
	 *
	 * @param mixed  $var         要测试期望的值
	 * @param string $description 描述文本
	 * @param string $info        更多描述信息
	 */
	function setUp($var, $description, $info)
	{
		$this->var = $var;
		$this->description = $description;
		$this->info = $info;
	}

	/**
	 * 比较是否与期望值相等
	 *
	 * @param  mixed $value 期望值
	 * @return bool 是否相等
	 */
	function toBe($value)
	{
		return $this->_unit->assertEqual($this->description, $this->var, $value, $this->info);
	}

	/**
	 * 比较是否不与期望值相等
	 *
	 * @param  mixed $value 期望值
	 * @return bool 是否不等
	 */
	function notToBe($value)
	{
		return $this->_unit->assertUnequal($this->description, $this->var, $value, $this->info);
	}
}	// End of class Expetor
