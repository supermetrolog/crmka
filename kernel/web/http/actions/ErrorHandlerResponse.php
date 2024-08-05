<?php

namespace app\kernel\web\http\actions;

use app\exceptions\http\ValidateHttpException;
use Throwable;
use Yii;
use yii\web\Application;
use yii\web\Response;

class ErrorHandlerResponse
{
	private Application $app;
	private Response    $response;

	public function __construct(Response $response)
	{
		$this->app      = Yii::$app;
		$this->response = $response;
	}

	private function getException(): ?Throwable
	{
		return $this->app->getErrorHandler()->exception;
	}


	public function processed(): void
	{
		if ($this->response->isSuccessful) {
			return;
		}

		$this->app->response->format = Response::FORMAT_JSON;
		$exception                   = $this->getException();

		if (!$exception) {
			$this->response->data = [
				'message' => 'Server error, exception not found',
				'code'    => 1,
				'status'  => 500
			];

			return;
		}

		$response = [
			'message' => $exception->getMessage(),
			'code'    => $exception->getCode(),
			'status'  => $this->response->getStatusCode()
		];

		if ($exception instanceof ValidateHttpException) {
			$response['errors'] = $exception->getErrors();
		}

		if (!YII_ENV_PROD) {
			$response['file']               = $exception->getFile();
			$response['line']               = $exception->getLine();
			$response['type']               = get_class($exception);
			$response['stack-trace-string'] = $exception->getTraceAsString();
//            $response['stack-trace'] = $exception->getTrace(); TODO: Переполнение памяти
		}

		$this->response->data = $response;
	}
}