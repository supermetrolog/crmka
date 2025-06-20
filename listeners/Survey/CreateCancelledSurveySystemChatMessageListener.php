<?php

namespace app\listeners\Survey;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\EntityPinnedMessage\EntityPinnedMessageDto;
use app\events\Survey\CompleteSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\Survey;
use app\usecases\ChatMember\ChatMemberMessageService;
use app\usecases\EntityPinnedMessage\EntityPinnedMessageService;
use Throwable;
use yii\base\Event;


class CreateCancelledSurveySystemChatMessageListener implements EventListenerInterface
{
	private const SURVEY_DEFAULT_MESSAGE = 'Без важных комментариев..';

	private ChatMemberMessageService     $chatMemberMessageService;
	private EntityPinnedMessageService   $entityPinnedMessageService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(ChatMemberMessageService $chatMemberMessageService, EntityPinnedMessageService $entityPinnedMessageService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->chatMemberMessageService   = $chatMemberMessageService;
		$this->entityPinnedMessageService = $entityPinnedMessageService;
		$this->transactionBeginner        = $transactionBeginner;
	}

	/**
	 * @param CompleteSurveyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$survey = $event->getSurvey();

		if (!$survey->isCanceled()) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$message = $this->createSystemMessage($survey, $survey->chatMember);

			if (!empty($survey->comment)) {
				$this->pinMessageToCompany($survey->chatMember->company, $message);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createSystemMessage(Survey $survey, ChatMember $chatMember): ChatMemberMessage
	{
		$dto = new CreateChatMemberSystemMessageDto([
			'message'   => $survey->comment ?? self::SURVEY_DEFAULT_MESSAGE,
			'to'        => $chatMember,
			'surveyIds' => [$survey->id],
			'template'  => ChatMemberMessage::UNAVAILABLE_SURVEY_TEMPLATE
		]);

		return $this->chatMemberMessageService->createSystemMessage($dto);
	}

	/**
	 * @throws SaveModelException
	 */
	private function pinMessageToCompany(Company $company, ChatMemberMessage $message): void
	{
		$this->entityPinnedMessageService->create(
			new EntityPinnedMessageDto([
				'entity_id'   => $company->id,
				'entity_type' => $company::getMorphClass(),
				'message'     => $message,
				'user'        => $message->fromChatMember->user
			])
		);
	}
}