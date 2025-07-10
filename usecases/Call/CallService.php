<?php

declare(strict_types=1);

namespace app\usecases\Call;

use app\dto\Call\UpdateCallDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Call;
use Throwable;
use yii\db\StaleObjectException;

class CallService
{

	/**
	 * @throws SaveModelException
	 */
	public function update(Call $model, UpdateCallDto $dto): Call
	{
		$model->load([
			'contact_id'  => $dto->contact->id,
			'phone_id'    => $dto->phone->id ?? null,
			'type'        => $dto->type,
			'status'      => $dto->status,
			'description' => $dto->description
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Call $model): void
	{
		$model->delete();
	}
}