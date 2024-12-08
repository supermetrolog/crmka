<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\helpers\ArrayHelper;
use app\models\Task;
use app\models\TaskEvent;
use app\models\TaskTaskTag;
use yii\base\ErrorException;

class ChangeTaskTrackerService
{
	private array $trackedAttributes = [
		'status'        => TaskEvent::EVENT_TYPE_STATUS_CHANGED,
		'message'       => TaskEvent::EVENT_TYPE_DESCRIPTION_CHANGED,
		'user_id'       => TaskEvent::EVENT_TYPE_ASSIGNED,
		'start'         => TaskEvent::EVENT_TYPE_STARTING_DATE_CHANGED,
		'end'           => TaskEvent::EVENT_TYPE_ENDING_DATE_CHANGED,
		'impossible_to' => TaskEvent::EVENT_TYPE_IMPOSSIBLE_TO_CHANGED
	];

	/**
	 * @throws ErrorException
	 */
	public function trackChanges(Task $newTask, Task $oldTask, array $oldTagIds, array $oldObserverIds): array
	{
		$changedAttributes = $this->trackPrimitiveChanges($newTask, $oldTask);

		if ($this->hasTagsChanges($newTask, $oldTagIds)) {
			$changedAttributes['tag_ids'] = TaskEvent::EVENT_TYPE_TAGS_CHANGED;
		}

		if ($this->hasObserverChanges($newTask, $oldObserverIds)) {
			$changedAttributes['observer_ids'] = TaskEvent::EVENT_TYPE_OBSERVERS_CHANGED;
		}

		return $changedAttributes;
	}

	private function trackPrimitiveChanges(Task $newTask, Task $oldTask): array
	{
		$changedAttributes = [];

		foreach ($this->trackedAttributes as $attribute => $event) {
			if ($newTask->getAttribute($attribute) !== $oldTask->getAttribute($attribute)) {
				$changedAttributes[$attribute] = $event;
			}
		}

		return $changedAttributes;
	}

	/**
	 * @throws ErrorException
	 */
	private function hasTagsChanges(Task $task, array $oldTagIds): bool
	{
		$newTagIds = $task->getTaskTags()->select(TaskTaskTag::field('task_tag_id'))->column();

		return !ArrayHelper::hasEqualsValues($oldTagIds, $newTagIds);
	}

	private function hasObserverChanges(Task $task, array $oldObserverIds): bool
	{
		$newObserverIds = $task->getUserIdsInObservers();

		return !ArrayHelper::hasEqualsValues($oldObserverIds, $newObserverIds);
	}
}