<?php

namespace app\models\forms\AttributeOption;

use app\dto\Attribute\CreateAttributeOptionDto;
use app\dto\Attribute\UpdateAttributeOptionDto;
use app\kernel\common\models\Form\Form;
use app\models\Attribute;

class AttributeOptionForm extends Form
{
	public const SCENARIO_CREATE = 'create';
	public const SCENARIO_UPDATE = 'update';

	public $attribute_id;
	public $value;
	public $label;
	public $sort_order;

	public function rules(): array
	{
		return [
			[['attribute_id', 'value'], 'required'],
			[['attribute_id', 'sort_order'], 'integer'],
			['attribute_id', 'exist', Attribute::class, 'targetAttribute' => ['attribute_id' => 'id']],
			[['value', 'label'], 'string', 'max' => 128],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'value',
			'label',
			'sort_order'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'attribute_id'],
			self::SCENARIO_UPDATE => [$common]
		];
	}

	public function attributeLabels(): array
	{
		return [
			'attribute_id' => 'Атрибут',
			'value'        => 'Значение',
			'label'        => 'Название',
			'sort_order'   => 'Порядок сортировки',
		];
	}

	/**
	 * @return CreateAttributeOptionDto|UpdateAttributeOptionDto
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateAttributeOptionDto([
					'attributeId' => $this->attribute_id,
					'value'       => $this->value,
					'label'       => $this->label,
					'sortOrder'   => $this->sort_order,
				]);
			default:
				return new UpdateAttributeOptionDto([
					'value'     => $this->value,
					'label'     => $this->label,
					'sortOrder' => $this->sort_order,
				]);
		}
	}
}