<?php

declare(strict_types=1);

namespace app\actions\TaskHistory;

use app\dto\TaskHistory\TaskHistoryDto;
use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskEvent;
use app\models\TaskHistory;
use app\usecases\TaskEvent\TaskEventService;
use app\usecases\TaskHistory\TaskHistoryService;
use Throwable;
use yii\base\ErrorException;

class InitTaskHistoryAction extends Action
{
	private TaskHistoryService $historyService;
	private TaskEventService   $taskEventService;

	public function __construct(
		$id,
		$controller,
		TaskHistoryService $historyService,
		TaskEventService $taskEventService,
		array $config = []
	)
	{
		$this->historyService   = $historyService;
		$this->taskEventService = $taskEventService;

		parent::__construct($id, $controller, $config);
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function run(): void
	{
		$query = Task::find()
		             ->joinWith(['lastHistory'])
		             ->with(['createdByUser'])
		             ->andWhereNull(TaskHistory::field('id'));

		/** @var Task $task */
		foreach ($query->each(1000) as $task) {
			$history = $this->historyService->create(new TaskHistoryDto([
				'task'      => $task,
				'createdBy' => $task->createdBy
			]));

			$this->taskEventService->create(TaskEvent::EVENT_TYPE_CREATED, $history);

			$this->infof('Created initial history for Task ID: %d', $task->id);
		}

		$this->infof('Initialization finished. Created initial histories for %d tasks', $query->count());
	}
}