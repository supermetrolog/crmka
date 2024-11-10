<?php

namespace app\listeners\Survey;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\events\Survey\CreateSurveyEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\services\ChatMemberSystemMessage\CreateSurveyChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Event;


class CreateSurveySystemChatMessageListener implements EventListenerInterface
{
	private ChatMemberMessageService $chatMemberMessageService;

	public function __construct(ChatMemberMessageService $chatMemberMessageService)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
	}

	/**
	 * @param CreateSurveyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$survey     = $event->getSurvey();
		$chatMember = $event->getChatMember();

		$message = CreateSurveyChatMemberSystemMessage::create()
		                                              ->setSurvey($survey)
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