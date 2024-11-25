<?php

namespace app\components\EffectStrategy;

use app\components\EffectStrategy\Traits\HandlingByBoolEffectStrategyTrait;
use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
use app\models\Survey;
use app\services\ChatMemberSystemMessage\CompanyWantsToBuyOrBuildSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;

class CompanyWantsToBuyOrBuildEffectStrategy extends AbstractEffectStrategy
{
	use HandlingByBoolEffectStrategyTrait;

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
	public function process(Survey $survey, $additionalData = null): void
	{
		$chatMember = $survey->chatMember;

		if ($chatMember->model_type === ObjectChatMember::getMorphClass()) {
			$companyChatMember = $chatMember->model->company->chatMember;

			$this->sendSystemMessageIntoCompany($companyChatMember, $survey);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendSystemMessageIntoCompany(ChatMember $chatMember, Survey $survey): ChatMemberMessage
	{
		$message = CompanyWantsToBuyOrBuildSystemMessage::create()
		                                                ->setSurveyId($survey->id)
		                                                ->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		return $this->chatMemberMessageService->createSystemMessage($dto);
	}
}