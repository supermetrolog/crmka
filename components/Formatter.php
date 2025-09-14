<?php

declare(strict_types=1);

namespace app\components;

use app\enum\UserProfile\UserProfileGenderEnum;
use yii\i18n\Formatter as YiiFormatter;

class Formatter extends YiiFormatter
{
	public function asRange($min, $max): string
	{
		return $min === $max
			? $this->asDecimal($min, 0)
			: $this->asDecimal($min, 0) . ' - ' . $this->asDecimal($max, 0);
	}

	public function genderize(string $gender, string $maleForm, string $femaleForm): string
	{
		return $gender === UserProfileGenderEnum::MALE
			? $maleForm
			: $femaleForm;
	}
}