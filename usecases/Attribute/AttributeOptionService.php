<?php

namespace app\usecases\Attribute;

use __WebUser;
use app\dto\Attribute\CreateAttributeOptionDto;
use app\dto\Attribute\UpdateAttributeOptionDto;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\AttributeOption;
use app\models\User\User;
use app\repositories\AttributeOptionRepository;
use yii\db\StaleObjectException;

class AttributeOptionService
{
	private AttributeOptionRepository $repository;

	public function __construct(AttributeOptionRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateAttributeOptionDto $dto): AttributeOption
	{
		$model = new AttributeOption([
			'attribute_id' => $dto->attributeId,
			'value'        => $dto->value,
			'label'        => $dto->label,
			'sort_order'   => $dto->sortOrder ?? AttributeOption::DEFAULT_SORT_ORDER,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function update(int $id, UpdateAttributeOptionDto $dto): AttributeOption
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->load([
			'value'      => $dto->value,
			'label'      => $dto->label,
			'sort_order' => $dto->sortOrder ?? AttributeOption::DEFAULT_SORT_ORDER,
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

	/**
	 * @param User|__WebUser $user
	 *
	 * @throws ModelNotFoundException
	 */
	public function getModel(int $id, $user = null): AttributeOption
	{
		if ($user && $user->identity->isAdministrator()) {
			return $this->repository->findOneOrThrow($id, false);
		} else {
			return $this->repository->findOneOrThrow($id);
		}
	}
}