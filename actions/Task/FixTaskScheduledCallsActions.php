<?php

declare(strict_types=1);

namespace app\actions\Task;

use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use yii\base\ErrorException;

class FixTaskScheduledCallsActions extends Action
{
	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 */
	public function run(): void
	{
		$this->info('Start migration old scheduled calls tasks to typed events');

		$query = Task::find()
		             ->byType(Task::TYPE_BASE)
		             ->andWhere(['like', Task::field('title'), 'Прозвонить '])
		             ->notCompleted();

		$changedTasksCount = 0;

		/** @var Task $task */
		foreach ($query->each() as $task) {
			$this->fixTaskType($task);

			$changedTasksCount++;

			$this->comment("Fix type for task #$task->id");
		}

		$this->infof('Complete. Changed tasks: %d', $changedTasksCount);
	}

	/**
	 * @throws SaveModelException
	 */
	private function fixTaskType(Task $task): void
	{
		$task->type = Task::TYPE_SCHEDULED_CALL;
		$task->saveOrThrow();
	}
}