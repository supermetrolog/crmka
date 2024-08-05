<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\UpdateTaskDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use Throwable;
use UnexpectedValueException;
use yii\db\StaleObjectException;

class TaskService
{

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
		$this->setStatus($task, Task::STATUS_ACCEPTED);
	}

	/**
	 * @throws SaveModelException
	 */
	public function done(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_DONE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function impossible(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_IMPOSSIBLE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function changeStatus(Task $task, int $status): void
	{
		switch ($status) {
			case Task::STATUS_DONE:
				$this->done($task);
				break;
			case Task::STATUS_ACCEPTED:
				$this->accept($task);
				break;
			case Task::STATUS_IMPOSSIBLE:
				$this->impossible($task);
				break;
			default:
				throw new UnexpectedValueException('Unexpected status');
		};
	}

	/**
	 * @throws SaveModelException
	 */
	private function setStatus(Task $task, int $status): void
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