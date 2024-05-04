<?php

namespace app\exceptions\http;

use app\kernel\common\models\exceptions\ModelException;
use app\kernel\common\models\exceptions\ValidateException;
use yii\web\HttpException;

class ValidateHttpException extends HttpException
{
    public $statusCode;
    private array $errors;
    private string $statusText;

    /**
     * @param ValidateException $th
     */
    public function __construct(ModelException $th)
    {
        $this->statusCode = 422;
        $this->statusText = 'Validate error';
        $this->errors = $th->getModel()->getErrorSummary(true);

        parent::__construct($this->statusCode, 'Validate error', 1, $th);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->statusText;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}