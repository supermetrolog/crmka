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
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskTag;
use app\models\User;
use app\repositories\UserRepository;
use app\services\ChatMemberSystemMessage\CompanyHasNewRequestSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Exception;

class CompanyHasNewRequestEffectStrategy extends AbstractEffectStrategy
{
	private const DAYS_FOR_TASK_EXECUTION = 7; // days
	private const TASK_MESSAGE_TEXT       = 'Новый запрос у %s (#%s), нужно оформить его.';

	protected ChatMemberMessageService     $chatMemberMessageService;
	protected UserRepository               $userRepository;
	protected TransactionBeginnerInterface $transactionBeginner;

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

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->getMaybeBool();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer): void
	{
		$chatMember      = $survey->chatMember;
		$chatMemberModel = $chatMember->model;

		if ($chatMember->model_type !== Company::getMorphClass()) {
			$chatMemberModel = $chatMember->model->company;
			$chatMember      = $chatMember->model->company->chatMember;
		}

		$tx = $this->transactionBeginner->begin();

		try {
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
		$message = CompanyHasNewRequestSystemMessage::create()
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

	protected function getTaskMessage(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws Exception
	 */
	protected function createTaskForMessage(ChatMemberMessage $message, User $user, Company $company): void
	{
		$moderator = $this->userRepository->getModerator();

		if (!$moderator) {
			throw new ModelNotFoundException('Moderator not found');
		}

		$dto = new CreateTaskDto([
			'user'            => $moderator,
			'message'         => $this->getTaskMessage($company),
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