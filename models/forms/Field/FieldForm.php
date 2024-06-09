<?php

declare(strict_types=1);

namespace app\models\forms\Field;

use app\dto\Field\CreateFieldDto;
use app\dto\Field\UpdateFieldDto;
use app\kernel\common\models\Form\Form;
use app\models\Field;
use Exception;

class FieldForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $field_type;
	public $type;

	public function rules(): array
	{
		return [
			[['field_type', 'type'], 'required'],
			[['field_type', 'type'], 'safe'],
			[['field_type', 'type'], 'string', 'max' => 255],
			['field_type', 'in', 'range' => Field::getFieldTypes()],
			['type', 'in', 'range' => Field::getTypes()],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'field_type',
			'type',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	/**
	 * @return CreateFieldDto|UpdateFieldDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateFieldDto([
					'field_type' => $this->field_type,
					'type'       => $this->type,
				]);

			default:
				return new UpdateFieldDto([
					'field_type' => $this->field_type,
					'type'       => $this->type,
				]);
		}
	}
}