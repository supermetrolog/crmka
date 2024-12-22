<?php

namespace app\listeners\Task;

use app\events\Task\AssignTaskEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Task;
use app\models\TaskEvent;
use app\usecases\TaskEvent\TaskEventService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Event;

class ChangeStatusTaskListener implements EventListenerInterface
{
	private TaskEventService $taskEventService;

	public function __construct(TaskEventService $taskEventService)
	{
		$this->taskEventService = $taskEventService;
	}

	/**
	 * @param AssignTaskEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$task = $event->getTask();

		$this->createTaskEvent($task);
		// TODO: Если задачу выполнили, то уведомление создателю об этом
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function createTaskEvent(Task $task): void
	{
		$this->taskEventService->create(TaskEvent::EVENT_TYPE_STATUS_CHANGED, $task->lastHistory);
	}
}