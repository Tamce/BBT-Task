<?php

// 基础需求不需要
class Relation extends SplEnum
{
	const __default = self::None;

	const None = 0;
	const Student = 1;
	const Teacher = 2;
	const Master = 3;
}

class AccountStatus extends SplEnum
{
	// Todo
	// 基础需求不需要
}
