<?php

namespace app\components\EffectStrategy\Strategies;

use app\builders\Task\TaskBuilderFactory;
use app\components\EffectStrategy\AbstractEffectStrategy;
use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\User;
use app\services\ChatMemberSystemMessage\CompanyWantsToBuyOrBuildSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Exception;

class CompanyWantsToBuyOrBuildEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) - хотят купить или построить объект, нужно предложить им.';

	private ChatMemberMessageService     $chatMemberMessageService;
	private TaskBuilderFactory           $taskBuilderFactory;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		TaskBuilderFactory $taskBuilderFactory,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->taskBuilderFactory       = $taskBuilderFactory;
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
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$chatMember      = $survey->chatMember;
		$chatMemberModel = $chatMember->model;

		$tx = $this->transactionBeginner->begin();

		try {
			if ($chatMember->model_type !== Company::getMorphClass()) {
				$chatMemberModel   = $chatMemberModel->company;
				$companyChatMember = $chatMemberModel->chatMember;

				$this->sendSystemMessageIntoCompany($companyChatMember, $survey);
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
		$message = CompanyWantsToBuyOrBuildSystemMessage::create()
		                                                ->setSurveyId($survey->id)
		                                                ->toMessage();

		$dto = new CreateChatMemberSystemMessageDto([
			'message'    => $message,
			'to'         => $chatMember,
			'surveyIds'  => [$survey->id],
			'contactIds' => [$survey->contact_id],
		]);

		$this->chatMemberMessageService->createSystemMessage($dto);
	}

	public function getTaskMessage(Company $company): string
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
		$dto = $this->taskBuilderFactory
			->createEffectBuilder()
			->setMessage($this->getTaskMessage($company))
			->setCreatedBy($user)
			->build();

		$this->chatMemberMessageService->createTask($message, $dto);
	}
}
