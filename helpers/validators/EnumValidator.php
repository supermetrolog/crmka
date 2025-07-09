<?php


namespace app\helpers\validators;

use app\enum\AbstractEnum;
use yii\base\InvalidConfigException;
use yii\validators\Validator;


class EnumValidator extends Validator
{
	public $message = '{attribute} is invalid.';

	/**
	 * @var class-string
	 */
	public string $class;

	/**
	 * @throws InvalidConfigException
	 */
	public function init(): void
	{
		parent::init();

		if (!is_subclass_of($this->class, AbstractEnum::class)) {
			throw new InvalidConfigException('EnumValidator::$class must be a subclass of AbstractEnum');
		}
	}

	public function validateValue($value): ?array
	{
		$isValid = call_user_func([$this->class, 'isValid'], $value);

		if (!$isValid) {
			return [$this->message, []];
		}

		return null;
	}
}
