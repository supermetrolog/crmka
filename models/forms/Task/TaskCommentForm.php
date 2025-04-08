<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\CreateTaskCommentDto;
use app\dto\TaskComment\UpdateTaskCommentDto;
use app\helpers\validators\AnyValidator;
use app\kernel\common\models\Form\Form;
use app\models\Media;
use app\models\Task;

class TaskCommentForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $task_id;
	public $created_by_id;
	public $message;

	public $files         = [];
	public $current_files = [];

	public function rules(): array
	{
		return [
			[['task_id', 'created_by_id'], 'integer'],
			[['task_id', 'created_by_id'], 'required', 'on' => self::SCENARIO_CREATE],
			[['message'], 'string', 'max' => 1024],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
			[
				['message', 'files', 'current_files'],
				AnyValidator::class,
				'message' => 'Сообщение или список файлов должны быть заполнены',
				'rule'    => 'required',
			],
			['current_files', 'each', 'rule' => [
				'exist',
				'targetClass'     => Media::class,
				'targetAttribute' => ['current_files' => 'id'],
			]],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'message',
			'files'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'task_id', 'created_by_id'],
			self::SCENARIO_UPDATE => [...$common, 'current_files'],
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
					'message'      => $this->message,
					'currentFiles' => $this->current_files
				]);
		}
	}
}