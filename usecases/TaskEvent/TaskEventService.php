<?php

declare(strict_types=1);

namespace app\usecases\TaskEvent;

use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskEvent;
use app\models\TaskHistory;
use Throwable;

class TaskEventService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(string $eventType, TaskHistory $taskHistory): TaskEvent
	{
		$model = new TaskEvent([
			'task_history_id' => $taskHistory->id,
			'event_type'      => $eventType
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws Throwable
	 */
	public function delete(TaskEvent $event): void
	{
		$event->delete();
	}
}