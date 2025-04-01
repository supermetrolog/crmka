<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\miniModels\TimelineActionComment;
use app\models\Timeline;
use ErrorException;

class TimelineCommentRepository
{
	/**
	 * @return Timeline[]
	 * @throws ErrorException
	 */
	public function findAllByTimelineId(int $timelineId): array
	{
		return TimelineActionComment::find()->andWhere(['timeline_id' => $timelineId])
		                            ->orderBy([TimelineActionComment::field('created_at') => SORT_DESC])
		                            ->all();
	}
}