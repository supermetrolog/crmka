<?php

namespace app\usecases\Attribute;

use app\dto\AttributeRule\CreateAttributeRuleDto;
use app\dto\AttributeRule\UpdateAttributeRuleDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\AttributeRule;
use yii\db\StaleObjectException;

class AttributeRuleService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateAttributeRuleDto $dto): AttributeRule
	{
		$model = new AttributeRule([
			'attribute_id'       => $dto->attributeId,
			'attribute_group_id' => $dto->attributeGroupId,
			'entity_type'        => $dto->entityType,
			'is_required'        => $dto->isRequired,
			'is_inheritable'     => $dto->isInheritable,
			'is_editable'        => $dto->isEditable,
			'status'             => $dto->status,
			'sort_order'         => $dto->sortOrder ?? AttributeRule::DEFAULT_SORT_ORDER,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(AttributeRule $model, UpdateAttributeRuleDto $dto): AttributeRule
	{
		$model->load([
			'attribute_group_id' => $dto->attributeGroupId,
			'entity_type'        => $dto->entityType,
			'is_required'        => $dto->isRequired,
			'is_inheritable'     => $dto->isInheritable,
			'is_editable'        => $dto->isEditable,
			'status'             => $dto->status,
			'sort_order'         => $dto->sortOrder,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws \Throwable
	 * @throws StaleObjectException
	 */
	public function delete(AttributeRule $model): void
	{
		$model->delete();
	}
}