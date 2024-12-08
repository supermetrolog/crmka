<?php

declare(strict_types=1);

namespace app\usecases\TaskHistory;

use app\dto\TaskHistory\TaskHistoryDto;
use app\factories\TaskHistoryViewFactory;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskHistory;
use app\models\views\TaskHistoryView;
use app\repositories\TaskHistoryRepository;
use Throwable;
use yii\base\ErrorException;
use yii\db\StaleObjectException;
use yii\helpers\Json;

class TaskHistoryService
{
	private TaskHistoryRepository  $repository;
	private TaskHistoryViewFactory $viewFactory;

	public function __construct(
		TaskHistoryRepository $repository,
		TaskHistoryViewFactory $viewFactory
	)
	{
		$this->repository  = $repository;
		$this->viewFactory = $viewFactory;
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function create(TaskHistoryDto $dto): TaskHistory
	{
		$prevTaskHistory   = $this->repository->findLastByTaskId($dto->task->id);
		$prevTaskHistoryId = !is_null($prevTaskHistory) ? $prevTaskHistory->id : null;

		$taskState = $this->generateTaskState($dto);

		$model = new TaskHistory([
			'task_id' => $dto->task->id,
			'prev_id' => $prevTaskHistoryId,
			'state'   => $taskState,

			'user_id'         => $dto->task->user_id,
			'message'         => $dto->task->message,
			'status'          => $dto->task->status,
			'start'           => $dto->task->start,
			'end'             => $dto->task->end,
			'created_by_type' => $dto->createdBy::getMorphClass(),
			'created_by_id'   => $dto->createdBy->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	private function generateTaskState(TaskHistoryDto $dto): string
	{
		return Json::encode([
			'tag_ids'      => $dto->tagIds,
			'observer_ids' => $dto->observerIds
		]);
	}

	/* @return TaskHistoryView[] */
	public function generateHistory(Task $task): array
	{
		$models = $this->repository->findByTaskId($task->id);

		return $this->viewFactory->createViews($models);
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(TaskHistory $task): void
	{
		$task->delete();
	}
}