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
	 * @throws \Exception
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
		$task->updateManyToManyRelations('tags', $dto->tagIds);

		return $task;
	}

	/**
	 * Установить статус задачи "В работе"
	 *
	 * @param Task $task
	 *
	 * @return void
	 * @throws SaveModelException
	 */
	public function accept(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_ACCEPTED);
	}

	/**
	 * Установить статус задачи "Выполнена"
	 *
	 * @param Task $task
	 *
	 * @return void
	 * @throws SaveModelException
	 */
	public function done(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_DONE);
	}

	/**
	 * Установить статус задачи "Отложена"
	 *
	 * @param Task $task
	 *
	 * @return void
	 * @throws SaveModelException
	 */
	public function impossible(Task $task, ?DateTimeInterface $impossibleToDate): void
	{
		$task->impossible_to = $impossibleToDate ? $impossibleToDate->format('Y-m-d H:i:s') : null;
		$this->setStatus($task, Task::STATUS_IMPOSSIBLE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function changeStatus(Task $task, ChangeTaskStatusDto $dto): void
	{
		switch ($dto->status) {
			case Task::STATUS_DONE:
				$this->done($task);
				break;
			case Task::STATUS_ACCEPTED:
				$this->accept($task);
				break;
			case Task::STATUS_IMPOSSIBLE:
				$this->impossible($task, $dto->impossible_to);
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