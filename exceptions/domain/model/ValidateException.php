<?php

declare(strict_types=1);

namespace app\exceptions\domain\model;

use Exception;
use Throwable;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ValidateException extends ModelException
{
	/**
	 * @throws Exception
	 */
	public function __construct(Model $model, Throwable $previous = null)
	{
		parent::__construct($model, ArrayHelper::getValue($model->getFirstErrors(), 0), 0, $previous);
	}
}
