<?php


namespace app\helpers;

use Closure;


class ValidatorHelper
{
	/**
	 * @param string $attribute
	 *
	 * @return Closure
	 */
	public static function whenIsArray(string $attribute): Closure
	{
		return function ($model) use ($attribute) {
			return isset($model->$attribute) && ArrayHelper::isArray($model->$attribute);
		};
	}

	/**
	 * @param string $attribute
	 *
	 * @return Closure
	 */
	public static function whenIsNotArray(string $attribute): Closure
	{
		return function ($model) use ($attribute) {
			return isset($model->$attribute) && !ArrayHelper::isArray($model->$attribute);
		};
	}
}
