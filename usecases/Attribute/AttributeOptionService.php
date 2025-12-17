<?php

namespace app\usecases\Attribute;

use app\dto\AttributeOption\CreateAttributeOptionDto;
use app\dto\AttributeOption\UpdateAttributeOptionDto;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\AttributeOption;
use yii\db\StaleObjectException;

class AttributeOptionService
{
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
	 * @throws SaveModelException
	 */
	public function update(AttributeOption $model, UpdateAttributeOptionDto $dto): AttributeOption
	{
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
	public function delete(AttributeOption $model): void
	{
		$model->delete();
	}
}