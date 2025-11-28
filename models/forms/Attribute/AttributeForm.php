<?php

namespace app\models\forms\Attribute;

use app\dto\Attribute\CreateAttributeDto;
use app\dto\Attribute\UpdateAttributeDto;
use app\enum\Attribute\AttributeInputTypeEnum;
use app\enum\Attribute\AttributeValueTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;

class AttributeForm extends Form
{
	public const SCENARIO_CREATE = 'create';
	public const SCENARIO_UPDATE = 'update';

	public $kind;
	public $label;
	public $description;
	public $value_type;
	public $input_type;

	public function rules(): array
	{
		return [
			[['kind', 'label', 'value_type', 'input_type'], 'required'],
			[['kind', 'label'], 'string', 'max' => 64],
			[['description'], 'string', 'max' => 255],
			[['value_type'], EnumValidator::class, 'enumClass' => AttributeValueTypeEnum::class],
			[['input_type'], EnumValidator::class, 'enumClass' => AttributeInputTypeEnum::class],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'label',
			'description',
			'value_type',
			'input_type',
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'kind'],
			self::SCENARIO_UPDATE => $common,
		];
	}

	public function attributeLabels(): array
	{
		return [
			'kind'        => 'Идентификатор',
			'label'       => 'Название',
			'description' => 'Описание',
			'value_type'  => 'Тип значения',
			'input_type'  => 'Тип ввода'
		];
	}

	/**
	 * @return CreateAttributeDto|UpdateAttributeDto
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateAttributeDto([
					'kind'        => $this->kind,
					'label'       => $this->label,
					'description' => $this->description,
					'valueType'   => $this->value_type,
					'inputType'   => $this->input_type,
				]);
			default:
				return new UpdateAttributeDto([
					'label'       => $this->label,
					'description' => $this->description,
					'valueType'   => $this->value_type,
					'inputType'   => $this->input_type,
				]);
		}
	}
}