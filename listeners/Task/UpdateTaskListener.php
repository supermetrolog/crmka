<?php

namespace app\listeners\Task;

use app\events\Task\UpdateTaskEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Task;
use app\models\TaskHistory;
use app\usecases\TaskEvent\TaskEventService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Event;

class UpdateTaskListener implements EventListenerInterface
{
	private TaskEventService             $taskEventService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(TaskEventService $taskEventService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->taskEventService    = $taskEventService;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param UpdateTaskEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$task       = $event->getTask();
		$eventTypes = $event->getEventTypes();

		$this->createTaskEvents($task, $eventTypes);
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function createTaskEvents(Task $task, array $eventTypes): void
	{
		/** @var TaskHistory $lastHistory */
		$lastHistory = $task->getLastHistory()->one();

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($eventTypes as $eventType) {
				$this->taskEventService->create($eventType, $lastHistory);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}