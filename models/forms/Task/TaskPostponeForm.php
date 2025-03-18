<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\PostponeTaskDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form\Form;
use Exception;

class TaskPostponeForm extends Form
{
	public $start;
	public $end;

	public function rules(): array
	{
		return [
			[['start', 'end'], 'required'],
			[['start', 'end'], 'validateDateTime'],
		];
	}

	/**
	 * @throws Exception
	 */
	public function validateDateTime($attribute): void
	{
		if (!$this->hasErrors()) {
			$isValid = DateTimeHelper::isValid($this->$attribute);

			if (!$isValid) {
				$this->addError($attribute, 'Неверный формат даты для "' . $this->getAttributeLabel($attribute) . '"');
			}
		}
	}

	public function attributeLabels(): array
	{
		return [
			'start' => 'Дата начала исполнения',
			'end'   => 'Дата окончания исполнения',
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): PostponeTaskDto
	{
		return new PostponeTaskDto([
			'start' => DateTimeHelper::make($this->start),
			'end'   => DateTimeHelper::make($this->end)
		]);

	}
}