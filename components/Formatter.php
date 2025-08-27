<?php

declare(strict_types=1);

namespace app\components;

use yii\i18n\Formatter as YiiFormatter;

class Formatter extends YiiFormatter
{
	public function asRange($min, $max): string
	{
		return $min === $max
			? $this->asDecimal($min, 0)
			: $this->asDecimal($min, 0) . ' - ' . $this->asDecimal($max, 0);
	}
}