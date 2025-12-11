<?php

namespace app\models\forms\Attribute;

use app\dto\Attribute\CreateAttributeRuleDto;
use app\dto\Attribute\UpdateAttributeRuleDto;
use app\kernel\common\models\Form\Form;
use app\models\Attribute;
use app\models\AttributeGroup;

class AttributeRuleForm extends Form
{
	public const SCENARIO_CREATE = 'create';
	public const SCENARIO_UPDATE = 'update';

	public $attribute_id;
	public $attribute_group_id;
	public $entity_type;
	public $is_required;
	public $is_inheritable;
	public $is_editable;
	public $status;
	public $sort_order;

	public function rules(): array
	{
		return [
			[['attribute_id', 'entity_type', 'is_required', 'is_inheritable', 'is_editable', 'status'], 'required'],
			[['attribute_id', 'attribute_group_id', 'sort_order'], 'integer'],
			[['is_required', 'is_inheritable', 'is_editable'], 'boolean'],
			[['entity_type'], 'string', 'max' => 64],
			[['status'], 'string', 'max' => 32],
			['attribute_id', 'exist', 'targetClass' => Attribute::class, 'targetAttribute' => ['attribute_id' => 'id']],
			['attribute_group_id', 'exist', 'targetClass' => AttributeGroup::class, 'targetAttribute' => ['attribute_group_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'attribute_group_id',
			'entity_type',
			'is_required',
			'is_inheritable',
			'is_editable',
			'status',
			'sort_order',
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'attribute_id'],
			self::SCENARIO_UPDATE => $common,
		];
	}

	public function attributeLabels(): array
	{
		return [
			'attribute_id'       => 'Атрибут',
			'attribute_group_id' => 'Группа атрибутов',
			'entity_type'        => 'Тип сущности',
			'status'             => 'Статус',
			'sort_order'         => 'Порядок сортировки',
		];
	}

	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateAttributeRuleDto([
					'attributeId'      => $this->attribute_id,
					'attributeGroupId' => $this->attribute_group_id,
					'entityType'       => $this->entity_type,
					'isRequired'       => $this->is_required,
					'isInheritable'    => $this->is_inheritable,
					'isEditable'       => $this->is_editable,
					'status'           => $this->status,
					'sortOrder'        => $this->sort_order,
				]);
			default:
				return new UpdateAttributeRuleDto([
					'attributeGroupId' => $this->attribute_group_id,
					'entityType'       => $this->entity_type,
					'isRequired'       => $this->is_required,
					'isInheritable'    => $this->is_inheritable,
					'isEditable'       => $this->is_editable,
					'status'           => $this->status,
					'sortOrder'        => $this->sort_order,
				]);
		}
	}
}