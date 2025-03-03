<?php

namespace app\services\ChatMemberSystemMessage;

use app\helpers\ArrayHelper;
use app\helpers\HTMLHelper;
use app\helpers\StringHelper;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\Survey;
use InvalidArgumentException;
use yii\base\Exception;

class CreateSurveyChatMemberSystemMessage extends AbstractChatMemberSystemMessage
{
	private ?Survey  $survey                = null;
	protected string $template              = '%s заполнил(а) опрос %s в результате разговора с %s. %s';
	private string   $withoutAnswersMessage = 'Без важных изменений';

	private string $questionRowPrefix = '— ';

	public function validateOrThrow(): void
	{
		parent::validateOrThrow();

		if (!$this->survey) {
			throw new InvalidArgumentException('Survey must be set');
		}
	}

	public function setSurvey(Survey $survey): self
	{
		$this->survey = $survey;

		return $this;
	}

	/**
	 * @throws Exception
	 */
	protected function generateAdditionalMessage(): string
	{
		$questionsWithSurveyAnswers = $this->survey->getQuestions()->with(['answers.surveyQuestionAnswer' => function (SurveyQuestionAnswerQuery $query) {
			$query->bySurveyId($this->survey->id);
		}, 'answers.field'])->all();

		$questionMessages = [];

		foreach ($questionsWithSurveyAnswers as $question) {
			$answerMessages = [];

			foreach ($question->answers as $answer) {
				if (!$answer->hasAdditionalMessage() || !$answer->surveyQuestionAnswer->hasAnswer()) {
					continue;
				}

				$shouldBeInserted = false;

				if ($answer->field->canBeConvertedToBool()) {
					$shouldBeInserted = $answer->surveyQuestionAnswer->getBool();
				} elseif ($answer->field->canBeConvertedToString()) {
					$value = $answer->surveyQuestionAnswer->getMaybeString();

					$shouldBeInserted = StringHelper::isString($value) && StringHelper::isNotEmpty($value);
				}

				if ($shouldBeInserted) {
					$answerMessages[] = $answer->message;
				}
			}

			if (ArrayHelper::notEmpty($answerMessages)) {
				$questionMessages[] = $this->questionRowPrefix . StringHelper::join(StringHelper::SPACED_COMMA, ...$answerMessages);
			}
		}

		if (ArrayHelper::notEmpty($questionMessages)) {
			return StringHelper::join(HTMLHelper::TAG_LINE_BREAK, 'Главное:', ...$questionMessages);
		}

		return $this->withoutAnswersMessage;
	}

	/**
	 * @throws Exception
	 */
	public function getTemplateArgs(): array
	{
		return [
			HTMLHelper::bold($this->survey->user->userProfile->getMediumName()),
			HTMLHelper::bold("#{$this->survey->id}"),
			HTMLHelper::bold($this->survey->contact->getFullName()),
			$this->generateAdditionalMessage()
		];
	}
}