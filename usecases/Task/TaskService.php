<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\dto\Media\CreateMediaDto;
use app\dto\Media\DeleteMediaDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\Task\ChangeTaskStatusDto;
use app\dto\Task\UpdateTaskDto;
use app\dto\TaskObserver\CreateTaskObserverDto;
use app\events\Task\CreateFileTaskEvent;
use app\events\Task\DeleteFileTaskEvent;
use app\exceptions\services\RelationNotExistsException;
use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Media;
use app\models\Task;
use app\models\TaskObserver;
use app\models\User;
use app\usecases\Media\CreateMediaService;
use app\usecases\Media\MediaService;
use app\usecases\Relation\RelationService;
use app\usecases\TaskObserver\TaskObserverService;
use DateTimeInterface;
use Exception;
use Throwable;
use UnexpectedValueException;
use yii\base\ErrorException;
use yii\base\InvalidCallException;
use yii\db\StaleObjectException;

class TaskService
{

	private TransactionBeginnerInterface $transactionBeginner;
	private TaskObserverService          $taskObserverService;
	private RelationService              $relationService;
	private MediaService                 $mediaService;
	private CreateMediaService           $createMediaService;
	private EventManager                 $eventManager;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskObserverService $taskObserverService,
		RelationService $relationService,
		MediaService $mediaService,
		CreateMediaService $createMediaService,
		EventManager $eventManager
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->taskObserverService = $taskObserverService;
		$this->relationService     = $relationService;
		$this->mediaService        = $mediaService;
		$this->createMediaService  = $createMediaService;
		$this->eventManager        = $eventManager;
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function update(Task $task, UpdateTaskDto $dto, User $initiator, array $mediaDtos): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$task->load([
				'message' => $dto->message,
				'start'   => DateTimeHelper::tryMakef($dto->start),
				'end'     => DateTimeHelper::tryMakef($dto->end)
			]);

			$task->saveOrThrow();

			$this->updateTags($task, $dto->tagIds);
			$this->updateObservers($task, $dto->observerIds, $initiator);
			$this->updateFiles($task, $dto->currentFiles, $mediaDtos);

			$task->refresh();

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
	private function updateObservers(Task $task, array $newObserverIds, User $initiator): void
	{
		$currentObserverIds = $task->getUserIdsInObservers();

		$addedObservers   = ArrayHelper::diff($newObserverIds, $currentObserverIds);
		$removedObservers = ArrayHelper::diff($currentObserverIds, $newObserverIds);

		foreach ($addedObservers as $observerId) {
			$this->taskObserverService->create(new CreateTaskObserverDto([
				'task_id'       => $task->id,
				'user_id'       => $observerId,
				'created_by_id' => $initiator->id,
			]));
		}

		if (ArrayHelper::notEmpty($removedObservers)) {
			$this->taskObserverService->deleteAll([
				'task_id' => $task->id,
				'user_id' => $removedObservers,
			]);
		}
	}

	/**
	 * @throws Throwable
	 * @throws \yii\db\Exception
	 */
	private function updateTags(Task $task, array $newTagIds): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$task->updateManyToManyRelations('tags', $newTagIds);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/* @throws SaveModelException */
	public function accept(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_ACCEPTED);
	}

	/* @throws SaveModelException */
	public function done(Task $task): void
	{
		$this->setStatus($task, Task::STATUS_DONE);
	}

	/* @throws SaveModelException */
	public function impossible(Task $task, ?DateTimeInterface $impossibleToDate): void
	{
		$task->impossible_to = $impossibleToDate ? $impossibleToDate->format('Y-m-d H:i:s') : null;
		$this->setStatus($task, Task::STATUS_IMPOSSIBLE);
	}

	/* @throws SaveModelException */
	public function changeStatus(Task $task, ChangeTaskStatusDto $dto): void
	{
		switch ($dto->status) {
			case Task::STATUS_DONE:
				$this->done($task);

				$observer = $task->targetUserObserver;
				if ($observer instanceof TaskObserver && $observer->isNotViewed()) {
					$this->taskObserverService->observe($observer);
				}

				break;
			case Task::STATUS_ACCEPTED:
				$this->accept($task);
				break;
			case Task::STATUS_IMPOSSIBLE:
				$this->impossible($task, $dto->impossible_to);
				break;
			default:
				throw new UnexpectedValueException('Unexpected status');
		}
	}

	/* @throws SaveModelException */
	private function setStatus(Task $task, int $status): void
	{
		$task->status = $status;
		$task->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function assign(Task $task, User $user): Task
	{
		$task->user_id = $user->id;
		$task->saveOrThrow();

		return $task;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Task $task): void
	{
		$task->delete();
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function restore(Task $task): void
	{
		if (!$task->canBeRestored()) {
			throw new InvalidCallException("Task can't be restored");
		}

		$task->restore();
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @return Media[]
	 * @throws Throwable
	 */
	public function createFiles(Task $task, array $mediaDtos): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$medias = [];

			foreach ($mediaDtos as $mediaDto) {
				$media    = $this->createMediaService->create($mediaDto);
				$medias[] = $media;

				$this->linkRelation($task, $media::getMorphClass(), $media->id);
			}

			$tx->commit();

			return $medias;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @return Media[]
	 * @throws Throwable
	 */
	public function createFilesWithEvent(Task $task, array $mediaDtos, User $initiator): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$files = $this->createFiles($task, $mediaDtos);

			$this->eventManager->trigger(new CreateFileTaskEvent($task, $initiator));

			$tx->commit();

			return $files;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param DeleteMediaDto[] $dtos
	 *
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function deleteFiles(Task $task, array $dtos, User $initiator): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($dtos as $dto) {
				$media = $this->mediaService->getById($dto->mediaId);

				$relationIsExists = $this->relationService->checkIfExistsByModels($task, $media);

				if (!$relationIsExists) {
					throw new RelationNotExistsException($task::getMorphClass(), Media::getMorphClass(), [$media->original_name]);
				}

				$this->mediaService->delete($media);
			}

			$this->eventManager->trigger(new DeleteFileTaskEvent($task, $initiator));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param int[]            $currentFileIds
	 * @param CreateMediaDto[] $newMediaDtos
	 *
	 * @throws StaleObjectException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	public function updateFiles(Task $task, array $currentFileIds, array $newMediaDtos): void
	{
		$deletedMedias = $task->getFiles()->andWhere(['not in', 'id', $currentFileIds])->all();

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($deletedMedias as $media) {
				$this->mediaService->delete($media);
			}

			$this->createFiles($task, $newMediaDtos);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param int|string $relationId
	 *
	 * @throws SaveModelException
	 */
	public function linkRelation(Task $task, string $relationType, $relationId): void
	{
		$this->relationService->create(new CreateRelationDto([
			'first_type'  => $task::getMorphClass(),
			'first_id'    => $task->id,
			'second_type' => $relationType,
			'second_id'   => $relationId,
		]));
	}

	/**
	 * @param string|number|null $relationId
	 *
	 * @throws SaveModelException
	 */
	public function linkRelationIfNeeded(Task $task, string $relationType, $relationId): void
	{
		if (!is_null($relationId)) {
			$this->linkRelation($task, $relationType, $relationId);
		}
	}

}