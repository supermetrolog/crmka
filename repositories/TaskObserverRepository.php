<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskObserver;

class TaskObserverRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneByTaskIdAndUserId(int $taskId, int $userId): TaskObserver
	{
		return TaskObserver::find()
		                   ->andWhere([
			                   'task_id' => $taskId,
			                   'user_id' => $userId
		                   ])->oneOrThrow();
	}
}