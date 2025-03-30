<?php

namespace app\listeners\Timeline;

use app\events\Request\RequestDeactivatedEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\usecases\Timeline\TimelineService;
use Throwable;
use yii\base\Event;

class SyncTimelineOnRequestDeactivationListener implements EventListenerInterface
{
	private TimelineService              $timelineService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(TimelineService $timelineService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->timelineService     = $timelineService;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param RequestDeactivatedEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$request = $event->getRequest();

		$timelines = $request->activeTimelines;

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($timelines as $timeline) {
				$this->timelineService->markAsPassive($timeline);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}