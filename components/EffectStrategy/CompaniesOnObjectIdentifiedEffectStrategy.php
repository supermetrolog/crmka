<?php

namespace app\components\EffectStrategy;

use app\models\QuestionAnswer;
use app\models\Survey;
use app\usecases\ChatMember\ChatMemberMessageService;

class CompaniesOnObjectIdentifiedEffectStrategy implements EffectStrategyInterface
{
	private ChatMemberMessageService $chatMemberMessageService;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
	}

	public function handle(Survey $survey, QuestionAnswer $answer): void
	{
	}

	private function process(Survey $survey): void
	{
	}

	private function sendSystemMessage(): void
	{
	}
}