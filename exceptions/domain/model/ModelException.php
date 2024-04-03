<?php

declare(strict_types=1);

namespace app\exceptions\domain\model;

use Exception;
use Throwable;
use yii\base\Model;

class ModelException extends Exception
{
	private Model $model;

	public function __construct(Model $model, string $message = "", int $code = 0, Throwable $previous = null)
	{
		$this->model = $model;
		parent::__construct($message, $code, $previous);
	}

	public function getModel(): Model
	{
		return $this->model;
	}
}