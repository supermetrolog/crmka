<?php

namespace app\exceptions\http;

use app\enum\ErrorCodeEnum;
use yii\web\ForbiddenHttpException;

class RestrictedIpHttpException extends ForbiddenHttpException
{
	public function __construct($message = 'Доступ с текущего IP-адреса запрещен', $code = ErrorCodeEnum::IP_RESTRICTED, $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}