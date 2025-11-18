<?php

namespace app\usecases\AttributeOption;

use app\dto\AttributeOption\CreateAttributeOptionDto;
use app\dto\AttributeOption\UpdateAttributeOptionDto;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\AttributeOption;
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
			'attribute_id' => $dto->attribute_id,
			'value'        => $dto->value,
			'label'        => $dto->label,
			'sort_order'   => $dto->sort_order ?? AttributeOption::DEFAULT_SORT_ORDER,
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
			'label'      => $dto->value,
			'sort_order' => $dto->sort_order ?? AttributeOption::DEFAULT_SORT_ORDER,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws ModelNotFoundException
	 * @throws \Throwable
	 */
	public function delete(int $id)
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->delete();
	}
}