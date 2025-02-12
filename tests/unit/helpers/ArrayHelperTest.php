<?php

namespace app\tests\unit\helpers;

use app\helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
	/**
	 * @covers       ArrayHelper::toDistributedValue
	 * @dataProvider providerToDistributedValue
	 */
	public function testToDistributedValue(array $input, int $value, array $expected): void
	{
		$result = ArrayHelper::toDistributedValue($input, $value);

		$this->assertEquals($expected, $result);
	}

	public static function providerToDistributedValue(): array
	{
		return [
			'basic case'                 => [
				[1, 2, 3, 4],
				4,
				[4, 3, 3, 4]
			],
			'single element in array'    => [
				[5],
				10,
				[15]
			],
			'no change needed'           => [
				[10, 20, 30],
				0,
				[10, 20, 30]
			],
			'large gap'                  => [
				[1, 10, 20],
				14,
				[13, 12, 20]
			],
			'empty array'                => [
				[],
				5,
				[]
			],
			'overflow value'             => [
				[1, 2, 3, 4],
				20,
				[8, 8, 7, 7]
			],
			'same values in input array' => [
				[2, 2, 2, 2],
				9,
				[5, 4, 4, 4]
			]
		];
	}
}