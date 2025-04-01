<?php

declare(strict_types=1);

namespace app\usecases\Timeline\StepUpdater\Strategies;

use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineActionComment;
use app\models\miniModels\TimelineStep;
use app\usecases\Timeline\StepUpdater\AbstractTimelineStepUpdater;
use app\usecases\Timeline\TimelineService;
use app\usecases\Timeline\TimelineStepService;
use Throwable;

class FeedbackTimelineStepUpdater extends AbstractTimelineStepUpdater
{
	private const SYSTEM_COMMENT_FEEDBACK_RECEIVED = 'Получена обратная связь от клиента по объектам.';
	private const SYSTEM_COMMENT_FEEDBACK_WAYS     = 'Отмечены способы получения обратной связи.';
	private const SYSTEM_COMMENT_NEGATIVE          = 'Предложения клиента не заинтересовали, попробуйте поискать еще варианты.';

	private TransactionBeginnerInterface $transactionBeginner;
	private TimelineService              $service;
	private TimelineStepService          $stepService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TimelineService $service,
		TimelineStepService $stepService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->service             = $service;
		$this->stepService         = $stepService;
	}

	public function shouldBeProcessed(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): bool
	{
		return ArrayHelper::notEmpty($objectDtos) || ArrayHelper::notEmpty($feedbackDtos) || ($step->isNegative() && !$oldStepSnapshot->isNegative());
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): void
	{
		if ($step->isNegative() && !$oldStepSnapshot->isNegative()) {
			$this->stepService->createSystemComment($step, self::SYSTEM_COMMENT_NEGATIVE);

			return;
		}

		if (ArrayHelper::notEmpty($objectDtos)) {
			$this->addObjectsWithSystemComment($step, $objectDtos);

			return;
		}


		if (ArrayHelper::notEmpty($feedbackDtos)) {
			$this->createFeedbacksWithSystemComment($step, $feedbackDtos);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function addObjectsWithSystemComment(TimelineStep $step, array $objectDtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->stepService->addObjects($step, $objectDtos);
			$this->stepService->createSystemComment($step, self::SYSTEM_COMMENT_FEEDBACK_RECEIVED, TimelineActionComment::TYPE_DONE);
			$this->service->reopenOrCreateStep($step->timeline, TimelineStep::INSPECTION_STEP_NUMBER);

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
	private function createFeedbacksWithSystemComment(TimelineStep $step, array $feedbackDtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->stepService->createFeedbacks($step, $feedbackDtos);
			$this->stepService->createSystemComment($step, self::SYSTEM_COMMENT_FEEDBACK_WAYS, TimelineActionComment::TYPE_DONE);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}