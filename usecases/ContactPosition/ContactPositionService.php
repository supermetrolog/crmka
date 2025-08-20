<?php

declare(strict_types=1);

namespace app\usecases\ContactPosition;

use app\dto\ContactPosition\CreateContactPositionDto;
use app\dto\ContactPosition\UpdateContactPositionDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ContactPosition;
use Throwable;
use yii\db\StaleObjectException;

class ContactPositionService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateContactPositionDto $dto): ContactPosition
	{
		$model = new ContactPosition([
			'created_by_id' => $dto->createdBy->id ?? null,
			'name'          => $dto->name,
			'slug'          => $dto->slug,
			'short_name'    => $dto->short_name,
			'description'   => $dto->description,
			'icon'          => $dto->icon,
			'color'         => $dto->color,
			'sort_order'    => $dto->sort_order ?? ContactPosition::DEFAULT_SORT_ORDER,
			'is_active'     => $dto->is_active
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(ContactPosition $model, UpdateContactPositionDto $dto): ContactPosition
	{
		$model->load([
			'name'        => $dto->name,
			'slug'        => $dto->slug,
			'short_name'  => $dto->short_name,
			'description' => $dto->description,
			'icon'        => $dto->icon,
			'color'       => $dto->color,
			'sort_order'  => $dto->sort_order ?? ContactPosition::DEFAULT_SORT_ORDER,
			'is_active'   => $dto->is_active
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(ContactPosition $model): void
	{
		$model->delete();
	}
}