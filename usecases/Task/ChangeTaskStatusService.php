<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\ChangeTaskStatusDto;
use app\dto\Task\CreateTaskCommentDto;
use app\dto\TaskHistory\TaskHistoryDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskEvent;
use app\usecases\TaskEvent\TaskEventService;
use app\usecases\TaskHistory\TaskHistoryService;
use Throwable;

class ChangeTaskStatusService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private CreateTaskCommentService     $createTaskCommentService;
	private TaskHistoryService           $taskHistoryService;
	private TaskEventService             $taskEventService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		CreateTaskCommentService $createTaskCommentService,
		TaskHistoryService $taskHistoryService,
		TaskEventService $taskEventService
	)
	{
		$this->transactionBeginner      = $transactionBeginner;
		$this->taskService              = $taskService;
		$this->createTaskCommentService = $createTaskCommentService;
		$this->taskHistoryService       = $taskHistoryService;
		$this->taskEventService         = $taskEventService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function changeStatus(Task $task, ChangeTaskStatusDto $dto): void
	{
		if ($task->status === $dto->status) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$oldTask        = clone $task;
			$oldObserverIds = $task->getUserIdsInObservers();
			$oldTagIds      = $task->getTagIds();

			$this->taskService->changeStatus($task, $dto);

			$taskHistory = $this->taskHistoryService->create(new TaskHistoryDto([
				'task'        => $oldTask,
				'createdBy'   => $dto->changedBy,
				'observerIds' => $oldObserverIds,
				'tagIds'      => $oldTagIds
			]));

			$this->taskEventService->create(TaskEvent::EVENT_TYPE_STATUS_CHANGED, $taskHistory);

			if ($dto->comment) {
				$this->createTaskCommentService->create(new CreateTaskCommentDto([
					'message'       => $dto->comment,
					'created_by_id' => $dto->changedBy->id,
					'task_id'       => $task->id
				]));
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}