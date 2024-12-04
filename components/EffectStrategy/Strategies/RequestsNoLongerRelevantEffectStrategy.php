<?php

namespace app\components\EffectStrategy\Strategies;

use app\builders\Task\TaskBuilderFactory;
use app\components\EffectStrategy\AbstractEffectStrategy;
use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\User;
use app\services\ChatMemberSystemMessage\RequestsNoLongerRelevantChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;

class RequestsNoLongerRelevantEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) - устарели запросы, необходимо отправить их в пассив.';

	private ChatMemberMessageService     $chatMemberMessageService;
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskBuilderFactory           $taskBuilderFactory;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		TransactionBeginnerInterface $transactionBeginner,
		TaskBuilderFactory $taskBuilderFactory
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->transactionBeginner      = $transactionBeginner;
		$this->taskBuilderFactory       = $taskBuilderFactory;
	}

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->getMaybeBool();
	}

	protected function getTaskMessage(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$chatMember      = $survey->chatMember;
			$chatMemberModel = $chatMember->model;

			if ($chatMember->model_type !== Company::getMorphClass()) {
				$chatMemberModel = $chatMemberModel->company;
				$chatMember      = $chatMemberModel->chatMember;

				$this->sendSystemMessageIntoCompany($chatMember, $survey);
			}

			$this->createTaskForMessage($surveyChatMemberMessage, $survey->user, $chatMemberModel);

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
	private function sendSystemMessageIntoCompany(ChatMember $chatMember, Survey $survey): void
	{
		$message = RequestsNoLongerRelevantChatMemberSystemMessage::create()->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		$this->chatMemberMessageService->createSystemMessage($dto);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createTaskForMessage(ChatMemberMessage $message, User $user, Company $company): void
	{
		$dto = $this->taskBuilderFactory
			->createEffectBuilder()
			->setMessage($this->getTaskMessage($company))
			->setCreatedBy($user)
			->build();

		$this->chatMemberMessageService->createTask($message, $dto);
	}
}