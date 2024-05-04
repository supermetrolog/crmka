<?php

declare(strict_types=1);

namespace app\kernel\common\models\Form;

use app\kernel\common\models\exceptions\ValidateException;
use Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Form extends Model
{

	/**
	 * @return array
	 */
	public function rules(): array
	{
		return parent::rules();
	}

	/**
	 * @return array
	 */
	public function scenarios(): array
	{
		return parent::scenarios();
	}

	public function formName(): string
	{
		return '';
	}

	/**
	 * @throws Exception
	 */
	public function getAnyError(): ?string
	{
		return ArrayHelper::getValue($this->getFirstErrors(), 0);
	}

	/**
	 * @throws ValidateException
	 */
	public function validateOrThrow(?array $attributes = null, bool $clearError = true): void
	{
		if (!$this->validate($attributes, $clearError)) {
			throw new ValidateException($this);
		}
	}
}