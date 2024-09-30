<?php


namespace app\helpers\validators;

use yii\base\NotSupportedException;
use yii\validators\RequiredValidator;
use yii\validators\Validator;


class AnyValidator extends Validator
{
	public $message = null;

	/**
	 * @var string|Validator $rule
	 */
	public $rule = null;

	public function init()
	{
		parent::init();

		if ($this->message === null) {
			$this->message = 'At least one of the attributes ({attributes}) must be filled';
		}

		if ($this->rule === null) {
			$this->rule = RequiredValidator::class;
		}
	}

	private function createEmbeddedValidator($model = null, $current = null): Validator
	{
		$rule = $this->rule;

		if ($rule instanceof Validator) {
			return $rule;
		}

		return Validator::createValidator($rule, $model, $this->attributes, ['current' => $current]);
	}

	/**
	 * @throws NotSupportedException
	 */
	public function validateAttributes($model, $attributes = null)
	{
		$hasNotEmptyAttr = false;
		$attributes      = $this->getValidationAttributes($attributes);

		foreach ($attributes as $attribute) {
			$validator = $this->createEmbeddedValidator($model, $attribute);
			$result    = $validator->validateValue($model->$attribute);

			if ($result === null) {
				$hasNotEmptyAttr = true;
				break;
			}
		}

		if ($hasNotEmptyAttr === false) {
			$this->addError($model, $attributes[0], $this->message, ['attributes' => implode(', ', $attributes)]);
		}
	}
}
