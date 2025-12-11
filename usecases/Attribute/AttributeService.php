<?php

namespace app\usecases\Attribute;

use __WebUser;
use app\dto\Attribute\CreateAttributeDto;
use app\dto\Attribute\UpdateAttributeDto;
use app\exceptions\services\AttributeAlreadyExistsException;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Attribute;
use app\models\User\User;
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

	/**
	 * @param User|__WebUser $user
	 *
	 * @throws ModelNotFoundException
	 */
	public function getModel(int $id, $user = null): Attribute
	{
		if ($user && $user->identity->isAdministrator()) {
			return $this->repository->findOneOrThrow($id, false);
		} else {
			return $this->repository->findOneOrThrow($id);
		}
	}
}