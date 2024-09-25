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
	 */
	public function update(Task $task, UpdateTaskDto $dto): Task
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
			$task->updateManyToManyRelations('tags', $dto->tagIds);

			$currentObservers = $task->getUserIdsInObservers();
			$addedObservers   = ArrayHelper::diff($dto->observerIds, $currentObservers);
			$removedObservers = ArrayHelper::diff($currentObservers, $dto->observerIds);

			foreach ($addedObservers as $observerId) {
				$this->taskObserverService->create(new CreateTaskObserverDto([
					'task_id'       => $task->id,
					'user_id'       => $observerId,
					'created_by_id' => $dto->created_by_id,
				]));
			}

			if (ArrayHelper::notEmpty($removedObservers)) {
				$this->taskObserverService->deleteAll([
					'task_id' => $task->id,
					'user_id' => $removedObservers,
				]);
			}

			$tx->commit();

		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}

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