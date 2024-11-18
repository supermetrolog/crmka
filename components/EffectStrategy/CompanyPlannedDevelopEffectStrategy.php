<?php

namespace app\components\EffectStrategy;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\services\ChatMemberSystemMessage\CompanyPlannedDevelopChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;

class CompanyPlannedDevelopEffectStrategy implements EffectStrategyInterface
{
	private ChatMemberMessageService $chatMemberMessageService;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
	}

	/**
	 * @throws Throwable
	 */
	public function handle(Survey $survey, QuestionAnswer $answer): void
	{
		$surveyQuestionAnswer  = $answer->surveyQuestionAnswer;
		$effectShouldBeProcess = $surveyQuestionAnswer->getMaybeBool();

		if ($effectShouldBeProcess) {
			$this->process($survey);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function process(Survey $survey): void
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