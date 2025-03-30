<?php

declare(strict_types=1);

namespace app\usecases\Timeline\StepUpdater;

use app\models\miniModels\TimelineStep;

abstract class AbstractTimelineStepUpdater implements TimelineStepUpdaterInterface
{
	public function handle(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): void
	{
		$effectShouldBeProcess = $this->shouldBeProcessed($step, $oldStepSnapshot, $objectDtos, $feedbackDtos);

		if ($effectShouldBeProcess) {
			$this->process($step, $oldStepSnapshot, $objectDtos, $feedbackDtos);
		}
	}
}