<?php

declare(strict_types=1);

namespace app\usecases\TaskHistory;

use app\dto\TaskHistory\TaskHistoryDto;
use app\helpers\ArrayHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Media;
use app\models\Task;
use app\models\TaskHistory;
use app\models\TaskObserver;
use app\models\TaskTag;
use app\models\User\User;
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
			'title'           => $dto->task->title,
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
		$observers = $task->observers;

		$userIdsInObservers = ArrayHelper::map($observers, static fn(TaskObserver $observer) => $observer->user_id);

		$observedUserIds = ArrayHelper::values(
			ArrayHelper::map(
				ArrayHelper::filter($observers, static fn(TaskObserver $observer) => $observer->viewed_at !== null),
				static fn(TaskObserver $observer) => $observer->user_id
			)
		);

		$fileIds = ArrayHelper::map($task->files, static fn($file) => $file->id);

		return Json::encode([
			'tag_ids'      => $task->getTagIds(),
			'observer_ids' => $userIdsInObservers,
			'observed_ids' => $observedUserIds,
			'file_ids'     => $fileIds
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

		[$observerIds, $tagIds, $fileIds] = $this->collectRelatedIds($histories);

		$observers = $this->dataProvider->getUsers($observerIds);
		$tags      = $this->dataProvider->getTags($tagIds);
		$files     = $this->dataProvider->getMedias($fileIds);

		return ArrayHelper::map(
			$histories,
			fn(TaskHistoryView $history) => $this->injectIntoView($history, $observers, $tags, $files)
		);
	}

	/**
	 * @param User[]    $observers
	 * @param TaskTag[] $tags
	 * @param Media[]   $files
	 */
	private function injectIntoView(TaskHistoryView $historyView, array $observers, array $tags, array $files): TaskHistoryView
	{
		$state = $historyView->getJsonState();

		$historyView->observers = $this->mapRelatedData($state['observer_ids'] ?? [], $observers);
		$historyView->tags      = $this->mapRelatedData($state['tag_ids'] ?? [], $tags);
		$historyView->files     = $this->mapRelatedData($state['file_ids'] ?? [], $files);

		return $historyView;
	}

	/**
	 * @param int[]                  $ids
	 * @param (TaskTag|User|Media)[] $items
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
		$fileIds     = [];

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

			if (ArrayHelper::keyExists($state, 'file_ids') && ArrayHelper::notEmpty($state['file_ids'])) {
				foreach ($state['file_ids'] as $fileId) {
					$fileIds[$fileId] = $fileId;
				}
			}
		}

		return [
			ArrayHelper::values($observerIds),
			ArrayHelper::values($tagIds),
			ArrayHelper::values($fileIds)
		];
	}
}