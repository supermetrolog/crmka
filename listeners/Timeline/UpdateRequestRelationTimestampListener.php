<?php

namespace app\listeners\Timeline;

use app\events\Timeline\UpdateTimelineStepEvent;
use app\listeners\EventListenerInterface;
use app\usecases\Request\RequestService;
use yii\base\Event;


class UpdateRequestRelationTimestampListener implements EventListenerInterface
{
	private RequestService $requestService;

	public function __construct(RequestService $requestService)
	{
		$this->requestService = $requestService;
	}

	/**
	 * @param UpdateTimelineStepEvent $event
	 */
	public function handle(Event $event): void
	{
		$step = $event->getStep();

		$this->requestService->updateRelatedTimestamp($step->timeline->request);
	}
}