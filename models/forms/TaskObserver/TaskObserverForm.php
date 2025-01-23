<?php

declare(strict_types=1);

namespace app\models\forms\TaskObserver;

use app\dto\TaskObserver\CreateTaskObserverDto;
use app\kernel\common\models\Form\Form;
use app\models\Task;
use app\models\User;
use Exception;

class TaskObserverForm extends Form
{
	public $user_id;
	public $created_by_id;
	public $task_id;

	public function rules(): array
	{
		return [
			[['user_id', 'created_by_id', 'task_id'], 'required'],
			[['user_id', 'created_by_id', 'task_id'], 'integer'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']]
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): CreateTaskObserverDto
	{
		return new CreateTaskObserverDto([
			'user_id'       => $this->user_id,
			'created_by_id' => $this->created_by_id,
			'task_id'       => $this->task_id
		]);

	}
}