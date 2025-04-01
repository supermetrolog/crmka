<?php

declare(strict_types=1);

namespace app\usecases\Timeline;

use app\dto\Timeline\TimelineStepCommentDto;
use app\dto\Timeline\TimelineStepFeedbackDto;
use app\dto\Timeline\TimelineStepObjectDto;
use app\dto\Timeline\UpdateTimelineStepDto;
use app\helpers\DateTimeHelper;
use app\helpers\StringHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineActionComment;
use app\models\miniModels\TimelineStep;
use app\models\Timeline;
use Throwable;

class TimelineStepService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TimelineStepObjectService    $stepObjectService;
	private TimelineStepFeedbackService  $stepFeedbackService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TimelineStepObjectService $stepObjectService,
		TimelineStepFeedbackService $stepFeedbackService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->stepObjectService   = $stepObjectService;
		$this->stepFeedbackService = $stepFeedbackService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(Timeline $timeline, int $stepNumber): TimelineStep
	{
		$step = new TimelineStep([
			'timeline_id' => $timeline->id,
			'number'      => $stepNumber
		]);

		$step->saveOrThrow();

		return $step;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(TimelineStep $step, UpdateTimelineStepDto $dto): TimelineStep
	{
		$step->load([
			'comment'    => $dto->comment,
			'status'     => $dto->status,
			'additional' => $dto->additional,
			'done'       => $dto->done,
			'negative'   => $dto->negative,
			'date'       => DateTimeHelper::tryFormat($dto->date)
		]);

		$step->saveOrThrow();

		return $step;
	}

	/**
	 * @throws SaveModelException
	 */
	public function markAsProcessed(TimelineStep $step): void
	{
		$step->status     = TimelineStep::STATUS_PROCESSED;
		$step->additional = 0;
		$step->done       = 0;

		$step->saveOrThrow();
	}

	/**
	 * @param TimelineStepObjectDto[] $objectDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function addObjects(TimelineStep $step, array $objectDtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($objectDtos as $dto) {
				$this->addObject($step, $dto);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	public function addObject(TimelineStep $step, TimelineStepObjectDto $dto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$object = $this->stepObjectService->create($step, $dto);

			if ($object->hasComment()) {
				$this->stepObjectService->createComment($object);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	public function deleteAllObjects(TimelineStep $step): void
	{
		$this->stepObjectService->deleteAllByStep($step);
	}

	/**
	 * @param TimelineStepFeedbackDto[] $feedbackDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createFeedbacks(TimelineStep $step, array $feedbackDtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->stepFeedbackService->deleteAllByStep($step);

			foreach ($feedbackDtos as $dto) {
				$this->stepFeedbackService->create($step, $dto);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	public function createComment(TimelineStep $step, TimelineStepCommentDto $dto): TimelineActionComment
	{
		$comment = new TimelineActionComment([
			'timeline_step_id'     => $step->id,
			'timeline_id'          => $step->timeline_id,
			'timeline_step_number' => $step->number,
			'letter_id'            => $dto->letter_id,
			'comment'              => StringHelper::trim($dto->comment),
			'title'                => $dto->title,
			'type'                 => $dto->type
		]);

		$comment->saveOrThrow();

		return $comment;
	}

	/**
	 * @throws SaveModelException
	 */
	public function createSystemComment(TimelineStep $step, string $comment, int $type = TimelineActionComment::TYPE_DEFAULT): TimelineActionComment
	{
		return $this->createComment(
			$step,
			new TimelineStepCommentDto([
				'title'   => TimelineActionComment::SYSTEM_COMMENT_TITLE,
				'type'    => $type,
				'comment' => $comment,
			])
		);
	}
}