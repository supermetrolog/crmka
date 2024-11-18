<?php

namespace app\listeners\Survey;

use app\components\EffectStrategy\Factory\EffectStrategyFactory;
use app\events\Survey\CreateSurveyEvent;
use app\listeners\EventListenerInterface;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class QuestionAnswerEffectListener implements EventListenerInterface
{
	private EffectStrategyFactory $effectStrategyFactory;

	public function __construct(EffectStrategyFactory $effectStrategyFactory)
	{
		$this->effectStrategyFactory = $effectStrategyFactory;
	}

	/**
	 * @param CreateSurveyEvent $event
	 *
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
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
			foreach ($answer->effects as $effect) {
				if ($this->effectStrategyFactory->hasStrategy($effect->kind)) {
					$this->effectStrategyFactory->createStrategy($effect->kind)
					                            ->handle($survey, $answer);
				}
			}
		}
	}
}