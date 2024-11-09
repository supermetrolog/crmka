<?php

namespace app\listeners\Survey;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\events\Survey\SurveyCompanyPlannedDevelopEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMember;
use app\models\ObjectChatMember;
use app\models\Survey;
use app\services\ChatMemberSystemMessage\CompanyPlannedDevelopChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Event;


class SurveyCompanyPlannedDevelopListener implements EventListenerInterface
{
	private ChatMemberMessageService $chatMemberMessageService;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
	}

	/**
	 * @param SurveyCompanyPlannedDevelopEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$surveyId = $event->getSurveyId();
		$survey   = Survey::find()->byId($surveyId)->oneOrThrow();

		/** @var ChatMember $chatMember */
		$chatMember = $survey->chatMember;

		if ($chatMember->model_type === ObjectChatMember::getMorphClass()) {
			$companyChatMember = $chatMember->model->object->company->chatMember;

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