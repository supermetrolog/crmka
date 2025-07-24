<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\ChangeTaskStatusDto;
use app\kernel\common\models\Form\Form;
use app\models\Task;

/**
 *
 * @property-read ChangeTaskStatusDto $dto
 */
class TaskChangeTypeForm extends Form
{
	public $type;

	public function rules(): array
	{
		return [
			['type', 'required'],
			['type', 'string'],
			['type', 'in', 'range' => Task::getTypes()],
		];
	}
}