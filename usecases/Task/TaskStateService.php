<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\events\Task\DeleteTaskEvent;
use app\events\Task\RestoreTaskEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\Task;
use app\models\User\User;
use Throwable;
use yii\db\StaleObjectException;

class TaskStateService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private EventManager                 $eventManager;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		EventManager $eventManager
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->taskService         = $taskService;
		$this->eventManager        = $eventManager;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Task $task, User $initiator): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->taskService->delete($task);
			$this->eventManager->trigger(new DeleteTaskEvent($task, $initiator));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	public function restore(Task $task, User $initiator): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->taskService->restore($task);
			$this->eventManager->trigger(new RestoreTaskEvent($task, $initiator));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}