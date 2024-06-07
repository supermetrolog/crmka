<?php

declare(strict_types=1);

namespace app\usecases\Field;

use app\dto\Field\CreateFieldDto;
use app\dto\Field\UpdateFieldDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Field;
use Throwable;
use yii\db\StaleObjectException;

class FieldService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateFieldDto $dto): Field
	{
		$model = new Field([
			'field_type' => $dto->field_type,
			'type'       => $dto->type,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Field $model, UpdateFieldDto $dto): Field
	{
		$model->load([
			'field_type' => $dto->field_type,
			'type'       => $dto->type,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Field $model): void
	{
		$model->delete();
	}
}