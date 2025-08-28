<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\helpers\ArrayHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\Company\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Throwable;
use yii\base\Exception;

class HasNewRequestsEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Новый запрос у %s (#%s), подробности в опроснике.';

	private CreateEffectTaskService $effectTaskService;

	public function __construct(CreateEffectTaskService $effectTaskService)
	{
		$this->effectTaskService = $effectTaskService;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->hasAnswer() && ArrayHelper::notEmpty($answer->surveyQuestionAnswer->getJSON());
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$this->createTask($survey, $surveyQuestionAnswer, $surveyChatMemberMessage);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createTask(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$taskMessage = $this->getTaskMessage($survey);

		$this->effectTaskService->createTaskForMessage(
			$surveyChatMemberMessage,
			$survey->user,
			$surveyQuestionAnswer,
			$taskMessage
		);
	}

	public function getTaskMessage(Survey $survey): string
	{
		/** @var Company $company */
		$company = $survey->chatMember->model;

		return sprintf(
			self::TASK_MESSAGE_TEXT,
			$company->getShortName(),
			$company->id
		);
	}
}