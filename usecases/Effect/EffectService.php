<?php

declare(strict_types=1);

namespace app\usecases\Effect;

use app\dto\Effect\CreateEffectDto;
use app\dto\Effect\UpdateEffectDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Effect;
use Throwable;
use yii\db\StaleObjectException;

class EffectService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateEffectDto $dto): Effect
	{
		$model = new Effect([
			'title'       => $dto->title,
			'kind'        => $dto->kind,
			'description' => $dto->description
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Effect $model, UpdateEffectDto $dto): Effect
	{
		$model->load([
			'title'       => $dto->title,
			'description' => $dto->description
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Effect $model): void
	{
		$model->delete();
	}
}