<?php

namespace app\factories;

use app\helpers\ArrayHelper;
use app\models\TaskHistory;
use app\models\views\TaskHistoryView;
use app\usecases\TaskHistory\RelatedDataProvider;

class TaskHistoryViewFactory
{
	private RelatedDataProvider $dataProvider;

	public function __construct(RelatedDataProvider $dataProvider)
	{
		$this->dataProvider = $dataProvider;
	}

	/**
	 * @param TaskHistory[] $histories
	 *
	 * @return TaskHistoryView[]
	 */
	public function createViews(array $histories): array
	{
		if (ArrayHelper::empty($histories)) {
			return [];
		}

		[$observerIds, $tagIds] = $this->collectIds($histories);

		$observers = $this->dataProvider->getUsers($observerIds);
		$tags      = $this->dataProvider->getTags($tagIds);

		return ArrayHelper::map(
			$histories,
			fn(TaskHistory $history) => $this->createView($history, $observers, $tags)
		);
	}

	/** @param TaskHistory[] $histories */
	private function collectIds(array $histories): array
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

	private function createView(
		TaskHistory $history,
		array $observers,
		array $tags
	): TaskHistoryView
	{
		$view = new TaskHistoryView();
		$view->setAttributes($history->getAttributes(), false);

		$state = $history->jsonState;

		$view->observers = $this->mapRelatedData($state['observer_ids'] ?? [], $observers);
		$view->tags      = $this->mapRelatedData($state['tag_ids'] ?? [], $tags);

		return $view;
	}

	private function mapRelatedData(array $ids, array $items): array
	{
		return ArrayHelper::filter(
			ArrayHelper::map($ids, static fn($id) => $items[$id] ?? null),
			static fn($item) => !is_null($item)
		);
	}
}