<?php

namespace app\listeners\Survey;

use app\components\EffectStrategy\Factory\EffectStrategyFactory;
use app\events\Survey\CompleteSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\repositories\ChatMemberMessageRepository;
use app\usecases\Task\TaskService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;


class UpdateSurveySystemChatMessageListener implements EventListenerInterface
{
	private ChatMemberMessageRepository  $chatMemberMessageRepository;
	private EffectStrategyFactory        $effectStrategyFactory;
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;

	public function __construct(
		EffectStrategyFactory $effectStrategyFactory,
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		ChatMemberMessageRepository $chatMemberMessageRepository
	)
	{
		$this->effectStrategyFactory       = $effectStrategyFactory;
		$this->transactionBeginner         = $transactionBeginner;
		$this->taskService                 = $taskService;
		$this->chatMemberMessageRepository = $chatMemberMessageRepository;
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
		/** @var QuestionAnswer[] $questionAnswers */
		$questionAnswers = $survey->getQuestionAnswers()
		                          ->with(['effects', 'surveyQuestionAnswer' => function (SurveyQuestionAnswerQuery $query) use ($survey) {
			                          $query->bySurveyId($survey->id);
		                          }])
		                          ->all();

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($questionAnswers as $answer) {
				$tasks = $answer->surveyQuestionAnswer->tasks;

				foreach ($tasks as $task) {
					$this->taskService->delete($task);
				}

				foreach ($answer->effects as $effect) {
					if ($effect->isActive() && $this->effectStrategyFactory->hasStrategy($effect->kind)) {
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
}