<?php

namespace app\listeners\Survey;

use app\components\EventManager;
use app\enum\EffectKind;
use app\events\Survey\CreateSurveyEvent;
use app\events\Survey\SurveyCompanyPlannedDevelopEvent;
use app\events\Survey\SurveyRequestsNoLongerRelevantEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use Throwable;
use yii\base\Event;


class QuestionAnswerEffectListener implements EventListenerInterface
{
	private EventManager $eventManager;

	public function __construct(
		EventManager $eventManager
	)
	{
		$this->eventManager = $eventManager;
	}

	/**
	 * @param CreateSurveyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$survey = $event->getSurvey();

		$questionAnswers = $survey->getQuestionAnswers()
		                          ->with(['effects', 'surveyQuestionAnswer' => function (SurveyQuestionAnswerQuery $query) use ($survey) {
			                          $query->bySurveyId($survey->id);
		                          }])
		                          ->all();

		foreach ($questionAnswers as $answer) {
			if ($answer->hasEffectByKind(EffectKind::REQUESTS_NO_LONGER_RELEVANT)) {
				$eventShouldBeTriggered = $answer->surveyQuestionAnswer->getBool();

				if ($eventShouldBeTriggered) {
					$event = new SurveyRequestsNoLongerRelevantEvent($survey->id);
					$this->eventManager->trigger($event);
				}
			}

			if ($answer->hasEffectByKind(EffectKind::COMPANY_PLANNED_DEVELOP)) {
				$eventShouldBeTriggered = $answer->surveyQuestionAnswer->getBool();

				if ($eventShouldBeTriggered) {
					$event = new SurveyCompanyPlannedDevelopEvent($survey->id);
					$this->eventManager->trigger($event);
				}
			}
		}
	}
}