<?php

namespace app\usecases\Attribute;

use app\dto\Attribute\CreateAttributeDto;
use app\dto\Attribute\UpdateAttributeDto;
use app\exceptions\services\AttributeAlreadyExistsException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Attribute;
use app\repositories\AttributeRepository;
use Throwable;
use yii\db\StaleObjectException;

class AttributeService
{
	protected AttributeRepository $repository;

	public function __construct(AttributeRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @throws AttributeAlreadyExistsException
	 * @throws SaveModelException
	 */
	public function create(CreateAttributeDto $dto): Attribute
	{
		if ($this->repository->existsByKind($dto->kind)) {
			throw new AttributeAlreadyExistsException("Attribute with kind {$dto->kind}");
		}

		$model = new Attribute(
			[
				'kind'          => $dto->kind,
				'label'         => $dto->label,
				'description'   => $dto->description,
				'value_type'    => $dto->valueType,
				'input_type'    => $dto->inputType,
				'created_by_id' => $dto->createdById,
			]
		);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Attribute $model, UpdateAttributeDto $dto): Attribute
	{
		$model->load([
			'label'       => $dto->label,
			'description' => $dto->description,
			'value_type'  => $dto->valueType,
			'input_type'  => $dto->inputType,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Attribute $attribute): void
	{
		$attribute->delete();
	}
}