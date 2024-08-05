<?php

declare(strict_types=1);

namespace app\kernel\common\models\exceptions;

use Exception;
use Throwable;
use yii\base\Model;

class ValidateException extends ModelException
{
	/**
	 * @throws Exception
	 */
	public function __construct(Model $model, Throwable $previous = null)
	{
		$errors = $model->getFirstErrors();
		$error  = array_pop($errors) ?? 'Unknown validate error';

		parent::__construct($model, $error, 0, $previous);
	}
}
