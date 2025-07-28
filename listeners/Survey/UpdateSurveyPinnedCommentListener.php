<?php

namespace app\listeners\Survey;

use app\dto\EntityMessageLink\EntityMessageLinkDto;
use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\events\Survey\CompleteSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\Survey;
use app\repositories\ChatMemberMessageRepository;
use app\usecases\ChatMember\ChatMemberMessageService;
use app\usecases\EntityMessageLink\EntityMessageLinkService;
use Throwable;
use yii\base\Event;


class UpdateSurveyPinnedCommentListener implements EventListenerInterface
{
	private ChatMemberMessageService     $chatMemberMessageService;
	private ChatMemberMessageRepository  $chatMemberMessageRepository;
	private TransactionBeginnerInterface $transactionBeginner;
	private EntityMessageLinkService     $entityMessageLinkService;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		TransactionBeginnerInterface $transactionBeginner,
		ChatMemberMessageRepository $chatMemberMessageRepository,
		EntityMessageLinkService $entityMessageLinkService
	)
	{
		$this->chatMemberMessageService    = $chatMemberMessageService;
		$this->transactionBeginner         = $transactionBeginner;
		$this->chatMemberMessageRepository = $chatMemberMessageRepository;
		$this->entityMessageLinkService    = $entityMessageLinkService;
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

		if (!$survey->isCompleted()) {
			return;
		}

		$message = $this->chatMemberMessageRepository->findOneBySurveyIdAndTemplateAndChatMemberId($survey->id, ChatMemberMessage::SURVEY_TEMPLATE, $survey->chat_member_id);

		if ($message && !empty($survey->comment) && $message->message !== $survey->comment) {
			$this->updatePinnedMessage($message, $survey);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function updatePinnedMessage(ChatMemberMessage $message, Survey $survey): void
	{
		$company = $survey->chatMember->company;

		$tx = $this->transactionBeginner->begin();

		try {
			$this->chatMemberMessageService->changeMessage($message, $survey->comment);

			$pinnedMessageExists = $message->getEntityPinnedMessages()
			                               ->byEntity($company->id, $company::getMorphClass())
			                               ->exists();

			if (!$pinnedMessageExists) {
				$this->pinMessageToCompany($company, $message);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	private function pinMessageToCompany(Company $company, ChatMemberMessage $message): void
	{
		$this->entityMessageLinkService->createIfNotExists(
			new EntityMessageLinkDto([
				'entity_id'   => $company->id,
				'entity_type' => $company::getMorphClass(),
				'message'     => $message,
				'user'        => $message->fromChatMember->user,
				'kind'        => EntityMessageLinkKindEnum::COMMENT
			])
		);
	}
}