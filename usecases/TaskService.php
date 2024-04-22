<?php

declare(strict_types=1);

namespace app\usecases;

use app\dto\Task\CreateTaskDto;
use app\dto\Task\UpdateTaskDto;
use app\exceptions\domain\model\SaveModelException;
use app\models\Task;
use Throwable;
use yii\db\StaleObjectException;

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
			'start'           => $dto->start ? $dto->start->format('Y-m-d H:i:s') : null,
			'end'             => $dto->end ? $dto->end->format('Y-m-d H:i:s') : null,
			'created_by_type' => $dto->created_by_type,
			'created_by_id'   => $dto->created_by_id,
		]);

		$task->saveOrThrow();

		return $task;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Task $task, UpdateTaskDto $dto): Task
	{
		$task->load([
			'user_id' => $dto->user->id,
			'message' => $dto->message,
			'status'  => $dto->status,
			'start'   => $dto->start ? $dto->start->format('Y-m-d H:i:s') : null,
			'end'     => $dto->end ? $dto->end->format('Y-m-d H:i:s') : null,
		]);

		$task->saveOrThrow();

		return $task;
	}

	/**
	 * @throws SaveModelException
	 */
	public function accept(Task $task): void
	{
		$this->changeStatus($task, Task::STATUS_ACCEPTED);
	}

	/**
	 * @throws SaveModelException
	 */
	public function done(Task $task): void
	{
		$this->changeStatus($task, Task::STATUS_DONE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function impossible(Task $task): void
	{
		$this->changeStatus($task, Task::STATUS_IMPOSSIBLE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function changeStatus(Task $task, int $status): void
	{
		$task->status = $status;
		$task->saveOrThrow();
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Task $task): void
	{
		$task->delete();
	}
}