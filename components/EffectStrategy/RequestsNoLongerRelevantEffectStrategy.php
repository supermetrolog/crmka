<?php

namespace app\components\EffectStrategy;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\dto\Task\CreateTaskDto;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\Task;
use app\models\TaskTag;
use app\models\User;
use app\repositories\UserRepository;
use app\services\ChatMemberSystemMessage\RequestsNoLongerRelevantChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;

class RequestsNoLongerRelevantEffectStrategy implements EffectStrategyInterface
{
	private const DAYS_FOR_TASK_EXECUTION = 7; // days
	private const TASK_MESSAGE_TEXT       = 'Запросы компании устарели, необходимо отправить их в пассив.';

	private ChatMemberMessageService     $chatMemberMessageService;
	private UserRepository               $userRepository;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		UserRepository $userRepository,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->userRepository           = $userRepository;
		$this->transactionBeginner      = $transactionBeginner;
	}

	/**
	 * @throws Throwable
	 */
	public function handle(Survey $survey, QuestionAnswer $answer): void
	{
		$surveyQuestionAnswer  = $answer->surveyQuestionAnswer;
		$effectShouldBeProcess = $surveyQuestionAnswer->getMaybeBool();

		if ($effectShouldBeProcess) {
			$this->process($survey);
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function process(Survey $survey): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$chatMember = $survey->chatMember;

			if ($chatMember->model_type !== Company::getMorphClass()) {
				$chatMember = $chatMember->model->company->chatMember;
			}

			$message = $this->sendSystemMessageIntoCompany($chatMember, $survey);
			$this->createTaskForMessage($message, $survey->user);

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
	private function sendSystemMessageIntoCompany(ChatMember $chatMember, Survey $survey): ChatMemberMessage
	{
		$message = RequestsNoLongerRelevantChatMemberSystemMessage::create()->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		return $this->chatMemberMessageService->createSystemMessage($dto);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createTaskForMessage(ChatMemberMessage $message, User $user): void
	{
		$moderator = $this->userRepository->getModerator();

		if (!$moderator) {
			throw new ModelNotFoundException('Moderator not found');
		}

		$dto = new CreateTaskDto([
			'user'            => $moderator,
			'message'         => self::TASK_MESSAGE_TEXT,
			'status'          => Task::STATUS_CREATED,
			'start'           => DateTimeHelper::now(),
			'end'             => DateTimeHelper::now()
			                                   ->add(DateIntervalHelper::days(self::DAYS_FOR_TASK_EXECUTION)),
			'created_by_type' => $user::getMorphClass(),
			'created_by_id'   => $user->id,
			'tagIds'          => [TaskTag::SURVEY_TASK_TAG_ID],
			'observerIds'     => []
		]);

		$this->chatMemberMessageService->createTask($message, $dto);
	}
}