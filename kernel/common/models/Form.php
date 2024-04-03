<?php

declare(strict_types=1);

namespace app\kernel\common\models;

use app\exceptions\domain\model\ValidateException;
use Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Form extends Model
{

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
	public function validateOrThrow(array $attributes = [], bool $clearError = true): void
	{
		if (!$this->validate($attributes, $clearError)) {
			throw new ValidateException($this);
		}
	}
}