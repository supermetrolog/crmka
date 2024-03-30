<?php

declare(strict_types=1);

namespace app\usecases;

use app\dto\task\CreateTaskDto;
use app\exceptions\domain\model\SaveModelException;
use app\models\Task;

class TaskService
{

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateTaskDto $dto): Task
	{
		$task = new Task([
			'user_id'         => $dto->user->id,
			'message'         => $dto->message,
			'status'          => $dto->status,
			'start'           => $dto->start,
			'end'             => $dto->end,
			'created_by_type' => $dto->created_by_type,
			'created_by_id'   => $dto->created_by_id,
		]);

		$task->saveOrThrow();

		return $task;
	}
}