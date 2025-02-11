<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Media\CreateMediaDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\TaskComment\UpdateTaskCommentDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Media;
use app\models\TaskComment;
use app\usecases\Media\CreateMediaService;
use app\usecases\Media\MediaService;
use app\usecases\Relation\RelationService;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\db\StaleObjectException;

class TaskCommentService
{
	private MediaService                 $mediaService;
	private CreateMediaService           $createMediaService;
	private RelationService              $relationService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(MediaService $mediaService,
		CreateMediaService $createMediaService,
		RelationService $relationService,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->mediaService        = $mediaService;
		$this->createMediaService  = $createMediaService;
		$this->relationService     = $relationService;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function update(TaskComment $comment, UpdateTaskCommentDto $dto, array $mediaDtos = []): TaskComment
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$comment->load([
				'message' => $dto->message,
			]);

			$comment->saveOrThrow();

			$this->updateFiles($comment, $dto->currentFiles, $mediaDtos);

			$tx->commit();

			$comment->refresh();

			return $comment;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param int[]            $currentFileIds
	 * @param CreateMediaDto[] $newMediaDtos
	 *
	 * @return void
	 * @throws SaveModelException
	 * @throws StaleObjectException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	public function updateFiles(TaskComment $comment, array $currentFileIds, array $newMediaDtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$deletedMedias = $comment->getFiles()->andWhere(['not in', 'id', $currentFileIds])->all();

			foreach ($deletedMedias as $media) {
				$this->mediaService->delete($media);
			}

			$this->createFiles($comment, $newMediaDtos);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @return Media[]
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createFiles(TaskComment $comment, array $mediaDtos): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$medias = [];

			foreach ($mediaDtos as $mediaDto) {
				$media = $this->createMediaService->create($mediaDto);

				$medias[] = $media;

				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $comment::getMorphClass(),
					'first_id'    => $comment->id,
					'second_type' => $media::getMorphClass(),
					'second_id'   => $media->id
				]));
			}

			$tx->commit();

			return $medias;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(TaskComment $comment): void
	{
		$comment->delete();
	}
}