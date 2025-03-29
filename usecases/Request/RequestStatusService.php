<?php

declare(strict_types=1);

namespace app\usecases\Request;

use app\dto\Request\PassiveRequestDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Request;
use app\usecases\Timeline\TimelineService;
use Throwable;

class RequestStatusService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TimelineService              $timelineService;
	private RequestService               $requestService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TimelineService $timelineService,
		RequestService $requestService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->timelineService     = $timelineService;
		$this->requestService      = $requestService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function markAsPassive(Request $request, PassiveRequestDto $dto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->requestService->markAsPassive($request, $dto);

			$timelines = $request->activeTimelines;

			foreach ($timelines as $timeline) {
				$this->timelineService->markAsPassive($timeline);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	public function markAsActive(Request $request): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->requestService->markAsActive($request);

			$timeline = $request->mainTimeline;

			if ($timeline && $timeline->isPassive()) {
				$this->timelineService->markAsActive($timeline);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}