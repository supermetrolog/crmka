<?php

namespace app\usecases\Attribute;

use app\dto\Attribute\CreateAttributeRuleDto;
use app\dto\Attribute\UpdateAttributeRuleDto;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\AttributeRule;
use app\repositories\AttributeRuleRepository;
use yii\db\StaleObjectException;

class AttributeRuleService
{
	private AttributeRuleRepository $repository;

	public function __construct(AttributeRuleRepository $repository)
	{
		$this->repository = $repository;
	}

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
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function update(int $id, UpdateAttributeRuleDto $dto): AttributeRule
	{
		$model = $this->repository->findOneOrThrow($id);

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
	 * @throws ModelNotFoundException
	 */
	public function delete(int $id): void
	{
		$model = $this->repository->findOneOrThrow($id);

		$model->delete();
	}
}