<?php

namespace app\models\forms\Attribute;

use app\dto\Attribute\CreateAttributeOptionDto;
use app\kernel\common\models\Form\Form;

class AttributeOptionForm extends Form
{
	public $value;
	public $label;
	public $sort_order;

	public function rules(): array
	{
		return [
			[['sort_order'], 'integer'],
			[['value', 'label'], 'string', 'max' => 128],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'value'      => 'Значение',
			'label'      => 'Название',
			'sort_order' => 'Порядок сортировки',
		];
	}

	public function getDto(): CreateAttributeOptionDto
	{
		return new CreateAttributeOptionDto([
			'value'     => $this->value,
			'label'     => $this->label,
			'sortOrder' => $this->sort_order,
		]);
	}
}