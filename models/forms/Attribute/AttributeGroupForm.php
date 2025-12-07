<?php

namespace app\models\forms\Attribute;

use app\dto\Attribute\AttributeGroupDto;
use app\kernel\common\models\Form\Form;

class AttributeGroupForm extends Form
{
	public $name;

	public function rules(): array
	{
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 64],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'name' => 'Название группы',
		];
	}

	public function getDto(): AttributeGroupDto
	{
		return new AttributeGroupDto([
			'name' => $this->name,
		]);
	}
}