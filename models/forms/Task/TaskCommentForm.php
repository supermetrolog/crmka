<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\CreateTaskCommentDto;
use app\dto\TaskComment\UpdateTaskCommentDto;
use app\kernel\common\models\Form\Form;
use app\models\Task;
use app\models\User;

class TaskCommentForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $task_id;
	public $created_by_id;
	public $message;

	public function rules(): array
	{
		return [
			[['task_id', 'created_by_id'], 'integer'],
			[['task_id', 'created_by_id', 'message'], 'required'],
			[['message'], 'string', 'max' => 511],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'message'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'task_id', 'created_by_id'],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateTaskCommentDto([
					'message'       => $this->message,
					'created_by_id' => $this->created_by_id,
					'task_id'       => $this->task_id
				]);

			default:
				return new UpdateTaskCommentDto([
					'message'   => $this->message,
					'createdBy' => User::find()->byId($this->created_by_id)->one(),
					'task'      => Task::find()->byId($this->task_id)->one()
				]);
		}
	}
}