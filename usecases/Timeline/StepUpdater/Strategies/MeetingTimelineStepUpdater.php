<?php

declare(strict_types=1);

namespace app\usecases\Timeline\StepUpdater\Strategies;

use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineActionComment;
use app\models\miniModels\TimelineStep;
use app\usecases\Timeline\StepUpdater\AbstractTimelineStepUpdater;
use app\usecases\Timeline\TimelineService;
use app\usecases\Timeline\TimelineStepService;
use Throwable;

class MeetingTimelineStepUpdater extends AbstractTimelineStepUpdater
{
	private const SYSTEM_COMMENT_COMPLETED          = 'Запрос утвержден, переходим к отправке предложений.';
	private const SYSTEM_COMMENT_ACTIVITY_CONFIRMED = 'Состоялся первичный диалог с клиентом.';
	private const SYSTEM_COMMENT_PAUSED             = 'Шаг поставлен на паузу.';

	private const ADDITIONAL_ACTIVITY_CONFIRMED = 1;

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
		return true;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			if ($step->isCompleted() && $oldStepSnapshot->isProcessed() && $step->isDone() && !$oldStepSnapshot->isDone()) {
				$this->service->reopenOrCreateStep($step->timeline, TimelineStep::OFFER_STEP_NUMBER);
				$this->stepService->createSystemComment($step, self::SYSTEM_COMMENT_COMPLETED, TimelineActionComment::TYPE_DONE);
			} elseif ($step->isProcessed()) {
				if ($step->additional === self::ADDITIONAL_ACTIVITY_CONFIRMED) {
					$this->stepService->createSystemComment($step, self::SYSTEM_COMMENT_ACTIVITY_CONFIRMED);
				}

				if ($step->isNegative()) {
					// TODO: Добавить в сообщение дату и комментарий от пользователя
					$this->stepService->createSystemComment($step, self::SYSTEM_COMMENT_PAUSED);
				}
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}