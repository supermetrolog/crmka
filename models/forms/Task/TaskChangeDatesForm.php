<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\ChangeTaskDatesDto;
use app\dto\Task\ChangeTaskStatusDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form\Form;
use Exception;

/**
 *
 * @property-read ChangeTaskStatusDto $dto
 */
class TaskChangeDatesForm extends Form
{

	public $start;
	public $end;

	public function rules(): array
	{
		return [
			[['start'], 'required'],
			[['start', 'end'], 'safe'],
			['end', 'validateEnd']
		];
	}

	/**
	 * @throws Exception
	 */
	public function validateEnd($attribute): void
	{
		$value = $this->$attribute;

		if (is_null($value) || $this->hasErrors()) {
			return;
		}

		$isValid = DateTimeHelper::isValid($value);

		if (!$isValid) {
			$this->addError($attribute, 'Неверный формат даты для "' . $this->getAttributeLabel($attribute) . '"');

			return;
		}

		$end   = DateTimeHelper::make($value);
		$start = DateTimeHelper::make($this->start);

		if (DateTimeHelper::compare($end, $start) < 0) {
			$this->addError($attribute, 'Дата окончания не может быть раньше даты начала');
		}
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): ChangeTaskDatesDto
	{
		return new ChangeTaskDatesDto([
			'start' => DateTimeHelper::tryMake($this->start),
			'end'   => DateTimeHelper::tryMake($this->end)
		]);

	}
}