<?php
namespace ElfStack\Unit;

use ElfStack\Unit;

/**
 * 单元测试描述类
 *
 * 被单元测试接口类所包含使用
 *
 * @package ElfStack
 * @subpackage UnitTest
 * @category UnitTest
 * @license MIT
 * @copyright ElfStack Dev Team 2016, all rights reserved.
 * @author Tamce[github.com/tamce] - ElfStack Dev Team
 */
class Describer
{
	/**
	 * 某次测试的描述
	 *
	 * @var string
	 */
	public $description;

	/**
	 * 某次测试的更多描述信息，将显示为{{info}}
	 *
	 * @var string
	 */
	public $info;

	/**
	 * 单元测试接口类实例
	 *
	 * 存储该描述类所属于的单元测试接口类
	 *
	 * @var object
	 */
	protected $_unit;

	/**
	 * 构造函数，执行初始化工作
	 *
	 * @param object $unit 单元测试接口类实例
	 */
	function __construct(Unit $unit)
	{
		$this->_unit = $unit;
		$this->expector = new Expector($unit);
	}

	/**
	 * setup一个单元测试描述
	 *
	 * @param string $description 描述文本
	 * @param string $info        更多描述信息
	 */
	function setUp($description, $info)
	{
		$this->description = $description;
		$this->info = $info;
	}

	/**
	 * 为单元测试setup待测试的值
	 *
	 * @param  mixed  $val 一个变量或值
	 * @return object 已经被设置好的单元测试期望类实例
	 */
	function expect($val)
	{
		$this->expector->setUp($val, $this->description, $this->info);
		return $this->expector;
	}

	/**
	 * 测试断言
	 *
	 * @param  function $foo 一个可被调用的函数或者是包含[实例,方法]的数组，函数应该返回bool值
	 * @return bool 断言是否通过
	 */
	public function assert($foo)
	{
		return $this->_unit->assert($this->description, $foo);
	}
}	// End of class Describer
