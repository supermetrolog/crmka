<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectSystemMessageService;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\services\ChatMemberSystemMessage\CompanyPlannedDevelopChatMemberSystemMessage;
use Throwable;
use yii\base\Exception;

class CompanyPlannedDevelopEffectStrategy extends AbstractEffectStrategy
{
	private CreateEffectSystemMessageService $effectSystemMessageService;

	public function __construct(
		CreateEffectSystemMessageService $effectSystemMessageService
	)
	{
		$this->effectSystemMessageService = $effectSystemMessageService;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->hasPositiveAnswer() && $survey->chatMember->model_type === ObjectChatMember::getMorphClass();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$chatMember = $survey->chatMember;

		if ($chatMember->model_type !== Company::getMorphClass()) {
			$companyChatMember = $chatMember->model->company->chatMember;
			$this->sendSystemMessageIntoCompany($companyChatMember, $survey);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendSystemMessageIntoCompany(ChatMember $chatMember, Survey $survey): void
	{
		$message = CompanyPlannedDevelopChatMemberSystemMessage::create()
		                                                       ->setSurveyId($survey->id)
		                                                       ->toMessage();

		$this->effectSystemMessageService->createSystemMessage($chatMember, $survey, $message);
	}
}