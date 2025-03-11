<?php

declare(strict_types=1);

namespace app\usecases\Request;

use app\dto\Request\PassiveRequestDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Request;
use yii\base\InvalidArgumentException;

class RequestService
{
	/**
	 * @throws InvalidArgumentException
	 * @throws SaveModelException
	 */
	public function markAsPassive(Request $request, PassiveRequestDto $dto): void
	{
		if ($request->isPassive()) {
			throw new InvalidArgumentException('Request is already passive');
		}

		$request->status              = Request::STATUS_PASSIVE;
		$request->passive_why         = $dto->passive_why;
		$request->passive_why_comment = $dto->passive_why_comment;

		$request->saveOrThrow();
	}
}