<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\TaskAssignDto;
use app\kernel\common\models\Form\Form;
use app\models\User;
use Exception;

class TaskAssignForm extends Form
{
	public $user_id;
	public $assignedBy;

	public function rules(): array
	{
		return [
			[['user_id', 'assignedBy'], 'required'],
			[['user_id'], 'integer'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']]
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): TaskAssignDto
	{
		return new TaskAssignDto([
			'user'       => User::find()->byId($this->user_id)->one(),
			'assignedBy' => $this->assignedBy
		]);

	}
}