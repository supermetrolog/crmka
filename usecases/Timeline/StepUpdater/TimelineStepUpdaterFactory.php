<?php

declare(strict_types=1);

namespace app\usecases\Timeline\StepUpdater;

use app\models\miniModels\TimelineStep;
use app\usecases\Timeline\StepUpdater\Strategies\DealTimelineStepUpdater;
use app\usecases\Timeline\StepUpdater\Strategies\FeedbackTimelineStepUpdater;
use app\usecases\Timeline\StepUpdater\Strategies\InspectionTimelineStepUpdater;
use app\usecases\Timeline\StepUpdater\Strategies\InterestTimelineStepUpdater;
use app\usecases\Timeline\StepUpdater\Strategies\MeetingTimelineStepUpdater;
use app\usecases\Timeline\StepUpdater\Strategies\OfferTimelineStepUpdater;
use app\usecases\Timeline\StepUpdater\Strategies\TalkTimelineStepUpdater;
use app\usecases\Timeline\StepUpdater\Strategies\VisitTimelineStepUpdater;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class TimelineStepUpdaterFactory
{
	private array $strategies = [
		TimelineStep::MEETING_STEP_NUMBER    => MeetingTimelineStepUpdater::class,
		TimelineStep::OFFER_STEP_NUMBER      => OfferTimelineStepUpdater::class,
		TimelineStep::FEEDBACK_STEP_NUMBER   => FeedbackTimelineStepUpdater::class,
		TimelineStep::INSPECTION_STEP_NUMBER => InspectionTimelineStepUpdater::class,
		TimelineStep::VISIT_STEP_NUMBER      => VisitTimelineStepUpdater::class,
		TimelineStep::INTEREST_STEP_NUMBER   => InterestTimelineStepUpdater::class,
		TimelineStep::TALK_STEP_NUMBER       => TalkTimelineStepUpdater::class,
		TimelineStep::DEAL_STEP_NUMBER       => DealTimelineStepUpdater::class,
	];

	/**
	 * @throws NotInstantiableException
	 * @throws InvalidConfigException
	 */
	public function getUpdater(TimelineStep $step): TimelineStepUpdaterInterface
	{
		$strategyClass = $this->strategies[$step->number];

		return Yii::$container->get($strategyClass);
	}

}