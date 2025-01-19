<?php

namespace app\models\forms\TaskFavorite;

use app\dto\TaskFavorite\TaskFavoriteDto;
use app\kernel\common\models\Form\Form;
use app\models\Task;
use app\models\User;

/**
 * This is the form class for table "task_favorite".
 *
 * @property int $task_id
 * @property int $user_id
 */
class TaskFavoriteForm extends Form
{
	public $task_id;
	public $user_id;

	public function rules(): array
	{
		return [
			[['task_id', 'user_id'], 'required'],
			[['task_id', 'user_id'], 'integer'],
			[['task_id'], 'exist', 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function getDto(): TaskFavoriteDto
	{
		return new TaskFavoriteDto([
			'task_id' => $this->task_id,
			'user_id' => $this->user_id,
		]);
	}
}