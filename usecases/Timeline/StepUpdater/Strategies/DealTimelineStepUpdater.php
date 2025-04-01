<?php

declare(strict_types=1);

namespace app\usecases\Timeline\StepUpdater\Strategies;

use app\dto\Timeline\TimelineStepObjectDto;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineStep;
use app\usecases\Request\RequestService;
use app\usecases\Timeline\StepUpdater\AbstractTimelineStepUpdater;
use app\usecases\Timeline\TimelineStepService;
use Throwable;

class DealTimelineStepUpdater extends AbstractTimelineStepUpdater
{
	private const SYSTEM_COMMENT_NEGATIVE = 'Сделка провалилась. Разберите ситуацию, начните таймлайн заново и подберите новые предложения.';

	private TransactionBeginnerInterface $transactionBeginner;
	private TimelineStepService          $stepService;
	private RequestService               $requestService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TimelineStepService $stepService,
		RequestService $requestService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->stepService         = $stepService;
		$this->requestService      = $requestService;
	}

	public function shouldBeProcessed(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): bool
	{
		return $step->isCompleted() || ArrayHelper::notEmpty($objectDtos) || ($step->isNegative() && !$oldStepSnapshot->isNegative());
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

		if ($step->isCompleted()) {
			$request = $step->timeline->request;

			if (!$request->isCompleted()) {
				$this->requestService->markAsCompleted($request);
			}
		} elseif (ArrayHelper::notEmpty($objectDtos)) {
			$this->setObject($step, $objectDtos[0]);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function setObject(TimelineStep $step, TimelineStepObjectDto $dto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->stepService->deleteAllObjects($step);
			$this->stepService->addObject($step, $dto);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}