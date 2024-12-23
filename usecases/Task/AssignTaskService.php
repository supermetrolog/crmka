<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\dto\Task\CreateTaskCommentDto;
use app\dto\Task\TaskAssignDto;
use app\dto\TaskObserver\CreateTaskObserverDto;
use app\events\Task\AssignTaskEvent;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\usecases\TaskObserver\TaskObserverService;
use Throwable;
use yii\base\ErrorException;

class AssignTaskService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private TaskObserverService          $taskObserverService;
	private EventManager                 $eventManager;
	private CreateTaskCommentService     $createTaskCommentService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		TaskObserverService $taskObserverService,
		EventManager $eventManager,
		CreateTaskCommentService $createTaskCommentService
	)
	{
		$this->transactionBeginner      = $transactionBeginner;
		$this->taskService              = $taskService;
		$this->taskObserverService      = $taskObserverService;
		$this->eventManager             = $eventManager;
		$this->createTaskCommentService = $createTaskCommentService;
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
			$task = $this->taskService->assign($task, $dto->user);

			$this->createTaskCommentService->create(new CreateTaskCommentDto([
				'message'       => $dto->comment,
				'created_by_id' => $dto->assignedBy->id,
				'task_id'       => $task->id
			]));

			$currentObservedUsers = $task->getUserIdsInObservers();

			if (ArrayHelper::includes($currentObservedUsers, $dto->user->id, false)) {
				$this->taskObserverService->markAsNotObserved($task->targetUserObserver);
			} else {
				$this->taskObserverService->create(new CreateTaskObserverDto([
					'task_id'       => $task->id,
					'user_id'       => $dto->user->id,
					'created_by_id' => $dto->assignedBy->id
				]));
			}

			$this->eventManager->trigger(new AssignTaskEvent($task, $dto->assignedBy));

			$tx->commit();

			return $task;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}