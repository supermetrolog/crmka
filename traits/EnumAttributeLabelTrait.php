<?php

namespace app\traits;

use app\enum\AbstractEnum;

trait EnumAttributeLabelTrait
{
	/**
	 * @param class-string<AbstractEnum> $enumClass
	 */
	public function getEnumLabel(string $attribute, string $enumClass): ?string
	{
		return $enumClass::label($this->$attribute);
	}
}