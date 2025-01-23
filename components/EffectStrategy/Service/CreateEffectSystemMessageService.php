<?php

declare(strict_types=1);

namespace app\components\EffectStrategy\Service;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Survey;
use app\usecases\ChatMember\ChatMemberMessageService;

class CreateEffectSystemMessageService
{
	private ChatMemberMessageService $chatMemberMessageService;

	public function __construct(ChatMemberMessageService $chatMemberMessageService)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
	}

	/**
	 * @throws SaveModelException
	 * @throws \Throwable
	 */
	public function createSystemMessage(ChatMember $chatMember, Survey $survey, string $message): ChatMemberMessage
	{
		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		return $this->chatMemberMessageService->createSystemMessage($dto);
	}
}