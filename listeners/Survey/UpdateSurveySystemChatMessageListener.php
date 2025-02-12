<?php

namespace app\listeners\Survey;

use app\components\EffectStrategy\Factory\EffectStrategyFactory;
use app\events\Survey\CreateSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class UpdateSurveySystemChatMessageListener implements EventListenerInterface
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
		$survey = $event->getSurvey();

		$message = $this->chatMemberMessageService->getSystemMessageBySurveyIdAndTemplateAndChatMemberId(
			$survey->id,
			ChatMemberMessage::SURVEY_TEMPLATE,
			$survey->chat_member_id
		);

		if ($message) {
			$this->handleEffects($survey, $message);
		}
	}

	/**
	 * @throws NotInstantiableException
	 * @throws InvalidConfigException
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function handleEffects(Survey $survey, ChatMemberMessage $message): void
	{
		$questionAnswers = $survey->getQuestionAnswers()
		                          ->with(['effects', 'surveyQuestionAnswer' => function (SurveyQuestionAnswerQuery $query) use ($survey) {
			                          $query->bySurveyId($survey->id);
		                          }])
		                          ->all();

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($questionAnswers as $answer) {
				foreach ($answer->effects as $effect) {
					if ($this->effectStrategyFactory->hasStrategy($effect->kind) && $this->checkIfAnswerMustBeHandled($answer)) {
						$this->effectStrategyFactory->createStrategy($effect->kind)
						                            ->handle($survey, $answer, $message);
					}
				}
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws ErrorException
	 */
	private function checkIfAnswerMustBeHandled(QuestionAnswer $questionAnswer): bool
	{
		return !$questionAnswer->surveyQuestionAnswer->getTasks()->exists();
	}
}