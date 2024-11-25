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
use app\models\ObjectChatMember;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskTag;
use app\models\User;
use app\repositories\UserRepository;
use app\services\ChatMemberSystemMessage\CompanyWantsToSellSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Exception;

class CompanyWantsToSellEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT       = '%s (#%s) хочет продать объект, нужно создать или обновить предложение.';
	private const DAYS_FOR_TASK_EXECUTION = 7; // days

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

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer): void
	{
		$chatMember = $survey->chatMember;

		if ($chatMember->model_type === ObjectChatMember::getMorphClass()) {
			$company = $chatMember->model->company;

			$tx = $this->transactionBeginner->begin();

			try {
				$message = $this->sendSystemMessageIntoObject($chatMember, $survey);

				$this->createTaskForMessage($message, $survey->user, $company);

				$tx->commit();
			} catch (Throwable $th) {
				$tx->rollback();
				throw $th;
			}
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendSystemMessageIntoObject(ChatMember $chatMember, Survey $survey): ChatMemberMessage
	{
		$message = CompanyWantsToSellSystemMessage::create()
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

	public function getTaskMessageText(Company $company): string
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