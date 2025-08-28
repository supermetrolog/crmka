<?php

namespace app\listeners\Survey;

use app\components\EffectStrategy\Factory\EffectStrategyFactory;
use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\EntityMessageLink\EntityMessageLinkDto;
use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\events\Survey\CompleteSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company\Company;
use app\models\Survey;
use app\usecases\ChatMember\ChatMemberMessageService;
use app\usecases\EntityMessageLink\EntityMessageLinkService;
use Throwable;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class CreateCancelledSurveySystemChatMessageListener implements EventListenerInterface
{
	private const SURVEY_DEFAULT_MESSAGE = 'Без важных комментариев..';

	private ChatMemberMessageService     $chatMemberMessageService;
	private EntityMessageLinkService     $entityMessageLinkService;
	private EffectStrategyFactory        $effectStrategyFactory;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(ChatMemberMessageService $chatMemberMessageService, EntityMessageLinkService $entityMessageLinkService, EffectStrategyFactory $effectStrategyFactory, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->entityMessageLinkService = $entityMessageLinkService;
		$this->effectStrategyFactory    = $effectStrategyFactory;
		$this->transactionBeginner      = $transactionBeginner;
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

			$this->handleEffects($survey, $message);

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

	/**
	 * @throws NotInstantiableException
	 * @throws InvalidConfigException
	 */
	public function handleEffects(Survey $survey, ChatMemberMessage $message): void
	{
		$questionAnswers = $survey->getQuestionAnswers()
		                          ->with(['effects', 'surveyQuestionAnswer' => function (SurveyQuestionAnswerQuery $query) use ($survey) {
			                          $query->bySurveyId($survey->id);
		                          }])
		                          ->all();

		foreach ($questionAnswers as $answer) {
			foreach ($answer->effects as $effect) {
				if ($effect->isActive() && $this->effectStrategyFactory->hasStrategy($effect->kind)) {
					$this->effectStrategyFactory->createStrategy($effect->kind)
					                            ->handle($survey, $answer, $message);
				}
			}
		}
	}
}