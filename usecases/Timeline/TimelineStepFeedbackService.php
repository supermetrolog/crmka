<?php

declare(strict_types=1);

namespace app\usecases\Timeline;

use app\dto\Timeline\TimelineStepFeedbackDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineStep;
use app\models\miniModels\TimelineStepFeedbackway;
use Throwable;

class TimelineStepFeedbackService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(TimelineStep $step, TimelineStepFeedbackDto $dto): TimelineStepFeedbackway
	{
		$way = new TimelineStepFeedbackway([
			'timeline_step_id' => $step->id,
			'way'              => $dto->way,
		]);

		$way->saveOrThrow();

		return $way;
	}

	public function deleteAllByStep(TimelineStep $step): void
	{
		TimelineStepFeedbackway::deleteAll(['timeline_step_id' => $step->id]);
	}
}