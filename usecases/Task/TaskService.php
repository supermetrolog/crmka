<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\ChangeTaskStatusDto;
use app\dto\Task\UpdateTaskDto;
use app\dto\TaskObserver\CreateTaskObserverDto;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskObserver;
use app\models\User;
use app\usecases\TaskObserver\TaskObserverService;
use DateTimeInterface;
use Exception;
use Throwable;
use UnexpectedValueException;
use yii\db\StaleObjectException;

class TaskService
{

	private TransactionBeginnerInterface $transactionBeginner;
	private TaskObserverService          $taskObserverService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskObserverService $taskObserverService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->taskObserverService = $taskObserverService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function update(Task $task, UpdateTaskDto $dto, User $initiator): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$task->load([
				'user_id' => $dto->user->id,
				'message' => $dto->message,
				'status'  => $dto->status,
				'start'   => $dto->start ? $dto->start->format('Y-m-d H:i:s') : null,
				'end'     => $dto->end ? $dto->end->format('Y-m-d H:i:s') : null
			]);

			$task->saveOrThrow();

			$this->updateTags($task, $dto->tagIds);
			$this->updateObservers($task, $dto->observerIds, $initiator);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}

		return $task;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function updateObservers(Task $task, array $newObserverIds, User $initiator): void
	{
		$currentObserverIds = $task->getUserIdsInObservers();

		$addedObservers   = ArrayHelper::diff($newObserverIds, $currentObserverIds);
		$removedObservers = ArrayHelper::diff($currentObserverIds, $newObserverIds);

		foreach ($addedObservers as $observerId) {
			$this->taskObserverService->create(new CreateTaskObserverDto([
				'task_id'       => $task->id,
				'user_id'       => $observerId,
				'created_by_id' => $initiator,
			]));
		}

		if (ArrayHelper::notEmpty($removedObservers)) {
			$this->taskObserverService->deleteAll([
				'task_id' => $task->id,
				'user_id' => $removedObservers,
			]);
		}
	}


	/**
	 * @throws Throwable
	 * @throws \yii\db\Exception
	 */
	private function updateTags(Task $task, array $newTagIds): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$task->updateManyToManyRelations('tags', $newTagIds);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/* @throws SaveModelException */
	public function accept(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_ACCEPTED);
	}

	/* @throws SaveModelException */
	public function done(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_DONE);
	}

	/* @throws SaveModelException */
	public function impossible(Task $task, ?DateTimeInterface $impossibleToDate): void
	{
		$task->impossible_to = $impossibleToDate ? $impossibleToDate->format('Y-m-d H:i:s') : null;
		$this->setStatus($task, Task::STATUS_IMPOSSIBLE);
	}

	/* @throws SaveModelException */
	public function changeStatus(Task $task, ChangeTaskStatusDto $dto): void
	{
		switch ($dto->status) {
			case Task::STATUS_DONE:
				$this->done($task);

				$observer = $task->targetUserObserver;
				if ($observer instanceof TaskObserver && $observer->isNotViewed()) {
					$this->taskObserverService->observe($observer);
				}

				break;
			case Task::STATUS_ACCEPTED:
				$this->accept($task);
				break;
			case Task::STATUS_IMPOSSIBLE:
				$this->impossible($task, $dto->impossible_to);
				break;
			default:
				throw new UnexpectedValueException('Unexpected status');
		}
	}

	/* @throws SaveModelException */
	private function setStatus(Task $task, int $status): void
	{
		$task->status = $status;
		$task->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function assign(Task $task, User $user): Task
	{
		$task->user_id = $user->id;
		$task->saveOrThrow();

		return $task;
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