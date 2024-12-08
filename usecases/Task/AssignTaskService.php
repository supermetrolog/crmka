<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\TaskAssignDto;
use app\dto\TaskHistory\TaskHistoryDto;
use app\dto\TaskObserver\CreateTaskObserverDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskEvent;
use app\usecases\TaskEvent\TaskEventService;
use app\usecases\TaskHistory\TaskHistoryService;
use app\usecases\TaskObserver\TaskObserverService;
use Throwable;
use yii\base\ErrorException;

class AssignTaskService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private TaskObserverService          $taskObserverService;
	private TaskEventService             $taskEventService;
	private TaskHistoryService           $taskHistoryService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		TaskObserverService $taskObserverService,
		TaskEventService $taskEventService,
		TaskHistoryService $taskHistoryService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->taskService         = $taskService;
		$this->taskObserverService = $taskObserverService;
		$this->taskEventService    = $taskEventService;
		$this->taskHistoryService  = $taskHistoryService;

	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function assign(Task $task, TaskAssignDto $dto): Task
	{
		if (!$task->canBeReassigned()) {
			throw new ErrorException("Task can't be reassigned");
		}

		if ($task->user_id === $dto->user->id) {
			throw new ErrorException("Task already assigned to user");
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$oldTask        = clone $task;
			$oldObserverIds = $task->getUserIdsInObservers();
			$oldTagIds      = $task->getTagIds();

			$task = $this->taskService->assign($task, $dto->user);

			$this->taskObserverService->create(new CreateTaskObserverDto([
				'task_id'       => $task->id,
				'user_id'       => $dto->user->id,
				'created_by_id' => $dto->assignedBy->id
			]));

			$taskHistory = $this->taskHistoryService->create(new TaskHistoryDto([
				'task'        => $oldTask,
				'createdBy'   => $dto->assignedBy,
				'observerIds' => $oldObserverIds,
				'tagIds'      => $oldTagIds
			]));

			$this->taskEventService->create(TaskEvent::EVENT_TYPE_ASSIGNED, $taskHistory);

			$tx->commit();

			return $task;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}