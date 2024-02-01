<?php

declare(strict_types=1);

namespace app\helpers;

class ColumnGridsHelper
{
	public const COLUMN_GRID_LIST = [
		1  => '6x6',
		2  => '6x9',
		3  => '6x12',
		4  => '6x18',
		5  => '6x24',
		6  => '9x9',
		7  => '9x12',
		8  => '9x18',
		9  => '9x24',
		10 => '12x12',
		11 => '12x18',
		12 => '12x24',
		13 => 'Без колонн',
	];

	public static function toHumanReadable(array $column_grids): array
	{
		$result = [];

		foreach ($column_grids as $value) {
			$result[] = self::COLUMN_GRID_LIST[$value];
		}

		return $result;
	}
}