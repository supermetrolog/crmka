<?php

namespace app\usecases\Attribute;

use app\dto\Attribute\CreateAttributeValueDto;
use app\dto\Attribute\UpdateAttributeValueDto;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\AttributeValue;
use app\repositories\AttributeValueRepository;
use yii\db\StaleObjectException;

class AttributeValueService
{
	private AttributeValueRepository $repository;

	public function __construct(
		AttributeValueRepository $repository
	)
	{
		$this->repository = $repository;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateAttributeValueDto $dto): AttributeValue
	{
		$model = new AttributeValue([
			'attribute_id' => $dto->attributeId,
			'entity_id'    => $dto->entityId,
			'entity_type'  => $dto->entityType,
			'value'        => $dto->value ?? null,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function update(int $id, UpdateAttributeValueDto $dto): AttributeValue
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->load([
			'value' => $dto->value,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws \Throwable
	 * @throws StaleObjectException
	 */
	public function delete(int $id)
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->delete();
	}
}