<?php

declare(strict_types=1);

namespace app\usecases\TaskTag;

use app\dto\TaskTag\TaskTagDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskTag;
use Throwable;
use yii\db\StaleObjectException;

class TaskTagService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(TaskTagDto $dto): TaskTag
	{
		$model = new TaskTag([
			'name'        => $dto->name,
			'description' => $dto->description,
			'color'       => $dto->color,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(TaskTag $model, TaskTagDto $dto): TaskTag
	{
		$model->load([
			'name'        => $dto->name,
			'description' => $dto->description,
			'color'       => $dto->color
		]);

		$model->saveOrThrow();

		return $model;
	}


	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(TaskTag $model): void
	{
		$model->delete();
	}
}