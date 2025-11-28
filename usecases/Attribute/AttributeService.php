<?php

namespace app\usecases\Attribute;

use app\dto\Attribute\CreateAttributeDto;
use app\dto\Attribute\UpdateAttributeDto;
use app\exceptions\services\common\AlreadyExistsException;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Attribute;
use app\repositories\AttributeRepository;
use Throwable;
use yii\db\StaleObjectException;

class AttributeService
{
	private AttributeRepository $repository;

	public function __construct(AttributeRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @throws AlreadyExistsException
	 * @throws SaveModelException
	 */
	public function create(CreateAttributeDto $dto): Attribute
	{
		if ($this->repository->existsByKind($dto->kind)) {
			throw new AlreadyExistsException("Attribute with the kind {$dto->kind}");
		}

		$model = new Attribute(
			[
				'kind'        => $dto->kind,
				'label'       => $dto->label,
				'description' => $dto->description,
				'value_type'  => $dto->valueType,
				'input_type'  => $dto->inputType,
			]
		);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function update(int $id, UpdateAttributeDto $dto): Attribute
	{
		$model = $this->repository->findOneOrThrow($id);

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
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function delete(int $id): void
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->delete();
	}
}