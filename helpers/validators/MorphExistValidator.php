<?php


namespace app\helpers\validators;

use Yii;
use yii\base\NotSupportedException;
use yii\validators\ExistValidator;


class MorphExistValidator extends ExistValidator
{
	public array  $targetClassMap      = [];
	public        $targetAttribute     = 'id';
	public string $targetTypeAttribute = 'entity_type';

	/**
	 * @throws NotSupportedException
	 */
	public function validateAttribute($model, $attribute): void
	{
		if ($this->validateMorphClassExistence($model)) {
			$this->targetClass = $this->getTargetMorphClass($model);

			Yii::debug($this->validateValue((int)$model->$attribute));

			$errors = $this->validateValue((int)$model->$attribute);

			if (!is_null($errors)) {
				$this->addError($model, $attribute, Yii::t('yii', '{attribute} is invalid.'));
			}
		} else {
			$this->addError($model, $this->targetTypeAttribute, Yii::t('yii', '{attribute} is invalid.'));
		}
	}

	private function validateMorphClassExistence($model): bool
	{
		return isset($this->targetClassMap[$model->{$this->targetTypeAttribute}]);
	}

	private function getTargetMorphClass($model)
	{
		return $this->targetClassMap[$model->{$this->targetTypeAttribute}];
	}
}