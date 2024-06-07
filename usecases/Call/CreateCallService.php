<?php

declare(strict_types=1);

namespace app\usecases\Call;

use app\dto\Call\CreateCallDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Call;

class CreateCallService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateCallDto $dto): Call
	{
		$model = new Call([
			'user_id'    => $dto->user->id,
			'contact_id' => !!$dto->contact ? $dto->contact->id : null,
		]);

		$model->saveOrThrow();

		return $model;
	}
}