<?php

namespace app\listeners\Timeline;

use app\events\Request\RequestActivatedEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\usecases\Timeline\TimelineService;
use Throwable;
use yii\base\Event;

class SyncTimelineOnRequestActivationListener implements EventListenerInterface
{
	private TimelineService $timelineService;

	public function __construct(TimelineService $timelineService)
	{
		$this->timelineService = $timelineService;
	}

	/**
	 * @param RequestActivatedEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$request = $event->getRequest();

		$timeline = $request->mainTimeline;

		if ($timeline && $timeline->isPassive()) {
			$this->timelineService->markAsActive($timeline);
		}
	}
}