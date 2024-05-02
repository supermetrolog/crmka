<?php

declare(strict_types=1);

namespace app\exceptions\domain\model;


use app\kernel\common\models\AR\AR;
use Exception;
use Throwable;

class SaveModelException extends ModelException
{
	/**
	 * @throws Exception
	 */
	public function __construct(AR $model, Throwable $previous = null)
	{
		$message = $model->getAnyError();

		if (!$message && $previous) {
			$message = $previous->getMessage();
		} else if (!$message) {
			$message = 'Unknown error';
		}

		parent::__construct($model, $message, 0, $previous);
	}
}