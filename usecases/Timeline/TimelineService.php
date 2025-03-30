<?php

declare(strict_types=1);

namespace app\usecases\Timeline;

use app\components\EventManager;
use app\dto\Timeline\CreateTimelineDto;
use app\dto\Timeline\TimelineCommentDto;
use app\dto\Timeline\TimelineStepCommentDto;
use app\dto\Timeline\TimelineStepFeedbackDto;
use app\dto\Timeline\TimelineStepObjectDto;
use app\dto\Timeline\UpdateTimelineStepDto;
use app\events\Timeline\UpdateTimelineStepEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineActionComment;
use app\models\miniModels\TimelineStep;
use app\models\Timeline;
use app\repositories\TimelineStepRepository;
use app\usecases\Timeline\StepUpdater\TimelineStepUpdaterFactory;
use InvalidArgumentException;
use Throwable;

class TimelineService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TimelineStepService          $stepService;
	private TimelineStepRepository       $stepRepository;
	private TimelineStepUpdaterFactory   $stepUpdaterFactory;
	private EventManager                 $eventManager;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TimelineStepService $stepService,
		TimelineStepRepository $stepRepository,
		TimelineStepUpdaterFactory $stepUpdaterFactory,
		EventManager $eventManager
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->stepService         = $stepService;
		$this->stepRepository      = $stepRepository;
		$this->stepUpdaterFactory  = $stepUpdaterFactory;
		$this->eventManager        = $eventManager;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateTimelineDto $dto): Timeline
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$timeline = new Timeline([
				'request_id'    => $dto->request_id,
				'consultant_id' => $dto->consultant_id
			]);

			$timeline->saveOrThrow();

			$this->createInitialStep($timeline);

			$tx->commit();

			return $timeline;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createInitialStep(Timeline $timeline): TimelineStep
	{
		return $this->stepService->create(
			$timeline,
			TimelineStep::MEETING_STEP_NUMBER
		);
	}

	/**
	 * @throws SaveModelException
	 */
	public function createComment(TimelineCommentDto $dto): TimelineActionComment
	{
		return $this->stepService->createComment($dto->timelineStep,
			new TimelineStepCommentDto([
				'type'      => $dto->type,
				'letter_id' => $dto->letter_id,
				'comment'   => $dto->comment,
				'title'     => $dto->title
			])
		);
	}

	/**
	 * @param TimelineStepCommentDto[]  $commentDtos
	 * @param TimelineStepObjectDto[]   $objectDtos
	 * @param TimelineStepFeedbackDto[] $feedbackDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function updateStep(TimelineStep $step, UpdateTimelineStepDto $dto, array $commentDtos = [], array $objectDtos = [], array $feedbackDtos = []): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$oldStepState = clone $step;

			$this->stepService->update($step, $dto);

			foreach ($commentDtos as $commentDto) {
				$this->stepService->createComment($step, $commentDto);
			}

			$stepUpdater = $this->stepUpdaterFactory->getUpdater($step);
			$stepUpdater->handle($step, $oldStepState, $objectDtos, $feedbackDtos);

			$this->eventManager->trigger(new UpdateTimelineStepEvent($step));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function reopenOrCreateStep(Timeline $timeline, int $stepNumber): TimelineStep
	{
		$step = $this->stepRepository->findOneByTimelineIdAndNumber($timeline->id, $stepNumber);

		if ($step) {
			$this->stepService->markAsProcessed($step);

			return $step;
		}

		return $this->stepService->create($timeline, $stepNumber);
	}

	/**
	 * @throws SaveModelException
	 */
	public function markAsActive(Timeline $timeline): void
	{
		if ($timeline->isActive()) {
			throw new InvalidArgumentException('Timeline is already active');
		}

		$timeline->status = Timeline::STATUS_ACTIVE;

		$timeline->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 */
	public function markAsPassive(Timeline $timeline): void
	{
		$timeline->status = Timeline::STATUS_INACTIVE;

		$timeline->saveOrThrow();
	}
}