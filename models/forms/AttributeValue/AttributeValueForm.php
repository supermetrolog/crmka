<?php

namespace app\models\forms\AttributeValue;

use app\dto\Attribute\CreateAttributeValueDto;
use app\dto\Attribute\UpdateAttributeValueDto;
use app\kernel\common\models\Form\Form;

class AttributeValueForm extends Form
{
	public const SCENARIO_CREATE = 'create';
	public const SCENARIO_UPDATE = 'update';

	public $attribute_id;
	public $entity_id;
	public $entity_type;
	public $value;

	public function rules(): array
	{
		return [
			[['attribute_id', 'entity_id', 'entity_type'], 'required'],
			[['attribute_id', 'entity_id'], 'integer'],
			[['entity_type'], 'string', 'max' => 64],
			[['value'], 'string'],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'value'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'attribute_id', 'entity_id', 'entity_type'],
			self::SCENARIO_UPDATE => $common,
		];
	}

	public function attributeLabels(): array
	{
		return [
			'attribute_id' => 'Аттрибут',
			'entity_id'    => 'Сущность',
			'entity_type'  => 'Тип сущности',
			'value'        => 'Значение',
		];
	}

	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateAttributeValueDto([
					'attributeId' => $this->attribute_id,
					'entityId'    => $this->entity_id,
					'entityType'  => $this->entity_type,
					'value'       => $this->value,
				]);
			default:
				return new UpdateAttributeValueDto([
					'value' => $this->value,
				]);
		}
	}
}