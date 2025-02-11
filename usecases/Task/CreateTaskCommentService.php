<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Media\CreateMediaDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\Task\CreateTaskCommentDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskComment;
use app\usecases\Media\CreateMediaService;
use app\usecases\Relation\RelationService;
use Throwable;

class CreateTaskCommentService
{
	private CreateMediaService           $createMediaService;
	private RelationService              $relationService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(CreateMediaService $createMediaService, RelationService $relationService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->createMediaService  = $createMediaService;
		$this->relationService     = $relationService;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateTaskCommentDto $dto, array $mediaDtos = []): TaskComment
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$comment = new TaskComment([
				'message'       => $dto->message,
				'created_by_id' => $dto->created_by_id,
				'task_id'       => $dto->task_id
			]);

			$comment->saveOrThrow();

			foreach ($mediaDtos as $mediaDto) {
				$media = $this->createMediaService->create($mediaDto);

				$this->relationService->create(
					new CreateRelationDto([
						'first_type'  => $comment::getMorphClass(),
						'first_id'    => $comment->id,
						'second_type' => $media::getMorphClass(),
						'second_id'   => $media->id
					])
				);
			}

			$tx->commit();

			$comment->refresh();

			return $comment;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}