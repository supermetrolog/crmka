<?php

declare(strict_types=1);

namespace app\kernel\web\http\actions;

use LogicException;
use Yii;
use yii\base\Action;
use yii\web\Controller;
use yii\web\Response;

class ErrorAction extends Action
{
	private ErrorHandlerResponse $errorResponse;
	private Response             $response;

	public function __construct(string $id, Controller $controller)
	{
		parent::__construct($id, $controller);
		$this->response      = Yii::$app->getResponse();
		$this->errorResponse = new ErrorHandlerResponse($this->response);
	}

	public function run(): array
	{
		$this->errorResponse->processed();
		if (!$this->response->data) {
			throw new LogicException('Response data cannot be null');
		}

		return $this->response->data;
	}
}