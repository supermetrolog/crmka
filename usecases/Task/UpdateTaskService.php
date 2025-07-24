<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\dto\Media\CreateMediaDto;
use app\dto\Task\ChangeTaskDatesDto;
use app\dto\Task\UpdateTaskDto;
use app\events\Task\UpdateTaskEvent;
use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\TaskEvent;
use app\models\User;
use DateTimeInterface;
use Throwable;
use yii\base\ErrorException;

class UpdateTaskService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private EventManager                 $eventManager;

	private array $trackedAttributes = [
		'message' => TaskEvent::EVENT_TYPE_DESCRIPTION_CHANGED,
		'title'   => TaskEvent::EVENT_TYPE_TITLE_CHANGED,
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
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(Task $task, UpdateTaskDto $dto, User $initiator, array $mediaDtos = []): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$changedAttributes = $this->trackChanges($task, $dto, $mediaDtos);

			$this->taskService->update($task, $dto, $initiator, $mediaDtos);

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
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function changeDates(Task $task, ChangeTaskDatesDto $dto, User $initiator): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$changedAttributes = $this->trackPrimitiveChanges($task, $dto);

			$this->taskService->changeDates($task, $dto);

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
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws ErrorException
	 */
	private function trackChanges(Task $task, UpdateTaskDto $dto, array $mediaDtos): array
	{
		$changedAttributes = $this->trackPrimitiveChanges($task, $dto);

		if ($this->hasTagsChanges($task, $dto->tagIds)) {
			$changedAttributes[] = TaskEvent::EVENT_TYPE_TAGS_CHANGED;
		}

		if ($this->hasObserverChanges($task, $dto->observerIds)) {
			$changedAttributes[] = TaskEvent::EVENT_TYPE_OBSERVERS_CHANGED;
		}

		if ($this->hasFilesChanges($task, $dto->currentFiles, $mediaDtos)) {
			$changedAttributes[] = TaskEvent::EVENT_TYPE_FILES_CHANGED;
		}

		return $changedAttributes;
	}

	/**
	 * @param UpdateTaskDto|ChangeTaskDatesDto $dto
	 */
	private function trackPrimitiveChanges(Task $newTask, $dto): array
	{
		$changedAttributes = [];

		foreach ($this->trackedAttributes as $attribute => $event) {
			if (!$newTask->hasAttribute($attribute) || !$dto->hasProperty($attribute)) {
				continue;
			}

			if ($newTask->getAttribute($attribute) !== $this->parseDtoAttribute($dto->$attribute)) {
				$changedAttributes[] = $event;
			}
		}

		return $changedAttributes;
	}

	private function parseDtoAttribute($value)
	{
		if ($value instanceof DateTimeInterface) {
			return DateTimeHelper::format($value);
		}

		return $value;
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

	/**
	 * @param int[]            $currentFileIds
	 * @param CreateMediaDto[] $newMediaDtos
	 *
	 * @throws ErrorException
	 */
	private function hasFilesChanges(Task $task, array $currentFileIds, array $newMediaDtos): bool
	{
		if (ArrayHelper::notEmpty($newMediaDtos)) {
			return true;
		}

		$oldFileIds = $task->getFileIds();

		return !ArrayHelper::hasEqualsValues($oldFileIds, $currentFileIds);
	}
}