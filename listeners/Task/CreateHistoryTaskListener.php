<?php

namespace app\listeners\Task;

use app\dto\TaskHistory\TaskHistoryDto;
use app\events\Task\CreateTaskEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Task;
use app\models\User;
use app\usecases\TaskHistory\TaskHistoryService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Event;

class CreateHistoryTaskListener implements EventListenerInterface
{
	private TaskHistoryService $taskHistoryService;

	public function __construct(TaskHistoryService $taskHistoryService)
	{
		$this->taskHistoryService = $taskHistoryService;
	}

	/**
	 * @param CreateTaskEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$task      = $event->getTask();
		$initiator = $event->getInitiator();

		$this->createHistory($task, $initiator);
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function createHistory(Task $task, User $createdBy): void
	{
		$dto = new TaskHistoryDto([
			'task'      => $task,
			'createdBy' => $createdBy
		]);

		$this->taskHistoryService->create($dto);
	}
}