<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Timeline;
use ErrorException;

class TimelineRepository
{
	public function findOne(int $id): ?Timeline
	{
		return Timeline::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Timeline
	{
		return Timeline::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @return Timeline[]
	 * @throws ErrorException
	 */
	public function findAllByRequestId(int $requestId): array
	{
		return Timeline::find()->byRequestId($requestId)
		               ->orderBy([Timeline::field('status') => SORT_ASC])
		               ->all();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneByIdWithRelationsOrThrow(int $id): Timeline
	{
		return Timeline::find()->byId($id)
		               ->with([
			               'timelineActionComments',
			               'timelineSteps.timelineStepFeedbackways', 'timelineSteps.timelineActionComments',
			               'timelineSteps.timelineStepObjects.comments',
			               'timelineSteps.timelineStepObjects.offer.object', 'timelineSteps.timelineStepObjects.offer.comments',
			               'timelineSteps.timelineStepObjects.offer.generalOffersMix.offer'
		               ])
		               ->oneOrThrow();
	}

	public function findOneByRequestIdAndConsultantIdWithRelations(int $requestId, int $consultantId): ?Timeline
	{
		return Timeline::find()->byRequestId($requestId)->byConsultantId($consultantId)
		               ->with([
			               'timelineActionComments',
			               'timelineSteps.timelineStepFeedbackways', 'timelineSteps.timelineActionComments',
			               'timelineSteps.timelineStepObjects.comments',
			               'timelineSteps.timelineStepObjects.offer.object', 'timelineSteps.timelineStepObjects.offer.comments',
			               'timelineSteps.timelineStepObjects.offer.generalOffersMix.offer'
		               ])
		               ->one();
	}
}