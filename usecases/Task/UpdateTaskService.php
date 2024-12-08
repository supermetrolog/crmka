<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\UpdateTaskDto;
use app\dto\TaskHistory\TaskHistoryDto;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\User;
use app\usecases\TaskEvent\TaskEventService;
use app\usecases\TaskHistory\TaskHistoryService;
use Throwable;

class UpdateTaskService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private ChangeTaskTrackerService     $changeTaskTrackerService;
	private TaskHistoryService           $taskHistoryService;
	private TaskEventService             $taskEventService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		ChangeTaskTrackerService $changeTaskTrackerService,
		TaskHistoryService $taskHistoryService,
		TaskEventService $taskEventService
	)
	{
		$this->transactionBeginner      = $transactionBeginner;
		$this->taskService              = $taskService;
		$this->changeTaskTrackerService = $changeTaskTrackerService;
		$this->taskHistoryService       = $taskHistoryService;
		$this->taskEventService         = $taskEventService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(Task $task, UpdateTaskDto $dto, User $initiator): Task
	{
		$tx = $this->transactionBeginner->begin();

		$oldTask = clone $task;

		$oldTagIds      = $task->getTagIds();
		$oldObserverIds = $task->getUserIdsInObservers();

		try {
			$updatedTask = $this->taskService->update($task, $dto, $initiator);

			$changedAttributes = $this->changeTaskTrackerService->trackChanges($updatedTask, $oldTask, $oldTagIds, $oldObserverIds);

			if (ArrayHelper::notEmpty($changedAttributes)) {
				$taskHistory = $this->taskHistoryService->create(new TaskHistoryDto([
					'task'        => $oldTask,
					'createdBy'   => $initiator,
					'tagIds'      => $oldTagIds,
					'observerIds' => $oldObserverIds
				]));

				foreach ($changedAttributes as $attribute => $event) {
					$this->taskEventService->create(
						$event,
						$taskHistory
					);
				}
			}

			$tx->commit();

			return $updatedTask;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}