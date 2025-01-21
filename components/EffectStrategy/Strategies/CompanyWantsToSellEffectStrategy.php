<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Throwable;

class CompanyWantsToSellEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) хочет продать объект, нужно создать или обновить предложение.';

	private CreateEffectTaskService $effectTaskService;

	public function __construct(CreateEffectTaskService $effectTaskService)
	{
		$this->effectTaskService = $effectTaskService;
	}

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->getMaybeBool() && $survey->chatMember->model_type === ObjectChatMember::getMorphClass();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$company = $survey->chatMember->model->company;

		$this->effectTaskService->createTaskForMessage(
			$surveyChatMemberMessage,
			$survey->user,
			$surveyQuestionAnswer,
			$this->getTaskMessage($company)
		);
	}

	public function getTaskMessage(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}
}