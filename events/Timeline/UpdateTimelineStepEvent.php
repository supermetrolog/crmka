<?php

namespace app\events\Timeline;

use app\events\AbstractEvent;
use app\models\miniModels\TimelineStep;

class UpdateTimelineStepEvent extends AbstractEvent
{
	public TimelineStep $step;

	public function __construct(TimelineStep $step)
	{
		parent::__construct();

		$this->step = $step;
	}

	public function getStep(): TimelineStep
	{
		return $this->step;
	}
}
