<?php

declare(strict_types=1);

namespace app\exceptions\domain\model;


use app\kernel\common\models\AR;
use Exception;
use Throwable;

class SaveModelException extends ModelException
{
	/**
	 * @throws Exception
	 */
	public function __construct(AR $model, Throwable $previous = null)
	{
		parent::__construct($model, $model->getAnyError(), 0, $previous);
	}
}