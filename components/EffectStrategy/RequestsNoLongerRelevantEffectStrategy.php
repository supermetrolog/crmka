<?php

namespace app\components\EffectStrategy;

use app\components\EffectStrategy\Traits\HandlingByBoolEffectStrategyTrait;
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
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskTag;
use app\models\User;
use app\repositories\UserRepository;
use app\services\ChatMemberSystemMessage\RequestsNoLongerRelevantChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;

class RequestsNoLongerRelevantEffectStrategy extends AbstractEffectStrategy
{
	use HandlingByBoolEffectStrategyTrait;

	private const TASK_MESSAGE_TEXT       = '%s (#%s) - устарели запросы, необходимо отправить их в пассив.';
	private const DAYS_FOR_TASK_EXECUTION = 7; // days

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

	protected function getTaskMessageText(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$chatMember      = $survey->chatMember;
			$chatMemberModel = $chatMember->model;

			if ($chatMember->model_type !== Company::getMorphClass()) {
				$chatMemberModel = $chatMemberModel->company;
				$chatMember      = $chatMemberModel->chatMember;
			}

			$message = $this->sendSystemMessageIntoCompany($chatMember, $survey);

			$this->createTaskForMessage($message, $survey->user, $chatMemberModel);

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
	private function createTaskForMessage(ChatMemberMessage $message, User $user, Company $company): void
	{
		$moderator = $this->userRepository->getModerator();

		if (!$moderator) {
			throw new ModelNotFoundException('Moderator not found');
		}

		$dto = new CreateTaskDto([
			'user'            => $moderator,
			'message'         => $this->getTaskMessageText($company),
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