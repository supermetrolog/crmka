<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Request;
use yii\base\ErrorException;

class RequestRepository
{
	public function findOne(int $id): ?Request
	{
		return Request::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Request
	{
		return Request::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrowWithRelations(int $id): Request
	{
		return Request::find()->byId($id)
		              ->with(['regions.info'])
		              ->oneOrThrow();
	}

	/**
	 * @return Request[]
	 * @throws ErrorException
	 */
	public function findAllByCompanyId(int $id): array
	{
		return Request::find()->byCompanyId($id)->all();
	}

	/**
	 * @return Request[]
	 * @throws ErrorException
	 */
	public function findAllByCompanyIdWithRelations(int $id): array
	{
		return Request::find()
		              ->with([
			              'company',
			              'consultant.userProfile',
			              'contact.emails', 'contact.phones',
			              'directions', 'districts', 'gateTypes', 'objectClasses', 'objectTypes', 'objectTypesGeneral', 'regions.info',
			              'deal.company', 'deal.competitor', 'deal.consultant.userProfile', 'deal.offer.generalOffersMix',
			              'mainTimeline.timelineSteps.timelineStepObjects', 'mainTimeline.timelineSteps.timelineStepObjects',
			              'mainTimeline.consultant.userProfile', 'mainTimeline.timelineActionComments',
			              'mainTimeline.timelineSteps.timelineStepFeedbackways'
		              ])
		              ->byCompanyId($id)
		              ->orderBy([Request::field('created_at') => SORT_DESC])
		              ->all();
	}
}