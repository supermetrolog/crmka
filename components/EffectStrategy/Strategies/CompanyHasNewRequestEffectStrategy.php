<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Throwable;
use yii\base\Exception;

class CompanyHasNewRequestEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Новый запрос у %s (#%s), нужно оформить его.';

	private CreateEffectTaskService $effectTaskService;

	public function __construct(
		CreateEffectTaskService $effectTaskService
	)
	{
		$this->effectTaskService = $effectTaskService;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->hasPositiveAnswer() && $survey->chatMember->isCompanyChatMember();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		/** @var Company $chatMemberModel */
		$chatMemberModel = $survey->chatMember->model;

		$this->effectTaskService->createTaskForMessage($surveyChatMemberMessage, $survey->user, $surveyQuestionAnswer, $this->getTaskMessage($chatMemberModel));
	}

	protected function getTaskMessage(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}
}