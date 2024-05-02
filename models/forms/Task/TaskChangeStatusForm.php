<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\kernel\common\models\Form\Form;
use app\models\Task;

class TaskChangeStatusForm extends Form
{

	public $status;

	public function rules(): array
	{
		return [
			[['status'], 'required'],
			[['status'], 'integer'],
			[['status'], 'in', 'range' => [Task::STATUS_DONE, Task::STATUS_ACCEPTED, Task::STATUS_IMPOSSIBLE]],
		];
	}
}