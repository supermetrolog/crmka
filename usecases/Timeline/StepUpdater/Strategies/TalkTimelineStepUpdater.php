<?php

declare(strict_types=1);

namespace app\usecases\Timeline\StepUpdater\Strategies;

use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineStep;
use app\usecases\Timeline\StepUpdater\AbstractTimelineStepUpdater;
use app\usecases\Timeline\TimelineService;
use app\usecases\Timeline\TimelineStepService;
use Throwable;

class TalkTimelineStepUpdater extends AbstractTimelineStepUpdater
{
	private const SYSTEM_COMMENT_NEGATIVE = 'Переговоры не состоялись. Разберите ситуацию, попробуйте понять причину и подберите новые предложения.';

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
		return ArrayHelper::notEmpty($objectDtos) || ($step->isNegative() && !$oldStepSnapshot->isNegative());
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
			$this->addObjects($step, $objectDtos);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function addObjects(TimelineStep $step, array $objectDtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->stepService->addObjects($step, $objectDtos);
			$this->service->reopenOrCreateStep($step->timeline, TimelineStep::DEAL_STEP_NUMBER);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}