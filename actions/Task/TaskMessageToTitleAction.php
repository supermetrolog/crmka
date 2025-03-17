<?php

declare(strict_types=1);

namespace app\actions\Task;

use app\helpers\StringHelper;
use app\kernel\common\actions\Action;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskHistory;
use Throwable;

class TaskMessageToTitleAction extends Action
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		$id,
		$controller,
		TransactionBeginnerInterface $transactionBeginner,
		array $config = []
	)
	{
		$this->transactionBeginner = $transactionBeginner;

		parent::__construct($id, $controller, $config);
	}

	public function run(): void
	{
		$this->info('Start migration Task message to title');

		$query = Task::find()->with(['lastHistory']);

		$changedTasksCount = 0;

		/** @var Task $task */
		foreach ($query->each() as $task) {
			if (empty($task->title)) {
				$this->moveTaskMessageToTitle($task);
				$changedTasksCount++;
			}
		}

		$this->infof('Complete. Changed tasks: %d', $changedTasksCount);
	}

	private function moveTaskMessageToTitle(Task $task): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$taskMessageIsOverflow = StringHelper::length($task->message) > Task::TITLE_MAX_LENGTH;

			if ($taskMessageIsOverflow) {
				$task->title = StringHelper::substr($task->message, 0, Task::TITLE_MAX_LENGTH - 3) . '...';
			} else {
				$task->title   = $task->message;
				$task->message = null;
			}

			$task->saveOrThrow(false);

			$lastHistory = $task->lastHistory;

			if ($lastHistory) {
				$this->fixTaskHistory($lastHistory, $task);
			}


			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
		}
	}

	/**
	 * @throws SaveModelException
	 */
	private function fixTaskHistory(TaskHistory $history, Task $task): void
	{
		$history->title   = $task->title;
		$history->message = $task->message;

		$history->saveOrThrow(false);
	}
}