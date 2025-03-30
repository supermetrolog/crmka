<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\miniModels\TimelineStep;

class TimelineStepRepository
{
	public function findOne(int $id): ?TimelineStep
	{
		/** @var ?TimelineStep */
		return TimelineStep::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): TimelineStep
	{
		/** @var TimelineStep */
		return TimelineStep::find()->byId($id)->oneOrThrow();
	}

	public function findOneByTimelineIdAndNumber(int $timelineId, int $stepNumber): ?TimelineStep
	{
		/** @var ?TimelineStep */
		return TimelineStep::find()->andWhere(['timeline_id' => $timelineId, 'number' => $stepNumber])->one();
	}
}