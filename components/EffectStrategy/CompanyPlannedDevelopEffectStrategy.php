<?php

namespace app\components\EffectStrategy;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ObjectChatMember;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\services\ChatMemberSystemMessage\CompanyPlannedDevelopChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;

class CompanyPlannedDevelopEffectStrategy extends AbstractEffectStrategy
{
	private ChatMemberMessageService $chatMemberMessageService;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer): void
	{
		$chatMember = $survey->chatMember;

		if ($chatMember->model_type === ObjectChatMember::getMorphClass()) {
			$companyChatMember = $chatMember->model->company->chatMember;

			$this->sendSystemMessage($companyChatMember, $survey);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendSystemMessage(ChatMember $chatMember, Survey $survey): void
	{
		$message = CompanyPlannedDevelopChatMemberSystemMessage::create()
		                                                       ->setSurveyId($survey->id)
		                                                       ->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		$this->chatMemberMessageService->createSystemMessage($dto);
	}
}