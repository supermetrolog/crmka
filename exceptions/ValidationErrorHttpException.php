<?php

namespace app\exceptions;

use yii\web\HttpException;


class ValidationErrorHttpException extends HttpException
{
    public $statusCode = 422;
    public $statusText = "Ошибка валидации";
    public function __construct($message = null, $code = 1)
    {
        parent::__construct($this->statusCode, json_encode($message), $code);
    }
    public function getName()
    {
        return $this->statusText;
    }
}
