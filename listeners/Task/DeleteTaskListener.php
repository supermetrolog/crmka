<?php

namespace app\listeners\Task;

use app\events\Task\DeleteTaskEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Task;
use app\models\TaskEvent;
use app\usecases\TaskEvent\TaskEventService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Event;

class DeleteTaskListener implements EventListenerInterface
{
	private TaskEventService $taskEventService;

	public function __construct(TaskEventService $taskEventService)
	{
		$this->taskEventService = $taskEventService;
	}

	/**
	 * @param DeleteTaskEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$task = $event->getTask();

		$this->createTaskEvent($task);
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function createTaskEvent(Task $task): void
	{
		$this->taskEventService->create(TaskEvent::EVENT_TYPE_DELETED, $task->lastHistory);
	}
}