<?php

declare(strict_types=1);

namespace app\models\forms\Reminder;

use app\kernel\common\models\Form\Form;
use app\models\Reminder;

class ReminderChangeStatusForm extends Form
{

	public $status;

	public function rules(): array
	{
		return [
			[['status'], 'required'],
			[['status'], 'integer'],
			[['status'], 'in', 'range' => [Reminder::STATUS_DONE, Reminder::STATUS_ACCEPTED, Reminder::STATUS_IMPOSSIBLE]],
		];
	}
}