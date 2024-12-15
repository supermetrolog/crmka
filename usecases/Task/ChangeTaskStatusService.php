<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\dto\Task\ChangeTaskStatusDto;
use app\dto\Task\CreateTaskCommentDto;
use app\events\Task\ChangeStatusTaskEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use Throwable;

class ChangeTaskStatusService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private CreateTaskCommentService     $createTaskCommentService;
	private EventManager                 $eventManager;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		CreateTaskCommentService $createTaskCommentService,
		EventManager $eventManager
	)
	{
		$this->transactionBeginner      = $transactionBeginner;
		$this->taskService              = $taskService;
		$this->createTaskCommentService = $createTaskCommentService;
		$this->eventManager             = $eventManager;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function changeStatus(Task $task, ChangeTaskStatusDto $dto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->taskService->changeStatus($task, $dto);

			if ($dto->comment) {
				$this->createTaskCommentService->create(new CreateTaskCommentDto([
					'message'       => $dto->comment,
					'created_by_id' => $dto->changedBy->id,
					'task_id'       => $task->id
				]));
			}

			$this->eventManager->trigger(new ChangeStatusTaskEvent($task, $dto->changedBy));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}