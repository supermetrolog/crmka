<?php

namespace app\listeners\Survey;

use app\dto\Call\CreateCallDto;
use app\events\Survey\CompleteSurveyEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMember;
use app\models\Survey;
use app\usecases\ChatMember\ChatMemberService;
use Exception;
use Throwable;
use yii\base\Event;


class CreateSurveyUpdateLastCallListener implements EventListenerInterface
{
	private ChatMemberService $chatMemberService;

	public function __construct(ChatMemberService $chatMemberService)
	{
		$this->chatMemberService = $chatMemberService;
	}

	/**
	 * @param CompleteSurveyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$survey     = $event->getSurvey();
		$chatMember = $survey->chatMember;

		$this->createCall($chatMember, $survey);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws Exception
	 */
	private function createCall(ChatMember $chatMember, Survey $survey): void
	{
		$dto = new CreateCallDto([
			'user'    => $survey->user,
			'contact' => $survey->contact
		]);

		$this->chatMemberService->createCall($chatMember, $dto);
	}
}