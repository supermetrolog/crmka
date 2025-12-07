<?php

namespace app\usecases\Attribute;

use app\dto\Attribute\AttributeGroupDto;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\AttributeGroup;
use app\repositories\AttributeGroupRepository;
use yii\db\StaleObjectException;

class AttributeGroupService
{
	private AttributeGroupRepository $repository;

	public function __construct(AttributeGroupRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(AttributeGroupDto $dto): AttributeGroup
	{
		$model = new AttributeGroup([
			'name' => $dto->name,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function update(int $id, AttributeGroupDto $dto): AttributeGroup
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->load([
			'name' => $dto->name,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws ModelNotFoundException
	 * @throws \Throwable
	 */
	public function delete(int $id): void
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->delete();
	}
}