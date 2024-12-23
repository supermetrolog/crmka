<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\dto\Task\UpdateTaskDto;
use app\events\Task\UpdateTaskEvent;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskEvent;
use app\models\User;
use Throwable;
use yii\base\ErrorException;

class UpdateTaskService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private EventManager                 $eventManager;

	private array $trackedAttributes = [
		'message' => TaskEvent::EVENT_TYPE_DESCRIPTION_CHANGED,
		'start'   => TaskEvent::EVENT_TYPE_STARTING_DATE_CHANGED,
		'end'     => TaskEvent::EVENT_TYPE_ENDING_DATE_CHANGED
	];

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		EventManager $eventManager
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->taskService         = $taskService;
		$this->eventManager        = $eventManager;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(Task $task, UpdateTaskDto $dto, User $initiator): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$changedAttributes = $this->trackChanges($task, $dto);

			$this->taskService->update($task, $dto, $initiator);

			if (ArrayHelper::notEmpty($changedAttributes)) {
				$this->eventManager->trigger(new UpdateTaskEvent($task, $initiator, $changedAttributes));
			}

			$tx->commit();

			return $task;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws ErrorException
	 */
	private function trackChanges(Task $task, UpdateTaskDto $dto): array
	{
		$changedAttributes = $this->trackPrimitiveChanges($task, $dto);

		if ($this->hasTagsChanges($task, $dto->tagIds)) {
			$changedAttributes[] = TaskEvent::EVENT_TYPE_TAGS_CHANGED;
		}

		if ($this->hasObserverChanges($task, $dto->observerIds)) {
			$changedAttributes[] = TaskEvent::EVENT_TYPE_OBSERVERS_CHANGED;
		}

		return $changedAttributes;
	}

	private function trackPrimitiveChanges(Task $newTask, UpdateTaskDto $dto): array
	{
		$changedAttributes = [];

		foreach ($this->trackedAttributes as $attribute => $event) {
			if (!$newTask->hasAttribute($attribute) || !$dto->hasProperty($attribute)) {
				continue;
			}

			if ($newTask->getAttribute($attribute) !== $dto->$attribute) {
				$changedAttributes[] = $event;
			}
		}

		return $changedAttributes;
	}

	/**
	 * @param int[] $tagIds
	 *
	 * @throws ErrorException
	 */
	private function hasTagsChanges(Task $task, array $tagIds): bool
	{
		$oldTagIds = $task->getTagIds();

		return !ArrayHelper::hasEqualsValues($oldTagIds, $tagIds);
	}

	/**
	 * @param int[] $observerIds
	 */
	private function hasObserverChanges(Task $task, array $observerIds): bool
	{
		$oldObserverIds = $task->getUserIdsInObservers();

		return !ArrayHelper::hasEqualsValues($oldObserverIds, $observerIds);
	}
}