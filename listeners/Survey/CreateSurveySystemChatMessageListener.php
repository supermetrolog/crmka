<?php

namespace app\listeners\Survey;

use app\components\EffectStrategy\Factory\EffectStrategyFactory;
use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\events\Survey\CreateSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\ChatMemberMessage;
use app\models\Survey;
use app\services\ChatMemberSystemMessage\CreateSurveyChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class CreateSurveySystemChatMessageListener implements EventListenerInterface
{
	private ChatMemberMessageService     $chatMemberMessageService;
	private EffectStrategyFactory        $effectStrategyFactory;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(ChatMemberMessageService $chatMemberMessageService, EffectStrategyFactory $effectStrategyFactory, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->effectStrategyFactory    = $effectStrategyFactory;
		$this->transactionBeginner      = $transactionBeginner;
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

		$tx = $this->transactionBeginner->begin();

		try {
			$dto = new CreateChatMemberSystemMessageDto([
				'message'    => $message,
				'to'         => $chatMember,
				'surveyIds'  => [$survey->id],
				'contactIds' => [$survey->contact_id],
				'template'   => ChatMemberMessage::SURVEY_TEMPLATE
			]);

			$message = $this->chatMemberMessageService->createSystemMessage($dto);

			$this->handleEffects($survey, $message);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
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