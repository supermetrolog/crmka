<?php

declare(strict_types=1);

namespace app\usecases\TaskHistory;

use app\dto\TaskHistory\TaskHistoryDto;
use app\helpers\ArrayHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\oldDb\User;
use app\models\Task;
use app\models\TaskHistory;
use app\models\TaskTag;
use app\models\views\TaskHistoryView;
use app\repositories\TaskHistoryRepository;
use yii\base\ErrorException;
use yii\helpers\Json;

class TaskHistoryService
{
	private TaskHistoryRepository $repository;
	private RelatedDataProvider   $dataProvider;

	public function __construct(
		TaskHistoryRepository $repository,
		RelatedDataProvider $dataProvider
	)
	{
		$this->repository   = $repository;
		$this->dataProvider = $dataProvider;
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function create(TaskHistoryDto $dto): TaskHistory
	{
		$prevTaskHistory   = $this->repository->findLastByTaskId($dto->task->id);
		$prevTaskHistoryId = !is_null($prevTaskHistory) ? $prevTaskHistory->id : null;

		$taskState = $this->generateTaskState($dto->task);

		$model = new TaskHistory([
			'task_id' => $dto->task->id,
			'prev_id' => $prevTaskHistoryId,
			'state'   => $taskState,

			'user_id'         => $dto->task->user_id,
			'message'         => $dto->task->message,
			'status'          => $dto->task->status,
			'start'           => $dto->task->start,
			'end'             => $dto->task->end,
			'impossible_to'   => $dto->task->impossible_to,
			'created_by_type' => $dto->createdBy::getMorphClass(),
			'created_by_id'   => $dto->createdBy->id,
			'deleted_at'      => $dto->task->deleted_at
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws ErrorException
	 */
	private function generateTaskState(Task $task): string
	{
		return Json::encode([
			'tag_ids'      => $task->getTagIds(),
			'observer_ids' => $task->getUserIdsInObservers()
		]);
	}

	/* @return TaskHistoryView[] */
	public function generateHistory(Task $task): array
	{
		$histories = $this->repository->findViewsByTaskId($task->id);

		return $this->injectRelatedData($histories);
	}


	private function injectRelatedData(array $histories): array
	{
		if (ArrayHelper::empty($histories)) {
			return [];
		}

		[$observerIds, $tagIds] = $this->collectRelatedIds($histories);

		$observers = $this->dataProvider->getUsers($observerIds);
		$tags      = $this->dataProvider->getTags($tagIds);

		return ArrayHelper::map(
			$histories,
			fn(TaskHistoryView $history) => $this->injectIntoView($history, $observers, $tags)
		);
	}

	/**
	 * @param User[]    $observers
	 * @param TaskTag[] $tags
	 */
	private function injectIntoView(TaskHistoryView $historyView, array $observers, array $tags): TaskHistoryView
	{
		$state = $historyView->getJsonState();

		$historyView->observers = $this->mapRelatedData($state['observer_ids'] ?? [], $observers);
		$historyView->tags      = $this->mapRelatedData($state['tag_ids'] ?? [], $tags);

		return $historyView;
	}

	/**
	 * @param int[]            $ids
	 * @param (TaskTag|User)[] $items
	 */
	private function mapRelatedData(array $ids, array $items): array
	{
		return ArrayHelper::filter(
			ArrayHelper::map($ids, static fn($id) => $items[$id] ?? null),
			static fn($item) => !is_null($item)
		);
	}

	/**
	 * @param TaskHistoryView[] $histories
	 */
	private function collectRelatedIds(array $histories): array
	{
		$observerIds = [];
		$tagIds      = [];

		foreach ($histories as $history) {
			$state = $history->getJsonState();

			if (ArrayHelper::notEmpty($state['observer_ids'])) {
				foreach ($state['observer_ids'] as $observerId) {
					$observerIds[$observerId] = $observerId;
				}
			}

			if (ArrayHelper::notEmpty($state['tag_ids'])) {
				foreach ($state['tag_ids'] as $tagId) {
					$tagIds[$tagId] = $tagId;
				}
			}
		}

		return [
			ArrayHelper::values($observerIds),
			ArrayHelper::values($tagIds)
		];
	}
}